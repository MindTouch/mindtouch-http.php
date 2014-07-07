MindTouch API PHP Client
========================
[![Latest Stable Version](https://poser.pugx.org/mindtouch/mindtouch-api-php-client/version.svg)](https://packagist.org/packages/mindtouch/mindtouch-api-php-client) [![Latest Unstable Version](https://poser.pugx.org/mindtouch/mindtouch-api-php-client/v/unstable.svg)](https://packagist.org/packages/mindtouch/mindtouch-api-php-client) [![License](https://poser.pugx.org/mindtouch/mindtouch-api-php-client/license.svg)](https://packagist.org/packages/mindtouch/mindtouch-api-php-client)

The MindTouch API PHP Client is a PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

Support
-------
This Library is provided for and supported by the open source community. Supported MindTouch site owners may file bug reports via [GitHub](https://github.com/MindTouch/MindTouch-API-PHP-Client/issues), but support plans do not cover the usage of this library.

Installation
------------
Use [Composer](https://getcomposer.org/). Add mindtouch/mindtouch-api-php-client to your project's composer.json:

```json
{
    "require": {
        "mindtouch/mindTouch-api-php-client": "dev-master"
    }
}
```

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
