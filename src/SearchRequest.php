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

namespace Mercari;

use Mercari\Enum\Color;
use Mercari\Enum\ItemCondition;
use Mercari\Enum\ItemStatus;
use Mercari\Enum\Marketplace;
use Override;
use Mercari\Enum\ShippingPayer;
use Mercari\Enum\SortBy;
use Mercari\Enum\SortOrder;

use function array_map;

/**
 * Where a property takes an enum, it takes the plain scalar just as well.
 * Properties given a list are sent as a comma-separated string.
 *
 * @property string $keyword The search keyword.
 * @property string $exclude_keyword The keywords to exclude
 * @property int $category_id The category ID.
 * @property int $brand_id The brand ID.
 * @property int $seller_id The seller ID.
 * @property int $size_id The size ID.
 * @property string $shop_id The Shop ID.
 * @property int|Color|list<int|Color> $color_id The color ID, or several of them.
 * @property int $price_min The minimum item price.
 * @property int $price_max The maximum item price.
 * @property int|ItemCondition|list<int|ItemCondition> $item_condition_id The condition ID, or several of them.
 * @property int|ShippingPayer $shipping_payer_id The shipping payer ID.
 * @property string|ItemStatus|list<string|ItemStatus> $status Item statuses to look for ("on_sale,trading,sold_out" used by default).
 * @property int $created_before_date Only items created before the given Unix timestamp.
 * @property int $created_after_date Only items created after the given Unix timestamp.
 * @property bool $item_authentication Search for items eligible for the item authentication service only.
 * @property bool $time_sale Search for Time-Sale (discounted) items only.
 * @property bool $with_offer_price_promotion Include the offer-to-everyone discount in the returned discount details.
 * @property int|Marketplace $marketplace Preferred marketplace for the search; defaults to Mercari.
 * @property string|SortBy $sort Sort using the given field.
 * @property string|SortOrder $order Sorting order ('desc' by default)
 * @property int $page Starting page index, zero-based (the first page is 0).
 * @property int $limit Items per page limit. Maximum is 100 and the default is 50.
 * @final
 */
class SearchRequest extends GenericRequest
{
    public static function build(): self
    {
        return new static();
    }

    public function searchMercariOnly(): self
    {
        $this->marketplace = MercariClient::MARKETPLACE_MERCARI;

        return $this;
    }

    public function searchShopsOnly(): self
    {
        $this->marketplace = MercariClient::MARKETPLACE_SHOP;

        return $this;
    }

    public function searchBothMarketplaces(): self
    {
        $this->marketplace = MercariClient::MARKETPLACE_ALL;

        return $this;
    }

    #[Override]
    public function getRequestParams(): array
    {
        return array_map(ParamValue::of(...), parent::getRequestParams());
    }
}
