<script lang="ts">
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Button, Helper, Input, Label, P, Select } from 'flowbite-svelte';
  import * as yup from 'yup';

  import DarkMode from './DarkMode.svelte';
  import RootServerApi from '../lib/ServerApi';
  import { translations } from '../stores/localization';
  import { spinner } from '../stores/spinner';
  import type { ApiCredentialsStore } from '../stores/apiCredentials';

  interface Props {
    apiCredentials: ApiCredentialsStore;
    authenticated: () => void;
  }

  let { apiCredentials, authenticated }: Props = $props();

  const globalSettings = settings;
  const languageOptions = Object.entries(globalSettings.languageMapping).map((lang) => ({ value: lang[0], name: lang[1] }));
  let selectedLanguage = $state(translations.getLanguage());
  let errorMessage: string | undefined = $state();

  const { form, errors } = createForm({
    initialValues: {
      username: '',
      password: ''
    },
    onSubmit: async (values) => {
      spinner.show();
      await apiCredentials.login(values.username, values.password);
    },
    onSuccess: () => {
      spinner.hide();
      authenticated();
    },
    onError: async (error) => {
      await RootServerApi.handleErrors(error as Error, {
        handleAuthenticationError: () => {
          errorMessage = $translations.invalidUsernameOrPassword;
        },
        handleAuthorizationError: () => {
          errorMessage = $translations.userIsDeactivated;
        },
        handleValidationError: (error) => {
          errors.set({
            username: (error?.errors?.username ?? []).join(' '),
            password: (error?.errors?.password ?? []).join(' ')
          });
        }
      });
      spinner.hide();
    },
    extend: validator({
      schema: yup.object({
        username: yup.string().required(),
        password: yup.string().required()
      })
    })
  });
</script>

<div class="mx-auto flex flex-col items-center justify-center px-6 py-8 md:h-screen lg:py-0">
  {#if globalSettings.bmltTitle}
    <div class="mb-4 text-4xl font-bold text-black dark:text-white">
      {globalSettings.bmltTitle}
    </div>
  {/if}
  <div class="mb-6 flex items-center text-2xl font-semibold text-gray-900 dark:text-white">
    {$translations.serverTitle} ({globalSettings.version})
  </div>
  <div class="w-full rounded-lg bg-white shadow sm:max-w-md md:mt-0 xl:p-0 dark:border dark:border-gray-700 dark:bg-gray-800">
    <div class="m-8">
      <form use:form>
        <div class="mb-4">
          <Label for="username" class="mb-2">{$translations.usernameTitle}</Label>
          <Input type="text" name="username" id="username" onInput={() => (errorMessage = '')} />
          <Helper class="mt-2" color="red">
            {#if $errors.username}
              {$errors.username}
            {/if}
          </Helper>
        </div>
        <div class="mb-4">
          <Label for="password" class="mb-2">{$translations.passwordTitle}</Label>
          <Input type="password" name="password" id="password" onInput={() => (errorMessage = '')} />
          <Helper class="mt-2" color="red">
            {#if $errors.password}
              {$errors.password}
            {/if}
          </Helper>
        </div>
        {#if globalSettings.isLanguageSelectorEnabled}
          <div class="mb-4">
            <Label for="languageSelection" class="mb-2">{$translations.languageSelectTitle}</Label>
            <Select id="languageSelection" items={languageOptions} bind:value={selectedLanguage} onchange={() => translations.setLanguage(selectedLanguage)} />
          </div>
        {/if}
        {#if errorMessage}
          <div class="mb-4">
            <P class="text-red-700 dark:text-red-500">{errorMessage}</P>
          </div>
        {/if}
        <div class="mb-2">
          <Button class="w-full" type="submit">{$translations.loginVerb}</Button>
        </div>
      </form>
    </div>
  </div>
  <DarkMode />
</div>
