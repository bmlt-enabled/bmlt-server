<script lang="ts">
  import { Button, Label, Select } from 'flowbite-svelte';
  import { createForm } from 'felte';
  import { validator } from '@felte/validator-yup';
  import * as yup from 'yup';

  import RootServerApi from '../lib/ServerApi';
  import type { Meeting } from 'bmlt-server-client';
  import { spinner } from '../stores/spinner';
  import { translations } from '../stores/localization';

  const globalSettings = settings;
  const mappings = globalSettings.languageMapping;
  const allLanguages = Object.entries(mappings).map(([code, name]) => ({ value: code, name: name }));

  interface Props {
    meeting: Meeting;
    onTranslate: (meeting: Meeting, targetLanguage: string) => void;
    onClosed: () => void;
  }

  let { meeting, onTranslate, onClosed }: Props = $props();
  let targetLanguage = $state('');

  const { form } = createForm({
    initialValues: { targetLanguage: '' },
    onSubmit: async () => {
      spinner.show();
      meeting = await RootServerApi.getMeetingTranslation(targetLanguage, meeting.id);
    },
    onError: async (error) => {
      await RootServerApi.handleErrors(error as Error, {
        handleConflictError: () => {
          console.log(error);
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      onTranslate(meeting, targetLanguage);
    },
    extend: validator({
      schema: yup.object({
        confirmed: yup.boolean().oneOf([true])
      })
    })
  });
  function exitTranslation() {
    onClosed();
  }
</script>

<form use:form>
  <div>
    <div class="mb-5">
      <Label for="targetLanguage" class="mb-2">{$translations.languageSelectTitle}</Label>
      <Select id="targetLanguage" bind:value={targetLanguage} items={allLanguages} name="targetLanguage" class="rounded-lg dark:bg-gray-600" />
    </div>
    <div class="mb-5">
      <Button type="submit" class="w-full" disabled={targetLanguage == ''}>Edit Translation</Button>
    </div>
    <div class="mb-5">
      <Button class="w-full" onclick={exitTranslation}>Exit</Button>
    </div>
  </div>
</form>
