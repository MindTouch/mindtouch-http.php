<?php
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

use InvalidArgumentException;
use MindTouch\Http\Exception\MalformedUriException;

/**
 * Class XUri
 *
 * @package MindTouch\Http
 */
class XUri {
    const SENSITIVE_DATA_REPLACEMENT = '###';

    /**
     * Return an instance or null
     *
     * @param string $string - full qualified URI
     * @return static|null
     */
    public static function tryParse($string) {
        try {
            return static::newFromString($string);
        } catch(MalformedUriException $e) {
            return null;
        }
    }

    /**
     * Return an instance, or throw if invalid
     *
     * @param $string - fully qualified URI
     * @return static
     * @throws MalformedUriException
     */
    public static function newFromString($string) {
        $data = parse_url($string);
        if(!$data || !isset($data['scheme']) || $data['scheme'] === null) {
            throw new MalformedUriException($string);
        }
        return static::newFromUriData($data);
    }

    /**
     * Is string is a valid URL?
     *
     * @param string $string
     * @return bool
     */
    public static function isValidUrl($string) {
        $filtered = filter_var($string, FILTER_VALIDATE_URL);
        return ($filtered !== false);
    }

    /**
     * Is string a schemeless URL? (ex: //example.com)
     *
     * @param string $string
     * @return bool
     */
    public static function isSchemelessUrl($string) {
        if(substr($string, 0, 2) !== '//') {
            return false;
        }
        return self::isValidUrl('http:' . $string);
    }

    /**
     * Is string an absolute URL?
     *
     * @param string $string
     * @return bool
     */
    public static function isAbsoluteUrl($string) {
        $filtered = filter_var($string, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED);
        return ($filtered !== false);
    }

    /**
     * Return an array with key/value pairs of query params
     *
     * @param string $query
     * @return string[]
     */
    public static function parseQuery($query) {
        $params = [];
        if($query !== null) {
            $pairs = explode('&', $query);
            foreach($pairs as $pair) {
                if(!StringUtil::isNullOrEmpty($pair)) {
                    if(strpos($pair, '=') === false) {
                        $k = $pair;
                        $v = null;
                    } else {
                        list($k, $v) = array_map('urldecode', explode('=', $pair));
                    }
                    $params[$k] = $v === null ? '' : $v;
                }
            }
        }
        return $params;
    }

    /**
     * Return an internal instance
     *
     * @param array $data - URI data (output from parse_url)
     * @return static
     */
    private static function newFromUriData(array $data) {
        $uri = new static();
        if(isset($data['port'])) {
            $data['port'] = intval($data['port']);
        }
        $uri->data = $data;
        return $uri;
    }

    /**
     * scheme - e.g. http
     * host
     * port
     * user
     * pass
     * path
     * query - after the question mark ?
     * fragment - after the hashmark #
     *
     * @var array
     */
    private $data;

    private function __construct() {}

    #region URI data accessors

    /**
     * Retrieve the scheme component of the URI
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     * @return string
     */
    public function getScheme() { return $this->data['scheme']; }

    /**
     * Retrieve the path component of the URI
     *
     * @note A root/homepage path may return '/' or ''. It is the client's responsibility to handle both cases!
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string
     */
    public function getPath() { return isset($this->data['path']) ? $this->data['path'] : ''; }

    /**
     * Retrieve the path segments of the URI
     *
     * @return string[]
     */
    public function getSegments() { return explode('/', trim($this->getPath(), '/')); }

    /**
     * Retrieve the query string of the URI
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string|null
     */
    public function getQuery() { return isset($this->data['query']) ? $this->data['query'] : null; }

    /**
     * Retrieve a query parameter value of the URI
     *
     * @param string $param - name of the parameter
     * @return string - parameter value
     */
    public function getQueryParam($param) {
        $params = self::parseQuery($this->getQuery());
        return isset($params[$param]) ? $params[$param] : null;
    }

    /**
     * Retrieve the query parameters of the URI
     *
     * @return string[] - name/value array of query params
     */
    public function getQueryParams() { return self::parseQuery($this->getQuery()); }

    /**
     * Retrieve the fragment component of the URI
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string|null
     */
    public function getFragment() { return isset($this->data['fragment']) ? $this->data['fragment'] : null; }

    /**
     * Retrieve the host component of the URI
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string
     */
    public function getHost() { return $this->data['host']; }

    /**
     * Retrieve the authority component of the URI, in "[user-info@]host[:port]" format
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string
     */
    public function getAuthority() {
        $result = '';
        $user = $this->getUserInfo();
        if($user !== '') {
            $result .= "{$user}@";
        }
        $result .= $this->getHost();
        $result .= isset($this->data['port']) ? (':' . $this->data['port']) : '';
        return $result;
    }

