<script lang="ts">
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Button, Helper, Input, Label, Select } from 'flowbite-svelte';
  import BasicAccordion from './BasicAccordion.svelte';
  import * as yup from 'yup';

  import { spinner } from '../stores/spinner';
  import RootServerApi from '../lib/ServerApi';
  import { formIsDirty } from '../lib/utils';
  import type { Format, FormatTranslation } from 'bmlt-server-client';
  import { translations } from '../stores/localization';

  interface Props {
    formats: Format[];
    selectedFormat: Format | null;
    reservedFormatKeys: string[];
    onSaveSuccess?: (format: Format) => void; // Callback function prop
  }

  let { formats, selectedFormat, reservedFormatKeys, onSaveSuccess }: Props = $props();

  const mappings = {
    ...settings.languageMapping,
    ...settings.formatLangNames
  };
  const allLanguages: string[] = Object.keys(mappings).sort();

  const initialValues: any = $state({ worldId: '', type: '' });
  if (selectedFormat && selectedFormat.worldId) {
    initialValues.worldId = selectedFormat.worldId;
  }
  if (selectedFormat && selectedFormat.type) {
    initialValues.type = selectedFormat.type;
  }
  // true if no format translations were entered (bit of a hack to allow an appropriate error message to be displayed)
  let noFormatTranslations: boolean = $state(false);

  const formatTypeCodes = [
    { name: $translations.formatTypeCode_OPEN_OR_CLOSED, value: 'OPEN_OR_CLOSED' },
    { name: $translations.formatTypeCode_COMMON_NEEDS_OR_RESTRICTION, value: 'COMMON_NEEDS_OR_RESTRICTION' },
    { name: $translations.formatTypeCode_ALERT, value: 'ALERT' },
    { name: $translations.formatTypeCode_LANGUAGE, value: 'LANGUAGE' },
    { name: $translations.formatTypeCode_LOCATION, value: 'LOCATION' },
    { name: $translations.formatTypeCode_MEETING_FORMAT, value: 'MEETING_FORMAT' },
    { name: $translations.formatTypeCode_NONE, value: '' }
  ];

  const nawsFormats = [
    { name: $translations.nawsFormat_BEG, value: 'BEG' },
    { name: $translations.nawsFormat_BT, value: 'BT' },
    { name: $translations.nawsFormat_CAN, value: 'CAN' },
    { name: $translations.nawsFormat_CH, value: 'CH' },
    { name: $translations.nawsFormat_CLOSED, value: 'CLOSED' },
    { name: $translations.nawsFormat_CPT, value: 'CPT' },
    { name: $translations.nawsFormat_CW, value: 'CW' },
    { name: $translations.nawsFormat_DISC, value: 'DISC' },
    { name: $translations.nawsFormat_GL, value: 'GL' },
    { name: $translations.nawsFormat_GP, value: 'GP' },
    { name: $translations.nawsFormat_HYBR, value: 'HYBR' },
    { name: $translations.nawsFormat_IP, value: 'IP' },
    { name: $translations.nawsFormat_IW, value: 'IW' },
    { name: $translations.nawsFormat_JFT, value: 'JFT' },
    { name: $translations.nawsFormat_LANG, value: 'LANG' },
    { name: $translations.nawsFormat_LC, value: 'LC' },
    { name: $translations.nawsFormat_LIT, value: 'LIT' },
    { name: $translations.nawsFormat_M, value: 'M' },
    { name: $translations.nawsFormat_MED, value: 'MED' },
    { name: $translations.nawsFormat_NC, value: 'NC' },
    { name: $translations.nawsFormat_NONE, value: '' },
    { name: $translations.nawsFormat_NS, value: 'NS' },
    { name: $translations.nawsFormat_OPEN, value: 'OPEN' },
    { name: $translations.nawsFormat_QA, value: 'QA' },
    { name: $translations.nawsFormat_RA, value: 'RA' },
    { name: $translations.nawsFormat_SD, value: 'S-D' },
    { name: $translations.nawsFormat_SMOK, value: 'SMOK' },
    { name: $translations.nawsFormat_SPAD, value: 'SPAD' },
    { name: $translations.nawsFormat_SPK, value: 'SPK' },
    { name: $translations.nawsFormat_STEP, value: 'STEP' },
    { name: $translations.nawsFormat_SWG, value: 'SWG' },
    { name: $translations.nawsFormat_TC, value: 'TC' },
    { name: $translations.nawsFormat_TOP, value: 'TOP' },
    { name: $translations.nawsFormat_TRAD, value: 'TRAD' },
    { name: $translations.nawsFormat_VAR, value: 'VAR' },
    { name: $translations.nawsFormat_VM, value: 'VM' },
    { name: $translations.nawsFormat_W, value: 'W' },
    { name: $translations.nawsFormat_WCHR, value: 'WCHR' },
    { name: $translations.nawsFormat_Y, value: 'Y' }
  ];

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

  const selectedFormatTranslations: FormatTranslation[] = selectedFormat ? selectedFormat.translations : [];
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
    onSubmit: async (values: any) => {
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
    onError: async (error: any) => {
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
              // Check that the key isn't in use for a translation of another format. Note that this check also takes care of
              // ensuring that other English translations don't use one of the reserved keys HY, TC, or VM.
              for (const f of formats) {
                if (f.id !== selectedFormat?.id) {
                  const translationToCheck = f.translations.find((t: FormatTranslation) => t.language === lang);
                  if (translationToCheck && translationToCheck.key === $data[lang + '_key']) {
                    k.push($translations.keyAlreadyInUse);
                  }
                }
              }
              // A translation is required for English for the reserved formats HY, TC, and VM -- it would be an error if we tried to delete one of these.
              // The server does check for this, but the UI won't let you delete one since editing the existing key for the English translation for
              // HY, TC, and VM is disabled.  So we don't include a check here since this error can't arise (unless something went badly wrong with the
              // code, but in that case we'll just let the server error suffice).
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

  const errorStringArray = $derived(Object.values($errors).filter(Boolean));

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

  function keyIsReservedForLang(lang: string): boolean {
    if (lang === 'en') {
      const e = selectedFormatTranslations.find((t) => t.language === 'en');
      return Boolean(e && reservedFormatKeys.includes(e.key));
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
            <Label for="{lang}_key" class="mb-2 text-gray-900 dark:text-white" aria-label="{lang} key">{getLabel('keyTitle', lang)}</Label>
            <Input type="text" id="{lang}_key" name="{lang}_key" disabled={keyIsReservedForLang(lang)} />
            <Helper class="mb-2" color="red">
              {#if $errors[lang + '_key']}
                {$errors[lang + '_key']}
              {/if}
            </Helper>
          </div>
          <div>
            <Label for="{lang}_name" class="mb-2 text-gray-900 dark:text-white" aria-label="{lang} name">{getLabel('nameTitle', lang)}</Label>
            <Input type="text" id="{lang}_name" name="{lang}_name" />
            <Helper class="mb-2" color="red">
              {#if $errors[lang + '_name']}
                {$errors[lang + '_name']}
              {/if}
            </Helper>
          </div>
          <div>
            <Label for="{lang}_description" class="mb-2 text-gray-900 dark:text-white" aria-label="{lang} description">{getLabel('descriptionTitle', lang)}</Label>
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
      <Select id="worldId" items={nawsFormats} name="worldId" bind:value={$data.worldId} class="rounded-lg dark:bg-gray-600" />
    </div>
    <div class="md:col-span-2">
      <Label for="type" class="mb-2 md:col-span-2">{$translations.formatTypeTitle}</Label>
      <Select id="type" items={formatTypeCodes} name="type" bind:value={$data.type} class="rounded-lg dark:bg-gray-600" />
    </div>
    <div class="md:col-span-2">
      <Button type="submit" class="w-full" disabled={!$isDirty} onclick={disableButtonHack}>
        {#if selectedFormat}
          {$translations.applyChangesTitle}
        {:else}
          {$translations.addFormat}
        {/if}
      </Button>
      <Helper class="mt-4" color="red">
        {#if errorStringArray.length}
          {errorStringArray.length === 1 ? $translations.error : $translations.errors}
          {#each errorStringArray as e}
            {e}
          {/each}
        {/if}
      </Helper>
    </div>
  </div>
</form>
