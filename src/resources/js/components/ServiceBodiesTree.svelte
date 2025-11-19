<script lang="ts">
  import { Button } from 'flowbite-svelte';
  import Node from './ServiceBodiesTreeNode.svelte';
  import type { ServiceBody } from 'bmlt-server-client';
  import { translations } from '../stores/localization';

  interface TreeNode {
    label: string;
    value: string;
    checked?: boolean;
    indeterminate?: boolean;
    expanded?: boolean;
    children?: TreeNode[];
  }

  interface Props {
    serviceBodies: ServiceBody[];
    selectedValues?: string[];
  }

  let { serviceBodies, selectedValues = $bindable([]) }: Props = $props();

  const treeMap: Record<string, TreeNode> = {};
  const expansionState: Record<string, boolean> = {};
  let updateCounter = $state(0);
  let trees = $derived.by(() => {
    void updateCounter; // Track to force re-derivation
    const newTrees = convertServiceBodiesToTreeNodes(serviceBodies);
    syncCheckedState(newTrees, selectedValues);
    return newTrees;
  });

  function convertServiceBodiesToTreeNodes(serviceBodies: ServiceBody[]): TreeNode[] {
    const nodeMap: Record<number, TreeNode> = {};
    const roots: TreeNode[] = [];

    serviceBodies.forEach((sb) => {
      const value = sb.id.toString();
      nodeMap[sb.id] = {
        label: sb.name,
        value,
        checked: false, // Will be set by syncCheckedState
        expanded: expansionState[value] ?? true,
        children: []
      };
    });

    serviceBodies.forEach((sb) => {
      const node = nodeMap[sb.id];
      if (sb.parentId && nodeMap[sb.parentId]) {
        nodeMap[sb.parentId].children!.push(node);
      } else {
        roots.push(node);
      }
    });

    return roots;
  }

  function syncCheckedState(trees: TreeNode[], selectedIds: string[]): void {
    const selectedSet = new Set(selectedIds);

    function updateNode(node: TreeNode): void {
      node.checked = selectedSet.has(node.value);
      if (node.children) {
        node.children.forEach(updateNode);
        const hasChecked = node.children.some((c) => c.checked || c.indeterminate);
        const hasUnchecked = node.children.some((c) => !c.checked);
        node.indeterminate = hasChecked && hasUnchecked;
      }
    }

    trees.forEach(updateNode);
  }

  function rebuildChildren(node: TreeNode, checkAsParent = true) {
    if (node.children) {
      for (const child of node.children) {
        if (checkAsParent) child.checked = !!node.checked;
        rebuildChildren(child, checkAsParent);
      }
      node.indeterminate = node.children.some((c) => c.indeterminate) || (node.children.some((c) => !!c.checked) && node.children.some((c) => !c.checked));
    }
  }

  function rebuildTree(e: CustomEvent<{ node: TreeNode }>, checkAsParent: boolean = true): void {
    const node = e.detail.node;
    let parent = treeMap[node.label];
    rebuildChildren(node, checkAsParent);
    while (parent) {
      const allCheck = parent.children?.every((c) => !!c.checked);
      if (allCheck) {
        parent.indeterminate = false;
        parent.checked = true;
      } else {
        const haveCheckedOrIndetermine = parent.children?.some((c) => !!c.checked || c.indeterminate);
        if (haveCheckedOrIndetermine) {
          parent.indeterminate = true;
        } else {
          parent.indeterminate = false;
        }
      }

      parent = treeMap[parent.label];
    }
    // trees = [...trees];
    selectedValues = collectSelectedValues(trees);
  }

  function collectSelectedValues(trees: TreeNode[]): string[] {
    const selected: string[] = [];

    function traverse(node: TreeNode) {
      if (node.checked) {
        selected.push(node.value);
      }
      if (node.children) {
        node.children.forEach(traverse);
      }
    }

    trees.forEach(traverse);
    return selected;
  }

  function selectAll() {
    let updatedTrees = trees.map((tree) => {
      checkAllNodes(tree, true);
      return { ...tree };
    });

    serviceBodies = [...serviceBodies];
    selectedValues = collectSelectedValues(updatedTrees);
  }

  function unselectAll() {
    trees.forEach((node) => {
      checkAllNodes(node, false);
    });
    serviceBodies = [...serviceBodies];
    selectedValues = [];
  }

  function checkAllNodes(node: TreeNode, check: boolean) {
    node.checked = check;
    node.indeterminate = false;
    if (node.children) {
      node.children.forEach((child) => checkAllNodes(child, check));
    }
  }
  function toggleAll() {
    if (isAllSelected) {
      unselectAll();
    } else {
      selectAll();
    }
  }

  function isNodeFullySelected(node: TreeNode): boolean {
    return !!node.checked && (!node.children || node.children.every(isNodeFullySelected));
  }
  let isAllSelected = $derived.by(() => trees.every((node) => isNodeFullySelected(node)));

  function handleExpansionToggle(node: TreeNode) {
    node.expanded = !node.expanded;
    expansionState[node.value] = node.expanded;
    updateCounter++;
  }
</script>

<div class="mb-4">
  <Button onclick={toggleAll} size="xs" color="primary" class="w-full" onmousedown={(e: MouseEvent) => e.preventDefault()}>
    {isAllSelected ? $translations.unselectAllServiceBodies : $translations.selectAllServiceBodies}
  </Button>
</div>

<div
  onmousedown={(e: MouseEvent) => e.preventDefault()}
  onclick={(e: MouseEvent) => {
    e.preventDefault();
    e.stopPropagation();
  }}
  onkeydown={(e: KeyboardEvent) => {
    e.stopPropagation();
  }}
  role="tree"
  aria-label="Service bodies tree"
  tabindex="0"
>
  {#each trees as tree (tree)}
    <Node {tree} toggle={rebuildTree} onExpansionToggle={handleExpansionToggle} />
  {/each}
</div>
