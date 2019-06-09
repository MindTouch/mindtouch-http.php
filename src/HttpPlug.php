<?php declare(strict_types=1);
/**
 * MindTouch HTTP
 * Copyright (C) 2006-2018 MindTouch, Inc.
 * www.mindtouch.com  oss@mindtouch.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace MindTouch\Http;

use Closure;
use InvalidArgumentException;
use MindTouch\Http\Content\FileContent;
use MindTouch\Http\Content\IContent;
use MindTouch\Http\Exception\HttpPlugUriHostRequiredException;
use MindTouch\Http\Exception\HttpResultParserContentExceedsMaxContentLengthException;
use MindTouch\Http\Exception\MalformedPathQueryFragmentException;
use MindTouch\Http\Exception\NotImplementedException;
use MindTouch\Http\Mock\MockPlug;
use MindTouch\Http\Mock\MockRequestMatcher;
use MindTouch\Http\Parser\IHttpResultParser;

/**
 * Class HttpPlug - builder and invocation for simple HTTP requests
 *
 * @package MindTouch\Http
 */
class HttpPlug {
    const DEFAULT_MAX_AUTO_REDIRECTS = 10;
    const METHOD_DELETE = 'DELETE';
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';

    /**
     * @var int
     */
    protected $maxAutoRedirects = self::DEFAULT_MAX_AUTO_REDIRECTS;

    /**
     * @var Closure[]
     */
    protected $preInvokeRequestCallbacks = [];

    /**
     * @var Closure[]
     */
    protected $postInvokeCallbacks = [];

    /**
     * @var IHttpResultParser[]
     */
    protected $parsers = [];

    /**
     * @var string - username for basic auth credentials
     */
    protected $user;

    /**
     * @var string|null - password for basic auth credentials
     */
    protected $password = null;

    /**
     * @var IMutableHeaders - stores the headers for the request
     */
    protected $headers;

    /**
     * @var int $timeout - sets the request timeout length (s)
     */
    protected $timeout = 300;

    /**
     * @var XUri
     */
    protected $uri;

    /**
     * @param XUri $uri - target uri
     * @throws HttpPlugUriHostRequiredException
     */
    public function __construct(XUri $uri) {
        $this->headers = new Headers();
        if(StringUtil::isNullOrEmpty($uri->getHost())) {
            throw new HttpPlugUriHostRequiredException($uri);
        }
        $this->uri = $uri;
    }

    public function __clone() {

        // deep copy internal data objects and arrays
        $this->headers = unserialize(serialize($this->headers));
        $this->uri = unserialize(serialize($this->uri));
    }

    #region Plug request data accessors

    /**
     * Retrieves HTTP headers
     *
     * @return IHeaders
     */
    public function getHeaders() : IHeaders { return $this->headers; }

    /**
     * Retrieves the fully qualified uri
     *
     * @param bool $includeCredentials - if true, any set username and password will be included
     * @return XUri
     */
    public function getUri(bool $includeCredentials = false) : XUri {
        $uri = clone $this->uri;

        // @note user & password are passed via Authorization headers when invoked, see #invokeApplyCredentials
        if($includeCredentials) {
            $uri = $uri->withUserInfo($this->user, $this->password);
        }
        return $uri;
    }

    /**
     * Retrieves the number of seconds before invocation will fail due to timeout
     *
     * @return int
     */
    public function getTimeout() : int { return $this->timeout; }

    /**
     * Retrieves the maximum number of redirects to follow before giving up
     *
     * @return int
     */
    public function getMaxAutoRedirects() : int { return $this->maxAutoRedirects; }

    /**
     * Will this plug automatically follow redirects (301, 302, 307)?
     *
     * @return bool
     */
    public function isAutoRedirectEnabled() : bool { return $this->maxAutoRedirects > 0; }

    #endregion

    #region Plug request builders

    /**
     * Return an instance with the specified result parser
     *
     * @param IHttpResultParser $parser
     * @return static
     */
    public function withHttpResultParser(IHttpResultParser $parser) : object {
        $plug = clone $this;
        $plug->setHttpResultParser($parser);
        return $plug;
    }

