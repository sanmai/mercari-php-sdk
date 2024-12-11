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
