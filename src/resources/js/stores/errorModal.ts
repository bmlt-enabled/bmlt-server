import { writable } from 'svelte/store';
import { spinner } from './spinner';

export interface ErrorDetails {
  title: string;
  message: string;
  details?: string;
  timestamp: Date;
}

const errorModalStore = writable<ErrorDetails | null>(null);

export const errorModal = {
  subscribe: errorModalStore.subscribe,
  show: (error: ErrorDetails): void => {
    spinner.reset();
    errorModalStore.set(error);
  },
  hide: (): void => errorModalStore.set(null)
};
