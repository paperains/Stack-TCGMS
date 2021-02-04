# Master List for Pre-Production Changes
This will be the complete list of the developers plans and suggestions as they make any progress for the script. Some features may be added in the future after further discussion.

### Front-end
- [x] Provide a default front-end layout.
- [x] Allow an admin to open or close TCG registration.
- [x] Set the levels on the default about page to reflect the database.
- [x] Allow users to choose which level badge set shows on their profile.
- [x] Any on-site contact form such as general contact, quit form and doubles exchange should be received via on-site mailbox instead of the TCG's email address.
- [x] Add a pre-prejoin system for potential members:
  - [x] A deck voting form in able to help the admin determine which decks to release for prejoin.
  - [x] A deck claims and donations form to get more decks to create for prejoin.
- [x] Allow users to show whether they accept random trades and/or let others to put trades through.

### Admin / Back-end
- [x] Allow changing social media links via the admin panel (discord, twitter, etc)
- [ ] Allow navigation bar to keep current category expanded by default - even with child pages of the ones linked. (?)
- [x] Add link back to Dashboard (index.php) - already linked via the `Dashboard` menu, can be added to `CoreAdmin` logo as well.
- [ ] Allow an admin to schedule a blog post.
  - [ ] Add a method of actually scheduling when the post will be released.
- [x] Allow an admin to draft a blog post.
- [x] Add an absolute path for general use via the settings database and admin panel.
- [x] Allow an admin to create/edit/delete a page content that can read PHP codes.

### Site-wide
- [x] Change all iterations of `cake` and `tickets` or `cur1` and `cur2` to a variable in which the admin can adjust in the settings.
  - [ ] Allow admin to decide how many currencies they can use upon installation (?)
- [ ] Allow admin to choose whether to have deck colors as a "thing".

### Features to Consider
- [ ] Member decks feature.
  - [ ] Allow admin to input how many cards are there in one member deck.
  - [ ] Allow admin to input/add/edit/delete the tasks to unlock cards.
  - [ ] Allow members to submit their finished tasks one at a time. A task must be approved first before a member can submit another task.
  - [ ] Allow an admin to review a submitted task before activating the card assigned to it.
- [ ] Built-in forum for larger TCGs.
  - [ ] Allow an admin to make it an option during installation.
- [x] Revert the script back to its original logs setup:
  - [x] Use only a general `user_logs` and `user_trades` database tables for all members.
