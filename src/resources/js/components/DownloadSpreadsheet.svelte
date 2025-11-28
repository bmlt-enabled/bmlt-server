<script lang="ts">
  import { Button } from 'flowbite-svelte';
  import { DownloadOutline } from 'flowbite-svelte-icons';
  import { translations } from '../stores/localization';

  interface Props {
    data: any[];
    filename: string;
    disabled?: boolean;
  }

  let { data, filename, disabled = false }: Props = $props();
  let isLoading = $state(false);
  let error = $state<string | null>(null);

  function generateFilename(): string {
    const now = new Date();
    const pad = (n: number) => String(n).padStart(2, '0');
    return `${filename}_${now.getFullYear()}_${pad(now.getMonth() + 1)}_${pad(now.getDate())}_${pad(now.getHours())}_${pad(now.getMinutes())}_${pad(now.getSeconds())}.xlsx`;
  }

  async function downloadSpreadsheet() {
    if (!data || data.length === 0) {
      error = 'No data to download';
      return;
    }

    isLoading = true;
    error = null;

    try {
      const XLSX = await import('xlsx');
      const ws = XLSX.utils.json_to_sheet(data);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, ws, 'Sheet1');
      XLSX.writeFileXLSX(workbook, generateFilename());
    } catch (err) {
      console.error('Error during download:', err);
      error = err instanceof Error ? err.message : 'An error occurred';
    } finally {
      isLoading = false;
    }
  }
</script>

<Button color="blue" size="xs" disabled={isLoading || disabled || !data || data.length === 0} onclick={downloadSpreadsheet} class={isLoading ? 'cursor-default' : 'cursor-pointer'}>
  {#if isLoading}
    <svg class="mr-2 h-4 w-4 animate-spin" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
    </svg>
    {$translations.downloading}
  {:else}
    <DownloadOutline class="me-2 h-4 w-4" />
    {$translations.downloadSpreadsheet}
  {/if}
</Button>

{#if error}
  <p class="mt-2 text-sm text-red-600 dark:text-red-500">{error}</p>
{/if}
