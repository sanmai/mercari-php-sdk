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

use Mercari\PurchaseResponse;

/**
 * @covers \Mercari\PurchaseResponse
 */
class PurchaseResponseTest extends TestCase
{
    public function testSuccess()
    {
        $response = new PurchaseResponse();
        $response->transaction_status = $response::SUCCESS;

        $this->assertTrue($response->isSuccess());

    }

    public function testFailure()
    {
        $response = new PurchaseResponse();
        $response->transaction_status = $response::FAILURE;

        $this->assertFalse($response->isSuccess());
    }

    public function testExample()
    {
        $file = __DIR__ . '/data/purchase_example.json';

        $response = $this->deserializeFile($file, PurchaseResponse::class);

        /** @var PurchaseResponse $response */

        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $response->request_id);
        $this->assertSame('success', $response->transaction_status);
        $this->assertGreaterThan(0, $response->transaction_details->trx_id);
        $this->assertGreaterThan(0, $response->transaction_details->buyer_shipping_fee);
        $this->assertSame('千住曙町４２－４', $response->transaction_details->user_address->address1);
        $this->assertSame(1, $response->transaction_details->shipping_method_id);

        $this->assertTrue($response->isSuccess());
    }
}
