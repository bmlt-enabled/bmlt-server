import { beforeAll, beforeEach, describe, test, vi } from 'vitest';
import { screen, waitFor } from '@testing-library/svelte';
import { login, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';
import ApiClientWrapper from '../lib/ServerApi';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

describe('check content in Meetings tab when logged in as various users', () => {
  test('check layout when logged in as serveradmin', async () => {
    const user = await login('serveradmin', 'Meetings');
    const serviceBodiesButton = await screen.findByRole('button', { name: 'Service Bodies' });
    expect(serviceBodiesButton).toBeInTheDocument();
    const dayButton = await screen.findByRole('button', { name: 'Day' });
    expect(dayButton).toBeInTheDocument();
    const addMeetingButton = await screen.findByRole('button', { name: 'Add Meeting' });
    expect(addMeetingButton).toBeInTheDocument();
    await user.click(await screen.findByRole('button', { name: 'Search' }));

    await waitFor(
      async () => {
        const cells = await screen.findAllByRole('cell');
        expect(cells.length).toBe(20);
        expect(screen.getByRole('cell', { name: 'Real Talk' })).toBeInTheDocument();
        expect(screen.getByRole('cell', { name: 'Mountain Meeting' })).toBeInTheDocument();
        expect(screen.getByRole('cell', { name: 'River Reflections' })).toBeInTheDocument();
        expect(screen.getByRole('cell', { name: 'Small Beginnings' })).toBeInTheDocument();
        expect(screen.getByRole('cell', { name: 'Big Region Gathering' })).toBeInTheDocument();
      },
      { timeout: 10000 }
    );

    await user.click(await screen.findByRole('cell', { name: 'Big Region Gathering' }));
    const day = screen.getByRole('combobox', { name: 'Weekday' }) as HTMLSelectElement;
    expect(day.value).toBe('5');
  }, 15000);

  test('test Validation errors are displayed with invalid data', async () => {
    const user = await login('serveradmin', 'Meetings');
    const addMeetingButton = await screen.findByRole('button', { name: 'Add Meeting' });
    await user.click(addMeetingButton);
    const nameInput = screen.getByLabelText('Name');
    await user.type(nameInput, ' ');
    const emailInput = screen.getByLabelText('Email');
    await user.type(emailInput, 'Invalid-email');
    // there are now 2 'Add Meeting' buttons
    const addMeeting2 = screen.getAllByText('Add Meeting');
    await user.click(addMeeting2[1]);
    await waitFor(
      () => {
        const nameError = screen.getByText('name is a required field');
        const emailError = screen.getByText('email must be a valid email');
        expect(nameError).toBeInTheDocument();
        expect(emailError).toBeInTheDocument();
      },
      { timeout: 10000 }
    );
  }, 15000);

  test('check empty state when user has no service bodies', async () => {
    await login('serveradmin', 'Meetings');
    vi.spyOn(ApiClientWrapper.api, 'getServiceBodies').mockResolvedValue([]);
    const user = await import('@testing-library/user-event').then((m) => m.default.setup());
    await user.click(await screen.findByRole('link', { name: 'Home' }));
    await user.click(await screen.findByRole('link', { name: 'Meetings' }));

    await waitFor(
      async () => {
        const emptyMessage = await screen.findByText('You are not assigned to any service bodies. Please contact your administrator.');
        expect(emptyMessage).toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'Search' })).not.toBeInTheDocument();
        expect(screen.queryByRole('button', { name: 'Add Meeting' })).not.toBeInTheDocument();
      },
      { timeout: 10000 }
    );
  }, 15000);

  test('check service body field is disabled text input when user has exactly one service body', async () => {
    const user = await login('serveradmin', 'Meetings');
    const singleServiceBody = { id: 999, name: 'Only Service Body', adminUserId: 1, type: 'AS', parentId: null, assignedUserIds: [], email: '', description: '', url: '', helpline: '', worldId: '' };
    vi.spyOn(ApiClientWrapper.api, 'getServiceBodies').mockResolvedValue([singleServiceBody]);

    await user.click(await screen.findByRole('link', { name: 'Home' }));
    await user.click(await screen.findByRole('link', { name: 'Meetings' }));

    const addMeetingButton = await screen.findByRole('button', { name: 'Add Meeting' });
    await user.click(addMeetingButton);

    await waitFor(
      async () => {
        const serviceBodyLabel = screen.getByText('Service Body');
        expect(serviceBodyLabel).toBeInTheDocument();

        // Look for disabled text input showing the service body name
        const textInputs = document.querySelectorAll('input[type="text"]');
        const serviceBodyTextInput = Array.from(textInputs).find((input) => (input as HTMLInputElement).value === 'Only Service Body') as HTMLInputElement;
        expect(serviceBodyTextInput).toBeInTheDocument();
        expect(serviceBodyTextInput.disabled).toBe(true);

        // Verify hidden input with the service body ID bound to form data
        const hiddenInput = document.getElementById('serviceBodyId') as HTMLInputElement;
        expect(hiddenInput).toBeInTheDocument();
        expect(hiddenInput.type).toBe('hidden');
        expect(hiddenInput.value).toBe('999');
      },
      { timeout: 10000 }
    );
  }, 15000);
});
