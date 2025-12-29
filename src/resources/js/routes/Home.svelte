<script lang="ts">
  import { Card } from 'flowbite-svelte';
  import { Label, Select } from 'flowbite-svelte';
  import { authenticatedUser } from '../stores/apiCredentials';
  import Nav from '../components/NavBar.svelte';
  import { translations } from '../stores/localization';
  import RootServerApi from '../lib/ServerApi';

  const globalSettings = settings;
  const mappings = globalSettings.languageMapping;
  const allLanguages = Object.entries(mappings).map(([code, name]) => ({ value: code, name: name }));
  let selectedLanguage = $state($translations.currentLanguage);

  async function setTargetLanguage(choice: string) {
    if ($authenticatedUser) {
      await RootServerApi.partialUpdateUser($authenticatedUser.id, { targetLanguage: choice });
    }
  }
</script>

<Nav />

{#if $authenticatedUser}
  <Card class="mx-auto my-8 w-full max-w-lg bg-white p-8 text-center shadow-lg dark:bg-gray-800">
    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
      {$translations.welcome}
      {$authenticatedUser.displayName}
    </h5>
  </Card>
  {#if settings.bmltNotice}
    <div class="mt-4 mb-4 px-6 pt-4 pb-4 text-center text-3xl font-bold text-yellow-600 dark:text-yellow-400">
      {settings.bmltNotice}
    </div>
  {/if}
  {#if $authenticatedUser?.type === 'translator'}
    <Card class="mx-auto my-8 w-full max-w-lg bg-white p-8 text-center shadow-lg dark:bg-gray-800">
      <Label for="languageSelection" class="mb-2">{$translations.languageSelectTitle}</Label>
      <Select id="languageSelection" items={allLanguages} bind:value={selectedLanguage} onchange={() => setTargetLanguage(selectedLanguage)} />
    </Card>
  {/if}
{/if}
