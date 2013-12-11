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

/**
 * Class ApiPlug - builder for MindTouch API requests
 * @package MindTouch\ApiClient
 *
 * @TODO (andyv): remove fqdn return types once phpstorm fixes phpdoc type hinting
 *
 * @method \MindTouch\ApiClient\ApiPlug with
 * @method \MindTouch\ApiClient\ApiPlug withHeader
 * @method \MindTouch\ApiClient\ApiPlug withCredentials
 * @method \MindTouch\ApiClient\ApiResult get
 * @method \MindTouch\ApiClient\ApiResult head
 * @method \MindTouch\ApiClient\ApiResult post
 * @method \MindTouch\ApiClient\ApiResult postFile
 * @method \MindTouch\ApiClient\ApiResult postFields
 * @method \MindTouch\ApiClient\ApiResult putFile
 * @method \MindTouch\ApiClient\ApiResult putFields
 * @method \MindTouch\ApiClient\ApiResult delete
 */
class ApiPlug extends HttpPlug {

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

    /**
     * @param string $uri
     * @param string $format
     * @param string $hostname
     * @param array $defaultHeaders
     * @return ApiPlug
     */
    public static function newPlug($uri, $format = self::DREAM_FORMAT_PHP, $hostname = null, $defaultHeaders = null) {

        // remove trailing slash from uri
        if(substr_compare($uri, '/', -1, 1) === 0) {
            $uri = substr($uri, 0, -1);
        }
        $class = __CLASS__;
        $Plug = new $class($uri);
        $Plug->class = $class;

        // include default & white-listed headers
        self::SetDefaultHeaders($Plug->headers, $defaultHeaders !== null ? $defaultHeaders : self::$dreamDefaultHeaders);

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
     * The api requires double urlencoded titles. This method will do it automatically for you.
     * @see #AtRaw() for creating unencoded path components
     *
     * @param string ... $path - path components to add to the request
     * @return ApiPlug
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
     * @return ApiPlug
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
     * @return ApiPlug
     */
    public function withApiKey($apikey) {
        return $this->withHeader(self::HEADER_API_KEY, $apikey);
    }

    /**
     * Add an authtoken to the request
     *
     * @param string $authtoken
     * @return ApiPlug
     */
    public function withAuthtoken($authtoken) {
        return $this->withHeader(self::HEADER_AUTHTOKEN, $authtoken);
    }

    /**
     * Performs a PUT request
     *
     * @param array $input - if array, gets encoded as xml
     * @return array - request response
     */
    public function put($input = null) {
        $Plug = $this->with('dream.in.verb', 'PUT');
        return $Plug->invokeXml(self::VERB_POST, $input);
    }

    /**
     * Format the invoke return
     *
     * @param array $request
     * @param array $response
     * @return ApiResult
     */
    protected function invokeComplete(&$request, &$response) {
        return new ApiResult($this->getFormattedResponse($request, $response));
    }
}
