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

namespace Tests\Mercari;

use Mercari\ReturnResponse;

/**
 * @covers \Mercari\ReturnResponse
 */
class ReturnResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = new ReturnResponse();
        $response->register_status = $response::SUCCESS;

        $this->assertTrue($response->isSuccess());
    }

    public function testFailure()
    {
        $response = new ReturnResponse();
        $response->register_status = '';

        $this->assertFalse($response->isSuccess());
    }

    public function testFailureWithoutStatus()
    {
        $response = new ReturnResponse();

        $this->assertFalse($response->isSuccess());
    }

    public function testDeserializeSuccess()
    {
        $file = __DIR__ . '/data/return_success.json';

        $response = $this->deserializeFile($file, ReturnResponse::class);

        $this->assertTrue($response->isSuccess());
        $this->assertSame('747717374', $response->transaction_id);

        $this->assertDeserializedSame($file, $response, false);
    }

    public function testDeserializeFailure()
    {
        $file = __DIR__ . '/data/return_failure.json';

        $response = $this->deserializeFile($file, ReturnResponse::class);

        $this->assertFalse($response->isSuccess());
        $this->assertSame('F0000', $response->failure_details->code);

        $this->assertDeserializedSame($file, $response, false);
    }
}
