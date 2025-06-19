<script lang="ts">
  import { Button, Card, Fileupload, Heading, P } from 'flowbite-svelte';

  import { authenticatedUser } from '../stores/apiCredentials';
  import Nav from '../components/NavBar.svelte';
  import { translations } from '../stores/localization';
  import RootServerApi from '../lib/ServerApi';
  import type { MeetingPartialUpdate } from 'bmlt-server-client';

  let files = $state<FileList | undefined>(undefined);
  let isLoading = $state(false);
  let errorMessage = $state<string | null>(null);
  let isProcessed = $state(false);

  // Statistics tracking.  Stats components are as follows. (worldId is known as 'Committee' in NAWS-speak.)
  //
  // totalRows       Total number of rows in the spreadsheet, excluding the header row
  // malformedRows   Array of row numbers in the spreadsheet of rows missing a bmlt_id or worldId (bmlt_id must be a number; worldId can be '' but not missing altogether)
  // updated         Array of bmlt_ids for meetings that were successfully updated with a new worldId
  // noUpdateNeeded  Array of bmlt_ids for meetings whose existing worldId is the same as the new worldId
  // notedAsDeleted  Array of bmlt_ids for meetings marked as 'deleted' and that are not in the database (i.e. they were actually deleted)
  // notFound        Array of bmlt_ids of meetings with a new worldId (other than 'deleted') but not found in the database.  This could be because the meeting was deleted from the database but not yet marked as 'deleted' on the spreadsheet, but could be due to an invalid bmlt_id.
  // errors          Array of strings for meetings for which the server returned an error code.  The string should consist of the bmlt_id and then the error returned by the server.
  interface Stats {
    totalRows: number;
    malformedRows: number[];
    updated: number[];
    noUpdateNeeded: number[];
    notedAsDeleted: number[];
    notFound: number[];
    errors: string[];
  }
  let stats: Stats = $state({
    totalRows: 0,
    malformedRows: [],
    updated: [],
    noUpdateNeeded: [],
    notedAsDeleted: [],
    notFound: [],
    errors: []
  });

  async function processFile(file: File): Promise<void> {
    try {
      isLoading = true;
      errorMessage = null;
      // Reset stats
      stats = {
        totalRows: 0,
        malformedRows: [],
        updated: [],
        noUpdateNeeded: [],
        notedAsDeleted: [],
        notFound: [],
        errors: []
      };

      console.log('Processing file:', file.name, file.type);

      const extension = getFileExtension(file.name);

      if (!isValidFileType(extension)) {
        throw new Error('Unsupported file type. Please upload a CSV or Excel file.');
      }

      const XLSX = await import('xlsx');
      const buffer = await file.arrayBuffer();
      const workbook = XLSX.read(buffer, {
        type: 'array',
        raw: false
      });

      const firstSheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheetName];
      // Convert the spreadsheet to json using the { header: 1 } option so that it explicitly includes the header row.  Then we can check that
      // the header contains 'bmlt_id' and 'Committee'.  If we didn't use the option, we couldn't tell whether a spreadsheet was missing the
      // worldId column altogether or if the heading was there but the worldId values were the empty string (which is legal).
      const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
      if (jsonData.length < 2) {
        throw new Error('The file has no readable data.');
      }
      const header = jsonData[0] as string[];
      const bmltIdIndex = header.findIndex((c) => c === 'bmlt_id');
      const worldIdIndex = header.findIndex((c) => c === 'Committee');
      if (bmltIdIndex < 0 || worldIdIndex < 0) {
        throw new Error('The file is missing a column for bmlt_id or Committee or both.');
      }

      stats.totalRows = jsonData.length - 1;

      // Build a map of meetingId -> worldId from the CSV
      const updateMap = new Map<number, string>();
      const meetingIds: number[] = [];

      for (let i = 1; i < jsonData.length; i++) {
        const row = jsonData[i] as any[];
        const meetingId = row[bmltIdIndex];
        if (typeof meetingId !== 'number') {
          console.warn(`Skipping row ${i + 1} due to missing bmlt_id`);
          stats.malformedRows.push(i + 1);
          continue;
        }
        const c = row[worldIdIndex];
        updateMap.set(meetingId, c === undefined ? '' : c.toString());
        meetingIds.push(meetingId);
      }

      if (meetingIds.length === 0) {
        throw new Error('No valid meeting IDs found in the file.');
      }

      console.log(`Found ${meetingIds.length} meetings to process`);

      // Fetch all meetings at once
      const existingMeetings = await RootServerApi.getMeetings({
        meetingIds: meetingIds.map((i) => i.toString()).join(',')
      });

      console.log(`Retrieved ${existingMeetings.length} existing meetings`);

      // Create a map of existing meetings for quick lookup
      const existingMeetingsMap = new Map();
      existingMeetings.forEach((meeting) => {
        existingMeetingsMap.set(meeting.id, meeting);
      });

      // Process each meeting
      for (const meetingId of meetingIds) {
        const newWorldId = updateMap.get(meetingId);
        const existingMeeting = existingMeetingsMap.get(meetingId);

        if (!existingMeeting) {
          if (newWorldId?.toLowerCase() === 'deleted') {
            stats.notedAsDeleted.push(meetingId);
          } else {
            console.warn(`Meeting ${meetingId} not found`);
            stats.notFound.push(meetingId);
          }
          continue;
        }

        // Check if update is needed
        if (existingMeeting.worldId === newWorldId || (existingMeeting.worldId === null && newWorldId === '')) {
          console.log(`Meeting ${meetingId} already has committee ${newWorldId} - no update needed`);
          stats.noUpdateNeeded.push(meetingId);
          continue;
        }

        try {
          const updatedValues: MeetingPartialUpdate = {
            worldId: newWorldId
          };
          await RootServerApi.partialUpdateMeeting(meetingId, updatedValues);
          console.log(`Successfully updated meeting ${meetingId}: ${existingMeeting.worldId} → ${newWorldId}`);
          stats.updated.push(meetingId);
        } catch (err) {
          console.error(`Failed to update meeting ${meetingId}:`, err);
          stats.errors.push(meetingId.toString() + ' ' + err);
          errorMessage = `Failed to update meeting ${meetingId}: ${err}`;
        }
      }

      console.log('Processing complete:', $state.snapshot(stats));
    } catch (err) {
      errorMessage = err instanceof Error ? err.message : 'An error occurred while processing the file';
      console.error('Error processing file:', err);
    } finally {
      isProcessed = true;
      isLoading = false;
    }
  }

  function handleProcessFile() {
    if (files && files.length > 0) {
      processFile(files[0]);
    }
  }

  function getFileExtension(filename: string): string {
    return filename.toLowerCase().split('.').pop() || '';
  }

  function isValidFileType(extension: string): boolean {
    return ['csv', 'xlsx'].includes(extension);
  }

  function meetingIdsToString(ids: number[]): string {
    return ids
      .slice()
      .sort((a, b) => a - b)
      .map((m) => m.toString())
      .join(', ');
  }

  // Reset processed state when new files are selected
  $effect(() => {
    if (files && files.length > 0) {
      isProcessed = false;
      errorMessage = null;
      stats = {
        totalRows: 0,
        malformedRows: [],
        updated: [],
        noUpdateNeeded: [],
        notedAsDeleted: [],
        notFound: [],
        errors: []
      };
    }
  });
