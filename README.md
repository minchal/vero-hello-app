Vero CMS
========

@TODO

Requirements
------------

 * PHP 5.4+

Installation
------------

Get application code, e.g.:

    ``` sh
    $ git clone git@github.com:minchal/vero-hello-app.git
    ```

Install [Composer](https://getcomposer.org) and dependencies:

    ``` sh
    $ cd vero-hello-app/system
    $ curl -sS https://getcomposer.org/installer | php
    $ php composer.phar install
    ```

Create configuration file:

    ``` sh
    $ cp config.php.example config.php
    ```

Make Vero visible for Apache, ex.:

    ``` sh
    $ cd ..
    $ sudo ln -s . /var/www/vero/
    ```

and go to [http://localhost/vero/](http://localhost/vero/)

If you need Vero in other base path than http://localhost/vero/ edit variable 'routing.base' in your config.php.

Authors
-------

Michał Pawłowski - <michal@pawlowski.be> - <http://pawlowski.be>

License
-------

The MIT License (MIT) - see the LICENSE file for details
