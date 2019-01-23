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
use Exception;
use MindTouch\Http\Content\IContent;
use MindTouch\Http\Exception\ApiResultException;
use MindTouch\Http\Exception\HttpResultParserContentExceedsMaxContentLengthException;
use MindTouch\Http\Parser\SerializedPhpArrayParser;

/**
 * Class ApiPlug - builder and invocation for MindTouch API requests
 *
 * @package MindTouch\Http
 * @method ApiResult get()
 * @method ApiResult head()
 * @method ApiResult post(IContent|null $content = null)
 * @method ApiResult delete()
 * @method ApiResult invoke(string $method, IContent|null $content = null)
 */
class ApiPlug extends HttpPlug {
    const DREAM_FORMAT_PHP = 'php';
    const DREAM_FORMAT_JSON = 'json';
    const DREAM_FORMAT_XML = 'xml';

    /**
     * Path segments that should not be url encoded
     *
     * @var string[]
     */
    private static $rawUriPathSegments = [
        'files,subpages',
        'children,siblings'
    ];

    /**
     * @param string $string - string to url encode
     * @param bool $doubleEncode - if true, the string will be urlencoded twice
     * @return string
     */
    public static function urlEncode(string $string, bool $doubleEncode = false) : string {

        // encode trailing dots (. => %2E)
        for($i = strlen($string) - 1, $dots = 0; $i >= 0; $dots++, $i--) {
            if(substr($string, $i, 1) !== '.') {
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
     * @var IApiToken|null
     */
    protected $token = null;

    /**
     * @var Closure|null
     */
    protected $postInvokeErrorHandler = null;

    /**
     * @param XUri $uri - target uri
     * @param string $format
     */
    public function __construct(XUri $uri, string $format = self::DREAM_FORMAT_PHP) {
        parent::__construct($uri);
        $this->uri = $this->uri->withQueryParam('dream.out.format', $format);
        $this->setHttpResultParser(new SerializedPhpArrayParser());
    }

    /**
     * The api requires double urlencoded titles. This method will do it automatically for you.
     * @see #AtRaw() for creating unencoded path components
     *
     * @param string ...$segments - path segments to add to the request (ex: $this->at('foo', 'bar', 'baz'))
     * @return static
     */
    public function at(...$segments) : object {
        $plug = clone $this;
        $path = $plug->uri->getPath();
        foreach($segments as $segment) {
            if(!in_array($segment, self::$rawUriPathSegments)) {

                // auto-double encode, check for '=' sign
                $segment = (strncmp($segment, '=', 1) === 0)
                    ? '=' . self::urlEncode(substr($segment, 1), true)
                    : self::urlEncode($segment, true);
            }
            $path .= '/' . ltrim($segment, '/');
        }
        $plug->uri = $plug->uri->withPath($path);
        return $plug;
    }

    /**
     * Appends a single path parameter to the plug, unencoded.
     *
     * @note Do not use this method unless you have to (you probably don't).
     * A real need occurs when initially creating the plug baseuri and an
     * unencoded "@api" is required.
     *
     * @see #At() for creating urlencoded paths
     * @param string $segment
     * @return static
     */
    public function atRaw(string $segment) : object {
        $plug = clone $this;
        $plug->uri = $plug->uri->at($segment);
        return $plug;
    }

    /**
     * Return an instance with  a server API token to the request
     *
     * @link https://success.mindtouch.com/Support/Extend/API_Documentation/API_Tokens/Use_a_server_API_token_with_an_integration
     * @param IApiToken $token
     * @return static
     */
    public function withApiToken(IApiToken $token) : object {
        $plug = clone $this;
        $plug->token = $token;
        return $plug;
    }

    /**
     * Return an instance with a post-invoke unsuccessful result handler
     * Adding the handler supresses the default exception behavior if (bool)true is returned
     * Only one error handler can be set, executing this method will return an instance with the handler replaced
     *
     * @param Closure $handler - $handler(ApiResultException $exception) : bool
     * @return static
     */
    public function withResultErrorHandler(Closure $handler) : object {
        $plug = clone $this;
        $plug->postInvokeErrorHandler = $handler;
        return $plug;
    }

    /**
     * Return an instance with the post-invoke unsuccessful result handler removed
     *
     * @return static
     */
    public function withoutResultErrorHandler() : object {
        $plug = clone $this;
        $plug->postInvokeErrorHandler = null;
        return $plug;
    }

    /**
     * Performs a PUT request
     *
     * @param IContent|null $content - optionally send a content body with the request
     * @return ApiResult
     * @throws HttpResultParserContentExceedsMaxContentLengthException
     */
    public function put(IContent $content = null) : object {
        $plug = $this->with('dream.in.verb', 'PUT');
        return $plug->invoke(self::METHOD_POST, $content);
    }

    /**
     * @param IMutableHeaders $headers
     * @return void
     */
    protected function invokeApplyCredentials(IMutableHeaders $headers) : void {
        parent::invokeApplyCredentials($headers);
        if($this->token !== null) {
            $headers->setHeader('X-Deki-Token', $this->token->toHash());
        }
    }

    /**
     * Return the formatted invocation result
     *
     * @param string $method
     * @param XUri $uri
     * @param IHeaders $headers
     * @param float $start
     * @param float $end
     * @param HttpResult $result
     * @return ApiResult
     * @throws ApiResultException
     */
    protected function invokeComplete(string $method, XUri $uri, IHeaders $headers, float $start, float $end, HttpResult $result) : object {
        $exception = null;
        try {
            $result = parent::invokeComplete($method, $uri, $headers, $start, $end, $result);
        } catch(Exception $e) {
            $exception = $e;
        }
        $result = new ApiResult($result->toArray());
        if($exception === null && !$result->isSuccess()) {
            $exception = new ApiResultException($result);
        }
        if($exception !== null) {
            if($this->postInvokeErrorHandler !== null) {
                $handler = $this->postInvokeErrorHandler;
                if($handler($exception) === true) {
                    return $result;
                }
            }
            throw $exception;
        }
        return $result;
    }
}
