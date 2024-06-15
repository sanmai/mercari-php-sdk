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
