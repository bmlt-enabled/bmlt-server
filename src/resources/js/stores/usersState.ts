import { writable } from 'svelte/store';
import type { User } from 'bmlt-server-client';

interface UsersState {
  users: User[];
  searchTerm: string;
  lastEditedUserId: number | null;
}

const createUsersState = () => {
  const { subscribe, set, update } = writable<UsersState>({
    users: [],
    searchTerm: '',
    lastEditedUserId: null
  });

  return {
    subscribe,
    set,
    update,
    reset: () =>
      set({
        users: [],
        searchTerm: '',
        lastEditedUserId: null
      })
  };
};

export const usersState = createUsersState();
