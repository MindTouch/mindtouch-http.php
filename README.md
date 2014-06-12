MindTouch API PHP Client
========================

The MindTouch API PHP Client is a PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

[![Package version](http://img.shields.io/packagist/v/mindtouch/mindtouch-api-php-client.svg)](https://packagist.org/packages/mindtouch/mindtouch-api-php-client)

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
