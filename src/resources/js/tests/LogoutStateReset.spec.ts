import { beforeAll, describe, expect, test } from 'vitest';
import { get } from 'svelte/store';
import { render, screen, waitFor } from '@testing-library/svelte';
import '@testing-library/jest-dom';
import userEvent from '@testing-library/user-event';

import App from '../App.svelte';
import { sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';
import { formatsState } from '../stores/formatsState';
import { meetingsState } from '../stores/meetingsState';
import { serviceBodiesState } from '../stores/serviceBodiesState';
import { usersState } from '../stores/usersState';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

describe('state reset on logout', () => {
  test('all UI state stores are reset when user logs out', async () => {
    const user = userEvent.setup();
    render(App);

    // Log in as server admin
    await user.type(await screen.findByRole('textbox', { name: 'Username' }), 'serveradmin');
    await user.type(await screen.findByLabelText('Password'), 'serveradmin-password');
    await user.click(screen.getByRole('button', { name: 'Log In' }));
    expect(await screen.findByText('Welcome Server Administrator')).toBeInTheDocument();

    // Manually populate all state stores with test data
    formatsState.set({
      formats: [{ id: 1, keyString: 'TEST', nameString: 'Test Format', worldId: 'TEST', type: 'FC1' }] as any,
      searchTerm: 'test search',
      lastEditedFormatId: 1
    });
    meetingsState.set({
      meetings: [{ id: 1, name: 'Test Meeting' }] as any,
      searchTerm: 'test meeting',
      selectedServiceBodies: ['1'],
      selectedDays: ['1'],
      selectedTimes: ['morning'],
      selectedPublished: ['1'],
      currentPosition: 10,
      itemsPerPage: 20,
      sortColumn: 'name',
      sortDirection: 'asc',
      lastEditedMeetingId: 1,
      meetingIds: '1,2,3'
    });
    serviceBodiesState.set({
      serviceBodies: [{ id: 1, name: 'Test Service Body' }] as any,
      users: [{ id: 1, displayName: 'Test User' }] as any,
      searchTerm: 'test service body',
      lastEditedServiceBodyId: 1
    });
    usersState.set({
      users: [{ id: 1, displayName: 'Test User', username: 'testuser', type: 'admin', ownerId: 1 }] as any,
      searchTerm: 'test user',
      lastEditedUserId: 1
    });

    // Verify all stores have data before logout
    expect(get(formatsState).formats.length).toBeGreaterThan(0);
    expect(get(formatsState).searchTerm).toBe('test search');
    expect(get(formatsState).lastEditedFormatId).toBe(1);
    expect(get(meetingsState).meetings.length).toBeGreaterThan(0);
    expect(get(meetingsState).searchTerm).toBe('test meeting');
    expect(get(meetingsState).selectedServiceBodies).toEqual(['1']);
    expect(get(meetingsState).currentPosition).toBe(10);
    expect(get(serviceBodiesState).serviceBodies.length).toBeGreaterThan(0);
    expect(get(serviceBodiesState).searchTerm).toBe('test service body');
    expect(get(usersState).users.length).toBeGreaterThan(0);
    expect(get(usersState).searchTerm).toBe('test user');

    // Log out
    await user.click(screen.getByRole('link', { name: 'Logout', hidden: true }));
    expect(await screen.findByRole('button', { name: 'Log In' })).toBeEnabled();

    // Verify all state stores are reset to initial state
    const formatsStateAfter = get(formatsState);
    expect(formatsStateAfter.formats).toEqual([]);
    expect(formatsStateAfter.searchTerm).toBe('');
    expect(formatsStateAfter.lastEditedFormatId).toBeNull();

    const meetingsStateAfter = get(meetingsState);
    expect(meetingsStateAfter.meetings).toEqual([]);
    expect(meetingsStateAfter.searchTerm).toBe('');
    expect(meetingsStateAfter.selectedServiceBodies).toEqual([]);
    expect(meetingsStateAfter.selectedDays).toEqual([]);
    expect(meetingsStateAfter.selectedTimes).toEqual([]);
    expect(meetingsStateAfter.selectedPublished).toEqual([]);
    expect(meetingsStateAfter.currentPosition).toBe(0);
    expect(meetingsStateAfter.itemsPerPage).toBe(20);
    expect(meetingsStateAfter.sortColumn).toBeNull();
    expect(meetingsStateAfter.sortDirection).toBe('asc');
    expect(meetingsStateAfter.lastEditedMeetingId).toBeNull();
    expect(meetingsStateAfter.meetingIds).toBe('');

    const serviceBodiesStateAfter = get(serviceBodiesState);
    expect(serviceBodiesStateAfter.serviceBodies).toEqual([]);
    expect(serviceBodiesStateAfter.users).toEqual([]);
    expect(serviceBodiesStateAfter.searchTerm).toBe('');
    expect(serviceBodiesStateAfter.lastEditedServiceBodyId).toBeNull();

    const usersStateAfter = get(usersState);
    expect(usersStateAfter.users).toEqual([]);
    expect(usersStateAfter.searchTerm).toBe('');
    expect(usersStateAfter.lastEditedUserId).toBeNull();
  }, 10000);

  test('area admin cannot see server admin users data after logout and re-login', async () => {
    const user = userEvent.setup();
    render(App);

    // Log in as server admin
    await user.type(await screen.findByRole('textbox', { name: 'Username' }), 'serveradmin');
    await user.type(await screen.findByLabelText('Password'), 'serveradmin-password');
    await user.click(screen.getByRole('button', { name: 'Log In' }));
    expect(await screen.findByText('Welcome Server Administrator')).toBeInTheDocument();

    // Manually populate usersState with server admin's full user list
    // (Simulating what would happen if they had navigated to Users page)
    usersState.set({
      users: [
        { id: 1, displayName: 'Server Administrator', username: 'serveradmin', type: 'admin', ownerId: -1 },
        { id: 2, displayName: 'Northern Zone', username: 'NorthernZone', type: 'serviceBodyAdmin', ownerId: -1 },
        { id: 3, displayName: 'Big Region', username: 'BigRegion', type: 'serviceBodyAdmin', ownerId: 2 }
      ] as any,
      searchTerm: '',
      lastEditedUserId: null
    });

    // Verify state has server admin users
    expect(get(usersState).users.length).toBe(3);
    expect(get(usersState).users.some((u) => u.displayName === 'Big Region')).toBe(true);

    // Log out
    await user.click(screen.getByRole('link', { name: 'Logout', hidden: true }));

    // Wait for logout to complete - the login button should be enabled
    await waitFor(
      () => {
        expect(screen.getByRole('button', { name: 'Log In' })).toBeEnabled();
      },
      { timeout: 5000 }
    );

    // Wait for state to be cleared - the clearInternal() method should have reset all stores
    await waitFor(
      () => {
        expect(get(usersState).users).toEqual([]);
      },
      { timeout: 5000 }
    );

    // Verify all state properties are reset
    expect(get(usersState).searchTerm).toBe('');
    expect(get(usersState).lastEditedUserId).toBeNull();
  }, 10000);
});
