import { writable } from 'svelte/store';
import type { Meeting } from 'bmlt-server-client';

interface MeetingsState {
  meetings: Meeting[];
  searchTerm: string;
  selectedServiceBodies: string[];
  selectedDays: string[];
  selectedTimes: string[];
  selectedPublished: string[];
  currentPosition: number;
  itemsPerPage: number;
  sortColumn: keyof Meeting | null;
  sortDirection: 'asc' | 'desc';
  lastEditedMeetingId: number | null;
  meetingIds: string;
}

const createMeetingsState = () => {
  const { subscribe, set, update } = writable<MeetingsState>({
    meetings: [],
    searchTerm: '',
    selectedServiceBodies: [],
    selectedDays: [],
    selectedTimes: [],
    selectedPublished: [],
    currentPosition: 0,
    itemsPerPage: 20,
    sortColumn: null,
    sortDirection: 'asc',
    lastEditedMeetingId: null,
    meetingIds: ''
  });

  return {
    subscribe,
    set,
    update,
    reset: () =>
      set({
        meetings: [],
        searchTerm: '',
        selectedServiceBodies: [],
        selectedDays: [],
        selectedTimes: [],
        selectedPublished: [],
        currentPosition: 0,
        itemsPerPage: 20,
        sortColumn: null,
        sortDirection: 'asc',
        lastEditedMeetingId: null,
        meetingIds: ''
      })
  };
};

export const meetingsState = createMeetingsState();
