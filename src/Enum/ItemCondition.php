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

declare(strict_types=1);

namespace Mercari\Enum;

use function array_filter;
use function array_values;

/**
 * Item condition, as used by the item_condition_id search filter and reported in ItemCondition DTOs.
 */
enum ItemCondition: int
{
    /** 新品、未使用 */
    case BrandNew = 1;

    /** 未使用に近い */
    case LikeNew = 2;

    /** 目立った傷や汚れなし */
    case NoNoticeableDamage = 3;

    /** やや傷や汚れあり */
    case SlightDamage = 4;

    /** 傷や汚れあり */
    case Damaged = 5;

    /** 全体的に状態が悪い */
    case PoorCondition = 6;

    /**
     * Every condition but BrandNew.
     *
     * @return list<self>
     */
    public static function used(): array
    {
        return array_values(array_filter(
            self::cases(),
            static fn(self $condition): bool => $condition !== self::BrandNew,
        ));
    }
}
