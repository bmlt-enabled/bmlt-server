import { beforeAll, beforeEach, describe, test } from 'vitest';
import { screen } from '@testing-library/svelte';
import '@testing-library/jest-dom';
import { login, mockDeletedServiceBodyId, mockSavedServiceBodyCreate, mockSavedServiceBodyUpdate, sharedAfterEach, sharedBeforeAll, sharedBeforeEach } from './sharedDataAndMocks';
import userEvent from '@testing-library/user-event';

beforeAll(sharedBeforeAll);
beforeEach(sharedBeforeEach);
afterEach(sharedAfterEach);

describe('check content in Service Body tab when logged in as various users', () => {
  test('check layout when logged in as serveradmin', async () => {
    await login('serveradmin', 'Service Bodies');
    expect(await screen.findByRole('heading', { name: 'Service Bodies', level: 2 })).toBeInTheDocument();
    expect(await screen.findByRole('textbox', { name: 'Search' })).toBeInTheDocument();
    // There should be 6 service bodies, with 2 cells per user (name and a delete icon)
    const cells = screen.getAllByRole('cell');
    expect(cells.length).toBe(12);
    // check for a couple of representative service bodies
    expect(screen.getByRole('cell', { name: 'Big Region' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Rural Area' })).toBeInTheDocument();
  });

  test('check layout when logged in as Northern Zone', async () => {
    await login('NorthernZone', 'Service Bodies');
    // There should be 6 service bodies, with 1 cell per service body (display name but no delete icon)
    const cells = await screen.findAllByRole('cell');
    expect(cells.length).toBe(6);
    expect(screen.getByRole('cell', { name: 'Northern Zone' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Big Region' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Mountain Area' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'River City Area' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Rural Area' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Small Region' })).toBeInTheDocument();
  });

  test('check layout when logged in as Big Region', async () => {
    login('BigRegion', 'Service Bodies');
    // There should be 4 service bodies, with 1 cell per service body (display name but no delete icon)
    const cells = await screen.findAllByRole('cell');
    expect(cells.length).toBe(4);
    expect(screen.getByRole('cell', { name: 'Big Region' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Mountain Area' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'River City Area' })).toBeInTheDocument();
    expect(screen.getByRole('cell', { name: 'Rural Area' })).toBeInTheDocument();
  });
});

describe('check editing, adding, and deleting service bodies using the popup dialog boxes', () => {
  test('logged in as serveradmin; edit Rural Area Service Body', async () => {
    // For each field displayed in the popup, check the default contents, edit the field, and check the result.
    // Then save the edits, and check the contents of the User Update request.
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('cell', { name: 'Rural Area' }));
    // The applyChanges button should be disabled at this point since there haven't been any edits.
    // We'll need to find the Apply Changes button again later, after there have been changes -- for some reason it's
    // a different button after it's enabled.
    const b = screen.getByRole('button', { name: 'Apply Changes' });
    expect(b).toBeDisabled();
    const name = screen.getByRole('textbox', { name: 'Name' }) as HTMLInputElement;
    expect(name.value).toBe('Rural Area');
    await user.clear(name);
    await user.type(name, 'More Rural Area');
    expect(name.value).toBe('More Rural Area');
    const serviceBodyAdmin = screen.getByRole('combobox', { name: 'Admin' }) as HTMLSelectElement;
    expect(serviceBodyAdmin.value).toBe('7'); // id of Rural Area Service Body
    await userEvent.selectOptions(serviceBodyAdmin, ['Mountain Area']);
    expect(serviceBodyAdmin.value).toBe('6'); // id of Mountain Area User
    const serviceBodiesType = screen.getByRole('combobox', { name: 'Service Body Type' }) as HTMLSelectElement;
    expect(serviceBodiesType.value).toBe('AS');
    await userEvent.selectOptions(serviceBodiesType, ['RS']);
    expect(serviceBodiesType.value).toBe('RS');
    const serviceBodyParent = screen.getByRole('combobox', { name: 'Service Body Parent' }) as HTMLSelectElement;
    expect(serviceBodyParent.value).toBe('102'); // id of Big Region
    await userEvent.selectOptions(serviceBodyParent, ['101']);
    expect(serviceBodyParent.value).toBe('101');

    const hiddenSelect = document.querySelector('select[name="assignedUserIds"]') as HTMLSelectElement;
    const initialSelectedOptions = Array.from(hiddenSelect.selectedOptions).map((option) => option.value);
    expect(initialSelectedOptions).toEqual(['11']);

    // Set the values directly on the hidden select (more reliable approach)
    const optionsToSelect = ['10', '6', '11']; // Big Region Admin 2, Mountain Area, Rural Area Admin 2

    // Clear existing selections
    Array.from(hiddenSelect.options).forEach((option) => (option.selected = false));

    // Select the desired options
    optionsToSelect.forEach((value) => {
      const option = Array.from(hiddenSelect.options).find((opt) => opt.value === value);
      if (option) option.selected = true;
    });

    // Trigger a change event
    hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));

    // Verify the selection
    const selectedOptions = Array.from(hiddenSelect.selectedOptions).map((option) => option.value);
    expect(selectedOptions).toEqual(expect.arrayContaining(['10', '6', '11']));

    const email = screen.getByRole('textbox', { name: 'Email' }) as HTMLInputElement;
    expect(email.value).toBe('rural@bmlt.app');
    await user.clear(email);
    await user.type(email, 'morerural@bmlt.app');
    expect(email.value).toBe('morerural@bmlt.app');
    const description = screen.getByRole('textbox', { name: 'Description' }) as HTMLInputElement;
    expect(description.value).toBe('Rural Area Description');
    await user.type(description, ' now more rural');
    expect(description.value).toBe('Rural Area Description now more rural');
    const url = screen.getByRole('textbox', { name: 'Web Site URL' }) as HTMLInputElement;
    expect(url.value).toBe('https://ruralarea.example.com');
    await user.clear(url);
    await user.type(url, 'https://moreruralarea.example.com');
    expect(url.value).toBe('https://moreruralarea.example.com');
    const helpline = screen.getByRole('textbox', { name: 'Helpline' }) as HTMLInputElement;
    expect(helpline.value).toBe('803-555-7247');
    await user.clear(helpline);
    await user.type(helpline, '843-555-7247');
    expect(helpline.value).toBe('843-555-7247');
    const worldid = screen.getByRole('textbox', { name: 'World Committee Code' }) as HTMLInputElement;
    expect(worldid.value).toBe('AS778');
    await user.clear(worldid);
    await user.type(worldid, 'AS788');
    expect(worldid.value).toBe('AS788');
    const applyChanges = screen.getByRole('button', { name: 'Apply Changes' });
    // no need to explicitly test that applyChanges is enabled, since clicking on it wouldn't work if it were disabled
    await user.click(applyChanges);
    // // check all the fields in the mock Service Body Update for their new values
    expect(mockSavedServiceBodyUpdate?.name).toBe('More Rural Area');
    expect(mockSavedServiceBodyUpdate?.adminUserId).toBe(6);
    expect(mockSavedServiceBodyUpdate?.type).toBe('RS');
    expect(mockSavedServiceBodyUpdate?.parentId).toBe(101);
    expect(mockSavedServiceBodyUpdate?.assignedUserIds).toEqual(expect.arrayContaining([10, 6, 11]));
    expect(mockSavedServiceBodyUpdate?.email).toBe('morerural@bmlt.app');
    expect(mockSavedServiceBodyUpdate?.description).toBe('Rural Area Description now more rural');
    expect(mockSavedServiceBodyUpdate?.url).toBe('https://moreruralarea.example.com');
    expect(mockSavedServiceBodyUpdate?.helpline).toBe('843-555-7247');
    expect(mockSavedServiceBodyUpdate?.worldId).toBe('AS788');
    // check that service body create and service body delete weren't touched
    expect(mockSavedServiceBodyCreate).toBe(null);
    expect(mockDeletedServiceBodyId).toBe(null);
  });

  test('logged in as serveradmin; select Add Service Body', async () => {
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('button', { name: 'Add Service Body' }));
    // The ServiceBodyForm should now be displayed.  So now there are *two* 'Add Service Body' buttons. Grab the second one.  It should be
    // initially disabled since no changes have been made to the form.
    const actuallyAddButton = screen.getAllByRole('button', { name: 'Add Service Body' })[1];
    expect(actuallyAddButton).toBeDisabled();
    // check that the User Type menu is there but don't change the default (we already tested changing it in the update user test)
    const serviceBodyType = screen.getByRole('combobox', { name: 'Service Body Type' }) as HTMLSelectElement;
    expect(serviceBodyType.value).toBe('AS'); // default area service
    const serviceBodyParent = screen.getByRole('combobox', { name: 'Service Body Parent' }) as HTMLSelectElement;
    expect(serviceBodyParent.value).toBe('-1'); // no parent
    const serviceBodyAdmin = screen.getByRole('combobox', { name: 'Admin' }) as HTMLSelectElement;
    await userEvent.selectOptions(serviceBodyAdmin, ['Mountain Area']);
    expect(serviceBodyAdmin.value).toBe('6'); // id of Mountain Area User
    const serviceBodiesType = screen.getByRole('combobox', { name: 'Service Body Type' }) as HTMLSelectElement;
    await userEvent.selectOptions(serviceBodiesType, ['RS']);
    expect(serviceBodiesType.value).toBe('RS');
    await userEvent.selectOptions(serviceBodyParent, ['101']);
    expect(serviceBodyParent.value).toBe('101');
    const hiddenSelect = document.querySelector('select[name="assignedUserIds"]') as HTMLSelectElement;
    const initialSelectedOptions = Array.from(hiddenSelect.selectedOptions).map((option) => option.value);
    expect(initialSelectedOptions).toEqual([]);
    const optionsToSelect = ['10', '6']; // Big Region Admin 2 (ID 10), Mountain Area (ID 6)
    optionsToSelect.forEach((value) => {
      const option = Array.from(hiddenSelect.options).find((opt) => opt.value === value);
      if (option) option.selected = true;
    });
    hiddenSelect.dispatchEvent(new Event('change', { bubbles: true }));
    const selectedOptions = Array.from(hiddenSelect.selectedOptions).map((option) => option.value);
    expect(selectedOptions).toEqual(expect.arrayContaining(['10', '6']));
    const name = screen.getByRole('textbox', { name: 'Name' }) as HTMLInputElement;
    await user.type(name, 'More Rural Area');
    expect(name.value).toBe('More Rural Area');
    const email = screen.getByRole('textbox', { name: 'Email' }) as HTMLInputElement;
    await user.type(email, 'morerural@bmlt.app');
    expect(email.value).toBe('morerural@bmlt.app');
    const description = screen.getByRole('textbox', { name: 'Description' }) as HTMLInputElement;
    await user.type(description, 'Rural Area Description');
    expect(description.value).toBe('Rural Area Description');
    const url = screen.getByRole('textbox', { name: 'Web Site URL' }) as HTMLInputElement;
    await user.type(url, 'https://moreruralarea.example.com');
    expect(url.value).toBe('https://moreruralarea.example.com');
    const helpline = screen.getByRole('textbox', { name: 'Helpline' }) as HTMLInputElement;
    await user.type(helpline, '843-555-7247');
    expect(helpline.value).toBe('843-555-7247');
    const worldid = screen.getByRole('textbox', { name: 'World Committee Code' }) as HTMLInputElement;
    await user.type(worldid, 'AS788');
    expect(worldid.value).toBe('AS788');
    expect(actuallyAddButton).toBeEnabled();
    await user.click(actuallyAddButton);
    expect(mockSavedServiceBodyCreate?.adminUserId).toBe(6);
    expect(mockSavedServiceBodyCreate?.type).toBe('RS');
    expect(mockSavedServiceBodyCreate?.parentId).toBe(101);
    expect(mockSavedServiceBodyCreate?.assignedUserIds).toEqual(expect.arrayContaining([10, 6]));
    expect(mockSavedServiceBodyCreate?.name).toBe('More Rural Area');
    expect(mockSavedServiceBodyCreate?.email).toBe('morerural@bmlt.app');
    expect(mockSavedServiceBodyCreate?.description).toBe('Rural Area Description');
    expect(mockSavedServiceBodyCreate?.url).toBe('https://moreruralarea.example.com');
    expect(mockSavedServiceBodyCreate?.helpline).toBe('843-555-7247');
    expect(mockSavedServiceBodyCreate?.worldId).toBe('AS788');
    expect(mockSavedServiceBodyUpdate).toBe(null);
    expect(mockDeletedServiceBodyId).toBe(null);
  });

  test('logged in as serveradmin; select Add Service body, fill in bad data, and check for error messages', async () => {
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('button', { name: 'Add Service Body' }));
    const email = screen.getByLabelText('Email') as HTMLInputElement;
    await user.type(email, 'blah');
    const addButtons = screen.getAllByRole('button', { name: 'Add Service Body' });
    await user.click(addButtons[1]);
    expect(screen.getByText('name is a required field')).toBeInTheDocument();
    expect(screen.getByText('email must be a valid email')).toBeInTheDocument();
  });

  test('logged in as Northern Zone; edit Big Region Service Body', async () => {
    // We already tested the editing form when logged in as serveradmin.  Here just test that the Name
    // field and the Admin selector menu are disabled, and that the Email field is present and enabled.
    const user = await login('NorthernZone', 'Service Bodies');
    await user.click(await screen.findByRole('cell', { name: 'Big Region' }));
    expect(screen.getByRole('textbox', { name: 'Name' })).toBeDisabled();
    expect(screen.getByRole('combobox', { name: 'Admin' })).toBeDisabled();
    expect(screen.getByRole('textbox', { name: 'Email' })).toBeEnabled();
  });

  test('logged in as serveradmin; delete Small Region Service Body', async () => {
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('button', { name: 'Delete Service Body Small Region' }));
    // TODO: see comment in Users.spec.ts test about finding the checkbox
    // await user.click(await screen.findByRole('checkbox', { name: "Yes, I'm sure." }));
    await user.click(await screen.findByRole('checkbox'));
    await user.click(await screen.findByRole('button', { name: 'Delete' }));
    expect(mockDeletedServiceBodyId).toBe(103);
    expect(mockSavedServiceBodyCreate).toBe(null);
    expect(mockSavedServiceBodyUpdate).toBe(null);
  });

  test('logged in as serveradmin; try to delete Big Region Service Body', async () => {
    // this should fail because Big Region has children
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('button', { name: 'Delete Service Body Big Region' }));
    // TODO: see comment in Users.spec.ts test about finding the checkbox
    // await user.click(await screen.findByRole('checkbox', { name: "Yes, I'm sure." }));
    await user.click(await screen.findByRole('checkbox'));
    await user.click(await screen.findByRole('button', { name: 'Delete' }));
    expect(screen.getByText(/Error: The service body could not be deleted/)).toBeInTheDocument();
    expect(mockDeletedServiceBodyId).toBe(null);
    expect(mockSavedServiceBodyCreate).toBe(null);
    expect(mockSavedServiceBodyUpdate).toBe(null);
  });

  test('test Confirm modal appears when attempting to click outside modal with unsaved changes', async () => {
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('cell', { name: 'Rural Area' }));
    const helpline = screen.getByRole('textbox', { name: 'Helpline' }) as HTMLInputElement;
    await user.clear(helpline);
    await user.type(helpline, '555-867-5309');
    const outsideElement = document.body;
    await user.click(outsideElement);
    expect(screen.getByText('You have unsaved changes. Do you really want to close?')).toBeInTheDocument();
  });

  test('test Confirm modal appears when attempting to close with unsaved changes', async () => {
    const user = await login('serveradmin', 'Service Bodies');
    await user.click(await screen.findByRole('cell', { name: 'Rural Area' }));
    const helpline = screen.getByRole('textbox', { name: 'Helpline' }) as HTMLInputElement;
    await user.clear(helpline);
    await user.type(helpline, '555-867-5309');
    // There are two close buttons at this point: one for the modal as a whole, the other for the Meeting List Editors multiselect.
    // Mock clicking either one closes the modal, but the second one is for the modal as a whole, so use that.  (If there were no
    // meeting list editors then there would be only one close button, but Rural Area does have meeting list editors.)
    const buttons = await screen.findAllByRole('button', { name: 'Close' });
    await user.click(buttons[1]);
    expect(screen.getByText('You have unsaved changes. Do you really want to close?')).toBeInTheDocument();
  });
});
