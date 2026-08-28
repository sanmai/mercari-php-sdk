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

/**
 * All fields are required.
 *
 * @property string $transaction_id Transaction ID
 * @property string $item_information Description of the item as assessed by the partner
 * @property int $item_condition Item condition ID
 * @property int $purchase_quantity Purchase quantity
 * @property string $paying_out_company_name Name of the paying-out company
 * @property string $paying_out_company_address Address of the paying-out company
 * @property string $paying_out_name Name of the person paying out
 * @property string $paying_out_address Address of the person paying out
 * @property string $paying_out_occupation Occupation of the person paying out
 * @property string $paying_out_department Department of the person paying out
 * @property int $paying_out_age Age of the person paying out
 */
class AcceptTransactionRequest extends GenericRequest
{
    public function __construct(?string $transaction_id = null)
    {
        parent::__construct();

        if ($transaction_id !== null) {
            $this->transaction_id = $transaction_id;
        }
    }
}
