<script lang="ts">
  import { Modal } from 'flowbite-svelte';
  import { get } from 'svelte/store';

  import type { Format } from 'bmlt-server-client';
  import FormatForm from './FormatForm.svelte';
  import { isDirty } from '../lib/utils';
  import UnsavedChangesModal from './UnsavedChangesModal.svelte';

  interface Props {
    showModal: boolean;
    selectedFormat: Format | null;
    reservedFormatKeys: string[];
    onSaveSuccess?: (format: Format) => void; // Callback function prop
    onClose?: () => void; // Callback function prop
  }

  let { showModal = $bindable(), selectedFormat, reservedFormatKeys, onSaveSuccess, onClose }: Props = $props();

  let showConfirmModal = $state(false);
  let forceClose = false;

  function handleClose() {
    if (get(isDirty) && !forceClose) {
      showModal = true;
      showConfirmModal = true;
    } else {
      showModal = false;
      forceClose = false;
      if (onClose) onClose();
    }
  }

  function handleConfirmClose() {
    showConfirmModal = false;
    forceClose = true;
    showModal = false;
    if (onClose) onClose();
  }

  function handleCancelClose() {
    showConfirmModal = false;
  }

  function handleOutsideClick(event: MouseEvent) {
    const modalContent = document.querySelector('.modal-content');
    const closeModalButton = document.querySelector('[aria-label*="Close"]');
    if ((modalContent && !modalContent.contains(event.target as Node)) || (closeModalButton && closeModalButton.contains(event.target as Node))) {
      handleClose();
    }
  }

  $effect(() => {
    if (showModal) {
      document.addEventListener('mousedown', handleOutsideClick);
    } else {
      document.removeEventListener('mousedown', handleOutsideClick);
    }
  });
</script>

<Modal bind:open={showModal} size="sm" onclose={handleClose} outsideclose={true} class="modal-content">
  <div class="p-4">
    <FormatForm {selectedFormat} {reservedFormatKeys} {onSaveSuccess} />
  </div>
</Modal>

<UnsavedChangesModal bind:open={showConfirmModal} {handleCancelClose} {handleConfirmClose} />
