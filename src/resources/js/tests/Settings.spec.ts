import { beforeAll, beforeEach, describe, expect, test } from 'vitest';
import { screen, waitFor } from '@testing-library/svelte';
import '@testing-library/jest-dom';

import { login, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

// Helper function to open settings modal
async function openSettingsModal(user: ReturnType<typeof import('@testing-library/user-event').default.setup>) {
  // Wait for settings to load and button to appear (it's behind serverSettingsLoaded check)
  const manageButton = await screen.findByRole('button', { name: /Manage Server Settings/i }, { timeout: 10000 });
  await user.click(manageButton);

  // Wait for modal to appear
  await waitFor(
    () => {
      expect(screen.getByRole('dialog')).toBeInTheDocument();
    },
    { timeout: 10000 }
  );
}

beforeAll(() => {
  sharedBeforeAll();
});

beforeEach(() => {
  sharedBeforeEach();
});

afterEach(() => {
  sharedAfterEach();
});

describe('check content in Settings modal when logged in as various users', () => {
  test('Settings button is not visible when logged in as non-admin', async () => {
    await login('BigRegion', 'Administration');
    await waitFor(
      () => {
        // The "Manage Server Settings" button should not be present for non-admin users
        expect(screen.queryByRole('button', { name: /Manage Server Settings/i })).not.toBeInTheDocument();
      },
      { timeout: 10000 }
    );
  }, 15000);
});

describe('check editing and saving settings', () => {
  test('edit Google Maps API Key and save', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    // Wait for settings to load
    const googleApiKeyInput = (await screen.findByLabelText(/Google Maps API Key/i)) as HTMLInputElement;

    // Check initial value
    expect(googleApiKeyInput.value).toBe('AIzaSyCTRjSYhE685S0QHbRTDqRl1YKD44KXSKw');

    // Clear and enter new value
    await user.clear(googleApiKeyInput);
    await user.type(googleApiKeyInput, 'NEW_API_KEY_12345');
    expect(googleApiKeyInput.value).toBe('NEW_API_KEY_12345');

    // Save settings
    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    // Check for success message (modal should close and toast should appear)
    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });

  test('edit distance units', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const distanceUnitsSelect = (await screen.findByLabelText(/Distance Units/i)) as HTMLSelectElement;
    expect(distanceUnitsSelect.value).toBe('mi');

    await user.selectOptions(distanceUnitsSelect, ['km']);
    expect(distanceUnitsSelect.value).toBe('km');

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });

  test('edit boolean settings', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    // Find auto geocoding checkbox
    const autoGeocodingCheckbox = (await screen.findByLabelText(/Enable Auto Geocoding/i)) as HTMLInputElement;
    const initialValue = autoGeocodingCheckbox.checked;

    // Toggle the checkbox
    await user.click(autoGeocodingCheckbox);
    expect(autoGeocodingCheckbox.checked).toBe(!initialValue);

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });

  test('edit map center coordinates', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const longitudeInput = (await screen.findByLabelText(/Longitude/i)) as HTMLInputElement;
    const latitudeInput = (await screen.findByLabelText(/Latitude/i)) as HTMLInputElement;

    // Check initial values
    expect(longitudeInput.value).toBe('-118.563659');
    expect(latitudeInput.value).toBe('34.235918');

    // Change values by directly setting value and triggering input event (skip clear to avoid null)
    longitudeInput.value = '-122.4194';
    longitudeInput.dispatchEvent(new Event('input', { bubbles: true }));
    expect(longitudeInput.value).toBe('-122.4194');

    latitudeInput.value = '37.7749';
    latitudeInput.dispatchEvent(new Event('input', { bubbles: true }));
    expect(latitudeInput.value).toBe('37.7749');

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });

  test('edit BMLT title and notice', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const titleInput = (await screen.findByLabelText(/Server Title/i)) as HTMLInputElement;
    const noticeInput = (await screen.findByLabelText(/Server Notice/i)) as HTMLTextAreaElement;

    // Check initial values
    expect(titleInput.value).toBe('BMLT Administration');

    // Change title
    await user.clear(titleInput);
    await user.type(titleInput, 'New Server Title');
    expect(titleInput.value).toBe('New Server Title');

    // Change notice
    await user.clear(noticeInput);
    await user.type(noticeInput, 'This is a new notice for users');
    expect(noticeInput.value).toBe('This is a new notice for users');

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });
  });
});

describe('settings validation', () => {
  test('require valid Google Maps API Key format', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const googleApiKeyInput = (await screen.findByLabelText(/Google Maps API Key/i)) as HTMLInputElement;

    // Enter invalid format (too short)
    await user.clear(googleApiKeyInput);
    await user.type(googleApiKeyInput, 'a'.repeat(300)); // Too long

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    // Should show validation error
    await waitFor(() => {
      expect(screen.getByText(/must be at most 255/i)).toBeInTheDocument();
    });
  });

  test('require valid map coordinates', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const longitudeInput = (await screen.findByLabelText(/Longitude/i)) as HTMLInputElement;

    // Enter invalid longitude (out of range) by setting value directly (skip clear to avoid null)
    longitudeInput.value = '200';
    longitudeInput.dispatchEvent(new Event('input', { bubbles: true }));

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/must be less than or equal to 180/i)).toBeInTheDocument();
    });
  });

  test('require positive zoom level', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const zoomInput = (await screen.findByLabelText(/Zoom Level/i)) as HTMLInputElement;

    // Enter invalid zoom (negative) by setting value directly (skip clear to avoid null)
    zoomInput.value = '-1';
    zoomInput.dispatchEvent(new Event('input', { bubbles: true }));

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    await waitFor(() => {
      expect(screen.getByText(/must be greater than or equal to 0/i)).toBeInTheDocument();
    });
  });
});

describe('settings form behavior', () => {
  test('save button is disabled until changes are made', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const saveButton = (await screen.findByRole('button', { name: /Save/i })) as HTMLButtonElement;

    // Should be disabled initially
    expect(saveButton).toBeDisabled();
  });

  test('save button is enabled after making changes', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const saveButton = (await screen.findByRole('button', { name: /Save/i })) as HTMLButtonElement;
    expect(saveButton).toBeDisabled();

    // Make a change
    const titleInput = (await screen.findByLabelText(/Server Title/i)) as HTMLInputElement;
    await user.type(titleInput, ' Modified');

    // Save button should now be enabled
    expect(saveButton).toBeEnabled();
  });

  test('changes are preserved when reopening modal', async () => {
    const user = await login('serveradmin', 'Administration');
    await openSettingsModal(user);

    const titleInput = (await screen.findByLabelText(/Server Title/i)) as HTMLInputElement;

    // Make a change and save
    await user.clear(titleInput);
    await user.type(titleInput, 'Changed Title');
    expect(titleInput.value).toBe('Changed Title');

    const saveButton = screen.getByRole('button', { name: /Save/i });
    await user.click(saveButton);

    // Wait for save success
    await waitFor(() => {
      expect(screen.getByText(/saved successfully/i)).toBeInTheDocument();
    });

    // Reopen modal - changes should be persisted
    await openSettingsModal(user);
    const titleInputAgain = (await screen.findByLabelText(/Server Title/i)) as HTMLInputElement;
    expect(titleInputAgain.value).toBe('Changed Title');
  });
});
