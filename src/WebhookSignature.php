<?php

/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko
 *
 * Mercari PHP SDK is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * Mercari PHP SDK is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Mercari PHP SDK. If not, see <https://www.gnu.org/licenses/>.
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
