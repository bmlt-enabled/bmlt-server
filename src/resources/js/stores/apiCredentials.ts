import { get, writable } from 'svelte/store';
import type { Subscriber, Writable, Unsubscriber } from 'svelte/store';

import type { Token, User } from 'bmlt-server-client';

import { spinner } from './spinner';
import RootServerApi from '../lib/ServerApi';
import { formatsState } from './formatsState';
import { meetingsState } from './meetingsState';
import { serviceBodiesState } from './serviceBodiesState';
import { usersState } from './usersState';

export const authenticatedUser: Writable<User | null> = writable(null);

const ACCESS_TOKEN_STORAGE_KEY = 'bmltToken';
const REFRESH_LOCK_NAME = 'bmltTokenRefresh';

export class ApiCredentialsStore {
  private store: Writable<Token | null>;
  private refreshTokenTimeout: NodeJS.Timeout;

  constructor() {
    this.store = writable(null);
    this.refreshTokenTimeout = setTimeout(() => {}, 2147483647);
    const raw = localStorage.getItem(ACCESS_TOKEN_STORAGE_KEY);
    if (raw) {
      this.set(JSON.parse(raw) as Token);
    }
    // Keep tabs in sync: when one tab refreshes the token or logs out, the others
    // adopt the change instead of racing with a stale token (see refreshToken).
    if (typeof window !== 'undefined') {
      window.addEventListener('storage', (event: StorageEvent) => {
        if (event.key !== ACCESS_TOKEN_STORAGE_KEY) {
          return;
        }
        if (event.newValue) {
          const token = JSON.parse(event.newValue) as Token;
          if (get(this.store)) {
            this.adopt(token);
          } else {
            this.set(token);
          }
        } else if (get(this.store)) {
          this.clearInternal();
        }
      });
    }
  }

  // Serialize refreshes across tabs. Sanctum has no real refresh tokens, so refreshing
  // kills the old token after a short grace period; without a lock a second tab would
  // refresh with the now-dead token, get a 401, and sign everyone out.
  private async withRefreshLock<T>(fn: () => Promise<T>): Promise<T> {
    if (typeof navigator !== 'undefined' && navigator.locks?.request) {
      return navigator.locks.request(REFRESH_LOCK_NAME, fn) as Promise<T>;
    }
    return fn();
  }

  // Take on a token another tab produced: swap it in without re-validating or rewriting storage.
  private adopt(token: Token): void {
    clearTimeout(this.refreshTokenTimeout);
    const expiresInSeconds = this.secondsUntilExpiration(token);
    if (expiresInSeconds <= 0) {
      this.logout();
      return;
    }
    RootServerApi.token = token;
    this.store.set(token);
    const timeoutSeconds = expiresInSeconds <= 3600 ? 0 : 300;
    this.refreshTokenTimeout = setTimeout(() => this.refreshToken(token), timeoutSeconds * 1000);
  }

  private secondsUntilExpiration(token: Token): number {
    const currentTime = Math.floor(Date.now() / 1000);
    const expiresIn = token.expiresAt - currentTime;
    return expiresIn;
  }

  private setInternal(token: Token): void {
    this.store.set(token);
    localStorage.setItem(ACCESS_TOKEN_STORAGE_KEY, JSON.stringify(token));
  }

  private clearInternal(): void {
    clearTimeout(this.refreshTokenTimeout);
    formatsState.reset();
    meetingsState.reset();
    serviceBodiesState.reset();
    usersState.reset();
    this.store.set(null);
    authenticatedUser.set(null);
    localStorage.removeItem(ACCESS_TOKEN_STORAGE_KEY);
  }

  async set(token: Token): Promise<void> {
    if (token) {
      const expiresInSeconds = this.secondsUntilExpiration(token);

      if (expiresInSeconds) {
        // Token should still be good, so let's give it to the client
        RootServerApi.token = token;
        this.setInternal(token);

        // We make sure this token is actually still valid by retrieving the currently authenticated user
        if (!get(authenticatedUser)) {
          try {
            spinner.show();
            authenticatedUser.set(await RootServerApi.getUser(token.userId));
          } catch {
            this.logout();
            return;
          } finally {
            spinner.hide();
          }
        }

        // Refresh immediately if token has <= 1 hour, otherwise refresh in 5 minutes
        const timeoutSeconds = expiresInSeconds <= 3600 ? 0 : 300;
        this.refreshTokenTimeout = setTimeout(() => this.refreshToken(token), timeoutSeconds * 1000);
        return;
      }
    }

    this.logout();
  }

  private async refreshToken(token: Token) {
    await this.withRefreshLock(async () => {
      // Another tab may have refreshed (or logged out) while we waited for the lock.
      const raw = localStorage.getItem(ACCESS_TOKEN_STORAGE_KEY);
      const stored = raw ? (JSON.parse(raw) as Token) : null;
      if (!stored) {
        return;
      }
      if (stored.accessToken !== token.accessToken) {
        this.adopt(stored);
        return;
      }

      try {
        await this.set(await RootServerApi.refreshToken());
      } catch (err: any) {
        if (err?.response?.status === 401) {
          // The existing token was not valid... we are logged out.
          this.clearInternal();
          return;
        }

        // If there's at least a minute left on the refreshToken, try again in 5 secs
        if (this.secondsUntilExpiration(token) > 60) {
          setTimeout(() => this.set(token), 5 * 1000);
        } else {
          this.logout();
        }
      }
    });
  }

  async login(username: string, password: string): Promise<Token> {
    this.clearInternal();
    const token = await RootServerApi.login(username, password);
    await this.set(token);
    return token;
  }

  async logout(): Promise<void> {
    const token = get(this.store);
    if (token) {
      try {
        spinner.show();
        await RootServerApi.logout();
      } finally {
        this.clearInternal();
        spinner.hide();
      }
    }
  }

  get isLoggedIn(): boolean {
    const token = get(this.store);
    return !!token && this.secondsUntilExpiration(token) > 20;
  }

  get subscribe(): (run: Subscriber<Token | null>) => Unsubscriber {
    return this.store.subscribe;
  }

  get userId(): number {
    const token = get(this.store);
    if (token) {
      return token.userId;
    }
    return 0;
  }
}

export const apiCredentials = new ApiCredentialsStore();
