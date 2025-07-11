<script lang="ts">
  import { Button, P, TableBody, TableBodyCell, TableBodyRow, TableHead, TableHeadCell, TableSearch } from 'flowbite-svelte';
  import { TrashBinOutline } from 'flowbite-svelte-icons';
  import { onMount } from 'svelte';

  import type { ServiceBody, User } from 'bmlt-server-client';

  import Nav from '../components/NavBar.svelte';
  import ServiceBodyDeleteModal from '../components/ServiceBodyDeleteModal.svelte';
  import ServiceBodyForm from '../components/ServiceBodyForm.svelte';
  import ServiceBodyModal from '../components/ServiceBodyModal.svelte';
  import RootServerApi from '../lib/ServerApi';
  import { authenticatedUser } from '../stores/apiCredentials';
  import { translations } from '../stores/localization';
  import { spinner } from '../stores/spinner';

  let usersLoaded = $state(false);
  let serviceBodiesLoaded = $state(false);
  let users: User[] = $state([]);
  let serviceBodies: ServiceBody[] = $state([]);
  let filteredServiceBodies: ServiceBody[] = $state([]);
  let showModal = $state(false);
  let showDeleteModal = $state(false);
  let searchTerm = $state('');
  let selectedServiceBody: ServiceBody | null = $state(null);
  let deleteServiceBody: ServiceBody | null = $state(null);
  let lastEditedServiceBodyId: number | null = $state(null);

  async function getUsers(): Promise<void> {
    try {
      spinner.show();
      users = (await RootServerApi.getUsers()).filter((u) => u.id !== $authenticatedUser?.id);
      usersLoaded = true;
    } catch (error: any) {
      await RootServerApi.handleErrors(error);
    } finally {
      spinner.hide();
    }
  }

  async function getServiceBodies(): Promise<void> {
    try {
      spinner.show();
      serviceBodies = await RootServerApi.getServiceBodies();
      lastEditedServiceBodyId = null;
      serviceBodiesLoaded = true;
    } catch (error: any) {
      await RootServerApi.handleErrors(error);
    } finally {
      spinner.hide();
    }
  }

  function handleAdd() {
    selectedServiceBody = null;
    openModal();
  }

  function handleEdit(serviceBody: ServiceBody) {
    selectedServiceBody = serviceBody;
    openModal();
  }

  function handleDelete(event: MouseEvent, serviceBody: ServiceBody) {
    event.stopPropagation();
    deleteServiceBody = serviceBody;
    Promise.resolve().then(() => {
      showDeleteModal = true;
    });
  }

  function onSaved(serviceBody: ServiceBody) {
    const i = serviceBodies.findIndex((s) => s.id === serviceBody.id);
    if (i === -1) {
      serviceBodies = [...serviceBodies, serviceBody];
    } else {
      serviceBodies[i] = serviceBody;
    }
    lastEditedServiceBodyId = serviceBody.id;
    closeModal();
  }

  function onDeleted(serviceBody: ServiceBody) {
    serviceBodies = serviceBodies.filter((s) => s.id !== serviceBody.id);
    showDeleteModal = false;
  }

  function openModal() {
    showModal = true;
  }

  function closeModal() {
    showModal = false;
  }

  function isAdminForServiceBody(userId: number, sb: ServiceBody): boolean {
    let s: ServiceBody | undefined = sb;
    while (s) {
      if (s.adminUserId === userId) {
        return true;
      }
      s = serviceBodies.find((x) => x.id === s?.parentId);
    }
    return false;
  }

  onMount(() => {
    getUsers();
    getServiceBodies();
  });

  $effect(() => {
    // filteredServiceBodies will be an array of service bodies that the authenticated user can edit
    // prettier-ignore
    if ($authenticatedUser) {
          filteredServiceBodies = serviceBodies
              .sort((s1, s2) => s1.name.localeCompare(s2.name))
              .filter((s) => s.name.toLowerCase().indexOf(searchTerm.toLowerCase()) !== -1 &&
                  ($authenticatedUser?.type === 'admin' || isAdminForServiceBody($authenticatedUser.id, s)));
      }
  });
</script>

<Nav />

<div class="mx-auto max-w-3xl p-2">
  <h2 class="mb-4 text-center text-xl font-semibold dark:text-white">{$translations.serviceBodiesTitle}</h2>
  {#if usersLoaded && serviceBodiesLoaded}
    {#if filteredServiceBodies.length}
      <TableSearch placeholder={$translations.searchByName} hoverable={true} bind:inputValue={searchTerm}>
        <TableHead>
          <TableHeadCell colspan={$authenticatedUser?.type === 'admin' ? 2 : 1}>
            {#if $authenticatedUser?.type === 'admin'}
              <div class="flex">
                <div class="mt-2.5 grow">Name</div>
                <div>
                  <Button onclick={() => handleAdd()} class="whitespace-nowrap" aria-label={$translations.addServiceBody}>{$translations.addServiceBody}</Button>
                </div>
              </div>
            {:else}
              {$translations.nameTitle}
            {/if}
          </TableHeadCell>
        </TableHead>
        <TableBody>
          {#each filteredServiceBodies as serviceBody (serviceBody.id)}
            <TableBodyRow
              onclick={() => handleEdit(serviceBody)}
              class={`cursor-pointer ${serviceBody.id === lastEditedServiceBodyId ? 'bg-blue-50 dark:bg-blue-900' : ''}`}
              aria-label={$translations.editUser}
            >
              <TableBodyCell class="whitespace-normal">{serviceBody.name}</TableBodyCell>
              {#if $authenticatedUser?.type === 'admin'}
                <TableBodyCell class="text-right">
                  <Button color="alternative" onclick={(e: MouseEvent) => handleDelete(e, serviceBody)} class="border-none text-blue-700 dark:text-blue-500">
                    <TrashBinOutline title={{ id: 'deleteServiceBody', title: $translations.deleteServiceBody }} ariaLabel={$translations.deleteServiceBody + ' ' + serviceBody.name} />
                  </Button>
                </TableBodyCell>
              {/if}
            </TableBodyRow>
          {/each}
        </TableBody>
      </TableSearch>
    {:else if $authenticatedUser?.type === 'admin'}
      <div class="p-2">
        <ServiceBodyForm {serviceBodies} {selectedServiceBody} {users} onSaveSuccess={onSaved} />
      </div>
    {:else}
      <P class="text-center">{$translations.noServiceBodiesTitle}</P>
    {/if}
  {/if}
</div>

<ServiceBodyModal bind:showModal {serviceBodies} {selectedServiceBody} {users} onSaveSuccess={onSaved} onClose={closeModal} />
{#if deleteServiceBody}
  <ServiceBodyDeleteModal bind:showDeleteModal {deleteServiceBody} onDeleteSuccess={onDeleted} />
{/if}
