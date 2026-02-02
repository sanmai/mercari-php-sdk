<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko <alexey@kopytko.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Mercari;

use DuoClock\DuoClock;

class WebhookSignature
{
    public const VALIDITY_WINDOW = 300;

    private const VERSION = 'v0';

    private string $signingSecret;
    private string $requestBody;
    private int $timestamp;
    private string $signature;

    public function __construct(string $signingSecret, ?string $requestBody = null, ?int $timestamp = null, ?string $signature = null)
    {
        $this->signingSecret = $signingSecret;
        $this->requestBody = $requestBody ?? file_get_contents('php://input');

        // X-Mercari-Request-Timestamp
        $this->timestamp = $timestamp ?? intval($_SERVER['HTTP_X_MERCARI_REQUEST_TIMESTAMP'] ?? 0);

        // X-Mercari-Signature
        $this->signature = $signature ?? $_SERVER['HTTP_X_MERCARI_SIGNATURE'] ?? '';
    }

    public function isValid(?DuoClock $timekeeper = null, int $validityWindow = self::VALIDITY_WINDOW): bool
    {
        $timekeeper ??= new DuoClock();

        if (abs($this->timestamp - $timekeeper->time()) > $validityWindow) {
            return false;
        }

        $knownSignature = join(':', [
            self::VERSION,
            hash_hmac('sha256', join(':', [
                self::VERSION,
                $this->timestamp,
                $this->requestBody,
            ]), $this->signingSecret),
        ]);

        return hash_equals($knownSignature, $this->signature);
    }
}
