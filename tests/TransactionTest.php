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

declare(strict_types=1);

namespace Tests\Mercari;

use Mercari\DTO\Transaction;

/**
 * @covers \Mercari\DTO\Transaction
 * @covers \Mercari\DTO\ShippingInfo
 * @covers \Mercari\DTO\ProviderShippingInfo
 */
class TransactionTest extends TestCase
{
    public function testDeserialize()
    {
        $file = __DIR__ . '/data/transaction.json';

        $response = $this->deserializeFile($file, Transaction::class);
        $this->assertSame('wait_shipping', $response->status);

        $this->assertDeserializedSame($file, $response, false);
    }

    public function testDeserializeAuthenticated()
    {
        $file = __DIR__ . '/data/transaction_anshin.json';

        $response = $this->deserializeFile($file, Transaction::class);
        $this->assertSame(69000, $response->price);
        $this->assertSame(69120, $response->paid_price);
        $this->assertSame(120, $response->shipping_info->buyer_shipping_fee);
        $this->assertSame('yamato', $response->shipping_info_from_service_provider->carrier_code);
        $this->assertSame('11223311', $response->shipping_info_from_service_provider->tracking_number);

        $this->assertDeserializedSame($file, $response, false);
    }
}
