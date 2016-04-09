# MindTouch HTTP
A PHP library for interacting with the [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API).

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

## Usage
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

## Knowledge as a Service (KaaS) Examples
While the MindTouch API can be used to access or automate all features of the MindTouch platform, there are a handful of particularly useful API requests available for accessing knowledge from a MindTouch site. MindTouch refers to these API's as KaaS, or [Knowledge as a Service](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS).

### Search
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#Search)
```php
<?php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$SearchPlug = $Plug->at('site', 'query');

// request resources that match "sso"
$Result = $SearchPlug->with('q', 'sso')->with('limit', 1)->get();

// extract matching pages
$pages = $Result->getAll('body/page');
```

### Access a Page
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#Access_a_Page)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$PagesPlug = $ApiPlug->at('pages')->with('include', 'contents');

// request page id 7239 with contents
$Result = $PagesPlug->at('7239')->get();

// extract page content body
$content = $Result->getVal('body/page/contents/body');
```

### List all Tags
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#List_all_Tags)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');

// request all site tags
$Result = $ApiPlug = $ApiPlug->at('site', 'tags')->get();

// extract tags
$tags = $Result->getAll('body/tags/tag');
```

### List Pages with a Tag
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#List_Pages_with_a_Tag)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$TagsPlug = $ApiPlug->at('site', 'tags');

// request all pages that are tagged "access"
$Result = $TagsPlug->at('=access')->get();

// extract tags
$tags = $Result->getAll('body/tag/page');
```

### List SubPages
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#List_SubPages)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$PagesPlug = $ApiPlug->at('pages');

// request all immediate subpages of page id 7795
$Result = $PagesPlug->at('7795', 'subpages')->get();

// extract subpages
$pages = $Result->getAll('body/subpages/page.subpage');
```

### List Files
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#List_Files)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$PagesPlug = $ApiPlug->at('pages');

// request a list of all files attached to page path 'lorem/ipsum/dolor'
$Result = $PagesPlug->at('=lorem/ipsum/dolor', 'files')->get();

// extract list of files
$files = $Result->getAll('body/files/file');
```

### List Tags
* [Documentation](http://success.mindtouch.com/Documentation/Integration/API/01_MindTouch_API_Best_Practices/Best_Practices%3A__Knowledge_as_a_Service_KaaS#List_Tags)
```php
$ApiPlug = ApiPlug::newPlug('https://mysite.mindtouch.com/@api/deki');
$PagesPlug = $ApiPlug->at('pages');

// request a list of all tags on page id 619
$Result = $PagesPlug->at('619', 'tags')->get();

// extract list of tags
$tags = array();
foreach($Result->getAll('body/tags/tag') as $tag) {
    $tags[] = $tag['@value'];
}
```

Further Documentation
---------------------
 * [MindTouch REST API](http://success.mindtouch.com/Documentation/Integration/API)
