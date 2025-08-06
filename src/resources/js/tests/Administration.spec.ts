import { beforeAll, beforeEach, describe, test, type MockInstance } from 'vitest';
import { screen, waitFor } from '@testing-library/svelte';
import '@testing-library/jest-dom';
import { laravelLogMissing, login, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

// These tests include the mocks that override the behavior of clicking on a link and of console.error.  We don't want to change this
// for the other tests, so they are here rather than in the shared mocks file.  We still import the shared mocks.

// Perhaps overkill ... but keep track of whether URL.createObjectURL and URL.revokeObjectURL are defined, and if they are,
// restore the old versions after the test is complete.  (They will be defined in a browser but not the testing environment.)
let originalCreateObjectURL: any, originalRevokeObjectURL: any;
let mockClickInfo: { download: string; href: string } | null;
function mockClick(this: HTMLAnchorElement) {
  mockClickInfo = { download: this.download, href: this.href };
}
let consoleErrorSpy: MockInstance<typeof console.error>;

beforeAll(() => {
  originalCreateObjectURL = URL.createObjectURL;
  originalRevokeObjectURL = URL.revokeObjectURL;
  HTMLAnchorElement.prototype.click = vi.fn().mockImplementation(mockClick);
  URL.createObjectURL = vi.fn().mockImplementation(() => 'blob:http://localhost:8000/dummyblob');
  URL.revokeObjectURL = vi.fn();
  sharedBeforeAll();
});
beforeEach(() => {
  mockClickInfo = null;
  consoleErrorSpy = vi.spyOn(console, 'error').mockImplementation(() => {});
  sharedBeforeEach();
});
afterEach(() => {
  consoleErrorSpy.mockRestore();
  sharedAfterEach();
});
afterAll(() => {
  if (originalCreateObjectURL) {
    URL.createObjectURL = originalCreateObjectURL;
  }
  if (originalRevokeObjectURL) {
    URL.revokeObjectURL = originalRevokeObjectURL;
  }
});

describe('check Administration tab', () => {
  test('check NAWS import', async () => {
    await login('serveradmin', 'Administration');
    const fileInput = await screen.findByLabelText('Update World Committee Codes');
    expect(fileInput.nodeName).toBe('INPUT');
    expect(screen.queryByText('Supported formats: Excel (.xlsx) and CSV (.csv)')).toBeInTheDocument();
    // TODO: unfortunately I couldn't get the remaining steps in checking the NAWS import button to work, so am just leaving it with this minimal test for now
  });

  test('check download laravel log', async () => {
    laravelLogMissing.missing = false;
    const user = await login('serveradmin', 'Administration');
    const download = await screen.findByRole('button', { name: 'Download Laravel Log' });
    await user.click(download);
    expect(mockClickInfo?.download).toBe('laravel.log.gz');
    expect(mockClickInfo?.href).toBe('blob:http://localhost:8000/dummyblob');
    expect(consoleErrorSpy).toHaveBeenCalledTimes(0);
  });

  test('check bad laravel log', async () => {
    laravelLogMissing.missing = true;
    const user = await login('serveradmin', 'Administration');
    const download = await screen.findByRole('button', { name: 'Download Laravel Log' });
    await user.click(download);
    expect(mockClickInfo).toBe(null);
    expect(screen.getByText('No logs found')).toBeInTheDocument();
    expect(consoleErrorSpy).toHaveBeenCalledTimes(2);
    expect(consoleErrorSpy).toHaveBeenCalledWith('Failed to download Laravel log:', 'Response Error');
  });

  test('check download translations spreadsheet', async () => {
    laravelLogMissing.missing = false;
    const user = await login('serveradmin', 'Administration');
    const download = await screen.findByRole('button', { name: 'Download Translations Spreadsheet' });
    await user.click(download);

    // Wait for the async download operation to complete
    await waitFor(
      () => {
        expect(mockClickInfo?.download).toBe('translations.csv');
      },
      { timeout: 5000 }
    );

    expect(mockClickInfo?.href).toBe('blob:http://localhost:8000/dummyblob');
    expect(consoleErrorSpy).toHaveBeenCalledTimes(0);
  });
});
