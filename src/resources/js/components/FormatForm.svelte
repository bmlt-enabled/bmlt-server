<script lang="ts">
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Button, Helper, Input, Label, Select } from 'flowbite-svelte';
  import BasicAccordion from './BasicAccordion.svelte';
  import * as yup from 'yup';

  import { spinner } from '../stores/spinner';
  import RootServerApi from '../lib/ServerApi';
  import { formIsDirty } from '../lib/utils';
  import type { Format } from 'bmlt-server-client';
  import { translations } from '../stores/localization';

  interface Props {
    selectedFormat: Format | null;
    reservedFormatKeys: string[];
    onSaveSuccess?: (format: Format) => void; // Callback function prop
  }

  let { selectedFormat, reservedFormatKeys, onSaveSuccess }: Props = $props();

  const globalSettings = settings;
  const mappings = globalSettings.languageMapping;
  const allLanguages: string[] = Object.getOwnPropertyNames(mappings);

  const initialValues: any = $state({ worldId: '', type: '' });
  if (selectedFormat && selectedFormat.worldId) {
    initialValues.worldId = selectedFormat.worldId;
  }
  if (selectedFormat && selectedFormat.type) {
    initialValues.type = selectedFormat.type;
  }
  // true if no format translations were entered (bit of a hack to allow an appropriate error message to be displayed)
  let noFormatTranslations: boolean = $state(false);

  const yupSchema: any = {};

  /* Suppose the selected format is 'Beginners', and there are translations available for English and German only.
     Then initalValues has the following shape:
       {
         en_key: 'B', en_name: 'Beginners', en_description: 'Meeting for beginnings',
         de_key: 'A', de_name: 'Anfänger', de_description: 'Für Anfänger',
         es_key: '', es_name: '', es_description: '',
         fr_key: '', fr_name: '', fr_description: '',
         ...
       }

     Before I tried to use a nested form but was running into problems with validation.  initialValues for a nested form
     would have this shape, and the names in the form itself would have . instead of _
     See https://felte.dev/docs/svelte/nested-forms
       {
         en: {key: 'B', name: 'Beginners', description: 'Meeting for beginnings'},
         de: {key: 'A', name: 'Anfänger', description: 'Für Anfänger'},
         es: {key: '', name: '', description: ''},
         fr: {key: '', name: '', description: ''},
         ...
       }
      However, it's not clear that using a nested form has any significant advantages.

      All of the fields (key, name, and description) are required for a given translation, but it's OK for there to not be
      a translation for a given language.  Trying to test for this completely within yup results in a circular dependency.
      So the key is allowed to be empty as far as yup is concerned.  If the key is non-empty, then the name and description
      must be non-empty as well (this is tested in yup).  The test for a name and/or description but no key happens when the
      changes are submitted -- this will raise an exception that gets caught.
  */

  const selectedFormatTranslations = selectedFormat ? selectedFormat.translations : [];
  for (const n of allLanguages) {
    const tr = selectedFormatTranslations.find((t) => t.language === n);
    initialValues[n + '_key'] = tr?.key ?? '';
    initialValues[n + '_name'] = tr?.name ?? '';
    initialValues[n + '_description'] = tr?.description ?? '';
    initialValues[n + '_language'] = n;
    yupSchema[n + '_key'] = yup
      .string()
      .default('')
      .transform((v) => v.trim())
      .max(6)
      .matches(/^\S*$/, $translations.noWhitespaceInKey); // allow empty keys (see longer comment above)
    yupSchema[n + '_name'] = yup
      .string()
      .default('')
      .transform((v) => v.trim())
      .max(50)
      .when(n + '_key', {
        is: (k: string) => k !== '',
        then: (schema) => schema.required()
      });
    yupSchema[n + '_description'] = yup
      .string()
      .default('')
      .transform((v) => v.trim())
      .max(255)
      .when(n + '_key', {
        is: (k: string) => k !== '',
        then: (schema) => schema.required()
      });
    // no checks for _language since it is automatically supplied, rather than entered by the user
  }

  let savedFormat: Format;
  const { data, errors, form, isDirty } = createForm({
    initialValues: initialValues,
    onSubmit: async (values) => {
      spinner.show();
      const trs = [];
      for (const lang of allLanguages) {
        // Check whether any of key, name, or description is present; if so add a translation for language n.  All three
        // are required, and the UI will signal an error if one or more is missing.
        if (values[lang + '_key'] || values[lang + '_name'] || values[lang + '_description']) {
          trs.push({ key: values[lang + '_key'], name: values[lang + '_name'], description: values[lang + '_description'], language: lang });
        }
      }
      noFormatTranslations = trs.length === 0;
      if (selectedFormat) {
        await RootServerApi.updateFormat(selectedFormat.id, { worldId: values.worldId, type: values.type, translations: trs });
        savedFormat = await RootServerApi.getFormat(selectedFormat.id);
      } else {
        savedFormat = await RootServerApi.createFormat({ worldId: values.worldId, type: values.type, translations: trs });
      }
    },
    onError: async (error) => {
      await RootServerApi.handleErrors(error as Error, {
        handleValidationError: (error) => {
          const errorObject: any = {};
          if (error && error.errors) {
            for (const lang of allLanguages) {
              const k = error.errors[lang + '_key'] ?? [];
              // If there is a name or description but the key is missing, note that there is an error.  (To avoid a
              // circularity, this isn't done in yup, which would otherwise be the logical place for this check.)
              if (!$data[lang + '_key'] && ($data[lang + '_name'] || $data[lang + '_description'])) {
                k.push($translations.keyIsRequired);
              }
              // For English translations only: check that we're not trying to set the key of some other format to one of the
              // reserved keys HY, TC, or VM.  If the format already exists and is one of HY, TC, or VM, it's OK to edit the
              // name or description -- the condition includes a check to allow this.  (The key will be read-only.)
              if (lang === 'en' && initialValues[lang + '_key'] !== $data[lang + '_key'] && reservedFormatKeys.includes($data[lang + '_key'])) {
                k.push($translations.keyIsReserved);
              }
              // a translation is required for all languages for the reserved formats HY, TC, and VM -- it's an error
              // if we're trying to delete one of these (just need to check the key, since if the key is there the name and
              // description will be required)
              if (reservedFormatKeys.includes(initialValues.en_key) && !$data[lang + '_key']) {
                k.push($translations.formatTranslationIsRequired);
              }
              errorObject[lang + '_key'] = k.join(' ');
              const n = error?.errors[lang + '_name'] ?? [];
              errorObject[lang + '_name'] = n.join(' ');
              const d = error?.errors[lang + '_description'] ?? [];
              errorObject[lang + '_description'] = d.join(' ');
            }
          }
          errors.set(errorObject);
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      onSaveSuccess?.(savedFormat); // Call the callback function instead of dispatch
    },
    extend: validator({ schema: yup.object(yupSchema), castValues: true })
  });

  // This hack is required until https://github.com/themesberg/flowbite-svelte/issues/1395 is fixed.
  function disableButtonHack(event: MouseEvent) {
    if (!$isDirty) {
      event.preventDefault();
    }
  }

  // Hack to provide a label for the key, name, and description fields in all languages -- if it's not defined
  // in the current language, use English.  TODO: maybe get rid of this if these are defined for all languages?
  function getLabel(title: string, lang: string): string {
    const t = $translations.getString(title, lang, true);
    return t ? t : $translations.getString(title, 'en');
  }

  function isReservedKey(lang: string): boolean {
    if (lang === 'en') {
      const e = selectedFormatTranslations.find((t) => t.language === 'en');
      return e !== undefined && reservedFormatKeys.includes(e.key);
    } else {
      return false;
    }
  }

  $effect(() => {
    isDirty.set(formIsDirty(initialValues, $data));
  });
</script>

<form use:form>
  <div class="grid gap-4 md:grid-cols-2">
    {#if selectedFormat?.id}
      <div class="text-gray-700 md:col-span-2 dark:text-gray-300">
        <strong>{$translations.formatId}:</strong>
        {selectedFormat?.id}
      </div>
    {/if}
    <div class="md:col-span-2">
      {#each allLanguages as lang (lang)}
        <BasicAccordion header={mappings[lang]} open={$translations.getLanguage() === lang} label={'Toggle accordion ' + lang}>
          <div>
            <Label for="{lang}_key" class="mb-2" aria-label="{lang} key">{getLabel('keyTitle', lang)}</Label>
            <Input type="text" id="{lang}_key" name="{lang}_key" disabled={isReservedKey(lang)} />
            <Helper class="mb-2" color="red">
              {#if $errors[lang + '_key']}
                {$errors[lang + '_key']}
              {/if}
            </Helper>
          </div>
          <div>
            <Label for="{lang}_name" class="mb-2" aria-label="{lang} name">{getLabel('nameTitle', lang)}</Label>
            <Input type="text" id="{lang}_name" name="{lang}_name" />
            <Helper class="mb-2" color="red">
              {#if $errors[lang + '_name']}
                {$errors[lang + '_name']}
              {/if}
            </Helper>
          </div>
          <div>
            <Label for="{lang}_description" class="mb-2" aria-label="{lang} description">{getLabel('descriptionTitle', lang)}</Label>
            <Input type="text" id="{lang}_description" name="{lang}_description" />
            <Helper class="mb-2" color="red">
              {#if $errors[lang + '_description']}
                {$errors[lang + '_description']}
              {/if}
            </Helper>
          </div>
        </BasicAccordion>
      {/each}
    </div>
    {#if noFormatTranslations}
      <div class="md:col-span-2">
        <Helper class="mb-2" color="red">
          {$translations.noFormatTranslationsError}
        </Helper>
      </div>
    {/if}
    <div class="md:col-span-2">
      <Label for="worldId" class="mb-2 md:col-span-2">{$translations.nawsFormatTitle}</Label>
      <Select id="worldId" items={$translations.nawsFormats} name="worldId" class="rounded-lg dark:bg-gray-600" />
    </div>
    <div class="md:col-span-2">
      <Label for="type" class="mb-2 md:col-span-2">{$translations.formatTypeTitle}</Label>
      <Select id="type" items={$translations.formatTypeCodes} name="type" class="rounded-lg dark:bg-gray-600" />
    </div>
    <div class="md:col-span-2">
      <Button type="submit" class="w-full" disabled={!$isDirty} onclick={disableButtonHack}>
        {#if selectedFormat}
          {$translations.applyChangesTitle}
        {:else}
          {$translations.addFormat}
        {/if}
      </Button>
    </div>
  </div>
</form>
