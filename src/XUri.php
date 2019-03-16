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

use InvalidArgumentException;
use MindTouch\Http\Exception\MalformedPathQueryFragmentException;
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
    public static function tryParse(string $string) : ?object {
        try {
            return static::newFromString($string);
        } catch(MalformedUriException $e) {
            return null;
        }
    }

    /**
     * Return an instance, or throw if invalid
     *
     * @param string $string - fully qualified URI
     * @return static
     * @throws MalformedUriException
     */
    public static function newFromString(string $string) : object {
        $data = parse_url($string);
        if(!$data || !isset($data['scheme'])) {
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
    public static function isValidUrl(string $string) : bool {
        $filtered = filter_var($string, FILTER_VALIDATE_URL);
        return ($filtered !== false);
    }

    /**
     * Is string a schemeless URL? (ex: //example.com)
     *
     * @param string $string
     * @return bool
     */
    public static function isSchemelessUrl(string $string) : bool {
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
    public static function isAbsoluteUrl(string $string) : bool {
        $filtered = filter_var($string, FILTER_VALIDATE_URL);
        return ($filtered !== false);
    }

    /**
     * Return an internal instance
     *
     * @param array $data - URI data (output from parse_url)
     * @return static
     */
    private static function newFromUriData(array $data) : object {
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
    public function getScheme() : string { return $this->data['scheme']; }

    /**
     * Retrieve the path component of the URI
     *
     * @note A root/homepage path may return '/' or ''. It is the client's responsibility to handle both cases!
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     * @return string
     */
    public function getPath() : string { return isset($this->data['path']) ? $this->data['path'] : ''; }

    /**
     * Retrieve the path segments of the URI
     *
     * @return string[]
     */
    public function getSegments() : array { return explode('/', trim($this->getPath(), '/')); }

    /**
     * Retrieve the query string of the URI
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     * @return string|null
     */
    public function getQuery() : ?string { return isset($this->data['query']) ? $this->data['query'] : null; }

    /**
     * Retrieve a query parameter value of the URI
     *
     * @param string $param - name of the parameter
     * @return string|null - parameter value
     */
    public function getQueryParam(string $param) : ?string {
        return $this->getQueryParams()->get($param);
    }

    /**
     * Retrieve the query parameters of the URI
     *
     * @return IQueryParams - query params instance
     */
    public function getQueryParams() : IQueryParams {
        $query = $this->getQuery();
        return $query !== null ? QueryParams::newFromQuery($query) : new QueryParams();
    }

    /**
     * Retrieve the fragment component of the URI
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     * @return string|null
     */
    public function getFragment() : ?string { return isset($this->data['fragment']) ? $this->data['fragment'] : null; }

    /**
     * Retrieve the host component of the URI
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     * @return string
     */
    public function getHost() : string { return $this->data['host']; }

    /**
     * Retrieve the authority component of the URI, in "[user-info@]host[:port]" format
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2
     * @return string
     */
    public function getAuthority() : string {
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
    public function getUserInfo() : string {
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
    public function getPort() : ?int { return isset($this->data['port']) ? intval($this->data['port']) : null; }

    #endregion

    #region URI builders

    /**
     * Return an instance with the specified user information
     *
     * @param string $user - The user name to use for authority
     * @param string $password - The password associated with $user
     * @return static
     */
    public function withUserInfo(string $user, string $password = null) : object {
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
    public function withHost(string $host) : object {
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
    public function withPort(?int $port) : object {
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
    public function withoutPort() : object {
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
    public function withScheme(string $scheme) : object {
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
    public function withFragment(?string $fragment) : object {
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
    public function withoutFragment() : object {
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
    public function withQuery(string $query) : object {
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
    public function withoutQuery() : object {
        $data = $this->data;
        $data['query'] = null;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param appended
     *
     * @param string $param - query param key
     * @param mixed $value - query param value
     * @return static
     * @throws InvalidArgumentException for invalid query params
     */
    public function withQueryParam(string $param, $value) : object {
        if($value === null) {
            return $this;
        }
        $data = $this->data;
        $params = (isset($data['query']) ? QueryParams::newFromQuery($data['query']) : new QueryParams())
            ->toMutableQueryParams();
        $params->set($param, $value);
        $data['query'] = $params->toString();
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param appended (aliases Url::withQueryParam)
     *
     * @param string $param - query param key
     * @param mixed $value - query param value
     * @return static
     * @throws InvalidArgumentException for invalid query params
     */
    public function with(string $param, $value) : object { return $this->withQueryParam($param, $value); }

    /**
     * Return an instance without the specified query param
     *
     * @param string $param
     * @return static
     */
    public function withoutQueryParam(string $param) : object {
        $data = $this->data;
        $params = (isset($data['query']) ? QueryParams::newFromQuery($data['query']) : new QueryParams())
            ->toMutableQueryParams();
        if(!$params->isSet($param)) {

            // key not found, nothing to do
            return $this;
        }
        $params->set($param, null);
        $data['query'] = $params->toString();
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query param replaced
     *
     * @param string $param - query param key
     * @param mixed $value - query param value; a null value removes the query param information (aliases Uri::withoutQueryParam)
     * @return static
     */
    public function withReplacedQueryParam(string $param, $value) : object {
        if($value === null) {
            return $this->withoutQueryParam($param);
        }
        $data = $this->data;
        $params = (isset($data['query']) ? QueryParams::newFromQuery($data['query']) : new QueryParams())
            ->toMutableQueryParams();
        if(!$params->isSet($param)) {

            // key not found, nothing to do
            return $this;
        }
        $params->set($param, StringUtil::stringify($value));
        $data['query'] = $params->toString();
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query params appended
     *
     * @param IQueryParams $params - query params
     * @return static
     */
    public function withQueryParams(IQueryParams $params) : object {
        $data = $this->data;
        $currentParams = (isset($data['query']) ? QueryParams::newFromQuery($data['query']) : new QueryParams())
            ->toMutableQueryParams();
        $currentParams->addQueryParams($params);
        $data['query'] = $currentParams->toString();
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified query params removed
     *
     * @param string[] $params - list of query params to remove
     * @return static
     */
    public function withoutQueryParams(array $params) : object {
        $data = $this->data;
        $currentParams = (isset($data['query']) ? QueryParams::newFromQuery($data['query']) : new QueryParams())
            ->toMutableQueryParams();
        foreach($params as $param) {
            $currentParams->set($param, null);
        }
        $data['query'] = $currentParams->toString();
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with the specified path
     *
     * @param string $path -  The path to use with the new instance
     * @return static
     */
    public function withPath(string $path) : object {
        $data = $this->data;
        $data['path'] = $this->normalize($path);
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with a path segment appended
     *
     * @param mixed ...$segments,... - path segments to append
     * @return static
     */
    public function at(...$segments) : object {
        if(empty($segments)) {
            return $this;
        }
        $data = $this->data;
        $path = $this->getInternalPath($data);
        foreach($segments as $segment) {
            $path .= $this->normalize(StringUtil::stringify($segment));
        }
        $data['path'] = $path;
        return static::newFromUriData($data);
    }

    /**
     * Return an instance with path/query/fragment appended
     *
     * @param string $pathQueryFragment
     * @return static
     * @throws MalformedPathQueryFragmentException
     */
    public function atPath(string $pathQueryFragment) : object {
        $newUriData = parse_url($this->normalize($pathQueryFragment));
        if(!is_array($newUriData)) {
            throw new MalformedPathQueryFragmentException($pathQueryFragment);
        }
        $data = $this->data;
        if(isset($newUriData['path'])) {
            $path = $this->getInternalPath($data);
            $data['path'] = !StringUtil::isNullOrEmpty($path) ? $path . $this->normalize($newUriData['path']) : $this->normalize($newUriData['path']);
        }
        if(isset($newUriData['query'])) {
            if(isset($data['query'])) {
                $currentParams = QueryParams::newFromQuery($data['query'])
                    ->toMutableQueryParams();
                $currentParams->addQueryParams(QueryParams::newFromQuery($newUriData['query']));
                $data['query'] = $currentParams->toString();
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
    public function toBaseUri() : object {
        $scheme = $this->getScheme();
        $result = StringUtil::isNullOrEmpty($scheme) ? 'http://' : $scheme . '://';
        $result .= $this->getAuthority();
        return self::newFromString($result);
    }

    /**
     * Return an instance with basic auth password sensitive information scrubbed
     *
     * @param string[] $scrubQueryParams - list query param keys to scrub values
     * @param bool $scrubBasicAuthPassword - scrub basic auth password
     * @return static
     */
    public function toSanitizedUri(array $scrubQueryParams = [], bool $scrubBasicAuthPassword = true) : object {
        $data = $this->data;
        if($scrubBasicAuthPassword && isset($data['pass'])) {
            $data['pass'] = self::SENSITIVE_DATA_REPLACEMENT;
        }
        if(!empty($scrubQueryParams) && isset($data['query'])) {
            $params = QueryParams::newFromQuery($data['query'])
                ->toMutableQueryParams();
            foreach($scrubQueryParams as $param) {
                if($params->isSet($param)) {
                    $params->set($param, self::SENSITIVE_DATA_REPLACEMENT);
                }
            }
            $data['query'] = $params->toString();
        }
        return static::newFromUriData($data);
    }

    /**
     * @return string
     */
    public function toString() : string {
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
    public function toRelativeString() : string {
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
    public function __toString() : string { return $this->toString(); }

    #endregion

    #region Common helpers

    /**
     * @param string $path
     * @return string
     */
    private function normalize(string $path) : string { return '/' . trim($path, '/'); }

    /**
     * @param array $data
     * @return string
     */
    private function getInternalPath(array $data) : string { return (isset($data['path']) && $data['path'] !== '/') ? $this->normalize($data['path']) : ''; }

    #endregion
}
