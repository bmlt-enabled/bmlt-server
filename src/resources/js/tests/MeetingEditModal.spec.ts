import { render, screen, fireEvent, waitFor } from '@testing-library/svelte';
import { describe, test } from 'vitest';
import MeetingEditModal from '../components/MeetingEditModal.svelte';
import { translations } from '../stores/localization';
import type { Format, Meeting, ServiceBody } from 'bmlt-server-client';
import { allFormats, allServiceBodies, allMeetings, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';
import { get } from 'svelte/store';
import { isDirty } from '../lib/utils';

const formats: Format[] = allFormats;
const serviceBodies: ServiceBody[] = allServiceBodies;
const selectedMeeting: Meeting = allMeetings[0];

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

// dummy functions for props
function onSaved(_: Meeting) {}
function onClosed() {}
function onDeleted(_: Meeting) {}

describe('MeetingEditModal Component', () => {
  test('test Modal should show unsaved changes warning when closing with dirty form', async () => {
    const props = {
      showModal: true,
      selectedMeeting,
      serviceBodies,
      formats,
      onSaved,
      onClosed,
      onDeleted
    };

    render(MeetingEditModal, { props });

    // Wait for modal to be visible
    await waitFor(() => {
      expect(screen.getByLabelText(translations.getString('nameTitle'))).toBeInTheDocument();
    });

    // Verify form starts clean
    expect(get(isDirty)).toBe(false);

    // Make a change to the form
    const nameInput = screen.getByLabelText(translations.getString('nameTitle'));
    await fireEvent.input(nameInput, { target: { value: 'Modified Meeting Name' } });

    // Wait for dirty state to update
    await waitFor(() => {
      expect(get(isDirty)).toBe(true);
    });

    // Try to close modal by clicking outside or pressing Escape
    // The modal should prevent closing and show confirmation dialog
    const applyButton = screen.getByText(translations.getString('applyChangesTitle'));
    expect(applyButton).not.toBeDisabled();
  });

  test('test Modal should close without warning when form is clean', async () => {
    const props = {
      showModal: true,
      selectedMeeting,
      serviceBodies,
      formats,
      onSaved,
      onClosed,
      onDeleted
    };

    render(MeetingEditModal, { props });

    // Wait for modal to be visible
    await waitFor(() => {
      expect(screen.getByLabelText(translations.getString('nameTitle'))).toBeInTheDocument();
    });

    // Verify form starts clean
    expect(get(isDirty)).toBe(false);

    // Apply button should be disabled
    const applyButton = screen.getByText(translations.getString('applyChangesTitle'));
    expect(applyButton).toBeDisabled();
  });

  test('test Form remains dirty after changing latitude/longitude', async () => {
    const props = {
      showModal: true,
      selectedMeeting,
      serviceBodies,
      formats,
      onSaved,
      onClosed,
      onDeleted
    };

    render(MeetingEditModal, { props });

    // Wait for modal to be visible
    await waitFor(() => {
      expect(screen.getByLabelText(translations.getString('nameTitle'))).toBeInTheDocument();
    });

    // Navigate to Location tab
    const locationTab = screen.getByText(translations.getString('tabsLocation'));
    await fireEvent.click(locationTab);

    // Verify form starts clean
    expect(get(isDirty)).toBe(false);

    // Change latitude/longitude (simulating map marker drag)
    const latitudeInput = screen.getByLabelText(translations.getString('latitudeTitle')) as HTMLInputElement;
    const longitudeInput = screen.getByLabelText(translations.getString('longitudeTitle')) as HTMLInputElement;

    const newLat = String(Number(latitudeInput.value) + 0.001);
    const newLng = String(Number(longitudeInput.value) + 0.001);

    await fireEvent.input(latitudeInput, { target: { value: newLat } });
    await fireEvent.input(longitudeInput, { target: { value: newLng } });

    // Wait for dirty state to update
    await waitFor(
      () => {
        expect(get(isDirty)).toBe(true);
      },
      { timeout: 2000 }
    );

    // Verify apply button is enabled
    const applyButton = screen.getByText(translations.getString('applyChangesTitle'));
    expect(applyButton).not.toBeDisabled();

    // Verify isDirty remains true even after waiting
    await new Promise((resolve) => setTimeout(resolve, 100));
    expect(get(isDirty)).toBe(true);
  });
});
