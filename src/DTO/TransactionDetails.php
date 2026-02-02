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

namespace Mercari\DTO;

use JMS\Serializer\Annotation\Type;

class TransactionDetails
{
    public int $trx_id;

    public string $shop_order_id;

    public string $paid_method;

    public int $price;

    public int $buyer_shipping_fee;

    public int $paid_price;

    public string $item_id;

    public string $checksum;

    /**
     * @Type("Mercari\DTO\UserAddress")
     */
    public UserAddress $user_address;

    public int $shipping_method_id;
}
