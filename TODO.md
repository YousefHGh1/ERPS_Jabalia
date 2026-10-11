# TODO - Fix employees pagination edit/delete modals

- [x] Update `app/Http/Controllers/EmployeeController@index` to return (for AJAX) a JSON payload containing rendered `table` HTML and rendered `modals` HTML.

- [x] Update `resources/views/employee/index.blade.php`:
    - [x] Add `<div id="modals-container">` wrapper.

    - [x] Modify `fetchEmployees()` to replace both `#table-container` and `#modals-container`.

- [x] Ensure `employee.partials._modals` receives `$employees` explicitly (pass through from controller render).

- [ ] Quick manual test:
    - [ ] Page 1: Edit/Delete works.
    - [ ] Go to Page 2 via pagination: Edit/Delete works.
    - [ ] Confirm no duplicate modals or missing ids.
    - [ ] Pagination feels fast / no browser freeze when repeatedly clicking pages.
