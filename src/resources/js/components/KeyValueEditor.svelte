<script lang="ts">
  import { Button, Input, Label, Helper } from 'flowbite-svelte';
  import { CloseCircleSolid } from 'flowbite-svelte-icons';

  interface Props {
    value?: Record<string, string>;
    label?: string;
    helperText?: string;
    keyPlaceholder?: string;
    valuePlaceholder?: string;
    disabled?: boolean;
  }

  let { value = $bindable({}), label = '', helperText = '', keyPlaceholder = 'Key', valuePlaceholder = 'Value', disabled = false }: Props = $props();

  // Convert object to array of pairs for editing with stable IDs
  let pairs = $derived(Object.keys(value).map((key, index) => ({ key, value: value[key], index })));

  function addPair() {
    value = { ...value, '': '' };
  }

  function removePair(index: number) {
    const keys = Object.keys(value);
    const keyToRemove = keys[index];
    const newValue = { ...value };
    delete newValue[keyToRemove];
    value = newValue;
  }

  function handleKeyChange(index: number, newKey: string) {
    const keys = Object.keys(value);
    const oldKey = keys[index];
    const oldValue = value[oldKey];
    const newValue: Record<string, string> = {};

    keys.forEach((k, i) => {
      if (i === index) {
        if (newKey.trim() !== '') {
          newValue[newKey.trim()] = oldValue;
        }
      } else {
        newValue[k] = value[k];
      }
    });

    value = newValue;
  }

  function handleValueChange(index: number, newVal: string) {
    const keys = Object.keys(value);
    const key = keys[index];
    value = { ...value, [key]: newVal };
  }
</script>

<div class="space-y-2">
  {#if label}
    <Label class="mb-2">{label}</Label>
  {/if}

  {#if helperText}
    <Helper class="mb-2">{helperText}</Helper>
  {/if}

  <div class="space-y-2">
    {#each pairs as pair (pair.index)}
      <div class="flex items-start gap-2">
        <div class="flex-1">
          <Input
            type="text"
            placeholder={keyPlaceholder}
            value={pair.key}
            oninput={(e: Event) => {
              const target = e.currentTarget as HTMLInputElement;
              handleKeyChange(pair.index, target.value);
            }}
            {disabled}
            size="sm"
          />
        </div>
        <div class="flex-1">
          <Input
            type="text"
            placeholder={valuePlaceholder}
            value={pair.value}
            oninput={(e: Event) => {
              const target = e.currentTarget as HTMLInputElement;
              handleValueChange(pair.index, target.value);
            }}
            {disabled}
            size="sm"
          />
        </div>
        <Button color="red" size="sm" onclick={() => removePair(pair.index)} {disabled} class="px-2">
          <CloseCircleSolid class="h-4 w-4" />
        </Button>
      </div>
    {/each}

    {#if pairs.length === 0}
      <div class="py-2 text-sm text-gray-500 italic">No entries. Click "Add Entry" to add a key-value pair.</div>
    {/if}

    <Button color="light" size="sm" onclick={addPair} {disabled}>+ Add Entry</Button>
  </div>
</div>
