Rat
===

Rat is a PHP/MySQL boilerplate web app.

History
-------

When you start building a web app you always start out building the same functionality: signup, login, change password, create a new 'object' (ie. status update or image), delete objects etc. Imagine having that functionality straight out of the box! This is what Rat gives you. Now you can focus on the tricky parts of building a web app: making it useful and getting people using it. 

Getting started
---------------

1. Copy all files to your application directory
2. Create a MySQL database
3. Run rat.sql
4. Update the database variables in config/config.php
5. Visit admin.php to create your account

Functionality
-------------

Users

- Beta signup (collect email addresses before launch)
- Full signup
- Login
- Logout
- Sessions
- Change password

Objects

- Create object
- Delete object
- Titles (optional, re-namable)
- Comments (optional, re-namable)
- Likes (optional, re-namable)

Invites

- Allow users to invite their friends by email
- Limit the number of invites
- Grant new invites to users

Admin

- Grant access to users who have signed up for the beta
- Grant invites to users

Default plugins

- Google Analytics
- Gravatar
- Logging
- Points system (with leaderboard)

Themes

Rat uses a templating system to keep layout files separate from the rest of code. It comes with two themes be default.

- Default: a basic theme
- ScribeSub: the theme used on [ScribeSub.com](http://scribesub.com)

Missing functionality
---------------------

- Forgotten password
- Confirm email address
- Edit objects
- Delete user accounts
- Native avatars
- Connect to Facebook/Twitter
- Option to auto-post new objects to Facebook/Twitter
- Enhance friends functionality
- Find friends on Facebook/Twitter