<?php
/*
 * MindTouch API PHP Client
 * Copyright (C) 2006-2013 MindTouch, Inc.
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
namespace MindTouch\ApiClient;
use MindTouch\ApiClient\test\MockPlug;
use MindTouch\ApiClient\test\MockRequest;

/**
 * Class HttpPlug - builder for simple HTTP requests
 * @package MindTouch\ApiClient
 */
class HttpPlug {

    const DEFAULT_HOST = 'localhost';
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_AUTHORIZATION = 'Authorization';
    const HEADER_CONTENT_LENGTH = 'Content-Length';

    const VERB_DELETE = 'DELETE';
    const VERB_GET = 'GET';
    const VERB_HEAD = 'HEAD';
    const VERB_POST = 'POST';
    const VERB_PUT = 'PUT';

    const HTTPSUCCESS = 200;
    const HTTPBADREQUEST = 400;
    const HTTPNOTFOUND = 404;
    const HTTPAUTHFAILED = 401;
    const HTTPFORBIDDEN = 403;
    const HTTPCONFLICT = 409;
    const HTTPSERVERERROR = 500;

    /**
     * @note (guerrics): feel free to directly set this value
     * @var int $timeout - sets the request timeout length
     */
    public $timeout = 300;

    // URI components
    protected $scheme;
    protected $user;
    protected $password;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    /**
     * @var array $headers - stores the headers for the request
     */
    protected $headers = array();

    /**
     * @var
     */
    protected $class;

    /**
     * @param string $uri
     * @return HttpPlug
     */
    public static function newPlug($uri) {
        $class = __CLASS__;
        $Plug = new $class($uri);
        $Plug->class = $class;
        return $Plug;
    }

    /**
     * Helper method enables an array to have to multiple values per key. Creates
     * nested arrays when more than 1 value is assigned per key.
     *
     * @param array &$multi
     * @param string $key
     * @param string $value
     * @param bool $append
     */
    protected static function setMultiValueArray(&$multi, $key, $value, $append = false) {
        if($append && isset($multi[$key])) {
            if(!is_array($multi[$key])) {
                $current = $multi[$key];
                $multi[$key] = array();
                $multi[$key][] = $current;
            }

            $multi[$key][] = $value;
        } else {
            $multi[$key] = $value;
        }
    }

    /**
     * Helper method to flatten a Plug header array
     *
     * @param array $headers
     * @return array - string[] array of headers
     */
    protected static function flattenPlugHeaders(&$headers) {
        $flat = array();
        if(!empty($headers)) {
            foreach($headers as $name => $value) {
                if(is_array($value)) {
                    foreach($value as $multi) {
                        $flat[] = $name . ': ' . $multi;
                    }
                } else {
                    $flat[] = $name . ': ' . $value;
                }
            }
        }
        return $flat;
    }

    /**
     * @param mixed $data - of type string, array, or Plug object
     */
    protected function __construct($data) {
        if(is_string($data)) {

            // initialize from uri string
            $data = parse_url($data);
        }
        if(is_array($data)) {

            // initialize from uri array
            $this->scheme = isset($data['scheme']) ? $data['scheme'] : null;
            $this->user = isset($data['user']) ? $data['user'] : null;
            $this->password = isset($data['pass']) ? $data['pass'] : null;
            $this->host = isset($data['host']) ? $data['host'] : null;
            $this->port = isset($data['port']) ? $data['port'] : null;
            $this->path = isset($data['path']) ? $data['path'] : null;
            $this->query = isset($data['query']) ? $data['query'] : null;
            $this->fragment = isset($data['fragment']) ? $data['fragment'] : null;
        } elseif(is_object($data)) {

            // initialize from HttpPlug object
            $this->scheme = $data->scheme;
            $this->user = $data->user;
            $this->password = $data->password;
            $this->host = $data->host;
            $this->port = $data->port;
            $this->path = $data->path;
            $this->query = $data->query;
            $this->fragment = $data->fragment;
            $this->timeout = $data->timeout;
            $this->headers = $data->headers;
            $this->class = $data->class;
        }

        // default host if not provided
        if(empty($this->host)) {
            $this->host = self::DEFAULT_HOST;
        }
    }

    /**
     * Uri builder
     *
     * @param string ... $path - method takes any number of path components
     * @return HttpPlug
     */
    public function at( /* $path[] */) {
        $Plug = new $this->class($this);
        $args = func_get_args();

        // MT-7254 PHP Plug accepts trailing slashes
        if(!empty($args) && $Plug->path == '/') {
            $Plug->path = '';
        }
        foreach($args as $path) {
            $Plug->path .= '/' . ltrim($path, '/');
        }
        return $Plug;
    }

    /**
     * Appends to the query string GET variables
     *
     * @param string $name - variable name
     * @param string $value - variable value
     * @return HttpPlug
     */
    public function with($name, $value = null) {
        $Plug = new $this->class($this);
        if($Plug->query !== null) {
            $Plug->query .= '&' . urlencode($name) . ($value !== null ? '=' . urlencode($value) : '');
        } else {
            $Plug->query = urlencode($name) . ($value !== null ? '=' . urlencode($value) : '');
        }
        return $Plug;
    }