    /**
     * Retrieve the user information component of the URI, in "username[:password]" format
     *
     * @return string
     */
    public function getUserInfo() {
        $result = '';
        if(isset($this->data['user'])) {
            $result .= $this->data['user'] . ':';
            if(isset($this->data['pass'])) {
                $result .= $this->data['pass'];
            }
        }
        return $result;
    }

    /**
     * Retrieve the port component of the URI

     * @return int|null
     */
    public function getPort() { return isset($this->data['port']) ? intval($this->data['port']) : null; }

    #endregion

    #region URI builders

    /**
     * Return an instance with the specified user information
     *
     * @param string $user - The user name to use for authority
     * @param string|null $password - The password associated with $user
     * @return static
     */
    public function withUserInfo($user, $password = null) {
        $data = $this->data;
        $data['user'] = $user;
        $data['pass'] = $password;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified host
     *
     * @param string $host - The hostname to use with the new instance
     * @return static
     * @throws InvalidArgumentException
     */
    public function withHost($host) {
        if(StringUtil::isNullOrEmpty($host)) {
            throw new InvalidArgumentException('Host value must be non-empty string');
        }
        $data = $this->data;
        $data['host'] = $host;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified port
     *
     * @param int|null $port - The port to use with the new instance; a null value removes the port information (aliases Uri::withoutPort)
     * @return static
     */
    public function withPort($port) {
        if($port === null) {
            return $this->withoutPort();
        }
        $data = $this->data;
        $data['port'] = $port;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance without port information
     *
     * @return static
     */
    public function withoutPort() {
        $data = $this->data;
        $data['port'] = null;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified scheme
     *
     * @param string $scheme
     * @return static
     * @throws InvalidArgumentException
     */
    public function withScheme($scheme) {
        if(StringUtil::isNullOrEmpty($scheme)) {
            throw new InvalidArgumentException('Scheme value must non-empty string');
        }
        $data = $this->data;
        $data['scheme'] = $scheme;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified fragment
     *
     * @param string|null $fragment - The fragment to use with the new instance; a null value removes the fragment information (aliases Uri::withoutFragment)
     * @return static
     * @throws InvalidArgumentException
     */
    public function withFragment($fragment) {
        if($fragment === null) {
            return $this->withoutFragment();
        }
        $data = $this->data;
        $data['fragment'] = $fragment;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance without a fragment
     *
     * @return static
     */
    public function withoutFragment() {
        $data = $this->data;
        $data['fragment'] = null;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query string
     *
     * @param string $query - The query string to use with the new instance
     * @return static
     * @throws InvalidArgumentException
     */
    public function withQuery($query) {
        if(StringUtil::startsWith($query, '?')) {
            throw new InvalidArgumentException('Query value must not start with \'?\' character');
        }
        $data = $this->data;
        $data['query'] = $query;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance without query string
     *
     * @return static
     */
    public function withoutQuery() {
        $data = $this->data;
        $data['query'] = null;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param appended
     *
     * @param string $param - query param key
     * @param string $value - query param value
     * @return static
     * @throws InvalidArgumentException for invalid query params
     */
    public function withQueryParam($param, $value) {
        $data = $this->data;
        if(isset($data['query'])) {
            $params = array_merge(self::parseQuery($data['query']), [$param => $value]);
            $data['query'] = http_build_query($params);
        } else {
            $data['query'] = http_build_query([$param => $value]);
        }
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param appended (aliases Url::withQueryParam)
     *
     * @param string $param - query param key
     * @param string $value - query param value
     * @return static
     * @throws InvalidArgumentException for invalid query params
     */
    public function with($param, $value) { return $this->withQueryParam($param, $value); }

    /**
     * Return an instance without the specified query param
     *
     * @param string $param
     * @return static
     */
    public function withoutQueryParam($param) {
        $data = $this->data;
        $params = isset($data['query']) ? self::parseQuery($data['query']) : [];
        if(!isset($params[$param])) {

            // key not found, nothing to do
            return $this;
        }
        unset($params[$param]);
        $data['query'] = http_build_query($params);
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param replaced
     *
     * @param string $param - query param key
     * @param string|null $value - query param value; a null value removes the query param information (aliases Uri::withoutQueryParam)
     * @return static
     */
    public function withReplacedQueryParam($param, $value) {
        if($value === null) {
            return $this->withoutQueryParam($param);
        }
        $data = $this->data;
        $params = self::parseQuery($data['query']);
        if(!isset($params[$param])) {

            // key not found, nothing to do
            return $this;
        }
        $params[$param] = $value;
        $data['query'] = http_build_query($params);
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query params appended
     *
     * @param array $params - param/value pairs of query params
     * @return static
     */
    public function withQueryParams(array $params) {
        $data = $this->data;
        if(isset($data['query'])) {
            $currentParams = array_merge(self::parseQuery($data['query']), $params);
            $data['query'] = http_build_query($currentParams);
        } else {
            $data['query'] = http_build_query($params);
        }
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query params removed
     *
     * @param string[] $params - list of query params to remove
     * @return static
     */
    public function withoutQueryParams(array $params) {
        $data = $this->data;
        $currentParams = self::parseQuery($data['query']);
        foreach($params as $param) {
            if(isset($currentParams[$param])) {
                unset($currentParams[$param]);
            }
        }
        $data['query'] = http_build_query($currentParams);
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified path
     *
     * @param string $path -  The path to use with the new instance
     * @return static
     */
    public function withPath($path) {
        $data = $this->data;
        $data['path'] = $this->normalize($path);
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with a path segment appended
     *
     * @param string ... $segments,... - path segments to append
     * @return static
     */
    public function at() {
        $segments = func_get_args();
        if(empty($segments)) {
            return $this;
        }
        $data = $this->data;
        $path = $this->getInternalPath($data);
        foreach($segments as $segment) {
            $path .= $this->normalize($segment);
        }
        $data['path'] = $path;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with path/query/fragment appended
     *
     * @param string $pathQueryFragment
     * @return static
     */
    public function atPath($pathQueryFragment) {
        $newUriData = parse_url($this->normalize($pathQueryFragment));
        $data = $this->data;
        $path = $this->getInternalPath($data);
        $data['path'] = !StringUtil::isNullOrEmpty($path) ? $path . $this->normalize($newUriData['path']) : $this->normalize($newUriData['path']);
        if(isset($newUriData['query'])) {
            if(isset($data['query'])) {
                $currentParams = array_merge(self::parseQuery($data['query']), self::parseQuery($newUriData['query']));
                $data['query'] = http_build_query($currentParams);
            } else {
                $data['query'] = $newUriData['query'];
            }
        }
        if(isset($newUriData['fragment'])) {
            $data['fragment'] = $newUriData['fragment'];
        }
        return static::newFromUriData($data);
    }

    #endregion

    #region Conversion to Uri|string

    /**
     * @return static
     * @throws MalformedUriException
     */
    public function toBaseUri() {
        $scheme = $this->getScheme();
        $result = StringUtil::isNullOrEmpty($scheme) ? 'http://' : $scheme . '://';
        $result .= $this->getAuthority();
        return self::newFromString($result);
    }

    /**
     * Return an instance with basic auth password sensitive information scrubbed
     *
     * @param string[] $scrubQueryParams - list query param keys to scrub values
     * @param bool $scrubBasicAuthPassword - scrub basic auth password?
     * @return static
     */
    public function toSanitizedUri($scrubQueryParams = [], $scrubBasicAuthPassword = true) {
        $data = $this->data;
        if($scrubBasicAuthPassword && isset($data['pass'])) {
            $data['pass'] = self::SENSITIVE_DATA_REPLACEMENT;
        }
        if(!empty($scrubQueryParams)) {
            $params = self::parseQuery($data['query']);
            foreach($scrubQueryParams as $key) {
                if(isset($params[$key])) {
                    $params[$key] = self::SENSITIVE_DATA_REPLACEMENT;
                }
            }
            $data['query'] = http_build_query($params);
        }
        return static::newFromUriData($data);
    }

    /**
     * @return string
     */
    public function toString() {
        $scheme = $this->getScheme();
        $result = StringUtil::isNullOrEmpty($scheme) ? 'http://' : $scheme . '://';
        $result .= $this->getAuthority();
        $result .= $this->getPath();
        $query = $this->getQuery();
        $result .= StringUtil::isNullOrEmpty($query) ? '' : ('?' . $query);
        $fragment = $this->getFragment();
        $result .= StringUtil::isNullOrEmpty($fragment) ? '' : ('#' . $fragment);
        return $result;
    }

    /**
     * @return string
     */
    public function toRelativeString() {
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();
        $result = '';
        $result .= StringUtil::isNullOrEmpty($path) ? '/' : $path;
        $result .= StringUtil::isNullOrEmpty($query) ? '' : ('?' . $query);
        $result .= StringUtil::isNullOrEmpty($fragment) ? '' : ('#' . $fragment);
        return $result;
    }

    /**
     * @return string
     */
    public function __toString() { return $this->toString(); }

    #endregion

    #region Common helpers

    /**
     * @param string $path
     * @return string
     */
    private function normalize($path) { return '/' . trim($path, '/'); }

    /**
     * @param array $data
     * @return bool
     */
    private function getInternalPath($data) { return (isset($data['path']) && $data['path'] !== '/') ? $this->normalize($data['path']) : ''; }

    #endregion
}
