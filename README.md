MindTouch API PHP Client
========================
The MindTouch API PHP Client is a PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

[![travis-ci.org](https://travis-ci.org/MindTouch/MindTouch-API-PHP-Client.svg?branch=master)](https://travis-ci.org/MindTouch/MindTouch-API-PHP-Client)
[![codecov.io](https://codecov.io/github/MindTouch/MindTouch-API-PHP-Client/coverage.svg?branch=master)](https://codecov.io/github/MindTouch/MindTouch-API-PHP-Client?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mindtouch/mindtouch-api-php-client/version.svg)](https://packagist.org/packages/mindtouch/mindtouch-api-php-client)

## Support
This library is provided for and supported by the open source community. Supported MindTouch site owners may file bug reports via [GitHub](https://github.com/MindTouch/MindTouch-API-PHP-Client/issues), but support plans do not cover the usage of this library.

## Requirements
* PHP 5.3+

## Installation
Use [Composer](https://getcomposer.org/). There are two ways to add this library to your project.

From the composer CLI:
```sh
$ ./composer.phar require mindtouch/mindtouch-api-php-client
```

Or add mindtouch/mindtouch-api-php-client to your project's composer.json:
```json
{
    "require": {
        "mindtouch/mindtouch-api-php-client": "dev-master"
    }
}
```
"dev-master" is the master development branch. If you are using tis library in a production environment, it is advised that you use a stable release.

Assuming you have setup Composer's autoloader, the library can be found in the MindTouch\ApiClient\ namespace.

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
Read the [MindTouch API PHP Client documentation](https://github.com/mindtouch/mindtouch-api-php-client/wiki/Home) for more information.
