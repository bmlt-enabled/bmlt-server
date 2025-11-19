<script lang="ts">
  import ServiceBodiesTreeNode from './ServiceBodiesTreeNode.svelte';
  import { Checkbox, Label } from 'flowbite-svelte';

  interface TreeNode {
    label: string;
    value: string;
    checked?: boolean;
    indeterminate?: boolean;
    expanded?: boolean;
    children?: TreeNode[];
  }

  interface Props {
    toggle: (e: CustomEvent<{ node: TreeNode }>, checkAsParent?: boolean) => void;
    tree: TreeNode;
    onExpansionToggle?: (node: TreeNode) => void;
  }

  let { toggle, tree, onExpansionToggle }: Props = $props();

  if (tree.expanded === undefined) {
    tree.expanded = false;
  }

  const toggleExpansion = () => {
    onExpansionToggle?.(tree);
  };

  const toggleCheck = () => {
    tree.checked = !tree.checked;
    toggle(new CustomEvent('toggle', { detail: { node: tree } }));
  };
</script>

<ul>
  <li>
    <div class="flex items-center space-x-2">
      {#if tree.children && tree.children.length > 0}
        <button
          type="button"
          onclick={toggleExpansion}
          class="arrow font-mono text-lg leading-6 text-black dark:text-white"
          class:arrowDown={tree.expanded}
          aria-expanded={tree.expanded}
          aria-label="Toggle node"
        ></button>
      {/if}
      <Checkbox id={tree.value} data-label={tree.label} checked={tree.checked} indeterminate={tree.indeterminate} onclick={toggleCheck} />
      <Label class="ml-2 cursor-pointer" onclick={toggleCheck}>{tree.label}</Label>
    </div>
    {#if tree.children && tree.children.length > 0 && tree.expanded}
      <ul>
        {#each tree.children as child (child.value)}
          <li>
            <ServiceBodiesTreeNode tree={child} {toggle} {onExpansionToggle} />
          </li>
        {/each}
      </ul>
    {/if}
  </li>
</ul>

<style>
  ul {
    margin: 0;
    list-style: none;
    padding-left: 1.2rem;
    user-select: none;
  }

  .arrow::before {
    --tw-content: '+';
    content: var(--tw-content);
    display: inline-block;
    cursor: pointer;
  }
</style>
