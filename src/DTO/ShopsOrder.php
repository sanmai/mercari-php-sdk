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

class ShopsOrder
{
    public string $id;

    public string $order_id;

    #[Type('string')]
    public string $created_at;

    public int $total_price;

    public string $status;

    #[Type('Mercari\DTO\ShopsShippingInfo')]
    public ShopsShippingInfo $shipping_info;

    #[Type('Mercari\DTO\OrderProduct')]
    public OrderProduct $order_product;

    public function getAnyId(): string
    {
        return $this->order_id ?? $this->id;
    }
}
