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

/**
 * Interface ApiToken - MindTouch API server token interface
 *
 * @link https://success.mindtouch.com/Integrations/API/API_Tokens
 * @package MindTouch\Http
 */
interface IApiToken {

    /**
     * Return an instance with the user context set to a username
     *
     * @param string $username
     * @return IApiToken
     */
    function withUsername(string $username) : IApiToken;

    /**
     * Return an instance with the user context set to a userid
     *
     * @param int $userId
     * @return IApiToken
     */
    function withUserId(int $userId) : IApiToken;

    /**
     * Convert token to signature for use with "X-Deki-Token" header
     *
     * @param int|null $timestamp - unix timestamp (default: epoch)
     * @return string
     */
    function toSignature(int $timestamp = null) : string;
}
