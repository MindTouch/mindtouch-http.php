# MindTouch HTTP

A PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

[![travis-ci.org](https://travis-ci.org/MindTouch/mindtouch-http.php.svg?branch=master)](https://travis-ci.org/MindTouch/mindtouch-http.php)
[![codecov.io](https://codecov.io/github/MindTouch/mindtouch-http.php/coverage.svg?branch=master)](https://codecov.io/github/MindTouch/mindtouch-http.php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mindtouch/mindtouch-http/version.svg)](https://packagist.org/packages/mindtouch/mindtouch-http)

## Support

This library is provided for and supported by the open source community. Supported MindTouch site owners may file bug reports via [GitHub](https://github.com/MindTouch/mindtouch-http.php/issues), but support plans do not cover the usage of this library.

## Requirements

* PHP 5.5, 5.6 (1.x)
* PHP 7.2+ (master, 2.x)

## Installation

Use [Composer](https://getcomposer.org/). There are two ways to add this library to your project.

From the composer CLI:

```sh
./composer.phar require mindtouch/mindtouch-http
```

Or add mindtouch/mindtouch-http to your project's composer.json:

```json
{
    "require": {
        "mindtouch/mindtouch-http": "dev-master"
    }
}
```

"dev-master" is the master development branch. If you are using this library in a production environment, it is advised that you use a stable release.

Assuming you have setup Composer's autoloader, the library can be found in the MindTouch\Http\ namespace.

## Usage

A quick example:

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$result = $plug->at('pages', 'home', 'contents')->get();
if($result->isSuccess()) {

    // great job!
    echo $result->getVal('body/contents');
}
```

## More Examples

### Access a Page

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$pagesPlug = $plug->at('pages')->with('include', 'contents');

// request page id 7239 with contents
$result = $pagesPlug->at('7239')->get();

// extract page content body
$content = $result->getVal('body/page/contents/body');
```

### List all Tags

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));

// request all site tags
$result = $plug->at('site', 'tags')->get();

// extract tags
$tags = $result->getAll('body/tags/tag');
```

### List Pages with a Tag

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$tagsPlug = $plug->at('site', 'tags');

// request all pages that are tagged "access"
$result = $plug->at('=access')->get();

// extract pages
$pages = $result->getAll('body/tag/page');
```

### List Page SubPages

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$pagesPlug = $plug->at('pages');

// request all immediate subpages of page id 7795
$result = $pagesPlug->at('7795', 'subpages')->get();

// extract subpages
$pages = $result->getAll('body/subpages/page.subpage');
```

### List Page Files

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$pagesPlug = $plug->at('pages');

// request a list of all files attached to page path 'lorem/ipsum/dolor'
$result = $pagesPlug->at('=lorem/ipsum/dolor', 'files')->get();

// extract list of files
$files = $result->getAll('body/files/file');
```

### List Page Tags

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$pagesPlug = $plug->at('pages');

// request a list of all tags on page id 619
$result = $pagesPlug->at('619', 'tags')->get();

// extract list of tags
$tags = [];
foreach($result->getAll('body/tags/tag') as $tag) {
    $tags[] = $tag['@value'];
}
```

## Further Documentation

* [MindTouch RESTful API](https://success.mindtouch.com/Support/Extend/API_Documentation)
