import { beforeAll, describe, expect, test } from 'vitest';
import { get } from 'svelte/store';

import { apiCredentials, authenticatedUser } from '../stores/apiCredentials';
import RootServerApi from '../lib/ServerApi';
import { sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

const ACCESS_TOKEN_STORAGE_KEY = 'bmltToken';

describe('cross-tab token sync', () => {
  test('adopts a token refreshed by another tab via the storage event', async () => {
    // This tab logs in and holds the original token.
    await apiCredentials.login('serveradmin', 'serveradmin-password');
    expect(get(authenticatedUser)?.username).toBe('serveradmin');
    expect(get(apiCredentials)?.accessToken).toBe('mysteryString42');

    // Another tab refreshes the token and writes the new one to localStorage.
    const now = Math.round(Date.now() / 1000);
    const refreshed = {
      accessToken: 'tokenFromAnotherTab',
      expiresAt: now + 60 * 60 * 24,
      tokenType: 'bearer',
      userId: apiCredentials.userId
    };
    localStorage.setItem(ACCESS_TOKEN_STORAGE_KEY, JSON.stringify(refreshed));
    window.dispatchEvent(new StorageEvent('storage', { key: ACCESS_TOKEN_STORAGE_KEY, newValue: JSON.stringify(refreshed) }));

    // This tab adopts the new token in place, staying logged in rather than racing with a stale one.
    expect(get(apiCredentials)?.accessToken).toBe('tokenFromAnotherTab');
    expect(RootServerApi.token?.accessToken).toBe('tokenFromAnotherTab');
    expect(get(authenticatedUser)?.username).toBe('serveradmin');
  });

  test('logs out when another tab logs out via the storage event', async () => {
    await apiCredentials.login('serveradmin', 'serveradmin-password');
    expect(get(authenticatedUser)?.username).toBe('serveradmin');

    // Another tab logs out, clearing the stored token.
    localStorage.removeItem(ACCESS_TOKEN_STORAGE_KEY);
    window.dispatchEvent(new StorageEvent('storage', { key: ACCESS_TOKEN_STORAGE_KEY, newValue: null }));

    expect(get(apiCredentials)).toBeNull();
    expect(get(authenticatedUser)).toBeNull();
  });
});
