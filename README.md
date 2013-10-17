Vero Framework "Hello World" App
========

@TODO

Requirements
------------

 * PHP 5.4+

Installation
------------

Get application code, e.g.:

```bash
git clone git@github.com:minchal/vero-hello-app.git
```

Install [Composer](https://getcomposer.org) and dependencies:

```bash
cd vero-hello-app/system
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

Create configuration file:

```bash
cp config.php.example config.php
```

Make Vero visible for Apache, ex.:

```bash
cd ..
sudo ln -s . /var/www/vero/
```

and go to [http://localhost/vero/](http://localhost/vero/)

If you need Vero in other base path than http://localhost/vero/ edit variable 'routing.base' in your config.php.

Authors
-------

Michał Pawłowski - <michal@pawlowski.be> - <http://pawlowski.be>

License
-------

The MIT License (MIT) - see the LICENSE file for details
