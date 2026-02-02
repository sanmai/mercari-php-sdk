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

namespace Tests\Mercari;

use Mercari\WebhookSignature;
use DuoClock\TimeSpy;

/**
 * @covers \Mercari\WebhookSignature
 */
class WebhookSignatureTest extends TestCase
{
    public function testInvalid()
    {
        $signature = new WebhookSignature('123', '{}', 123, '123');

        $this->assertFalse($signature->isValid(new TimeSpy(123)));
    }

    public function testInvalidServerVars()
    {
        $signature = new WebhookSignature('123');

        $this->assertFalse($signature->isValid(new TimeSpy(123)));
    }

    public function testInvalidNow()
    {
        $signature = new WebhookSignature('456', '{}', 123, '567');

        $this->assertFalse($signature->isValid());
    }

    public function testValidZero()
    {
        $validSignature = 'v0:ad4bebe4e330ef4a37323290ff5c65727a9b285b1b8aa90073aebf7d20ebd6f8';

        $signature = new WebhookSignature('456', '{}', null, $validSignature);

        $this->assertTrue($signature->isValid(new TimeSpy(0)));
    }

    private const TEST_TIME = 1531420618;

    public static function provideTimestamps(): iterable
    {
        $time = self::TEST_TIME;

        yield 'on time' => [$time, true];

        yield 'on time + 1' => [$time + 1, true];

        yield 'on time - 1' => [$time - 1, true];

        yield 'minus window' => [$time - WebhookSignature::VALIDITY_WINDOW, true];
        yield 'plus window' => [$time + WebhookSignature::VALIDITY_WINDOW, true];

        yield 'under window' => [$time - WebhookSignature::VALIDITY_WINDOW - 1, false];
        yield 'beyond window' => [$time + WebhookSignature::VALIDITY_WINDOW + 1, false];
    }

    /**
     * @dataProvider provideTimestamps
     */
    public function testValid(int $time, bool $valid)
    {
        $timekeeper = new TimeSpy($time);

        $signature = new WebhookSignature(
            '8f742231b10e8888abcd99yyyzzz85a5',
            '{"webhook_type":"test_webhook"}',
            self::TEST_TIME,
            'v0:249e47edc1980531306517e4435b54ef1ff224020029284bdf19c8eda99aa325'
        );

        $this->assertSame($valid, $signature->isValid($timekeeper));
    }

    public function testValidOverrideServerVars()
    {
        $_SERVER['HTTP_X_MERCARI_REQUEST_TIMESTAMP'] = 0;
        $_SERVER['HTTP_X_MERCARI_SIGNATURE'] = '';

        $timekeeper = new TimeSpy(self::TEST_TIME);

        $signature = new WebhookSignature(
            '8f742231b10e8888abcd99yyyzzz85a5',
            '{"webhook_type":"test_webhook"}',
            self::TEST_TIME,
            'v0:249e47edc1980531306517e4435b54ef1ff224020029284bdf19c8eda99aa325'
        );

        $this->assertTrue($signature->isValid($timekeeper));
    }

    /**
     * @dataProvider provideTimestamps
     */
    public function testValidServerVars(int $time, bool $valid)
    {
        $_SERVER['HTTP_X_MERCARI_REQUEST_TIMESTAMP'] = self::TEST_TIME;
        $_SERVER['HTTP_X_MERCARI_SIGNATURE'] = 'v0:249e47edc1980531306517e4435b54ef1ff224020029284bdf19c8eda99aa325';

        $timekeeper = new TimeSpy($time);

        $signature = new WebhookSignature(
            '8f742231b10e8888abcd99yyyzzz85a5',
            '{"webhook_type":"test_webhook"}'
        );

        $this->assertSame($valid, $signature->isValid($timekeeper));
    }

    public function tearDown(): void
    {
        unset($_SERVER['HTTP_X_MERCARI_REQUEST_TIMESTAMP']);
        unset($_SERVER['HTTP_X_MERCARI_SIGNATURE']);
    }
}
