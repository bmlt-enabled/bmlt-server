<script lang="ts">
  import { Modal, Button, P, Heading, Badge, Textarea } from 'flowbite-svelte';
  import { ExclamationCircleSolid, CloseOutline } from 'flowbite-svelte-icons';
  import { translations } from '../stores/localization';
  import { errorModal } from '../stores/errorModal';

  let open = $derived(!!$errorModal);

  function handleClose() {
    errorModal.hide();
  }

  function formatTimestamp(timestamp: Date): string {
    return timestamp.toLocaleString();
  }

  function copyToClipboard() {
    if (!$errorModal) return;

    const errorText = `${$translations.error}: ${$errorModal.title}
${$translations.descriptionTitle}: ${$errorModal.message}
${$errorModal.details ? `${$translations.technicalDetails}: ${$errorModal.details}` : ''}
${$translations.time}: ${formatTimestamp($errorModal.timestamp)}`;
    navigator.clipboard.writeText(errorText);
  }
</script>

{#if $errorModal}
  <Modal bind:open size="lg" class="border-4 border-red-500" dismissable={false}>
    <div class="space-y-4">
      <div class="mb-4 flex items-center gap-3">
        <ExclamationCircleSolid class="h-6 w-6 text-red-500" />
        <Heading tag="h3" class="text-red-700">{$translations.error}</Heading>
      </div>

      <div>
        <Badge color="red" class="mb-2">{$errorModal.title}</Badge>
        <P class="text-gray-900">{$errorModal.message}</P>
      </div>

      <div class="text-sm text-gray-500">
        {$translations.occurredAt}: {formatTimestamp($errorModal.timestamp)}
      </div>

      {#if $errorModal.details}
        <div class="rounded-lg border bg-gray-50 p-3">
          <P class="mb-2 text-xs text-gray-600">{$translations.technicalDetails}:</P>
          <Textarea value={$errorModal.details} readonly rows={6} class="font-mono text-xs" />
          <Button color="light" size="xs" onclick={copyToClipboard} class="mt-2">
            {$translations.copyToClipboard}
          </Button>
        </div>
      {/if}

      <div class="flex justify-end pt-4">
        <Button color="red" onclick={handleClose}>
          <CloseOutline class="mr-2 h-4 w-4" />
          {$translations.close}
        </Button>
      </div>
    </div>
  </Modal>
{/if}