    /**
     * Return an instance with the added header value
     *
     * @param string $name - case-insensitive header field name to add
     * @param mixed $value - header value
     * @return static
     */
    public function withAddedHeader(string $name, $value) : object {
        $plug = clone $this;
        $plug->headers->addHeader($name, $value);
        return $plug;
    }

    /**
     * Return an instance with the set or replaced header value
     *
     * @param string $name - case-insensitive header field name
     * @param mixed $value - header value
     * @return static
     */
    public function withHeader(string $name, $value) : object {
        $plug = clone $this;
        $plug->headers->setHeader($name, $value);
        return $plug;
    }

    /**
     * Return an instance without the specified header
     *
     * @param string $name - case-insensitive header field name to remove
     * @return static
     */
    public function withoutHeader(string $name) : object {
        $plug = clone $this;
        $plug->headers->removeHeader($name);
        return $plug;
    }

    /**
     * Return an instance with the provided URI
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     * @param XUri $uri - new request URI to use
     * @param bool $preserveHost - preserve the original state of the Host header
     * @return static
     */
    public function withUri(XUri $uri, bool $preserveHost = false) : object {
        $plug = clone $this;
        $host = StringUtil::stringify($plug->uri->getHost());
        $plug->uri = $uri;
        if($preserveHost) {
            $plug->uri = $plug->uri->withHost($host);
        }
        return $plug;
    }

    /**
     * Return an instance with appended path segments
     *
     * @param mixed ...$segments - path segments to add to the request (ex: $this->at('foo', 'bar', 'baz'))
     * @return static
     * @throws MalformedPathQueryFragmentException
     */
    public function at(...$segments) : object {
        $plug = clone $this;
        $path = '';
        foreach($segments as $segment) {
            $path .= '/' . ltrim(StringUtil::stringify($segment), '/');
        }
        $plug->uri = $plug->uri->atPath($path);
        return $plug;
    }

    /**
     * Return an instance with query string GET variables appaneded
     *
     * @param string $name - variable name
     * @param mixed $value - variable value
     * @return static
     */
    public function with(string $name, $value) : object {
        $plug = clone $this;
        $plug->uri = $value !== null
            ? $plug->uri->withQueryParam($name, $value)
            : $plug->uri->withoutQueryParam($name);
        return $plug;
    }

    /**
     * Return an instance with standard HTTP auth credentials for the request
     *
     * @param string $user - user name to use for authorization
     * @param string|null $password - optional password
     * @return static
     */
    public function withCredentials(string $user, ?string $password) : object {
        $plug = clone $this;
        $plug->user = $user;
        $plug->password = $password;
        return $plug;
    }

    /**
     * Return an instance with the specified request timeout (ms)
     *
     * @param int $timeout
     * @return static
     */
    public function withTimeout(int $timeout) : object {
        $plug = clone $this;
        $plug->timeout = $timeout;
        return $plug;
    }

    /**
     * Return an instance that calls the supplied callback with the request before invocation
     * Multiple callbacks can be added, and are executed in the order they were added
     *
     * @param Closure $callback - $callback(string $method, XUri $uri, IMutableHeaders $headers, IContent $content) : void
     * @return static
     */
    public function withPreInvokeCallback(Closure $callback) : object {
        $plug = clone $this;
        $plug->preInvokeRequestCallbacks[] = $callback;
        return $plug;
    }

    /**
     * Return an instance that calls the supplied callback with the HttpResult instance after invocation
     * Multiple callbacks can be added, and are executed in the order they were added
     *
     * @param Closure $callback - $callback(HttpResult $result) : void
     * @return static
     */
    public function withPostInvokeCallback(Closure $callback) : object {
        $plug = clone $this;
        $plug->postInvokeCallbacks[] = $callback;
        return $plug;
    }

    /**
     * Return an instance with auto redirect behavior with the specified number of redirects
     *
     * @param int $maxAutoRedirects - maximum number of redirects to follow, 0 if no redirects should be followed
     * @return static
     */
    public function withAutoRedirects(int $maxAutoRedirects = self::DEFAULT_MAX_AUTO_REDIRECTS) : object {
        $plug = clone $this;
        $plug->maxAutoRedirects = $maxAutoRedirects;
        return $plug;
    }