    /**
     * Returns a list of the headers that have been set
     *
     * @return array
     */
    public function getHeaders() { return self::flattenPlugHeaders($this->headers); }

    /**
     * Retrieves the fully generate uri
     *
     * @param bool $includeCredentials - if true, any set username and password will be included
     * @return string - uri
     */
    public function getUri($includeCredentials = false) {
        $uri = $this->scheme ? $this->scheme . ':' . ((strtolower($this->scheme) == 'mailto') ? '' : '//') : '';

        // @note user & password are passed via Authorization headers, see #invokeApplyCredentials
        if($includeCredentials) {
            $uri .= $this->user ? $this->user . ($this->password ? ':' . $this->password : '') . '@' : '';
        }
        $uri .= $this->host ? $this->host : '';
        $uri .= $this->port ? ':' . $this->port : '';

        // ensure a trailing slash is provided
        if((substr($uri, -1) != '/') && (strncmp($this->path, '/', 1) != 0)) {
            $uri .= '/';
        }
        $uri .= $this->path ? $this->path : '';
        $uri .= $this->query ? '?' . $this->query : '';
        $uri .= $this->fragment ? '#' . $this->fragment : '';
        return $uri;
    }

    /**
     * Sets a header value to pass with the request
     *
     * @param $name - header name
     * @param $value - header value
     * @param bool $append - if true, then the headers are appended
     * @return HttpPlug
     */
    public function withHeader($name, $value, $append = false) {
        $Plug = new $this->class($this);
        self::setMultiValueArray($Plug->headers, $name, $value, $append);
        return $Plug;
    }

    /**
     * Adds standard HTTP auth credentials for the request
     *
     * @param string $user - user name to use for authorization
     * @param string $password
     * @return HttpPlug
     */
    public function withCredentials($user, $password) {
        $Plug = new $this->class($this);
        $Plug->user = $user;
        $Plug->password = $password;
        return $Plug;
    }

    /**
     * Performs a GET request
     *
     * @return array - request response
     */
    public function get() { return $this->invoke(self::VERB_GET); }

    /**
     * Performs a HEAD request
     *
     * @return array
     */
    public function head() { return $this->invoke(self::VERB_HEAD); }

    /**
     * Performs a POST request
     *
     * @param mixed $input - if array, gets encoded as xml. otherwise treated at post fields.
     * @return array - request response
     */
    public function post($input = null) {
        return is_array($input) ? $this->invokeXml(self::VERB_POST, $input) : $this->invokeFields(self::VERB_POST, $input);
    }

    /**
     * Performs a POST request with a file payload
     *
     * @param string $path
     * @param string|null $mimeType
     * @return array
     */
    public function postFile($path, $mimeType = null) { return $this->invoke(self::VERB_POST, $path, $mimeType, true); }

    /**
     * Performs a POST request with a form payload
     *
     * @param array $formFields
     * @return array
     */
    public function postFields($formFields) { return $this->invokeFields(self::VERB_POST, $formFields); }

    /**
     * Performs a PUT request
     *
     * @param array $input - if array, gets encoded as xml
     * @return array - request response
     */
    public function put($input = null) {
        $contentLength = $input === null ? 0 : strlen($input);

        // explicitly set content-length for put requests
        $Plug = $this->WithHeader(self::HEADER_CONTENT_LENGTH, $contentLength);
        return $Plug->invokeXml(self::VERB_PUT, $input);
    }

    /**
     * Performs a PUT request with a form payload
     *
     * @param array $formFields
     * @return array
     */
    public function putFields($formFields) { return $this->invokeFields(self::VERB_PUT, $formFields); }

    /**
     * Performs a PUT request with a file payload
     *
     * @param string $path
     * @param string|null $mimeType
     * @return array
     */
    public function putFile($path, $mimeType = null) { return $this->invoke(self::VERB_PUT, $path, $mimeType, true); }

    /**
     * Performs a DELETE request
     *
     * @param array $input
     * @return array - request response
     */
    public function delete($input = null) { return $this->invokeXml(self::VERB_DELETE, $input); }

    /**
     * @param string $verb
     * @param mixed $xml - XML encoded array or XML string
     * @return array - request response
     */
    protected function invokeXml($verb, $xml) {
        if(is_array($xml)) {
            $XArray = new XArray($xml);
            $xml = $XArray->toXml();
        }

        // @note (guerrics): adding empty check since dream dies on empty xml bodies
        $contentType = !empty($xml) && empty($this->headers[self::HEADER_CONTENT_TYPE]) ? 'application/xml' : null;
        return $this->invoke($verb, $xml, $contentType);
    }

    /**
     * @param string $verb
     * @param array $formFields
     * @return array - request response
     */
    protected function invokeFields($verb, $formFields) { return $this->invoke($verb, $formFields); }

