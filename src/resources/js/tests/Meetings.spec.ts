import { beforeAll, beforeEach, describe, test } from 'vitest';
import { screen, waitFor } from '@testing-library/svelte';
import { login, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

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
});