    #endregion

    #region Plug request invocation

    /**
     * Performs a GET request
     *
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    public function get() : object { return $this->invoke(self::METHOD_GET); }

    /**
     * Performs a HEAD request
     *
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    public function head() : object { return $this->invoke(self::METHOD_HEAD); }

    /**
     * Performs a POST request
     *
     * @param IContent|null $content - optionally send a content body with the request
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     * @throws InvalidArgumentException
     */
    public function post(IContent $content = null) : object { return $this->invoke(self::METHOD_POST, $content); }

    /**
     * Performs a PUT request
     *
     * @param IContent $content - optionally send a content body with the request
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     * @throws NotImplementedException
     */
    public function put(IContent $content = null) : object {
        if($content !== null && !($content instanceof FileContent)) {

            // TODO (modethirteen, 20180422): handle PUT content that is not file content
            throw new NotImplementedException();
        }
        return $this->invoke(self::METHOD_PUT, $content);
    }

    /**
     * Performs a DELETE request
     *
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    public function delete() : object { return $this->invoke(self::METHOD_DELETE); }

    #endregion

    #region Common helpers

    /**
     * @param string $method
     * @param IContent $content
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     * @throws InvalidArgumentException
     */
    protected function invoke(string $method, IContent $content = null) : object {
        $requestUri = $this->getUri();
        $requestHeaders = clone $this->headers;
        $this->invokeApplyCredentials($requestHeaders);
        foreach($this->preInvokeRequestCallbacks as $callback) {

            // mutate request settings with callback
            $callback($method, $requestUri, $requestHeaders, $content);
        }
        return $this->invokeRequest($method, $requestUri, $requestHeaders, $content);
    }

    /**
     * @param IMutableHeaders $headers
     * @return void
     */
    protected function invokeApplyCredentials(IMutableHeaders $headers) : void {

        // apply manually given credentials
        if($this->user !== null || $this->password !== null) {
            $headers->addHeader(Headers::HEADER_AUTHORIZATION, 'Basic ' . base64_encode($this->user . ':' . $this->password));
        }
    }

