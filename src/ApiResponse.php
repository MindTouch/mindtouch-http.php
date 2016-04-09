<?php
/**
 * MindTouch API Plug
 *
 * Copyright (C) 2006-2016 MindTouch, Inc.
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
use MindTouch\XArray\XArray;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class ApiResponse - wraps MindTouch API results with accessors
 * @package MindTouch\Plug
 */
class ApiResponse implements ResponseInterface {

    /**
     * @var string[]|null
     */
    private $headers = null;

    /**
     * @var XArray
     */
    private $Response;

    /**
     * @param array $response
     */
    public function __construct($response) { $this->Response = new XArray($response); }

    #region Accessors

    /**
     * @return string
     */
    public function getUri() { return $this->Response->getVal('uri'); }

    /**
     * Get the response or a portion of the response formatted as XML
     *
     * @param string $key - empty key returns entire response as XML
     * @return string
     */
    public function getXml($key = null) {
        if($key == null) {
            return $this->Response->toXml();
        }
        $val = $this->Response->getVal($key);
        $XArray = new XArray($val);
        return $XArray->toXml();
    }

    /**
     * @param string|null $key
     * @return string|bool|array
     */
    public function getVal($key = null) { return $this->Response->getVal($key); }

    /**
     * @param string|null $key
     * @return array
     */
    public function getAll($key = null) { return $this->Response->getAll($key, []); }
    #endregion

    #region Headers

    /**
     * Return the value of a Set-Cookie header by the cookie's name.
     *
     * @param string $cookie
     * @return string|null
     */
    public function getSetCookieHeader($cookie) {

        /*  if(!isset($this->array['headers'][$name])) {
            return $default;
        }
        $headers = $this->array['headers'][$name];
        if(!is_array($headers)) {
            $headers = array($headers);
        }
        return $headers;*/
 /*       $headers = $this->getHeaders('Set-Cookie');
        if(!is_null($headers)) {
            if(!is_array($headers)) {
                $headers = array($headers);
            }
            foreach($headers as $header) {
                if(strpos($header, $name) === 0) {
                    return $header;
                }
            }
        }*/
        return null;
    }

    /**
     * Retrieves all message header values.
     * 
     * @return string[]
     */
    public function getHeaders() { return $this->getAll('headers'); }

    /**
     * Checks if a header exists by the given case-insensitive name.
     * 
     * @param string $name
     * @return bool
     */
    public function hasHeader($name) { return $this->getHeader($name) !== null; }
    
    /**
     * Retrieves a message header value by the given case-insensitive name.
     * 
     * @param string $name
     * @return string[]
     */
    public function getHeader($name) {
        if($this->headers === null) {
            foreach($this->getHeaders() as $name => $value) {
                if(!is_array($value)) {
                    $value = [$value];
                }
                $this->headers[strtolower($name)] = $value;
            }
        }
        return isset($this->headers[$name]) ? $this->headers[$name] : [];
    }
    
    /**
     * Retrieves a comma-separated string of the values for a single header.
     * 
     * @param string $name
     * @return string
     */
    public function getHeaderLine($name) { return implode(',', $this->getHeader($name)); }

    /**
     * Return an instance with the provided value replacing the specified header.
     * 
     * @param string $name
     * @param string|string[] $value
     * @return ResponseInterface
     */
    public function withHeader($name, $value) {
        if(!is_string($name)) {
            throw new InvalidArgumentException('name');
        }
        if(!is_string($value) || !is_array($value)) {
            throw new InvalidArgumentException('value');
        }
        $Response = new self($this->Response->toArray());
        $Response->setVal("headers/{$name}", $value);
        return $Response;
    }

    /**
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws InvalidArgumentException for invalid header names or values.
     */
    public function withAddedHeader($name, $value) {
        // TODO: Implement withAddedHeader() method.
    }

    /**
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
     */
    public function withoutHeader($name) {
        // TODO: Implement withoutHeader() method.
    }
    #endregion

    #region Status

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return self
     * @throws InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '') {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase() {
        // TODO: Implement getReasonPhrase() method.
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        $status = $this->getVal('status');
        return ($status === null) ? 0 : $status;
    }

    /**
     * @param int $status
     * @return bool
     */
    public function is($status) { return $this->getStatusCode() === $status; }

    /**
     * @return bool
     */
    public function isSuccess() {
        $status = $this->getStatusCode();
        return $status >= 200 && $status < 300;
    }

    /**
     * @return bool
     */
    public function isServerError() {
        $status = $this->getStatusCode();
        return $status >= 500 && $status < 600;
    }

    /**
     * @return bool
     */
    public function isCurlError() { return $this->getVal('errno') > 0; }
    #endregion

    #region Error Handling

    /**
     * @return array|string|null
     */
    public function getErrorMessage() {

        // formatted API error
        $error = $this->getVal('body/error/message');
        if($error !== null) {
            return $error;
        }

        // exception API error
        $error = $this->getVal('body/exception/message');
        if($error !== null) {
            return $error;
        }

        // other API error
        $error = $this->getVal('body');
        if($error !== null) {
            return $error;
        }

        // curl error
        return (isset($this->array['error'])) ? $this->array['error'] : null;
    }
    #endregion

    /**
     * Retrieves the HTTP protocol version as a string.
     *
     * The string MUST contain only the HTTP version number (e.g., "1.1", "1.0").
     *
     * @return string HTTP protocol version.
     */
    public function getProtocolVersion() {
        // TODO: Implement getProtocolVersion() method.
    }

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new protocol version.
     *
     * @param string $version HTTP protocol version
     * @return self
     */
    public function withProtocolVersion($version) {
        // TODO: Implement withProtocolVersion() method.
    }

    /**
     * Gets the body of the message.
     *
     * @return StreamInterface Returns the body as a stream.
     */
    public function getBody() {
        // TODO: Implement getBody() method.
    }

    /**
     * Return an instance with the specified message body.
     *
     * The body MUST be a StreamInterface object.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return a new instance that has the
     * new body stream.
     *
     * @param StreamInterface $body Body.
     * @return self
     * @throws InvalidArgumentException When the body is not valid.
     */
    public function withBody(StreamInterface $body) {
        // TODO: Implement withBody() method.
    }
}
