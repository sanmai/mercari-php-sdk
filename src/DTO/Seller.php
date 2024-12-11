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
