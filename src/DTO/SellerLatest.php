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

class SellerLatest extends NamedDetail
{
    /**
     * The calculated rating from all of their transactions.
     */
    public float $rating;

    public string $shop_id;

    public int $num_sell_items;

    /**
     * @Type("Mercari\DTO\SellerRating")
     */
    public SellerRating $ratings;

    /**
     * @return string|int
     */
    public function getAnyId()
    {
        return $this->id ?? $this->shop_id ?? $this->name;
    }
}