</script>

<Nav />

{#if $authenticatedUser?.type === 'admin' || $authenticatedUser?.type === 'serviceBodyAdmin'}
  <Card class="mx-auto my-8 w-full max-w-lg bg-white p-8 text-center shadow-lg dark:bg-gray-800">
    <div class="p-4">
      <div class="mb-4">
        <Heading tag="h1" class="mb-4 text-2xl dark:text-white">{$translations.updateWorldCommitteeCodes}</Heading>
        <Fileupload bind:files accept=".xlsx,.csv" size="md" clearable={true} disabled={isLoading} id="committee-codes-upload" />
        <p class="mt-1 text-sm text-gray-500">{$translations.supportedFileFormats}</p>
      </div>

      {#if files && files.length > 0}
        <div class="mb-4">
          <Button onclick={handleProcessFile} disabled={isLoading || !files || files.length === 0 || isProcessed} color={isProcessed ? 'light' : 'primary'} class="w-full">
            {#if isLoading}
              <div class="flex items-center justify-center">
                <div class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                {$translations.processingFile}
              </div>
            {:else if isProcessed}
              {$translations.fileProcessedSuccessfully}
            {:else}
              {$translations.loadFile}
            {/if}
          </Button>
        </div>
      {/if}

      {#if isProcessed}
        <Heading tag="h2" class="mb-4 text-xl dark:text-white">{$translations.summary}</Heading>
        <div class="mb-4 space-y-2">
          <P class="text-gray-700 dark:text-gray-300">
            📊 {$translations.totalRows}: {stats.totalRows}
          </P>
          {#if stats.malformedRows.length > 0}
            <P class="text-red-600 dark:text-red-400">
              ❌ {$translations.malformedRows}: {stats.malformedRows.length}
              {$translations.rows}
            </P>
          {/if}
          {#if stats.updated.length > 0}
            <P class="text-green-600 dark:text-green-400">
              ✅ {$translations.updated}: {stats.updated.length}
              {$translations.meetings}
            </P>
          {/if}
          {#if stats.noUpdateNeeded.length > 0}
            <P class="text-blue-600 dark:text-blue-400">
              ℹ️ {$translations.noUpdateNeeded}: {stats.noUpdateNeeded.length}
              {$translations.meetings}
            </P>
          {/if}
          {#if stats.notedAsDeleted.length > 0}
            <P class="text-blue-600 dark:text-blue-400">
              ℹ️ {$translations.notedAsDeleted}: {stats.notedAsDeleted.length}
              {$translations.meetings}
            </P>
          {/if}
          {#if stats.notFound.length > 0}
            <P class="text-yellow-600 dark:text-yellow-400">
              ⚠️ {$translations.notFound}: {stats.notFound.length}
              {$translations.meetings}
            </P>
          {/if}
          {#if stats.errors.length > 0}
            <P class="text-red-600 dark:text-red-400">
              ❌ {$translations.errors}: {stats.errors.length}
              {$translations.meetings}
            </P>
          {/if}
        </div>
      {/if}

      {#if errorMessage}
        <div class="mb-4">
          <P class="text-red-700 dark:text-red-500">{errorMessage}</P>
        </div>
      {/if}

      {#if isProcessed}
        <Heading tag="h2" class="mb-4 text-xl dark:text-white">{$translations.details}</Heading>
        {#if stats.malformedRows.length > 0}
          <P class="text-red-600 dark:text-red-400">
            ❌ {$translations.malformedRows}: {meetingIdsToString(stats.malformedRows)}
          </P>
        {/if}
        {#if stats.updated.length > 0}
          <P class="text-green-600 dark:text-green-400">
            ✅ {$translations.updated}: {meetingIdsToString(stats.updated)}
          </P>
        {/if}
        {#if stats.noUpdateNeeded.length > 0}
          <P class="text-blue-600 dark:text-blue-400">
            ℹ️ {$translations.noUpdateNeeded}: {meetingIdsToString(stats.noUpdateNeeded)}
          </P>
        {/if}
        {#if stats.notedAsDeleted.length > 0}
          <P class="text-blue-600 dark:text-blue-400">
            ℹ️ {$translations.notedAsDeleted}: {meetingIdsToString(stats.notedAsDeleted)}
          </P>
        {/if}
        {#if stats.notFound.length > 0}
          <P class="text-yellow-600 dark:text-yellow-400">
            ⚠️ {$translations.notFound}: {meetingIdsToString(stats.notFound)}
          </P>
        {/if}
        {#if stats.errors.length > 0}
          {#each stats.errors as e}
            <P class="text-red-600 dark:text-red-400">
              ❌ {$translations.error}: {e}
            </P>
          {/each}
        {/if}
      {/if}
    </div>
  </Card>
{/if}
