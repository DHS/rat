Rat
===

Rat is a boilerplate web application written for PHP and MySQL. It provides you with a starting point for a stream-based social web application. You can see a vanilla installation of Rat in action [here](http://pleasebuildthis.com/).

History
-------

When you start building a web application you usually start with the same functionality: signup, login, change password, create 'items' (whatever they may be), delete items, add friends etc. Rat gives you that functionality straight out of the box. This frees you up to focus on the differentiating parts of your app: the design, making it useful and getting people using it. Why the name Rat? Because it's small, quick and dirty, and ultimately you should probably exterminate it and replace it with your own code.

Getting started
---------------

1. Copy all files to your application directory
2. Create a MySQL database and run rat.sql in it
3. In config/config.json, update the dev database details, dev_url, dev_base_dir and encryption_salt
4. Visit /admin/setup to configure your application

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
- Edit item
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

1. The server (using .htaccess) routes all requests to index.php
2. index.php initializes lib/application.php which does the following:
3. Initialise config
4. Parse URL
5. Initialise appropriate controller
6. Initialise models
7. Initialise plugins
8. Call the appropriate controller action
9. Controller actions first call the appropriate models
10. Then handle any application logic
11. Then load the appropriate view
12. Views can load re-usable fragments called partials
13. Views are loaded within layout files which handle common headers, footers, menus
14. A page is born

Theming Rat
-----------

To update the look and feel of your application, copy and rename the themes/default directory then update the $theme variable in config/config.json accordingly.

Environment variables
---------------------

You can use environment variables to keep your database credentials and encryption salt out of your code. In config/config.json, use ENV:: as a prefix to the config variable name you'd like to load ie. change the dev database host value to ENV::MYSQL_HOST to load the MYSQL_HOST environment variable.

On the shoulders of giants
--------------------------

Rat uses the following components:

- [HTML5 Boilerplate](http://html5boilerplate.com/)
- [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
- [Twig](http://twig.sensiolabs.org)
- [Humane Dates](https://github.com/zachleat/Humane-Dates)
- [TinyMCE](http://www.tinymce.com/)

License
-------

See [LICENSE](https://github.com/DHS/rat-private/blob/master/LICENSE)
