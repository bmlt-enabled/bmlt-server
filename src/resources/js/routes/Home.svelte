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
  let errorMessage = $state<string[]>([]);
  let isProcessed = $state(false);
  let processedWorldIds = $state<string[]>([]);

  async function processFile(file: File): Promise<void> {
    try {
      isLoading = true;
      errorMessage = [];
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

      for (const [idx, row] of tableData.entries()) {
        const meetingId = row['bmlt_id'];
        const committee = row['Committee'];

        if (!meetingId || !committee) {
          errorMessage.push(`Skipping row ${idx + 1} due to missing bmlt_id or Committee`);
          continue;
        }

        try {
          const updatedMeeting: MeetingPartialUpdate = {
            worldId: committee
          };
          await RootServerApi.partialUpdateMeeting(meetingId, updatedMeeting);
          console.log(`Successfully updated meeting ${meetingId} with committee: ${committee}`);
          processedWorldIds.push(meetingId);
        } catch (err) {
          errorMessage.push(`Failed to update meeting ${meetingId}: ${err}`);
        }
      }
    } catch (err) {
      errorMessage.push(err instanceof Error ? err.message : 'An error occurred while processing the file');
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
      errorMessage = [];
      processedWorldIds = [];
    }
  });
</script>

<Nav />

{#if $authenticatedUser}
  <Card class="mx-auto my-8 w-full max-w-lg bg-white p-8 text-center shadow-lg dark:bg-gray-800">
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
      {$translations.welcome}
      {$authenticatedUser.displayName}
    </h5>
  </Card>
{/if}

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
      {#if isProcessed && processedWorldIds.length > 0}
        <div class="mb-4">
          <P class="text-green-600 dark:text-green-400">
            Processed {processedWorldIds.length} meetings: {processedWorldIds.join(', ')}
          </P>
        </div>
      {/if}
      {#if errorMessage.length > 0}
        <div class="mb-4">
          <ul class="space-y-1 text-red-700 dark:text-red-500">
            {#each errorMessage as error}
              <li class="text-sm">• {error}</li>
            {/each}
          </ul>
        </div>
      {/if}
    </div>
  </Card>
{/if}
