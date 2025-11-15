<script lang="ts">
  import { onMount, onDestroy } from 'svelte';

  import Nav from '../components/NavBar.svelte';
  import RootServerApi from '../lib/ServerApi';
  import { translations } from '../stores/localization';
  import { spinner } from '../stores/spinner';
  import type { Format, ServiceBody } from 'bmlt-server-client';
  import MeetingsList from '../components/MeetingsList.svelte';
  import { serviceBodiesState } from '../stores/serviceBodiesState';
  import { formatsState } from '../stores/formatsState';

  let serviceBodies: ServiceBody[] = $state([]);
  let formats: Format[] = $state([]);
  let serviceBodiesLoaded = $state(false);
  let formatsLoaded = $state(false);

  async function getServiceBodies(): Promise<void> {
    try {
      spinner.show();
      serviceBodies = await RootServerApi.getServiceBodies();
      serviceBodies = serviceBodies.sort((s1, s2) => s1.name.localeCompare(s2.name));
      serviceBodiesLoaded = true;
    } catch (error: any) {
      await RootServerApi.handleErrors(error);
    } finally {
      spinner.hide();
    }
  }

  async function getFormats(): Promise<void> {
    try {
      spinner.show();
      formats = await RootServerApi.getFormats();
      formatsLoaded = true;
    } catch (error: any) {
      await RootServerApi.handleErrors(error);
    } finally {
      spinner.hide();
    }
  }

  onMount(() => {
    // Restore service bodies from store if available
    const storedServiceBodiesState = $serviceBodiesState;
    if (storedServiceBodiesState.serviceBodies.length > 0) {
      serviceBodies = storedServiceBodiesState.serviceBodies;
      serviceBodiesLoaded = true;
    } else {
      getServiceBodies();
    }

    // Restore formats from store if available
    const storedFormatsState = $formatsState;
    if (storedFormatsState.formats.length > 0) {
      formats = storedFormatsState.formats;
      formatsLoaded = true;
    } else {
      getFormats();
    }
  });

  onDestroy(() => {
    // Save service bodies to store when component unmounts
    if (serviceBodies.length > 0) {
      serviceBodiesState.update((state) => ({
        ...state,
        serviceBodies
      }));
    }

    // Save formats to store when component unmounts
    if (formats.length > 0) {
      formatsState.update((state) => ({
        ...state,
        formats
      }));
    }
  });
</script>

<Nav />

<div class="mx-auto max-w-6xl p-2">
  <h2 class="mb-4 text-center text-xl font-semibold dark:text-white">{$translations.meetingsTitle}</h2>
  {#if serviceBodiesLoaded && formatsLoaded}
    <MeetingsList {serviceBodies} {formats} />
  {/if}
</div>
