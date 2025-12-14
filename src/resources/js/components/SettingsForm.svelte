<script lang="ts">
  import { validator } from '@felte/validator-yup';
  import { createForm } from 'felte';
  import { Button, Helper, Input, Label, Select, Checkbox, Textarea } from 'flowbite-svelte';
  import * as yup from 'yup';
  import TagInput from './TagInput.svelte';
  import RegionSelect from './RegionSelect.svelte';
  import DurationSelector from './DurationSelector.svelte';
  import MapAccordion from './MapAccordion.svelte';
  import KeyValueEditor from './KeyValueEditor.svelte';

  import { spinner } from '../stores/spinner';
  import RootServerApi from '../lib/ServerApi';
  import { formIsDirty, isDirty as globalIsDirty } from '../lib/utils';
  import { translations } from '../stores/localization';

  interface Props {
    serverSettings: Record<string, any>;
    onSaveSuccess?: () => void;
  }

  let { serverSettings, onSaveSuccess }: Props = $props();

  let meetingStates = $state<string[]>(serverSettings.meetingStatesAndProvinces ?? []);
  let meetingCounties = $state<string[]>(serverSettings.meetingCountiesAndSubProvinces ?? []);
  let formatLangNames = $state<Record<string, string>>(serverSettings.formatLangNames ?? {});

  const languageItems = Object.entries(settings.languageMapping).map((lang) => ({ value: lang[0], name: lang[1] }));

  const distanceUnitsItems = $derived([
    { value: 'mi', name: $translations.miles },
    { value: 'km', name: $translations.kilometers }
  ]);

  const initialValues = {
    googleApiKey: serverSettings.googleApiKey ?? '',
    language: serverSettings.language ?? 'en',
    bmltTitle: serverSettings.bmltTitle ?? '',
    bmltNotice: serverSettings.bmltNotice ?? '',
    defaultDurationTime: serverSettings.defaultDurationTime ?? '01:00:00',
    distanceUnits: serverSettings.distanceUnits ?? 'mi',
    regionBias: serverSettings.regionBias ?? 'us',
    autoGeocodingEnabled: serverSettings.autoGeocodingEnabled ?? true,
    countyAutoGeocodingEnabled: serverSettings.countyAutoGeocodingEnabled ?? false,
    zipAutoGeocodingEnabled: serverSettings.zipAutoGeocodingEnabled ?? false,
    defaultClosedStatus: serverSettings.defaultClosedStatus ?? true,
    enableLanguageSelector: serverSettings.enableLanguageSelector ?? false,
    searchSpecMapCenterLatitude: serverSettings.searchSpecMapCenterLatitude ?? 34.235918,
    searchSpecMapCenterLongitude: serverSettings.searchSpecMapCenterLongitude ?? -118.563659,
    searchSpecMapCenterZoom: serverSettings.searchSpecMapCenterZoom ?? 6,
    numberOfMeetingsForAuto: serverSettings.numberOfMeetingsForAuto ?? 10,
    changeDepthForMeetings: serverSettings.changeDepthForMeetings ?? 0,
    defaultSortKey: serverSettings.defaultSortKey ?? '',
    includeServiceBodyEmailInSemantic: serverSettings.includeServiceBodyEmailInSemantic ?? false
  };

  const { data, errors, form, isDirty } = createForm({
    initialValues: initialValues,
    onSubmit: async (values) => {
      spinner.show();
      // Transform JSON strings back to objects/arrays
      // Convert Svelte Proxy arrays to plain arrays for serialization
      const transformedValues = {
        ...values,
        meetingStatesAndProvinces: [...meetingStates],
        meetingCountiesAndSubProvinces: [...meetingCounties],
        formatLangNames: formatLangNames
      };
      await RootServerApi.updateSettings(transformedValues);
    },
    onError: async (error) => {
      console.log(error);
      await RootServerApi.handleErrors(error as Error, {
        handleValidationError: (error) => {
          const errorObject: any = {};
          if (error?.errors) {
            Object.keys(error.errors).forEach((key) => {
              errorObject[key] = (error.errors[key] ?? []).join(' ');
            });
            errors.set(errorObject);
          }
        }
      });
      spinner.hide();
    },
    onSuccess: () => {
      spinner.hide();
      onSaveSuccess?.();
    },
    extend: validator({
      schema: yup.object({
        googleApiKey: yup.string().max(255),
        language: yup.string().required(),
        bmltTitle: yup.string().max(255),
        bmltNotice: yup.string().max(65535),
        defaultDurationTime: yup.string().required(),
        distanceUnits: yup.string().required(),
        regionBias: yup.string().required(),
        autoGeocodingEnabled: yup.boolean(),
        countyAutoGeocodingEnabled: yup.boolean(),
        zipAutoGeocodingEnabled: yup.boolean(),
        defaultClosedStatus: yup.boolean(),
        enableLanguageSelector: yup.boolean(),
        searchSpecMapCenterLatitude: yup.number().min(-90).max(90).required(),
        searchSpecMapCenterLongitude: yup.number().min(-180).max(180).required(),
        searchSpecMapCenterZoom: yup.number().min(0).max(20).required(),
        numberOfMeetingsForAuto: yup.number().min(0).required(),
        changeDepthForMeetings: yup.number().min(0).required(),
        defaultSortKey: yup.string().max(255),
        includeServiceBodyEmailInSemantic: yup.boolean()
      }),
      castValues: true
    })
  });

  function disableButtonHack(event: MouseEvent) {
    if (!$isDirty) {
      event.preventDefault();
    }
  }

  $effect(() => {
    // Track both form data changes and array state changes
    const formChanged = formIsDirty(initialValues, $data);
    const statesChanged = JSON.stringify(meetingStates) !== JSON.stringify(serverSettings.meetingStatesAndProvinces ?? []);
    const countiesChanged = JSON.stringify(meetingCounties) !== JSON.stringify(serverSettings.meetingCountiesAndSubProvinces ?? []);

    // Force tracking by accessing formatLangNames
    const currentLangNames = formatLangNames;

    // Deep compare objects by sorting keys first
    const sortedCurrent = JSON.stringify(
      Object.keys(currentLangNames)
        .sort()
        .reduce(
          (acc, key) => {
            acc[key] = currentLangNames[key];
            return acc;
          },
          {} as Record<string, string>
        )
    );
    const sortedOriginal = JSON.stringify(
      Object.keys(serverSettings.formatLangNames ?? {})
        .sort()
        .reduce(
          (acc, key) => {
            acc[key] = (serverSettings.formatLangNames ?? {})[key];
            return acc;
          },
          {} as Record<string, string>
        )
    );
    const langNamesChanged = sortedCurrent !== sortedOriginal;

    const isFormDirty = formChanged || statesChanged || countiesChanged || langNamesChanged;
    isDirty.set(isFormDirty);
    globalIsDirty.set(isFormDirty);
  });