    /**
     * Return the formatted invocation result
     *
     * @param HttpResult $result
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    protected function invokeComplete(HttpResult $result) : object {
        foreach($this->parsers as $parser) {
            $result = $parser->toParsedResult($result);
        }
        foreach($this->postInvokeCallbacks as $callback) {

            // mutate result instance with callback
            $callback($result);
        }
        return $result;
    }

    /**
     * @param string $method
     * @param XUri $requestUri
     * @param IMutableHeaders $requestHeaders
     * @param IContent|null $content
     * @return HttpResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    protected function invokeRequest(string $method, XUri $requestUri, IMutableHeaders $requestHeaders, ?IContent $content) : object {
        $requestStart = 0;
        $requestEnd = 0;

        // handle content data
        $filePath = null;
        $body = null;
        if($content !== null) {
            if($content instanceof FileContent) {
                $filePath = $content->toRaw();
            } else {
                $body = $content->toRaw();

                // explicitly set content length 0 if string content is empty
                if(is_string($body) && StringUtil::isNullOrEmpty($body)) {
                    $requestHeaders->setHeader(Headers::HEADER_CONTENT_LENGTH, '0');
                }
            }

            // set the content type if provided
            $contentType = $content->getContentType();
            if($contentType !== null && !StringUtil::isNullOrEmpty($contentType->toString())) {
                $requestHeaders->setHeader(Headers::HEADER_CONTENT_TYPE, $contentType->toString());
            }
        } else {
             $requestHeaders->setHeader(Headers::HEADER_CONTENT_LENGTH, '0');
        }

        // if MockPlug returns a response, curl is not needed
        if(MockPlug::$isRegistered && $filePath === null) {
            $matcher = (new MockRequestMatcher($method, $requestUri))
                ->withHeaders($requestHeaders)
                ->withBody($body);
            $result = MockPlug::getHttpResult($matcher);
            if($result !== null) {
                return $this->invokeComplete($result->withRequestInfo($method, $requestUri, $requestHeaders, $requestStart, $requestEnd));
            }
        }

        // normal plug request
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $requestUri->toString());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->getTimeout());
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, $this->isAutoRedirectEnabled());
        curl_setopt($curl, CURLOPT_MAXREDIRS, $this->getMaxAutoRedirects());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // add the request headers
        if(!$requestHeaders->isEmpty()) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $requestHeaders->toRawHeaders());
        }

        // retrieve the response headers
        $responseHeaders = new Headers();
        $rawResponseHeaders = [];
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, function(

            /** @noinspection PhpUnusedParameterInspection */
            $curl,
            $header
        ) use (&$responseHeaders, &$rawResponseHeaders) {
            $length = strlen($header);
            $header = trim($header);
            if(!StringUtil::isNullOrEmpty($header)) {
                $rawResponseHeaders[] = $header;
            }
            if(StringUtil::startsWithInvariantCase($header, 'HTTP/1.1')) {

                // status code means new http message section, we only care out the last section
                // for operational concerns so reset headers except for set-cookies
                $setCookieValues = $responseHeaders->getHeader(Headers::HEADER_SET_COOKIE);
                $responseHeaders = new Headers();
                foreach($setCookieValues as $setCookieValue) {
                    $responseHeaders->addHeader(Headers::HEADER_SET_COOKIE, $setCookieValue);
                }
                return $length;
            } else {
                try {
                    $responseHeaders->setRawHeader($header);
                } catch(InvalidArgumentException $e) {

                    // TODO (modethirteen, 20180424): add a handler for invalid http headers
                }
            }
            return $length;
        });

        // custom behavior based on the request type
        switch($method) {
            case self::METHOD_PUT:
                if($filePath !== null) {

                    // read in content from file
                    curl_setopt($curl, CURLOPT_PUT, true);
                    curl_setopt($curl, CURLOPT_INFILE, fopen($filePath, 'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));
                }

                // TODO (modethirteen, 20180422): handle PUT content that is not file content
                break;
            case self::METHOD_POST:
                if($filePath !== null) {

                    // POST a file without using multipart upload (required for some API's)
                    // to POST a file with multipart upload, use FormDataContent::withFileContent()
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_UPLOAD, true);
                    curl_setopt($curl, CURLOPT_INFILE, fopen($filePath, 'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE, filesize($filePath));
                } else {

                    /**
                     * The full data to post in a HTTP "POST" operation.
                     * This can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with the field name as
                     * key and field data as value. If value is an array, the Content-Type header will be set to multipart/form-data.
                     */
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
                }
                break;
            default:
        }

        // execute request
        $requestStart = $this->getTime();
        $httpMessage = curl_exec($curl);
        $requestEnd = $this->getTime();

        // create the result
        $type = curl_getinfo($curl, CURLINFO_CONTENT_TYPE);
        $data = [
            'rawheaders' => $rawResponseHeaders,
            'type' => is_string($type) ? $type : null,
            'errno' => curl_errno($curl),
            'error' => curl_error($curl)
        ];
        $result = (new HttpResult($data))
            ->withStatus(curl_getinfo($curl, CURLINFO_HTTP_CODE))
            ->withHeaders($responseHeaders)
            ->withRequestInfo($method, $requestUri, $requestHeaders, $requestStart, $requestEnd);
        if(!is_bool($httpMessage) && !StringUtil::isNullOrEmpty($httpMessage)) {
            $result = $result->withBody($httpMessage);
        }
        curl_close($curl);
        return $this->invokeComplete($result);
    }

    /**
     * @param IHttpResultParser $parser
     * @return void
     */
    protected function setHttpResultParser(IHttpResultParser $parser) : void {
        $this->parsers[get_class($parser)] = $parser;
    }

    /**
     * @return float
     */
    private function getTime() : float {
        $st = explode(' ', microtime());
        return (float)$st[0] + (float)$st[1];
    }

    #endregion
}
