# MindTouch HTTP

A PHP library for interacting with the [MindTouch API](https://success.mindtouch.com/Integrations/API).

[![travis-ci.org](https://travis-ci.com/MindTouch/mindtouch-http.php.svg?branch=master)](https://travis-ci.com/MindTouch/mindtouch-http.php)
[![codecov.io](https://codecov.io/github/MindTouch/mindtouch-http.php/coverage.svg?branch=master)](https://codecov.io/github/MindTouch/mindtouch-http.php?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mindtouch/mindtouch-http/version.svg)](https://packagist.org/packages/mindtouch/mindtouch-http)
[![Latest Unstable Version](https://poser.pugx.org/mindtouch/mindtouch-http/v/unstable)](https://packagist.org/packages/mindtouch/mindtouch-http)

## Support

This library is provided for and supported by the open source community. Supported [MindTouch](https://mindtouch.com) site owners may file bug reports via [GitHub](https://github.com/MindTouch/mindtouch-http.php/issues), but support plans do not cover the usage of this library.

## Requirements

* PHP 5.5, 5.6 (php5, 1.x)
* PHP 7.2+ (master, 2.x, 3.x)

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

## Getting Started

A quick example:

```php
$plug = new ApiPlug(XUri::newFromString('https://mindtouch.example.com/@api/deki'));
$result = $plug->at('pages', 'home', 'contents')->get();
if($result->isSuccess()) {

    // great job!
    echo $result->getVal('body/contents');
}
```

## Common Scenarios

### Access a Page and Contents

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

## Advanced Usage

This library is an extension of [modethirteen/HyperPlug](https://github.com/modethirteen/HyperPlug) and derives most if it's capabilities from it. However, this library's `ApiPlug` class provides specialized behavior for interacting with the MindTouch API.

```php
// the library allows for programmatic URL construction and parsing
$uri = XUri::newFromString('http://mindtouch.example.com/@api')

    // every step in a URL builder returns an immutable XUri object
    ->withScheme('https')
    ->at('scim', 'v2', 'users')
    ->withQueryParam('xyzzy', 'plugh')
    ->withQueryParams(QueryParams::newFromArray([
        'bar' => 'qux',
        'baz' => 'fred'
    ]))
    ->withoutQueryParam('bar');

// QueryParams objects are normally immutable
$params = $uri->getQueryParams();

// we can change the data structure of a QueryParams object if we must
$params = $params->toMutableQueryParams();
$params->set('baz', 'abc');

// QueryParams are also iterable
foreach($params as $param => $value) {
    $uri = $uri->withReplacedQueryParam($param, $value);
}

// what does our URL look like now?
$result = $uri->toString(); // https://mindtouch.example.com/@api/scim/v2/users?xyzzy=plugh&baz=abc

// we can give our XUri object to a Plug or an ApiPlug to create a client
$plug = new \modethirteen\Http\Plug($uri);

// Plug holds the main HTTP client functionality which can technically be used with any HTTP server
// ...however ApiPlug provides a layer of MindTouch API-specific request formatting and response handling
// ...and is highly recommended when connecting to the MindTouch API
$plug = new ApiPlug($uri);

// like every object in this library, attaching new values or behaviors to plugs is by default immutable
// ...and returns a new object reference

// add a Server API Token for administrator authorization
// ... which calculates Server API Token hash at HTTP request invocation
$plug->withApiToken((new ApiToken('rabbits', 'hasen'))->withUsername('admin'));

// we can add some additional URL path segements and query parameters that weren't part of the constructing URL
$plug = $plug->at('another', 'additional', 'endpoint', 'segment')->with('more', 'params');

// how many redirects will we follow?
$plug = $plug->withAutoRedirects(2);

// HTTP requests often need HTTP headers
$plug = $plug->withHeader('X-FcStPauli', 'hells')
    ->withAddedHeader('X-FcStPauli', 'bells')
    ->withHeader('X-HSV', 'you\'ll never walk again');

// ...or not
$plug = $plug->withoutHeader('X-HSV');

// the Headers object, like XUri and QueryParams, is normally immutable
$headers = $plug->getHeaders();
$result = $headers->getHeader('X-FcStPauli'); // ['hells', 'bells']
$result = $headers->getHeaderLine('X-FcStPauli'); // X-HSV: hells, bells

// but if you really want to...
$mutableHeaders = $headers->toMutableHeaders();
$mutableHeaders->set('X-HSV', 'keiner mag den hsv');

// a Headers object is iterable
foreach($mutableHeaders as $header => $values) {
    foreach($values as $value) {

        // HTTP headers can have multiple stored values
        // ...though normally sent via an HTTP client as comma separated on a single HTTP header line
        echo "{$header}: {$value}";
    }
}

// also we can merge the two sets of Headers (the original and the mutated one)
// ...to create a brand new object containing the values of both
$mergedHeaders = $headers->toMergedHeaders($mutableHeaders);

// we've built out a pretty complex HTTP client now
// ...but what if we want a client with a different URL but everything else the same?
$alternateApiPlug = $plug->withUri(XUri::newFromString('https://deki.example.com/@api/deki'));

// we are going to invoke an HTTP request
// ...pre and post invocation callbacks can attach special logic and handlers
// ...intended to be executed whenever or wherever this HTTP client is used
// ...maybe there is some logic we want to always perform at the moment the HTTP request is about to be sent?
$plug = $plug->withPreInvokeCallback(function(XUri $uri, IHeaders $headers) {

    // last chance to change the URL or HTTP headers before the request is made
    // ...URL and HTTP headers for the single request invocation can be mutated
    // ...this will not affect the URL or HTTP headers configured in the plug
    $headers->toMutableHeaders()->addHeader('something', 'contextual');
});

// multiple callbacks can be attached (they are executed in the order they are attached)
$plug = $plug->withPreInvokeCallback(function(XUri $uri, IHeaders $headers) {
});

// maybe we want to attach some special handling that always executes when we receive an HTTP response?
$plug = $plug->withPostInvokeCallback(function(HttpResult $result) {

    // perhaps there is special behavior to always trigger based on the HTTP response status code?
    if($result->is(403)) {
    }
});

// HTTP responses can be parsed from text into traversable data structures by attaching one or more HttpResultParser objects
// ...parsing can be possibly memory intensive, so limits can be put on the allowed size of a response to parse
$plug = $plug->withHttpResultParser((new JsonParser())->withMaxContentLength(640000));

// fetching HTTP data is handled via HTTP GET
$result = $plug->get();

// POST or PUT can optionally send data, in a several different content types as needed
$result = $plug->post(
    (new MultiPartFormDataContent([
        'a' => 'b',
        'c' => 'd'
    ]))
    ->withFileContent(new FileContent('/path/to/file'))
);
$result = $plug->put(new FileContent('/path/to/file'));
$result = $plug->post(new UrlEncodedFormDataContent([
    'e' => 'f',
    'g' => 'h'
]));
$result = $plug->post(JsonContent::newFromArray([
    'a' => [
        'multi-dimensional' => [
            'data',
            'structure'
        ]
    ]
]));
$result = $plug->post(XmlContent::newFromArray([
    'another' => [
        'multi-dimensional' => [
            'data',
            'structure'
        ],
        'formatted' => 'as xml'
    ]
]));
$result = $plug->put(new TextContent('good old text!'));

// during the invocation process, an ApiResultException may be raised
// ...such as a max HTTP response content length exceeded or an HTTP response parser failure
// ...exceptions can bubble up to the HTTP client callsite, or handled in the HTTP client internally
$plug = $plug->withResultErrorHandler(function(ApiResultException $e) : bool {
    if($e instanceof HttpResultParserException) {

        // always suppress this exception
        return false;
    }
    return true;
});
```

You are encouraged to explore the library [classes](src) and [tests](tests) to learn more about the capabilities not listed here.

## Development and Testing

Though the library is sponsored by [MindTouch, Inc.](https://mindtouch.com), contributions are always welcome from the community ([there are defects and enhancements to address](https://github.com/MindTouch/mindtouch-http.php/issues)).

The library is tested through a combination of [PHPUnit](https://github.com/sebastianbergmann/phpunit) and [`MockPlug`](src/Mock) (an interceptor that matches `ApiPlug` invocations and returns mocked responses). Further code quality is checked using [PHPStan](https://github.com/phpstan/phpstan) (PHP Static Analysis Tool).

```sh
# fork and clone the mindtouch-http.php repository
git clone git@github.com:{username}/mindtouch-http.php.git

# install dependencies
composer install

# run static analysis checks
vendor/bin/phpstan analyse --level 7 src

# run tests
vendor/bin/phpunit --configuration phpunit.xml.dist
```

## Learn More

* [MindTouch API Documentation](https://success.mindtouch.com/Integrations/API)
