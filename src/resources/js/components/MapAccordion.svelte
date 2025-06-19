<script lang="ts">
  import type { Snippet } from 'svelte';
  import { tv } from 'tailwind-variants';

  interface Props {
    title?: string;
    children?: Snippet;
    onToggle?: (isOpen: boolean) => void;
    size?: 'sm' | 'md' | 'lg';
  }

  let { title = 'Accordion Title', children, onToggle, size = 'md' }: Props = $props();
  let isOpen = $state(false);

  const accordion = tv({
    slots: {
      base: 'relative w-full',
      header:
        'block w-full disabled:cursor-not-allowed disabled:opacity-50 rtl:text-right focus:outline-hidden border border-gray-300 dark:border-gray-600 focus:border-primary-500 focus:ring-primary-500 dark:focus:border-primary-500 dark:focus:ring-primary-500 bg-gray-50 text-gray-900 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 rounded-lg flex items-center justify-between cursor-pointer',
      title: 'text-sm font-medium text-gray-700 dark:text-gray-200',
      icon: 'h-4 w-4 transform transition-transform text-gray-400',
      content: 'transition-all duration-300 ease-in-out overflow-hidden'
    },
    variants: {
      size: {
        sm: {
          header: 'text-xs px-2 py-1 min-h-[2.4rem]',
          title: 'text-xs',
          icon: 'h-3 w-3'
        },
        md: {
          header: 'text-sm px-2.5 py-2.5 min-h-[2.7rem]',
          title: 'text-sm',
          icon: 'h-4 w-4'
        },
        lg: {
          header: 'sm:text-base px-3 py-3 min-h-[3.2rem]',
          title: 'text-base',
          icon: 'h-5 w-5'
        }
      },
      isOpen: {
        true: {
          icon: 'rotate-180',
          content: 'max-h-96 opacity-100'
        },
        false: {
          icon: 'rotate-0',
          content: 'max-h-0 opacity-0'
        }
      }
    },
    defaultVariants: {
      size: 'md',
      isOpen: false
    }
  });

  const { base, header, title: titleCls, icon, content } = $derived(accordion({ size }));

  function toggleAccordion() {
    isOpen = !isOpen;
    onToggle?.(isOpen);
  }
</script>

<div class={base()}>
  <div role="button" tabindex="0" aria-expanded={isOpen} class={header()} onclick={toggleAccordion} onkeydown={(e) => (e.key === 'Enter' || e.key === ' ' ? toggleAccordion() : null)}>
    <span class={titleCls()}>{title}</span>
    <svg class={icon({ isOpen })} fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
    </svg>
  </div>

  <div class={content({ isOpen })}>
    <div class="overflow-hidden pt-2">
      {@render children?.()}
    </div>
  </div>
</div>
