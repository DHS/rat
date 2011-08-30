Rat
===

Rat is a boilerplate web app written for PHP and MySQL.

History
-------

When you start building a web app you usually start with the same functionality: signup, login, change password, create 'items' (whatever they may be), delete items, add friends etc. Rat gives you that functionality straight out of the box. This frees you up to focus on the differentiating parts of your app: the design, making it useful and getting people using it. Why the name Rat? Because it's small, quick and dirty, and ultimately you should probably exterminate it (and replace it with your own code).

Getting started
---------------

1. Copy all files to your application directory
2. Create a MySQL database and run rat.sql in it
3. Update the variables in config.php, in particular dev_url and the database variables
4. Visit /admin/setup to create your account

Functionality
-------------

**Users**

- Beta signup (collect email addresses before launch)
- Full signup
- Login
- Logout
- Sessions
- Forgotten password
- User profiles (name, bio, url)

**Items**

- Create item
- Delete item
- Titles (optional)
- Comments (optional)
- Likes (optional)

**Invites**

- Invite friends by email
- Number of invites can be limited
- Grant new invites to users

**Friends**

- Asymmetrical friending (following)
- Feed of friends' activity

**Admin section**

- Grant access to beta signups
- Grant invites to users

**Default plugins**

- Google Analytics
- Gravatar
- Logging
- Points system (with leaderboard)

**Themes**

- Default: a basic theme

Missing functionality
---------------------

- Symmetrical friending (send request, request must be accepted)
- Confirm email address
- Delete user accounts
- Native avatars
- Search users
- Edit items
- @ mentions
- Pagination/infinite scroll
- Tidier date formats
- Add an additional theme
- Plugin hooks similar to Wordpress
- Connect accounts to Facebook/Twitter
- Signup using Facebook/Twitter
- Option to post new item to Facebook/Twitter
- Option in Settings to auto-post to Facebook/Twitter
- Find friends on Facebook/Twitter
- OAuth API and developer section