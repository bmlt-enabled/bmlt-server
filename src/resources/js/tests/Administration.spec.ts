import { beforeAll, beforeEach, describe, test } from 'vitest';
import { screen, waitFor } from '@testing-library/svelte';
import '@testing-library/jest-dom';

import ApiClientWrapper from '../lib/ServerApi';
import { ResponseError } from 'bmlt-server-client';
import { laravelLogMissing, login, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';

// These tests include the mocks that override the behavior of clicking on a link.  We don't want to change this for the other tests,
// so they are here rather than in the shared mocks file.  We still import the shared mocks.

// Perhaps overkill ... but keep track of whether URL.createObjectURL and URL.revokeObjectURL are defined, and if they are,
// restore the old versions after the test is complete.  (They will be defined in a browser but not the testing environment.)
let originalCreateObjectURL: any, originalRevokeObjectURL: any;
let mockClickInfo: { download: string; href: string } | null;
function mockClick(this: HTMLAnchorElement) {
  mockClickInfo = { download: this.download, href: this.href };
}

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
  sharedBeforeEach();
});
afterEach(sharedAfterEach);
afterAll(() => {
  if (originalCreateObjectURL) {
    URL.createObjectURL = originalCreateObjectURL;
  }
  if (originalRevokeObjectURL) {
    URL.revokeObjectURL = originalRevokeObjectURL;
  }
});

// TODO: should maybe clean up mocks including DataTrasfer?

class MockDataTransfer {
  files: File[] = [];
  items: any = {
    add: (file: File) => {
      this.files.push(file);
    },
    clear: () => {
      this.files = [];
    }
  };
  setData(format: string, data: string) {
    // Implement as needed for your tests
  }
  getData(format: string): string {
    // Implement as needed for your tests
    return '';
  }
}

describe('check Administration tab', () => {
  test('check NAWS import', async () => {
    const user = await login('serveradmin', 'Administration');
    const fileInput = await screen.findByLabelText('Update World Committee Codes');
    expect(fileInput.nodeName).toBe('INPUT');
    expect(screen.queryByText('Supported formats: Excel (.xlsx) and CSV (.csv)')).toBeInTheDocument();
    // TODO: unfortunately I couldn't get the remaining steps in checking the NAWS import button to work, so am just leaving it with this minimal test for now
  });

  test('check dowload laravel log', async () => {
    laravelLogMissing.missing = false;
    const user = await login('serveradmin', 'Administration');
    const download = await screen.findByRole('button', { name: 'Download File' });
    await user.click(download);
    expect(mockClickInfo?.download).toBe('laravel.log.gz');
    expect(mockClickInfo?.href).toBe('blob:http://localhost:8000/dummyblob');
  });

  test('check bad laravel log', async () => {
    laravelLogMissing.missing = true;
    const user = await login('serveradmin', 'Administration');
    const download = await screen.findByRole('button', { name: 'Download File' });
    await user.click(download);
    expect(mockClickInfo).toBe(null);
    expect(screen.getByText('No logs found')).toBeInTheDocument();
  });
});
