<script lang="ts">
  import { Modal } from 'flowbite-svelte';
  import { get } from 'svelte/store';

  import type { Format, Meeting, ServiceBody } from 'bmlt-server-client';
  import MeetingEditForm from './MeetingEditForm.svelte';
  import { isDirty } from '../lib/utils';
  import UnsavedChangesModal from './UnsavedChangesModal.svelte';

  interface Props {
    showModal: boolean;
    selectedMeeting: Meeting | null;
    formats: Format[];
    serviceBodies: ServiceBody[];
    onSaved: (meeting: Meeting, targetLanguage: string) => void;
    onClosed: () => void;
    onDeleted: (meeting: Meeting) => void;
  }

  let { showModal = $bindable(), selectedMeeting, formats, serviceBodies, onSaved, onClosed, onDeleted }: Props = $props();
  let showConfirmModal = $state(false);
  let forceClose = false;

  function handleClose() {
    if (get(isDirty) && !forceClose) {
      showModal = true;
      showConfirmModal = true;
    } else {
      showModal = false;
      forceClose = false;
      onClosed();
    }
  }

  function handleConfirmClose() {
    showConfirmModal = false;
    forceClose = true;
    showModal = false;
    onClosed();
  }

  function handleCancelClose() {
    showConfirmModal = false;
  }

  function handleModalClose() {
    handleClose();
  }
</script>

<Modal
  bind:open={showModal}
  size="md"
  onclose={handleModalClose}
  outsideclose={true}
  bodyClass="p-4 md:p-5 space-y-4 flex-1 overflow-y-auto overscroll-contain min-h-[85vh] max-h-[85vh] md:min-h-[95vh] md:max-h-[95vh]"
  class="max-h-[85vh] min-h-[85vh] md:max-h-[95vh] md:min-h-[95vh]"
>
  <div class="p-2">
    <MeetingEditForm {selectedMeeting} {serviceBodies} {formats} {onSaved} {onClosed} {onDeleted} />
  </div>
</Modal>
<UnsavedChangesModal bind:open={showConfirmModal} {handleCancelClose} {handleConfirmClose} />
