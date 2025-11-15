import { writable } from 'svelte/store';
import type { Format } from 'bmlt-server-client';

interface FormatsState {
  formats: Format[];
  searchTerm: string;
  lastEditedFormatId: number | null;
}

const createFormatsState = () => {
  const { subscribe, set, update } = writable<FormatsState>({
    formats: [],
    searchTerm: '',
    lastEditedFormatId: null
  });

  return {
    subscribe,
    set,
    update,
    reset: () =>
      set({
        formats: [],
        searchTerm: '',
        lastEditedFormatId: null
      })
  };
};

export const formatsState = createFormatsState();
