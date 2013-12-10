<?php

/*
 * MindTouch API Client
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

class Plug {

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
    const HTTPSERVERERROR = 500;

    const HEADER_AUTHTOKEN = 'X-Authtoken';
    const HEADER_SESSION = 'X-Deki-Session';
    const HEADER_DATA_STATS = 'X-Data-Stats';
    const HEADER_DEKI_DB_CALLS = 'X-Deki-DB-Calls';
    const HEADER_DEKI_SITE = 'X-Deki-Site';
    const HEADER_DREAM_IN_AUTH = 'X-Dream-In-Auth';
    const HEADER_API_KEY = 'X-ApiKey';

    // dream.out.format
    const DREAM_FORMAT_PHP = 'php';
    const DREAM_FORMAT_JSON = 'json';
    const DREAM_FORMAT_XML = 'xml';

    /**
     * Determines which headers should be forwarded with every request
     * @note maps HTTP header to PHP defines
     * @var array
     */
    public static $dreamDefaultHeaders = array(
        'X-Forwarded-For' => 'HTTP_X_FORWARDED_FOR',
        'X-Forwarded-Host' => 'HTTP_HOST',
        'Referer' => 'HTTP_REFERER',
        'User-Agent' => 'HTTP_USER_AGENT'
    );

    /**
     * @var array
     */
    private static $rawUriSegments = array(
        'files,subpages',
        'children,siblings'
    );

    // used for calculating request profiling information
    private $requestTimeStart;
    private $requestTimeEnd;
    private $requestVerb;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @note (guerrics): feel free to directly set this value
     * @var int $timeout - sets the request timeout length
     */
    public $timeout = 300;

    // URI components
    private $scheme;
    private $user;
    private $password;
    private $host;
    private $port;
    private $path;
    private $query;
    private $fragment;

    /**
     * @var array $headers - stores the headers for the request
     */
    private $headers = array();

    /**
     * @param string $uri
     * @param string $format
     * @param string $hostname
     * @param array $defaultHeaders
     * @return Plug
     */
    public static function newPlug($uri, $format = self::DREAM_FORMAT_PHP, $hostname = null, $defaultHeaders = null) {

        // remove trailing slash from uri
        if(substr_compare($uri, '/', -1, 1) === 0) {
            $uri = substr($uri, 0, -1);
        }
        $Plug = new self($uri);

        // include default & white-listed headers
        self::SetDefaultHeaders($Plug->headers, !is_null($defaultHeaders) ? $defaultHeaders : self::$dreamDefaultHeaders);

        // set the default dream query params
        if($Plug->query) {
            $Plug->query .= '&';
        } else {
            $Plug->query = '';
        }
        if(empty($hostname) && isset($_SERVER['HTTP_HOST'])) {
            $hostname = $_SERVER['HTTP_HOST'];
        }
        if($format) {
            $Plug->query .= 'dream.out.format=' . rawurlencode($format);
        }

        // if a hostname was previously set, reuse it, otherwise take the new one
        $Plug->query .= '&dream.in.host=' . rawurlencode(!empty($hostname) ? $hostname : $Plug->hostname);

        // @note hack hack, pass in scheme until dream.in.uri is available
        // parse the scheme from the frontend request
        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
            $scheme = 'https';
        } else {
            $scheme = 'http';
        }
        $Plug->query .= '&dream.in.scheme=' . $scheme;
        if(isset($_SERVER['REMOTE_ADDR'])) {
            $Plug->query .= '&dream.in.origin=' . rawurlencode($_SERVER['REMOTE_ADDR']);
        }
        return $Plug;
    }

    /**
     * Dream specific urlencode method
     * @see Bugfix#7500: Unable to save a new page with a dot (.) at the end of the title on IIS
     *
     * @param string $string - string to urlencode
     * @param bool $doubleEncode - if true, the string will be urlencoded twice
     * @return string
     */
    public static function urlEncode($string, $doubleEncode = false) {

        // encode trailing dots (. => %2E)
        for($i = strlen($string) - 1, $dots = 0; $i >= 0; $dots++, $i--) {
            if(substr($string, $i, 1) != '.') {
                break;
            }
        }
        $string = urlencode(substr($string, 0, $i + 1)) . str_repeat('%2E', $dots);

        // we don't need to apply our custom encodings on the second pass
        if($doubleEncode) {
            $string = urlencode($string);
        }

        return $string;
    }

    /**
     * Method sets default headers and forwarded headers
     *
     * @param array $headers
     * @param array $defaults
     */
    public static function setDefaultHeaders(&$headers, $defaults = array()) {
        foreach($defaults as $header => $key) {
            if(isset($_SERVER[$key])) {
                self::setMultiValueArray($headers, $header, $_SERVER[$key]);
            }
        }

        // append REMOTE_ADDR to X-Forwarded-For if it exists
        if(isset($_SERVER['REMOTE_ADDR'])) {
            self::setMultiValueArray($headers, 'X-Forwarded-For', isset($headers['X-Forwarded-For'])
                ? $headers['X-Forwarded-For'] . ', ' . $_SERVER['REMOTE_ADDR']
                : $_SERVER['REMOTE_ADDR']);
        }
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
     * @param mixed $uri - of type string, array, or Plug object
     */
    protected function __construct($uri) {

        // initialize from uri string
        if(is_string($uri)) {
            $uri = parse_url($uri);
        }

        // initialize from uri array
        if(is_array($uri)) {
            $this->scheme = isset($uri['scheme']) ? $uri['scheme'] : null;
            $this->user = isset($uri['user']) ? $uri['user'] : null;
            $this->password = isset($uri['pass']) ? $uri['pass'] : null;
            $this->host = isset($uri['host']) ? $uri['host'] : null;
            $this->port = isset($uri['port']) ? $uri['port'] : null;
            $this->path = isset($uri['path']) ? $uri['path'] : null;
            $this->query = isset($uri['query']) ? $uri['query'] : null;
            $this->fragment = isset($uri['fragment']) ? $uri['fragment'] : null;
        } else {

            // initialize from Plug object
            if(is_object($uri)) {
                $this->scheme = $uri->scheme;
                $this->user = $uri->user;
                $this->password = $uri->password;
                $this->host = $uri->host;
                $this->port = $uri->port;
                $this->path = $uri->path;
                $this->query = $uri->query;
                $this->fragment = $uri->fragment;
                $this->timeout = $uri->timeout;
                $this->headers = $uri->headers;
            }
        }

        // default host if not provided
        if(empty($this->host)) {
            $this->host = self::DEFAULT_HOST;
        }
    }

    /**
     * The api requires double urlencoded titles. This method will do it automatically for you.
     * @see #AtRaw() for creating unencoded path components
     *
     * @param string ... $path - path components to add to the request
     * @return Plug
     */
    public function at( /* $path[] */) {
        $result = new self($this);
        foreach(func_get_args() as $path) {
            $result->path .= '/';
            if(in_array($path, self::$rawUriSegments)) {
                $result->path .= $path;
                continue;
            }

            // auto-double encode, check for '=' sign
            if(strncmp($path, '=', 1) == 0) {
                $result->path .= '=' . self::urlEncode(substr($path, 1), true);
            } else {
                $result->path .= self::urlEncode($path, true);
            }
        }
        return $result;
    }

    /**
     * Appends a single path parameter to the plug, unencoded.
     * @note Do not use this method unless you have to(you probably don't).
     * A real need occurs when initially creating the plug baseuri and an
     * unencoded "@api" is required.
     *
     * @see #At() for creating urlencoded paths
     * @param string $path
     * @return Plug
     */
    public function atRaw($path) {
        $result = new self($this);
        $result->path .= '/' . $path;
        return $result;
    }

    /**
     * Add an apikey to the request
     *
     * @param string $apikey
     * @return Plug
     */
    public function withApiKey($apikey) {
        return $this->withHeader(self::HEADER_API_KEY, $apikey);
    }

    /**
     * Add an authtoken to the request
     *
     * @param string $authtoken
     * @return Plug
     */
    public function withAuthtoken($authtoken) {
        return $this->withHeader(self::HEADER_AUTHTOKEN, $authtoken);
    }

     /**
     * Returns a list of the headers that have been set
     *
     * @return array
     */
    public function getHeaders() {
        return self::flattenPlugHeaders($this->headers);
    }

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

    /***
     * Appends to the query string GET variables
     *
     * @param string $name - variable name
     * @param string $value - variable value
     * @return Plug
     */
    public function with($name, $value = null) {
        $Plug = new self($this);
        if($Plug->query) {
            $Plug->query .= '&' . urlencode($name) . ($value !== null ? '=' . urlencode($value) : '');
        } else {
            $Plug->query = urlencode($name) . ($value !== null ? '=' . urlencode($value) : '');
        }
        return $Plug;
    }

    /**
     * Sets a header value to pass with the request
     *
     * @param $name - header name
     * @param $value - header value
     * @param bool $append - if true, then the headers are appended
     * @return Plug
     */
    public function withHeader($name, $value, $append = false) {
        $Plug = new self($this);
        self::setMultiValueArray($Plug->headers, $name, $value, $append);
        return $Plug;
    }

    /**
     * Adds standard HTTP auth credentials for the request
     *
     * @param string $user - user name to use for authorization
     * @param string $password
     * @return Plug
     */
    public function withCredentials($user, $password) {
        $Plug = new self($this);
        $Plug->user = $user;
        $Plug->password = $password;
        return $Plug;
    }

    /**
     * Performs a GET request
     *
     * @return array - request response
     */
    public function get() {
        return $this->invoke(self::VERB_GET);
    }

    /**
     * Performs a HEAD request
     *
     * @return array
     */
    public function head() {
        return $this->invoke(self::VERB_HEAD);
    }

    /**
     * Performs a POST request
     *
     * @param mixed $input - if array, gets encoded as xml. otherwise treated at post fields.
     * @return array - request response
     */
    public function post($input = null) {
        if(is_array($input)) {
            return $this->invokeXml(self::VERB_POST, $input);
        } else {
            return $this->invokeFields(self::VERB_POST, $input);
        }
    }

    public function postFile($path, $mimeType = null) {
        return $this->invoke(self::VERB_POST, $path, $mimeType, true);
    }

    public function postFields($formFields) {
        return $this->invokeFields(self::VERB_POST, $formFields);
    }

    /**
     * Performs a PUT request
     *
     * @param array $input - if array, gets encoded as xml
     * @return array - request response
     */
    public function put($input = null) {
        $Plug = $this->With('dream.in.verb', 'PUT');
        return $Plug->invokeXml(self::VERB_POST, $input);
    }

    public function PutFields($formFields) {
        return $this->invokeFields(self::VERB_PUT, $formFields);
    }

    public function putFile($path, $mimeType = null) {
        return $this->invoke(self::VERB_PUT, $path, $mimeType, true);
    }

    /**
     * Performs a DELETE request
     *
     * @param array $input
     * @return array - request response
     */
    public function delete($input = null) {
        return $this->invokeXml(self::VERB_DELETE, $input);
    }

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
     * @param $verb
     * @param $formFields
     * @return array - request response
     */
    protected function invokeFields($verb, $formFields) {
        return $this->invoke($verb, $formFields);
    }

    /**
     * @param string $verb
     * @param string $content
     * @param string $contentType
     * @param bool $contentFromFile - if true, then $content is assumed to be a file path
     * @return array - request response
     */
    protected function invoke($verb, $content = null, $contentType = null, $contentFromFile = false) {

        // create the request info
        $request = array('uri' => $this->GetUri(),

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
        $this->invokeRequest($curl, $verb, $content, $contentType, $contentFromFile, $request);
        $httpMessage = curl_exec($curl);
        $this->invokeResponse($curl, $verb, $content, $contentType, $contentFromFile, $httpMessage);

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
     * @param object $curl
     * @param string $verb
     * @param string $content
     * @param string $contentType
     * @param bool $contentFromFile
     * @param array $request
     */
    protected function invokeRequest(&$curl, &$verb, &$content, &$contentType, &$contentFromFile, &$request) {
        $this->requestTimeStart = $this->getTime();
        $this->requestTimeEnd = null;
        $this->requestVerb = $verb;
    }

    /**
     * @param object $curl
     * @param string $verb
     * @param string $content
     * @param string $contentType
     * @param bool $contentFromFile
     * @param string $httpMessage
     */
    protected function invokeResponse(&$curl, &$verb, &$content, &$contentType, &$contentFromFile, &$httpMessage) {
        $this->requestTimeEnd = $this->getTime();
    }

    /**
     * Format the invoke return
     *
     * @param array $request
     * @param array $response
     * @return Result
     */
    protected function invokeComplete(&$request, &$response) {
        $contentType = isset($response['type']) ? $response['type'] : '';

        // check if we need to deserialize
        if(strpos($contentType, '/php')) {
            $response['body'] = unserialize($response['body']);
        }
        $response['request'] = $request;
        $Result = new Result($response);
        return $Result;
    }

    /**
     * @return float
     */
    private function getTime() {
        $st = explode(' ', microtime());
        return (float)$st[0] + (float)$st[1];
    }
}
