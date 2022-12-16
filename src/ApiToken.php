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
 * Class ApiToken - MindTouch API server token
 *
 * @link https://success.mindtouch.com/Integrations/API/API_Tokens
 * @package MindTouch\Http
 */
class ApiToken implements IApiToken {

    /**
     * @var string
     */
    private $user = '2';

    /**
     * Returns an instance with the anonymous user context
     */
    public function __construct(private string $key, private string $secret)
    {
    }

    public function withUsername(string $username) : IApiToken {
        $token = clone $this;
        $token->user = '=' . $username;
        return $token;
    }

    public function withUserId(int $userId) : IApiToken {
        $token = clone $this;
        $token->user = strval($userId);
        return $token;
    }

    public function toHash(int $timestamp = null): string {
        return $this->toSignature($timestamp);
    }

    public function toSignature(int $timestamp = null) : string {
        if($timestamp === null) {
            $timestamp = time();
        }
        $hash = hash_hmac('sha256', ($this->key . $timestamp . $this->user), $this->secret, false);
        return "{$this->key}_{$timestamp}_{$this->user}_{$hash}";
    }
}
