# Change Log Summary
This is the record of the script's changes for easy access and review.

### December 2020
- Used `$GLOBALS` for SQL variables from outside the functions to avoid SQL credentials repetition under the Settings class.
- Added the levels and categories sections to the admin pages (main, add, edit and delete).
- Fixed the quit form & removed account reactivation
  - The form now directly sends to the TCG owner's on-site mailbox instead of the TCG's email address.
  - The inactive user will be automatically set to active once they logged in.
- Fixed general contact & doubles exchange
  - The forms now directly sends to the TCG owner's on-site mailbox instead of the TCG's email address.
- Games PHP file cleanup
  - Removed table rows for games set up per set
  - Added instructions how to add games both on case line and table rows
- Changed the SQL column owner to name from the add affiliates page.
- Changed the function Value to Password
  - This will now check if the password that were typed twice matches.
- Added absolute file path to the list of settings to be used for file uploads function.
