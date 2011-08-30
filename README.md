Rat
===

Rat is a boilerplate web application written for PHP and MySQL.

History
-------

When you start building a web application you usually start with the same functionality: signup, login, change password, create 'items' (whatever they may be), delete items, add friends etc. Rat gives you that functionality straight out of the box. This frees you up to focus on the differentiating parts of your app: the design, making it useful and getting people using it. Why the name Rat? Because it's small, quick and dirty, and ultimately you should probably exterminate it and replace it with your own code.

Getting started
---------------

1. Copy all files to your application directory
2. Create a MySQL database and run rat.sql in it
3. Copy config/server-sample.php to config/server.php and update the variables
4. Change the variables in config/application.php to customise the app
5. Visit /admin/setup to create your account

Functionality
-------------

**Users**

- Collect email addresses before launch
- Signup
- Login/logout
- Forgotten password
- User profiles (name, bio, url)
- Invite friends by email address
- Add users as friends

**Items**

- Create item
- Delete item
- Titles (optional)
- Comments (optional)
- Likes (optional)

**Admin section**

- Grant access to beta signups
- Grant invites to users
- View stream of all user activity

**JSON API**

- Built-in, read-only JSON API

**Plugins installed by default**

- Google Analytics
- Gravatar
- Logging
- Points system and leaderboard

**Themes**

- Default: a basic theme

How it works
------------

Rat is built using MVC (model-view-controller) separation. The models (located in the /models subdirectory) handle database interactions and generate PHP objects. The controllers (located in the /controllers subdirectory) handle the majority of the application logic. The views (located in /themes/default) generate the HTML/CSS for the application.

Rat URLs will look like this: example.com/users/show/1. This URL calls the 'show' function in the users controller and passes to it the number 1 as an argument. The URL schema is thus: example.com/controller/action/id. We call functions in controllers 'actions' and the argument is generally the id of the object in question.

The typical flow through the app for a given request is as follows:

1. The server calls .htaccess
2. .htaccess calls index.php
3. index.php handles exceptions
4. Then initializes lib/application.php which does the following:
5. Initialize config
6. Parse URL
7. Initialize appropriate controller
8. Initialize models
9. Initialize plugins
10. Call the appropriate controller action
11. Controller actions first call the appropriate models
12. Then handle any application logic
13. Then load the appropriate view
14. Views can load re-usable fragments called partials
15. Views are loaded within layout files which handle common headers, footers, menus
16. A page is born

Theming Rat
-----------

To update the look and feel of your application, copy and rename the themes/default directory then update the $theme variable in config/application.php accordingly.

On the shoulders of giants
--------------------------

Rat uses the following components:

- [HTML5 Boilerplate](http://html5boilerplate.com/)
- [Twitter Bootstrap](http://twitter.github.com/bootstrap/)

License
-------

See [LICENSE](https://github.com/DHS/rat-private/blob/master/LICENSE)

Missing functionality
---------------------

- Symmetrical friending (send request, request must be accepted)
- Confirm email address
- Delete user accounts
- Native avatars
- Search users
- Edit items
- @ mentions
- Badges system
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