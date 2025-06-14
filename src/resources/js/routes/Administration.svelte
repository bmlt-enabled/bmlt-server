<script lang="ts">
  import { Button, Card, Fileupload, Label, P } from 'flowbite-svelte';

  import { authenticatedUser } from '../stores/apiCredentials';
  import Nav from '../components/NavBar.svelte';
  import { translations } from '../stores/localization';
  import RootServerApi from '../lib/ServerApi';
  import type { MeetingPartialUpdate } from 'bmlt-server-client';

  let files = $state<FileList | undefined>(undefined);
  let tableData = $state<Record<string, any>[]>([]);
  let isLoading = $state(false);
  let errorMessage = $state<string | null>(null);
  let isProcessed = $state(false);

  // Statistics tracking
  let stats = $state({
    totalRows: 0,
    updated: 0,
    noUpdateNeeded: 0,
    notFound: 0,
    errors: 0,
    processedMeetingIds: [] as string[]
  });

  async function processFile(file: File): Promise<void> {
    try {
      isLoading = true;
      errorMessage = null;
      // Reset stats
      stats = {
        totalRows: 0,
        updated: 0,
        noUpdateNeeded: 0,
        notFound: 0,
        errors: 0,
        processedMeetingIds: []
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
        raw: extension === 'csv'
      });

      const firstSheetName = workbook.SheetNames[0];
      const worksheet = workbook.Sheets[firstSheetName];
      const jsonData = XLSX.utils.sheet_to_json(worksheet) as Record<string, any>[];

      if (jsonData.length === 0) {
        throw new Error('The file appears to be empty or has no readable data.');
      }

      tableData = jsonData;
      stats.totalRows = jsonData.length;

      // Build a map of meetingId -> committee from the CSV
      const updateMap = new Map<string, string>();
      const meetingIds: string[] = [];

      for (const [idx, row] of tableData.entries()) {
        const meetingId = row['bmlt_id'];
        const committee = row['Committee'];

        if (!meetingId || !committee) {
          console.warn(`Skipping row ${idx + 1} due to missing bmlt_id or Committee`);
          continue;
        }

        updateMap.set(meetingId.toString(), committee.toString());
        meetingIds.push(meetingId.toString());
      }

      if (meetingIds.length === 0) {
        throw new Error('No valid meeting IDs found in the file.');
      }

      console.log(`Found ${meetingIds.length} meetings to process`);

      // Fetch all meetings at once
      const existingMeetings = await RootServerApi.getMeetings({
        meetingIds: meetingIds.join(',')
      });

      console.log(`Retrieved ${existingMeetings.length} existing meetings`);

      // Create a map of existing meetings for quick lookup
      const existingMeetingsMap = new Map();
      existingMeetings.forEach((meeting) => {
        existingMeetingsMap.set(meeting.id.toString(), meeting);
      });

      // Process each meeting
      for (const meetingId of meetingIds) {
        const newCommittee = updateMap.get(meetingId);
        const existingMeeting = existingMeetingsMap.get(meetingId);

        if (!existingMeeting) {
          console.warn(`Meeting ${meetingId} not found`);
          stats.notFound++;
          continue;
        }

        // Check if update is needed
        if (existingMeeting.worldId === newCommittee) {
          console.log(`Meeting ${meetingId} already has committee ${newCommittee} - no update needed`);
          stats.noUpdateNeeded++;
          continue;
        }

        try {
          const updatedValues: MeetingPartialUpdate = {
            worldId: newCommittee
          };
          await RootServerApi.partialUpdateMeeting(Number(meetingId), updatedValues);
          console.log(`Successfully updated meeting ${meetingId}: ${existingMeeting.worldId} → ${newCommittee}`);
          stats.updated++;
          stats.processedMeetingIds.push(meetingId);
        } catch (err) {
          console.error(`Failed to update meeting ${meetingId}:`, err);
          stats.errors++;
          errorMessage = `Failed to update meeting ${meetingId}: ${err}`;
        }
      }

      console.log('Processing complete:', stats);
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

  // Reset processed state when new files are selected
  $effect(() => {
    if (files && files.length > 0) {
      isProcessed = false;
      stats = {
        totalRows: 0,
        updated: 0,
        noUpdateNeeded: 0,
        notFound: 0,
        errors: 0,
        processedMeetingIds: []
      };
    }
  });
</script>

<Nav />

{#if $authenticatedUser?.type === 'admin'}
  <Card class="mx-auto my-8 w-full max-w-lg bg-white p-8 text-center shadow-lg dark:bg-gray-800">
    <div class="p-4">
      <div class="mb-4">
        <Label for="committee-codes-upload" class="mb-2 block text-left">Updated World Committee Codes Spreadsheet</Label>
        <Fileupload bind:files accept=".xlsx,.csv" size="md" clearable={true} disabled={isLoading} id="committee-codes-upload" />
        <p class="mt-1 text-sm text-gray-500">Supported formats: Excel (.xlsx) and CSV (.csv)</p>
      </div>

      {#if files && files.length > 0}
        <div class="mb-4">
          <Button onclick={handleProcessFile} disabled={isLoading || !files || files.length === 0 || isProcessed} color={isProcessed ? 'light' : 'primary'} class="w-full">
            {#if isLoading}
              <div class="flex items-center justify-center">
                <div class="mr-2 h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></div>
                Processing file...
              </div>
            {:else if isProcessed}
              ✓ File Processed Successfully
            {:else}
              Update World Committee Codes
            {/if}
          </Button>
        </div>
      {/if}

      {#if isProcessed && (stats.updated > 0 || stats.noUpdateNeeded > 0 || stats.notFound > 0)}
        <div class="mb-4 space-y-2">
          <P class="text-gray-700 dark:text-gray-300">
            📊 Total rows: {stats.totalRows}
          </P>
          {#if stats.updated > 0}
            <P class="text-green-600 dark:text-green-400">
              ✅ Updated: {stats.updated} meetings
            </P>
          {/if}
          {#if stats.noUpdateNeeded > 0}
            <P class="text-blue-600 dark:text-blue-400">
              ℹ️ No update needed: {stats.noUpdateNeeded} meetings
            </P>
          {/if}
          {#if stats.notFound > 0}
            <P class="text-yellow-600 dark:text-yellow-400">
              ⚠️ Not found: {stats.notFound} meetings
            </P>
          {/if}
          {#if stats.errors > 0}
            <P class="text-red-600 dark:text-red-400">
              ❌ Errors: {stats.errors} meetings
            </P>
          {/if}
          {#if stats.processedMeetingIds.length > 0}
            <P class="text-sm text-gray-600 dark:text-gray-400">
              Updated meeting IDs: {stats.processedMeetingIds.join(', ')}
            </P>
          {/if}
        </div>
      {/if}

      {#if errorMessage}
        <div class="mb-4">
          <P class="text-red-700 dark:text-red-500">{errorMessage}</P>
        </div>
      {/if}
    </div>
  </Card>
{/if}
