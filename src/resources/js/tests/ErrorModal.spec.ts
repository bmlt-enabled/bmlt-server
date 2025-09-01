import { beforeAll, beforeEach, afterEach, describe, test, expect, vi } from 'vitest';
import { render, screen, waitFor } from '@testing-library/svelte';
import '@testing-library/jest-dom';

import ErrorModal from '../components/ErrorModal.svelte';
import { errorModal } from '../stores/errorModal';
import type { ErrorDetails } from '../stores/errorModal';
import { spinner } from '../stores/spinner';
import { sharedBeforeAll, sharedBeforeEach, sharedAfterEach } from './sharedDataAndMocks';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

// Mock navigator.clipboard
const mockWriteText = vi.fn().mockResolvedValue(undefined);
Object.assign(navigator, {
  clipboard: {
    writeText: mockWriteText
  }
});

describe('ErrorModal component', () => {
  beforeEach(() => {
    // Clear any existing error modal state before each test
    errorModal.hide();
    mockWriteText.mockClear();
  });

  test('renders nothing when no error is set', () => {
    render(ErrorModal);
    expect(screen.queryByText('Error')).not.toBeInTheDocument();
    expect(screen.queryByText('Test Error')).not.toBeInTheDocument();
  });

  test('renders error modal when error is set', async () => {
    const testError: ErrorDetails = {
      title: 'Test Error',
      message: 'This is a test error message',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByText('Error')).toBeInTheDocument();
      expect(screen.getByText('Test Error')).toBeInTheDocument();
      expect(screen.getByText('This is a test error message')).toBeInTheDocument();
      expect(screen.getByText(/Occurred at/)).toBeInTheDocument();
    });
  });

  test('renders error modal with details section when details provided', async () => {
    const testError: ErrorDetails = {
      title: 'Server Error',
      message: 'Something went wrong',
      details: 'Stack trace:\n  at function1()\n  at function2()',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByText('Error')).toBeInTheDocument();
      expect(screen.getByText('Server Error')).toBeInTheDocument();
      expect(screen.getByText('Something went wrong')).toBeInTheDocument();
      expect(screen.getByText(/Occurred at/)).toBeInTheDocument();
    });
    const toggleButtons = screen.getAllByRole('button');
    const detailsButton = toggleButtons.find((button) => button.textContent?.includes('Show Details'));
    expect(detailsButton).toBeTruthy();
  });

  test('does not show details section when no details are provided', async () => {
    const testError: ErrorDetails = {
      title: 'Simple Error',
      message: 'No additional details',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByRole('dialog')).toBeInTheDocument();
    });

    expect(screen.queryByText('Show Details')).not.toBeInTheDocument();
    expect(screen.queryByText('Hide Details')).not.toBeInTheDocument();
    expect(screen.queryByText('Technical Details')).not.toBeInTheDocument();
  });

  test('shows close button when modal is displayed', async () => {
    const testError: ErrorDetails = {
      title: 'Test Error',
      message: 'This is a test error message',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByText('Error')).toBeInTheDocument();
      expect(screen.getByRole('button', { name: /Close/i })).toBeInTheDocument();
    });
  });

  test('handles copy to clipboard when no details are present', async () => {
    const testError: ErrorDetails = {
      title: 'Simple Error',
      message: 'No details',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByRole('dialog')).toBeInTheDocument();
    });
    expect(screen.queryByRole('button', { name: /Copy to Clipboard/i })).not.toBeInTheDocument();
  });

  test('formats timestamp correctly', async () => {
    const testDate = new Date('2024-03-15T14:30:45Z');
    const testError: ErrorDetails = {
      title: 'Timestamp Test',
      message: 'Testing timestamp formatting',
      timestamp: testDate
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByRole('dialog')).toBeInTheDocument();
    });

    const timestampText = screen.getByText(/Occurred at/);
    expect(timestampText).toBeInTheDocument();
    expect(screen.getByText(/2024/)).toBeInTheDocument();
    expect(screen.getByText(/15/)).toBeInTheDocument();
  });

  test('displays basic translation text', async () => {
    const testError: ErrorDetails = {
      title: 'Translation Test',
      message: 'Testing translations',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    await waitFor(() => {
      expect(screen.getByText('Error')).toBeInTheDocument();
      expect(screen.getByText('Translation Test')).toBeInTheDocument();
      expect(screen.getByText('Testing translations')).toBeInTheDocument();
      expect(screen.getByText(/Occurred at/)).toBeInTheDocument();
      expect(screen.getByText('Close')).toBeInTheDocument();
    });
  });

  test('handles different error data correctly', async () => {
    render(ErrorModal);

    // Show first error
    const error1: ErrorDetails = {
      title: 'First Error',
      message: 'First message',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    errorModal.show(error1);

    await waitFor(() => {
      expect(screen.getByText('First Error')).toBeInTheDocument();
      expect(screen.getByText('First message')).toBeInTheDocument();
    });

    // Show second error (should replace first)
    const error2: ErrorDetails = {
      title: 'Second Error',
      message: 'Second message',
      timestamp: new Date('2024-01-02T12:00:00Z')
    };

    errorModal.show(error2);

    await waitFor(() => {
      expect(screen.getByText('Second Error')).toBeInTheDocument();
      expect(screen.getByText('Second message')).toBeInTheDocument();
      expect(screen.queryByText('First Error')).not.toBeInTheDocument();
    });
  });

  test('automatically hides spinner when error is shown', async () => {
    // Show spinner first
    spinner.show();
    spinner.show(); // Show multiple times to test complete reset

    // Verify spinner is active
    let spinnerCount = 0;
    const unsubscribe = spinner.subscribe((count) => {
      spinnerCount = count;
    });
    expect(spinnerCount).toBe(2);
    unsubscribe();

    const testError: ErrorDetails = {
      title: 'Test Error with Spinner',
      message: 'This should hide the spinner',
      timestamp: new Date('2024-01-01T12:00:00Z')
    };

    render(ErrorModal);
    errorModal.show(testError);

    // Verify spinner is now hidden
    await waitFor(() => {
      let finalSpinnerCount = 0;
      const unsubscribe2 = spinner.subscribe((count) => {
        finalSpinnerCount = count;
      });
      expect(finalSpinnerCount).toBe(0);
      unsubscribe2();

      expect(screen.getByText('Error')).toBeInTheDocument();
      expect(screen.getByText('Test Error with Spinner')).toBeInTheDocument();
    });
  });
});