</script>

<form use:form>
  <div class="space-y-6">
    <!-- General Settings Section -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{$translations.generalSettings}</h3>
      <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <Label for="bmltTitle" class="mb-2">{$translations.serverTitleLabel}</Label>
          <Input type="text" id="bmltTitle" name="bmltTitle" placeholder={$translations.myBmltServer} />
          <Helper class="mt-2" color="red">
            {#if $errors.bmltTitle}
              {$errors.bmltTitle}
            {/if}
          </Helper>
        </div>
        <div class="md:col-span-2">
          <Label for="bmltNotice" class="mb-2">{$translations.serverNotice}</Label>
          <Textarea id="bmltNotice" name="bmltNotice" placeholder={$translations.noticeMessageDisplayedToUsers} rows={3} class="w-full" />
          <Helper class="mt-2" color="red">
            {#if $errors.bmltNotice}
              {$errors.bmltNotice}
            {/if}
          </Helper>
        </div>
        <div>
          <Label for="language" class="mb-2">{$translations.defaultLanguage}</Label>
          <Select id="language" items={languageItems} name="language" bind:value={$data.language} class="rounded-lg dark:bg-gray-600" />
          <Helper class="mt-2" color="red">
            {#if $errors.language}
              {$errors.language}
            {/if}
          </Helper>
        </div>
        <div>
          <Label class="mb-2">{$translations.defaultMeetingDuration}</Label>
          <input type="hidden" name="defaultDurationTime" bind:value={$data.defaultDurationTime} />
          <DurationSelector
            initialDuration={$data.defaultDurationTime}
            updateDuration={(d) => {
              $data.defaultDurationTime = d + ':00';
            }}
          />
          <Helper class="mt-2" color="red">
            {#if $errors.defaultDurationTime}
              {$errors.defaultDurationTime}
            {/if}
          </Helper>
        </div>
        <div class="md:col-span-2">
          <Checkbox name="enableLanguageSelector" bind:checked={$data.enableLanguageSelector}>{$translations.enableLanguageSelector}</Checkbox>
        </div>
      </div>
    </div>

    <!-- Maps & Geocoding Section -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{$translations.mapsAndGeocoding}</h3>
      <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
          <Label for="googleApiKey" class="mb-2">{$translations.googleMapsApiKey}</Label>
          <Input type="text" id="googleApiKey" name="googleApiKey" placeholder={$translations.yourGoogleMapsApiKey} />
          <Helper class="mt-2" color="red">
            {#if $errors.googleApiKey}
              {$errors.googleApiKey}
            {/if}
          </Helper>
        </div>
        <div>
          <Label for="distanceUnits" class="mb-2">{$translations.distanceUnits}</Label>
          <Select id="distanceUnits" items={distanceUnitsItems} name="distanceUnits" bind:value={$data.distanceUnits} class="rounded-lg dark:bg-gray-600" />
          <Helper class="mt-2" color="red">
            {#if $errors.distanceUnits}
              {$errors.distanceUnits}
            {/if}
          </Helper>
        </div>
        <div>
          <Label for="regionBias" class="mb-2">{$translations.regionBias}</Label>
          <input type="hidden" name="regionBias" bind:value={$data.regionBias} />
          <RegionSelect bind:value={$data.regionBias} />
          <Helper class="mt-2" color="red">
            {#if $errors.regionBias}
              {$errors.regionBias}
            {/if}
          </Helper>
        </div>
        <div class="md:col-span-2">
          <MapAccordion title={$translations.mapCenterLocation}>
            <div class="grid gap-4 md:grid-cols-3">
              <div>
                <Label for="searchSpecMapCenterLatitude" class="mb-2">{$translations.latitudeTitle}</Label>
                <Input type="number" step="0.000001" id="searchSpecMapCenterLatitude" name="searchSpecMapCenterLatitude" />
                <Helper class="mt-2" color="red">
                  {#if $errors.searchSpecMapCenterLatitude}
                    {$errors.searchSpecMapCenterLatitude}
                  {/if}
                </Helper>
              </div>
              <div>
                <Label for="searchSpecMapCenterLongitude" class="mb-2">{$translations.longitudeTitle}</Label>
                <Input type="number" step="0.000001" id="searchSpecMapCenterLongitude" name="searchSpecMapCenterLongitude" />
                <Helper class="mt-2" color="red">
                  {#if $errors.searchSpecMapCenterLongitude}
                    {$errors.searchSpecMapCenterLongitude}
                  {/if}
                </Helper>
              </div>
              <div>
                <Label for="searchSpecMapCenterZoom" class="mb-2">{$translations.zoomLevel}</Label>
                <Input type="number" id="searchSpecMapCenterZoom" name="searchSpecMapCenterZoom" min="0" max="20" />
                <Helper class="mt-2" color="red">
                  {#if $errors.searchSpecMapCenterZoom}
                    {$errors.searchSpecMapCenterZoom}
                  {/if}
                </Helper>
              </div>
            </div>
          </MapAccordion>
        </div>
        <div>
          <Label for="numberOfMeetingsForAuto" class="mb-2">{$translations.numberOfMeetingsForAutoSearch}</Label>
          <Input type="number" id="numberOfMeetingsForAuto" name="numberOfMeetingsForAuto" min="0" />
          <Helper class="mt-2">{$translations.numberOfMeetingsForAutoSearchHelperText}</Helper>
          <Helper class="mt-2" color="red">
            {#if $errors.numberOfMeetingsForAuto}
              {$errors.numberOfMeetingsForAuto}
            {/if}
          </Helper>
        </div>
        <div class="space-y-2 md:col-span-2">
          <Checkbox name="autoGeocodingEnabled" bind:checked={$data.autoGeocodingEnabled}>{$translations.enableAutoGeocoding}</Checkbox>
          <Checkbox name="countyAutoGeocodingEnabled" bind:checked={$data.countyAutoGeocodingEnabled}>{$translations.enableCountyAutoGeocoding}</Checkbox>
          <Checkbox name="zipAutoGeocodingEnabled" bind:checked={$data.zipAutoGeocodingEnabled}>{$translations.enableZipAutoGeocoding}</Checkbox>
        </div>
      </div>
    </div>

    <!-- Meeting Settings Section -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{$translations.meetingSettings}</h3>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <Label for="changeDepthForMeetings" class="mb-2">{$translations.changeHistoryDepth}</Label>
          <Input type="number" id="changeDepthForMeetings" name="changeDepthForMeetings" min="0" />
          <Helper class="mt-2" color="red">
            {#if $errors.changeDepthForMeetings}
              {$errors.changeDepthForMeetings}
            {/if}
          </Helper>
        </div>
        <div>
          <Label for="defaultSortKey" class="mb-2">{$translations.defaultSortKey}</Label>
          <Input type="text" id="defaultSortKey" name="defaultSortKey" placeholder="weekday_tinyint,start_time" />
          <Helper class="mt-2" color="red">
            {#if $errors.defaultSortKey}
              {$errors.defaultSortKey}
            {/if}
          </Helper>
        </div>
        <div class="md:col-span-2">
          <Checkbox name="defaultClosedStatus" bind:checked={$data.defaultClosedStatus}>{$translations.defaultClosedStatus}</Checkbox>
        </div>
        <div class="md:col-span-2">
          <Label class="mb-2">{$translations.meetingStatesProvinces}</Label>
          <TagInput bind:value={meetingStates} placeholder={$translations.addStateProvince} />
          <Helper class="mt-2">{$translations.addStatesOrProvincesWhereMeetingsAreHeld}</Helper>
        </div>
        <div class="md:col-span-2">
          <Label class="mb-2">{$translations.meetingCountiesSubProvinces}</Label>
          <TagInput bind:value={meetingCounties} placeholder={$translations.addCountySubProvince} />
          <Helper class="mt-2">{$translations.addCountiesOrSubProvincesWhereMeetingsAreHeld}</Helper>
        </div>
      </div>
    </div>

    <!-- Advanced Settings Section -->
    <div class="rounded-lg border border-gray-200 bg-white p-6 dark:border-gray-700 dark:bg-gray-800">
      <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">{$translations.advancedSettings}</h3>
      <div class="space-y-4">
        <Checkbox name="includeServiceBodyEmailInSemantic" bind:checked={$data.includeServiceBodyEmailInSemantic}>{$translations.includeServiceBodyEmailInSemanticOutput}</Checkbox>

        <MapAccordion title={$translations.formatLangNames}>
          <KeyValueEditor bind:value={formatLangNames} helperText={$translations.formatLangNamesHelperText} keyPlaceholder="ga" valuePlaceholder="Gaelic" />
        </MapAccordion>
      </div>
    </div>

    <!-- Save Button -->
    <div class="flex justify-end">
      <Button type="submit" disabled={!$isDirty} onclick={disableButtonHack} class="w-full md:w-auto">{$translations.saveSettings}</Button>
    </div>
  </div>
</form>
