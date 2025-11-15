import { writable } from 'svelte/store';
import type { ServiceBody, User } from 'bmlt-server-client';

interface ServiceBodiesState {
  serviceBodies: ServiceBody[];
  users: User[];
  searchTerm: string;
  lastEditedServiceBodyId: number | null;
}

const createServiceBodiesState = () => {
  const { subscribe, set, update } = writable<ServiceBodiesState>({
    serviceBodies: [],
    users: [],
    searchTerm: '',
    lastEditedServiceBodyId: null
  });

  return {
    subscribe,
    set,
    update,
    reset: () =>
      set({
        serviceBodies: [],
        users: [],
        searchTerm: '',
        lastEditedServiceBodyId: null
      })
  };
};

export const serviceBodiesState = createServiceBodiesState();
