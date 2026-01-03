<script lang="ts">
  import { onMount } from 'svelte';
  import { Button, Checkbox, Helper, P, Alert } from 'flowbite-svelte';
  import { createForm } from 'felte';
  import { validator } from '@felte/validator-yup';
  import * as yup from 'yup';

  import RootServerApi from '../lib/ServerApi';
  import type { ServiceBody, Meeting } from 'bmlt-server-client';
  import { spinner } from '../stores/spinner';
  import { translations } from '../stores/localization';

  interface Props {
    deleteServiceBody: ServiceBody;
    onDeleteSuccess?: (serviceBody: ServiceBody) => void; // Callback function prop
  }

  let { deleteServiceBody, onDeleteSuccess }: Props = $props();
  let confirmed = $state(false);
  let forceDelete = $state(false);
  let errorMessage: string | undefined = $state();
  let meetings: Meeting[] = $state([]);
  let loadingMeetings = $state(true);

  onMount(async () => {
    try {
      meetings = await RootServerApi.getMeetings({ serviceBodyIds: String(deleteServiceBody.id) });
    } catch (error) {
      console.error('Failed to load meetings:', error);
    } finally {
      loadingMeetings = false;
    }
  });

  const { form } = createForm({
    initialValues: { ServiceBodyId: deleteServiceBody?.id, confirmed: false, forceDelete: false },
    onSubmit: async () => {
      spinner.show();
      await RootServerApi.deleteServiceBody(deleteServiceBody.id, forceDelete);
    },
    onError: async (error) => {
      await RootServerApi.handleErrors(error as Error, {
        handleConflictError: () => {
          confirmed = false;
          errorMessage = $translations.serviceBodyDeleteConflictError;
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      onDeleteSuccess?.(deleteServiceBody);
    },
    extend: validator({
      schema: yup.object({
        confirmed: yup.boolean().oneOf([true]),
        forceDelete: yup.boolean()
      })
    })
  });
</script>

<form use:form>
  <div>
    <P class="mb-5">{$translations.confirmDeleteServiceBody}</P>
    <P class="mb-5 font-semibold">{deleteServiceBody.name}</P>

    {#if loadingMeetings}
      <P class="mb-5 text-sm text-gray-600 dark:text-gray-400">{$translations.loading}</P>
    {:else if meetings.length > 0}
      <Alert color="yellow" class="mb-5">
        <P class="mb-2 font-semibold text-gray-800 dark:text-gray-900">{meetings.length} {$translations.serviceBodyHasMeetings}</P>
        <div class="max-h-32 overflow-y-auto text-sm text-gray-800 dark:text-gray-900">
          <ul class="list-disc pl-5">
            {#each meetings.slice(0, 10) as meeting}
              <li>{meeting.name}</li>
            {/each}
            {#if meetings.length > 10}
              <li class="font-semibold">... {meetings.length - 10} {$translations.more}</li>
            {/if}
          </ul>
        </div>
      </Alert>

      <div class="mb-5">
        <Checkbox bind:checked={forceDelete} name="forceDelete">{$translations.serviceBodyForceDelete}</Checkbox>
        {#if forceDelete}
          <Helper class="mt-2" color="red">
            {$translations.serviceBodyDeleteForceWarning}
          </Helper>
        {/if}
      </div>
    {/if}

    <div class="mb-5">
      <Checkbox bind:checked={confirmed} name="confirmed">{$translations.confirmYesImSure}</Checkbox>
      <Helper class="mt-4" color="red">
        {#if errorMessage}
          {errorMessage}
        {/if}
      </Helper>
    </div>

    <div class="mb-5">
      <Button type="submit" class="w-full" disabled={!confirmed || (meetings.length > 0 && !forceDelete)}>{$translations.delete}</Button>
    </div>
  </div>
</form>
