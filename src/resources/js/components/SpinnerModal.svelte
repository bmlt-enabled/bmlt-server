<script lang="ts">
  import { Spinner } from 'flowbite-svelte';
  import { spinner } from '../stores/spinner';
  import { fade } from 'svelte/transition';

  let isActive = $derived(!!$spinner);
</script>

{#if isActive}
  <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" transition:fade={{ duration: 150 }}>
    <Spinner size="10" />
  </div>
{/if}
