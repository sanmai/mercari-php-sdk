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

class Transaction
{
    public string $id;

    public string $status;

    public string $item_id;

    public string $seller_id;

    public int $updated_time;

    public int $price;

    public int $paid_price;

    #[Type('Mercari\DTO\ShippingInfo')]
    public ShippingInfo $shipping_info;

    /**
     * Shipping from the authentication service provider to the buyer; present only for authenticated items.
     */
    #[Type('Mercari\DTO\ProviderShippingInfo')]
    public ProviderShippingInfo $shipping_info_from_service_provider;
}
