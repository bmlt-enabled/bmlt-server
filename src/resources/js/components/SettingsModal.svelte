<script lang="ts">
  import { Modal } from 'flowbite-svelte';
  import { get } from 'svelte/store';

  import SettingsForm from './SettingsForm.svelte';
  import { isDirty } from '../lib/utils';
  import UnsavedChangesModal from './UnsavedChangesModal.svelte';

  interface Props {
    showModal: boolean;
    serverSettings: Record<string, any>;
    onSaveSuccess?: () => void;
    onClose?: () => void;
  }

  let { showModal = $bindable(), serverSettings, onSaveSuccess, onClose }: Props = $props();

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

  function handleModalCancel(event: Event) {
    event.preventDefault();
    handleClose();
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

<Modal bind:open={showModal} size="lg" oncancel={handleModalCancel} outsideclose={true} class="modal-content">
  <div class="p-2">
    <h2 class="mb-4 text-xl font-semibold dark:text-white">{$translations.serverSettings}</h2>
    {#key showModal}
      {#if showModal}
        <SettingsForm {serverSettings} {onSaveSuccess} />
      {/if}
    {/key}
  </div>
</Modal>

<UnsavedChangesModal bind:open={showConfirmModal} {handleCancelClose} {handleConfirmClose} />
