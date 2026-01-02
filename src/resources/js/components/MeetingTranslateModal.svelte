<script lang="ts">
  import { Modal } from 'flowbite-svelte';
  import type { Meeting } from 'bmlt-server-client';
  import MeetingTranslateForm from './MeetingTranslateForm.svelte';

  interface Props {
    showTranslateModal: boolean;
    meeting: Meeting
    onTranslate: (meeting: Meeting, targetLanguage: string) => void;
    onClosed: () => void;
  }

  let { showTranslateModal = $bindable(), meeting, onTranslate, onClosed }: Props = $props();
</script>

<Modal bind:open={showTranslateModal} size="sm" outsideclose>
  <div class="p-2">
    <MeetingTranslateForm {meeting} {onTranslate} {onClosed}/>
  </div>
</Modal>