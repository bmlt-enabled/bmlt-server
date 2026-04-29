<script lang="ts">
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Badge, Button, Helper, Input, Label, MultiSelect, Select, Textarea } from 'flowbite-svelte';
  import * as yup from 'yup';

  import { onMount } from 'svelte';

  import { spinner } from '../stores/spinner';
  import RootServerApi from '../lib/ServerApi';
  import { isDirty, formIsDirty } from '../lib/utils';
  import type { ServiceBody, ServiceBodyCreate, User } from 'bmlt-server-client';
  import { translations } from '../stores/localization';
  import { authenticatedUser } from '../stores/apiCredentials';

  interface Props {
    selectedServiceBody: ServiceBody | null;
    serviceBodies: ServiceBody[];
    users: User[];
    onSaveSuccess?: (serviceBody: ServiceBody) => void; // Callback function prop
  }

  let { selectedServiceBody, serviceBodies, users, onSaveSuccess }: Props = $props();

  const parentIdItems = [
    ...[{ value: -1, name: $translations.serviceBodiesNoParent ?? '' }],
    ...serviceBodies
      .filter((sb) => selectedServiceBody?.id !== sb.id)
      .map((sb) => ({ value: sb.id, name: sb.name }))
      .sort((a, b) => a.name.localeCompare(b.name))
  ];
  const userIdToUser = Object.fromEntries(users.map((u) => [u.id, u]));
  const userItems = users.map((u) => ({ value: u.id, name: u.displayName })).sort((a, b) => a.name.localeCompare(b.name));
  const adminUserItems = userItems
    // We hide observer users from the admin list, because they simply aren't allowed to edit meetings. If an observer user
    // is somehow already selected as an admin, we allow it to be displayed in the list to give the user a chance to assign
    // another admin.
    .filter((u) => userIdToUser[u.value].type !== 'observer' || selectedServiceBody?.adminUserId === userIdToUser[u.value].id)
    .map((u) => ({ value: u.value, name: userIdToUser[u.value].type === 'deactivated' ? `[${$translations.deactivatedTitle?.toUpperCase()}] ${u.name}` : u.name }));
  const SB_TYPE_AREA = 'AS';
  const typeItems = [
    { value: 'GR', name: 'Group' },
    { value: 'CO', name: 'Co-Op' },
    { value: 'GS', name: 'Group Service Unit' },
    { value: 'LS', name: 'Local Service Unit' },
    { value: SB_TYPE_AREA, name: 'Area Service Committee' },
    { value: 'MA', name: 'Metro Area' },
    { value: 'RS', name: 'Regional Service Conference' },
    { value: 'ZF', name: 'Zonal Forum' },
    { value: 'WS', name: 'World Service Conference' }
  ];
  let assignedUserIdsSelected: number[] = $state((selectedServiceBody?.assignedUserIds ?? []).filter((userId) => userId in userIdToUser));
  // If the user is logged in as a service body admin (as opposed to the server admin), then we only want to show meeting editors that the
  // service body admin has access to.  However, the selected service body might have additional editors.  Save those away in hiddenUserIds,
  // so that we can make sure they are still in the list of meeting list editors if the user edits the list.  hiddenUserIds is just a const,
  // since it won't change during the editing interaction.
  const hiddenUserIds: number[] = (selectedServiceBody?.assignedUserIds ?? []).filter((userId) => !(userId in userIdToUser));
  // Display names for hidden editors are loaded from the dedicated /editors endpoint, which exposes only userId, displayName, and readOnly.
  let hiddenEditors: { userId: number; displayName: string }[] = $state([]);
  const initialValues = {
    adminUserId: selectedServiceBody?.adminUserId ?? -1,
    type: selectedServiceBody?.type ?? SB_TYPE_AREA,
    parentId: selectedServiceBody?.parentId ?? -1,
    assignedUserIds: assignedUserIdsSelected,
    name: selectedServiceBody?.name ?? '',
    email: selectedServiceBody?.email ?? '',
    description: selectedServiceBody?.description ?? '',
    url: selectedServiceBody?.url ?? '',
    helpline: selectedServiceBody?.helpline ?? '',
    worldId: selectedServiceBody?.worldId ?? ''
  };

  let savedServiceBody: ServiceBody;

  const { data, errors, form, setData } = createForm({
    initialValues: initialValues,
    onSubmit: async (values) => {
      spinner.show();
      const serviceBody: ServiceBodyCreate = {
        ...values,
        // the api expects those with no parent to be null
        ...{ parentId: values.parentId !== -1 ? values.parentId : null }
      };
      serviceBody.assignedUserIds.push(...hiddenUserIds);
      if (selectedServiceBody) {
        await RootServerApi.updateServiceBody(selectedServiceBody.id, serviceBody);
        savedServiceBody = await RootServerApi.getServiceBody(selectedServiceBody.id);
      } else {
        savedServiceBody = await RootServerApi.createServiceBody(serviceBody);
      }
    },
    onError: async (error) => {
      console.log(error);
      await RootServerApi.handleErrors(error as Error, {
        handleValidationError: (error) => {
          errors.set({
            adminUserId: (error?.errors?.adminUserId ?? []).join(' '),
            type: (error?.errors?.type ?? []).join(' '),
            parentId: (error?.errors?.parentId ?? []).join(' '),
            assignedUserIds: (error?.errors?.assignedUserIds ?? []).join(' '),
            name: (error?.errors?.name ?? []).join(' '),
            email: (error?.errors?.email ?? []).join(' '),
            description: (error?.errors?.description ?? []).join(' '),
            url: (error?.errors?.url ?? []).join(' '),
            helpline: (error?.errors?.helpline ?? []).join(' '),
            worldId: (error?.errors?.worldId ?? []).join(' ')
          });
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      onSaveSuccess?.(savedServiceBody); // Call the callback function instead of dispatch
    },
    extend: validator({
      schema: yup.object({
        adminUserId: yup.number().required(),
        type: yup.string().required(),
        parentId: yup.number().required(),
        assignedUserIds: yup.array().of(yup.number()),
        name: yup
          .string()
          .transform((v) => v.trim())
          .max(255)
          .required(),
        email: yup.string().email().max(255),
        description: yup.string().transform((v) => v.trim()),
        url: yup
          .string()
          .url()
          .transform((v) => v.trim())
          .max(255),
        helpline: yup
          .string()
          .transform((v) => v.trim())
          .max(255),
        worldId: yup
          .string()
          .transform((v) => v.trim())
          .max(30)
      }),
      castValues: true
    })
  });

  function badgeColor(id: string) {
    if (userIdToUser[id].type === 'deactivated') {
      return 'red';
    } else if (userIdToUser[id].type === 'observer') {
      return 'yellow';
    } else {
      return 'green';
    }
  }
  // This hack is required until https://github.com/themesberg/flowbite-svelte/issues/1395 is fixed.
  function disableButtonHack(event: MouseEvent) {
    if (!$isDirty) {
      event.preventDefault();
    }
  }

  $effect(() => {
    formIsDirty(initialValues, $data);
  });
  $effect(() => {
    setData('assignedUserIds', assignedUserIdsSelected);
  });

  onMount(async () => {
    if (!selectedServiceBody?.id) {
      return;
    }
    try {
      // "Hidden" in the form sense means the editor isn't in the visible users list -- which can happen because the
      // caller can't manage them through /users (readOnly), or because the frontend filters them out (e.g. the caller
      // themselves is excluded from the editor multi-select). Use the editors API only to look up display names.
      const editors = await RootServerApi.getServiceBodyEditors(selectedServiceBody.id);
      hiddenEditors = editors.filter((e) => !(e.userId in userIdToUser)).map((e) => ({ userId: e.userId, displayName: e.displayName }));
    } catch (error) {
      await RootServerApi.handleErrors(error as Error);
    }
  });
</script>

<form use:form>
  {#if selectedServiceBody?.id}
    <div class="mb-4 flex items-center justify-between pr-8 text-sm">
      <div class="ml-auto text-gray-700 dark:text-gray-300">
        <span class="font-medium">{$translations.serviceBodyId}:</span>
        <span class="ml-2">{selectedServiceBody.id}</span>
      </div>
    </div>
  {/if}
  <div class="grid gap-4 md:grid-cols-2">
    <div class="md:col-span-2">
      <Label for="name" class="mb-2">{$translations.nameTitle}</Label>
      <Input type="text" id="name" name="name" required disabled={$authenticatedUser?.type !== 'admin'} />
      <Helper class="mt-2" color="red">
        {#if $errors.name}
          {$errors.name}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2 {$authenticatedUser?.type !== 'admin' ? 'hidden' : ''}">
      <Label for="adminUserId" class="mb-2">{$translations.adminTitle}</Label>
      <Select
        id="adminUserId"
        items={adminUserItems}
        bind:value={$data.adminUserId}
        name="adminUserId"
        placeholder={$translations.chooseOption}
        class="rounded-lg dark:bg-gray-600"
        disabled={$authenticatedUser?.type !== 'admin'}
      />
      <Helper class="mt-2" color="red">
        {#if $errors.adminUserId}
          {$errors.adminUserId}
        {/if}
      </Helper>
    </div>
    <div class={$authenticatedUser?.type !== 'admin' ? 'hidden' : ''}>
      <Label for="type" class="mb-2">{$translations.serviceBodyTypeTitle}</Label>
      <Select
        id="type"
        items={typeItems}
        name="type"
        bind:value={$data.type}
        placeholder={$translations.chooseOption}
        class="rounded-lg dark:bg-gray-600"
        disabled={$authenticatedUser?.type !== 'admin'}
      />
      <Helper class="mt-2" color="red">
        {#if $errors.type}
          {$errors.type}
        {/if}
      </Helper>
    </div>
    <div class={$authenticatedUser?.type !== 'admin' ? 'hidden' : ''}>
      <Label for="parentId" class="mb-2">{$translations.parentIdTitle}</Label>
      <Select
        id="parentId"
        items={parentIdItems}
        name="parentId"
        bind:value={$data.parentId}
        placeholder={$translations.chooseOption}
        class="rounded-lg dark:bg-gray-600"
        disabled={$authenticatedUser?.type !== 'admin'}
      />
      <Helper class="mt-2" color="red">
        {#if $errors.parentId}
          {$errors.parentId}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Label for="assignedUserIds" class="mb-2">{$translations.meetingListEditorsTitle}</Label>
      <MultiSelect id="assignedUserIds" items={userItems} name="assignedUserIds" class="hide-close-button bg-gray-50 dark:bg-gray-600" bind:value={assignedUserIdsSelected}>
        {#snippet children({ item, clear })}
          <Badge rounded color={badgeColor(String(item.value))} dismissable params={{ duration: 100 }} onclose={clear}>
            {item.name}
          </Badge>
        {/snippet}
      </MultiSelect>
      <Helper class="mt-2" color="red">
        <!-- For some reason yup fills the errors store with empty objects for this array. The === 'string' ensures only server side errors will display. -->
        {#if $errors.assignedUserIds && typeof $errors.assignedUserIds[0] === 'string'}
          {$errors.assignedUserIds}
        {/if}
      </Helper>
      {#if hiddenEditors.length > 0}
        <div class="mt-3">
          <Label class="mb-2">{$translations.otherMeetingEditorsTitle}</Label>
          <div class="flex flex-wrap gap-2">
            {#each hiddenEditors as editor (editor.userId)}
              <Badge rounded color="gray">{editor.displayName}</Badge>
            {/each}
          </div>
        </div>
      {/if}
    </div>
    <div class="md:col-span-2">
      <Label for="email" class="mb-2">{$translations.emailTitle}</Label>
      <Input type="email" id="email" name="email" />
      <Helper class="mt-2" color="red">
        {#if $errors.email}
          {$errors.email}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Label for="description" class="mb-2">{$translations.descriptionTitle}</Label>
      <Textarea id="description" name="description" rows={4} class="w-full" />
      <Helper class="mt-2" color="red">
        {#if $errors.description}
          {$errors.description}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Label for="url" class="mb-2">{$translations.websiteUrlTitle}</Label>
      <Input type="text" id="url" name="url" />
      <Helper class="mt-2" color="red">
        {#if $errors.url}
          {$errors.url}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Label for="helpline" class="mb-2">{$translations.helplineTitle}</Label>
      <Input type="text" id="helpline" name="helpline" />
      <Helper class="mt-2" color="red">
        {#if $errors.helpline}
          {$errors.helpline}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Label for="worldId" class="mb-2">{$translations.worldIdTitle}</Label>
      <Input type="text" id="worldId" name="worldId" />
      <Helper class="mt-2" color="red">
        {#if $errors.worldId}
          {$errors.worldId}
        {/if}
      </Helper>
    </div>
    <div class="md:col-span-2">
      <Button type="submit" class="w-full" disabled={!$isDirty} onclick={disableButtonHack}>
        {#if selectedServiceBody}
          {$translations.applyChangesTitle}
        {:else}
          {$translations.addServiceBody}
        {/if}
      </Button>
    </div>
  </div>
</form>

<style>
  :global(.hide-close-button button[aria-label='Close']) {
    display: none !important;
  }
</style>
