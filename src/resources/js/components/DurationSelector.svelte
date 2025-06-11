<script lang="ts">
  import { Label, Select } from 'flowbite-svelte';

  import { translations } from '../stores/localization';
  import { onMount } from 'svelte';

  // Better style with svelte 5 would be to have a bindable prop 'duration' rather than a function updateDuration.
  // However, felte doesn't know about svelte 5 yet, so this wouldn't work currently.
  interface Props {
    initialDuration: string;
    updateDuration: (d: string) => void;
  }

  let { initialDuration, updateDuration }: Props = $props();
  let hours: string = $state('');
  let minutes: string = $state('');

  function initialize() {
    [hours, minutes] = initialDuration.split(':').map((part) => part.padStart(2, '0'));
  }

  const hourOptions = Array.from({ length: 24 }, (_, i) => ({
    value: i.toString().padStart(2, '0'),
    name: i.toString().padStart(2, '0')
  }));

  const minuteOptions: { value: string; name: string }[] = [];
  for (let i = 0; i < 60; i = i + 5) {
    minuteOptions.push({ value: i.toString().padStart(2, '0'), name: i.toString().padStart(2, '0') });
  }

  function handleHourChange(event: Event) {
    const target = event.target as HTMLSelectElement;
    hours = target.value;
    updateDurationHelper();
  }

  function handleMinuteChange(event: Event) {
    const target = event.target as HTMLSelectElement;
    minutes = target.value;
    updateDurationHelper();
  }

  function updateDurationHelper() {
    updateDuration(`${hours}:${minutes}`);
  }

  onMount(initialize);
</script>

<div class="flex space-x-4">
  <div class="flex flex-col">
    <Select id="hours" class="dark:bg-gray-600" items={hourOptions} bind:value={hours} onchange={handleHourChange} />
    <Label for="hours" class="mt-2 text-sm font-semibold">{$translations.hoursTitle}</Label>
  </div>
  <div class="flex flex-col">
    <Select id="minutes" class="dark:bg-gray-600" items={minuteOptions} bind:value={minutes} onchange={handleMinuteChange} />
    <Label for="minutes" class="mt-2 text-sm font-semibold">{$translations.minutesTitle}</Label>
  </div>
</div>
