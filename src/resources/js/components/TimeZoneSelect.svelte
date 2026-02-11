<script lang="ts">
  import { SvelteMap } from 'svelte/reactivity';
  import { Input } from 'flowbite-svelte';
  import { timeZoneGroups } from '../lib/timeZone/timeZones';
  import { translations } from '../stores/localization';

  interface Props {
    value: string;
  }

  let { value = $bindable() }: Props = $props();

  const allTimeZones = timeZoneGroups.flatMap((group) =>
    group.values.map((tz) => ({
      value: tz.value,
      name: tz.name,
      group: group.name
    }))
  );

  let searchTerm = $state('');
  let showDropdown = $state(false);
  let isEditing = $state(false);

  let displayValue = $derived.by(() => {
    if (isEditing) {
      return searchTerm;
    }
    if (value) {
      const tz = allTimeZones.find((t) => t.value === value);
      return tz ? tz.name : value;
    }
    return '';
  });

  let filteredTimeZones = $derived(
    searchTerm
      ? allTimeZones.filter(
          (tz) => tz.name.toLowerCase().includes(searchTerm.toLowerCase()) || tz.value.toLowerCase().includes(searchTerm.toLowerCase()) || tz.group.toLowerCase().includes(searchTerm.toLowerCase())
        )
      : allTimeZones
  );

  // Group filtered results by continent
  let groupedFiltered = $derived.by(() => {
    const groups = new SvelteMap<string, typeof filteredTimeZones>();
    for (const tz of filteredTimeZones) {
      const list = groups.get(tz.group) ?? [];
      list.push(tz);
      groups.set(tz.group, list);
    }
    return groups;
  });

  function selectTimeZone(tzValue: string) {
    value = tzValue;
    isEditing = false;
    showDropdown = false;
  }

  function onInput(e: Event) {
    searchTerm = (e.target as HTMLInputElement).value;
    isEditing = true;
    showDropdown = true;
  }

  function onFocus() {
    showDropdown = true;
  }

  let dropdownEl: HTMLDivElement | undefined = $state();

  function onBlur(e: FocusEvent) {
    // If focus moved to something inside the dropdown (button or scrollbar), don't close
    if (dropdownEl?.contains(e.relatedTarget as Node)) {
      return;
    }
    setTimeout(() => {
      showDropdown = false;
      isEditing = false;
    }, 200);
  }
</script>

<div class="relative">
  <Input type="text" value={displayValue} oninput={onInput} placeholder={$translations.timeZoneSelectPlaceholder} onfocus={onFocus} onblur={onBlur} class="dark:bg-gray-600" />
  {#if showDropdown && filteredTimeZones.length > 0}
    <!-- svelte-ignore a11y_no_static_element_interactions -->
    <div
      bind:this={dropdownEl}
      onmousedown={(e) => e.preventDefault()}
      class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-300 bg-white shadow-lg dark:border-gray-600 dark:bg-gray-700"
    >
      {#each [...groupedFiltered] as [group, tzList] (group)}
        <div class="bg-gray-50 px-4 py-1 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">{group}</div>
        {#each tzList as tz (tz.value)}
          <button type="button" class="w-full px-4 py-2 text-left text-sm hover:bg-gray-100 dark:hover:bg-gray-600" onclick={() => selectTimeZone(tz.value)}>
            {tz.name}
          </button>
        {/each}
      {/each}
    </div>
  {/if}
</div>
