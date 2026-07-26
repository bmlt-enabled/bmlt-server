import { render, screen, fireEvent } from '@testing-library/svelte';
import { beforeAll, describe, expect, test } from 'vitest';
import '@testing-library/jest-dom';

import App from '../App.svelte';
import { sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

// Regression test for the admin UI freezing after login (Apply button stuck disabled, Logout
// doing nothing). Submitting with Enter left a field focused while the form was torn down; the
// resulting focusout let felte write to a store mid-teardown, and Svelte threw
// state_unsafe_mutation, which killed reactivity for the rest of the session.
describe('form submit blurs the focused field', () => {
  test('the focused input is blurred when a form is submitted', async () => {
    render(App);

    const password = (await screen.findByLabelText('Password')) as HTMLInputElement;
    password.focus();
    expect(document.activeElement).toBe(password);

    await fireEvent.submit(password.closest('form') as HTMLFormElement);

    expect(document.activeElement).not.toBe(password);
  });
});
