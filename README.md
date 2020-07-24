HTML5 MusicPlayer
=================

I wrote this code a few years ago as a concept, and since then streaming music web/mobile/native clients have really exploded onto the scene.
Despite this, I decided to rewrite the application for a bit of fun anyway, it sure has been!

Dependencies
============

* PHP >= 7.1.3
* Composer (getcomposer.org)
* Bash shell for installer script
* HTML5 compliant browser (of course everybody has one these days)

Installation
============

1. In the project root directory run:

```
composer install
```

2. To setup the directories & permissions the project needs, and setup the all important database:

```
php install.php
```

3. Once the installer has run you'd need to run this on a web server that uses PHP such as Nginx with php-fpm.
You would expose the `public` folder on the webserver to the network with the anything outside of that not exposed.

Or you can use simply use PHP's built-in server. I have included a shell script which can be run by running:

```
./server.sh
```

In the project root. This will spin up a local web server running on port 8000.

4. Enjoy