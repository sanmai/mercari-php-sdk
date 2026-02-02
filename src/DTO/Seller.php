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

class Seller
{
    public int $id;

    public string $shop_id;

    public string $name;

    public string $introduction;

    public int $num_sell_items;

    public string $photo_url;

    public string $photo_thumbnail_url;

    /**
     * @Type("Mercari\DTO\SellerRating")
     */
    public SellerRating $ratings;

    public int $num_ratings;

    public int $star_rating_score;

    public int $created;

    public bool $has_identity_verified;

    public bool $proper;

    /**
     * @var UserBadge[]
     * @Type("array<Mercari\DTO\UserBadge>")
     */
    public array $user_badges;
}