    /**
     * @param string $verb
     * @param string $content
     * @param string $contentType
     * @param bool $contentFromFile - if true, then $content is assumed to be a file path
     * @return array - request response
     */
    protected function invoke($verb, $content = null, $contentType = null, $contentFromFile = false) {

        // create the request info
        $request = array(
            'verb' => $verb,
            'uri' => $this->getUri(),
            'start' => 0,
            'end' => 0,

            // grab unflattened headers
            'headers' => $this->headers
        );

        // explicitly set content length for empty bodies
        if(is_null($content) || $content === false || (is_string($content) && strlen($content) == 0)) {
            self::setMultiValueArray($request['headers'], self::HEADER_CONTENT_LENGTH, 0);
        }

        // set the content type if provided
        if(!is_null($contentType)) {
            self::setMultiValueArray($request['headers'], self::HEADER_CONTENT_TYPE, $contentType);
        }
        $this->invokeApplyCredentials($request['headers']);

        // if MockPlug returns a response, curl is not needed
        if(MockPlug::$registered) {
            $Response = MockPlug::getResponse(
                MockRequest::newMockRequest($verb, $request['uri'], $request['headers'], $content)
            );
            if($Response !== null) {
                $response = array(
                    'verb' => $verb,
                    'body' => $Response->body,
                    'headers' => $Response->headers,
                    'status' => $Response->status,
                    'type' => $Response->headers[self::HEADER_CONTENT_TYPE],
                    'errno' => '',
                    'error' => ''
                );
                $request['headers'] = self::flattenPlugHeaders($request['headers']);
                return $this->invokeComplete($request, $response);
            }
        }

        // normal plug request
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request['uri']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $verb);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        // custom behavior based on the request type
        switch($verb) {
            case self::VERB_PUT:
                if($contentFromFile && is_file($content)) {

                    // read in content from file
                    curl_setopt($curl, CURLOPT_PUT, true);
                    curl_setopt($curl, CURLOPT_INFILE, fopen($content, 'r'));
                    curl_setopt($curl, CURLOPT_INFILESIZE, filesize($content));
                }
                break;

            case self::VERB_POST:

                /**
                 * The full data to post in a HTTP "POST" operation. To post a file, prepend a filename with @ and use the full path.
                 * This can either be passed as a urlencoded string like 'para1=val1&para2=val2&...' or as an array with the field name as
                 * key and field data as value. If value is an array, the Content-Type header will be set to multipart/form-data.
                 */
                if($contentFromFile && is_file($content)) {
                    curl_setopt($curl, CURLOPT_POST, true);
                    $postFields = array('file' => '@' . $content,);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postFields);
                } else {
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
                }
                break;

            default:
        }

        // add the request headers
        if(!empty($request['headers'])) {

            // flatten headers
            $request['headers'] = self::flattenPlugHeaders($request['headers']);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $request['headers']);
        }

        // retrieve the response headers
        curl_setopt($curl, CURLOPT_HEADER, true);

        // execute request
        $request['start'] = $this->getTime();
        $httpMessage = curl_exec($curl);
        $request['end'] = $this->getTime();

        // create the response info
        $response = array(
            'headers' => array(),
            'status' => curl_getinfo($curl, CURLINFO_HTTP_CODE),
            'type' => curl_getinfo($curl, CURLINFO_CONTENT_TYPE),
            'errno' => curl_errno($curl),
            'error' => curl_error($curl)
        );
        curl_close($curl);

        // header parsing
        // make sure ther response is not empty before trying to parse
        // also make sure there isn't a curl error
        if(($response['status'] != 0) && ($response['errno'] == 0)) {

            // split response into header and response body
            do {
                list($headers, $httpMessage) = explode("\r\n\r\n", $httpMessage, 2);
                $headers = explode("\r\n", $headers);

                // First line of headers is the HTTP response code, remove it
                $httpStatus = array_shift($headers);

                // check if there is another header chunk to parse
            } while($httpStatus == 'HTTP/1.1 100 Continue');

            // set the response body
            $response['body'] = &$httpMessage;

            // put the rest of the headers in an array
            foreach($headers as $headerLine) {
                list($header, $value) = explode(': ', $headerLine, 2);

                // allow for multiple header values
                self::setMultiValueArray($response['headers'], $header, trim($value), true);
            }
        }
        return $this->invokeComplete($request, $response);
    }

    /**
     * @param array $headers
     */
    protected function invokeApplyCredentials(&$headers) {

        // apply manually given credentials
        if(isset($this->user) || isset($this->password)) {
            $headers[self::HEADER_AUTHORIZATION] = 'Basic ' . base64_encode($this->user . ':' . $this->password);
        }
    }

    /**
     * Format the invoke return
     *
     * @param array $request
     * @param array $response
     * @return array
     */
    protected function invokeComplete($request, $response) { return $this->getFormattedResponse($request, $response); }

    /**
     * @param array $request
     * @param array $response
     * @return array
     */
    protected function getFormattedResponse($request, $response) {
        $contentType = isset($response['type']) ? $response['type'] : '';

        // check if we need to deserialize
        if(strpos($contentType, '/php')) {
            $response['body'] = unserialize($response['body']);
        }
        $response['request'] = $request;
        return $response;
    }

    /**
     * @return float
     */
    private function getTime() {
        $st = explode(' ', microtime());
        return (float)$st[0] + (float)$st[1];
    }
}
