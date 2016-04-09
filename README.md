mindtouch-http.php
========================
MindTouch HTTP is a PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

[![travis-ci.org](https://travis-ci.org/MindTouch/mindtouch-http.php.svg?branch=master)](https://travis-ci.org/MindTouch/mindtouch-http.php)
[![codecov.io](https://codecov.io/github/MindTouch/mindtouch-http.php/coverage.svg?branch=master)](https://codecov.io/github/MindTouch/mindtouch-http.php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mindtouch/mindtouch-http/version.svg)](https://packagist.org/packages/mindtouch/mindtouch-http)

## Support
This library is provided for and supported by the open source community. Supported MindTouch site owners may file bug reports via [GitHub](https://github.com/MindTouch/mindtouch-http.php/issues), but support plans do not cover the usage of this library.

## Requirements
* PHP 5.4+

## Installation
Use [Composer](https://getcomposer.org/). There are two ways to add this library to your project.

From the composer CLI:
```sh
$ ./composer.phar require mindtouch/mindtouch-http
```

Or add mindtouch/mindtouch-http to your project's composer.json:
```json
{
    "require": {
        "mindtouch/mindtouch-http": "dev-master"
    }
}
```
"dev-master" is the master development branch. If you are using tis library in a production environment, it is advised that you use a stable release.

Assuming you have setup Composer's autoloader, the library can be found in the MindTouch\Http\ namespace.

Usage
-----
A quick example:

```php
<?php
$Plug = ApiPlug::newPlug('http://mysite.mindtouch.us/@api/deki');
$Result = $Plug->at('pages', 'home', 'contents')->get();
if($Result->isSuccess()) {

    // great job!
    echo $Result->getVal('body/contents');
}
```

Advanced Usage
--------------
Read the [MindTouch HTTP documentation](https://github.com/mindtouch/mindtouch-http.php/wiki/Home) for more information.
