<script lang="ts">
  import { Badge, Input, Button } from 'flowbite-svelte';
  import { CloseCircleSolid } from 'flowbite-svelte-icons';

  interface Props {
    value: string[];
    placeholder?: string;
    name?: string;
  }

  let { value = $bindable([]), placeholder = 'Add item...', name }: Props = $props();

  let inputValue = $state('');

  function addItem() {
    const trimmed = inputValue.trim();
    if (!trimmed) return;

    // Split by comma to handle multiple items at once
    const items = trimmed
      .split(',')
      .map((item) => item.trim())
      .filter((item) => item && !value.includes(item));
    if (items.length > 0) {
      value = [...value, ...items].sort();
      inputValue = '';
    }
  }

  function removeItem(index: number) {
    value = value.filter((_, i) => i !== index).sort();
  }

  function handleKeydown(event: KeyboardEvent) {
    if (event.key === 'Enter') {
      event.preventDefault();
      addItem();
    }
  }
</script>

<div class="space-y-2">
  <div class="flex gap-2">
    <Input type="text" bind:value={inputValue} {placeholder} onkeydown={handleKeydown} class="flex-1" />
    <Button onclick={addItem} disabled={!inputValue.trim()}>Add</Button>
  </div>

  {#if value.length > 0}
    <div class="flex flex-wrap gap-2">
      {#each value as item, index}
        <Badge color="blue" large class="flex items-center gap-1">
          <span>{item}</span>
          <button type="button" onclick={() => removeItem(index)} class="ml-1 hover:text-blue-900 dark:hover:text-blue-300">
            <CloseCircleSolid class="h-4 w-4" />
          </button>
        </Badge>
      {/each}
    </div>
  {/if}

  <!-- Hidden input for form submission -->
  {#if name}
    <input type="hidden" {name} value={JSON.stringify(value)} />
  {/if}
</div>
