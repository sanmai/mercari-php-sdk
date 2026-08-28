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

use Mercari\AcceptTransactionRequest;

use function json_encode;

use const JSON_FORCE_OBJECT;

/**
 * @covers \Mercari\AcceptTransactionRequest
 */
class AcceptTransactionRequestTest extends TestCase
{
    public function testDefault()
    {
        $request = new AcceptTransactionRequest();

        $this->assertNull($request->transaction_id);

        $this->assertSame([], $request->getRequestParams());
        $this->assertSame('{}', json_encode($request, JSON_FORCE_OBJECT));
    }

    public function testFromTransactionId()
    {
        $request = new AcceptTransactionRequest('747717374');

        $this->assertSame('747717374', $request->transaction_id);
        $this->assertSame(['transaction_id' => '747717374'], $request->getRequestParams());
    }

    public function testAllFields()
    {
        $request = new AcceptTransactionRequest('747717374');
        $request->item_information = 'A watch';
        $request->item_condition = 2;
        $request->purchase_quantity = 1;
        $request->paying_out_company_name = 'Example Trading Co.';
        $request->paying_out_company_address = 'Tokyo';
        $request->paying_out_name = 'Yamada Taro';
        $request->paying_out_address = 'Tokyo';
        $request->paying_out_occupation = 'Buyer';
        $request->paying_out_department = 'Purchasing';
        $request->paying_out_age = 40;

        $params = $request->getRequestParams();

        $this->assertSame('747717374', $params['transaction_id']);
        $this->assertSame(2, $params['item_condition']);
        $this->assertSame(40, $params['paying_out_age']);
        $this->assertCount(11, $params);
    }
}
