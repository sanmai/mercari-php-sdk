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

/**
 * @property string $keyword The search keyword.
 * @property string $exclude_keyword The keywords to exclude
 * @property int $category_id The category ID.
 * @property int $brand_id The brand ID.
 * @property int $seller_id The seller ID.
 * @property int $size_id The size ID.
 * @property string $shop_id The Shop ID.
 * @property int $color_id The color ID.
 * @property int $price_min The minimum item price.
 * @property int $price_max The maximum item price.
 * @property int $item_condition_id The condition ID.
 * @property int $shipping_payer_id The shipping payer ID.
 * @property string $status Comma-separated list of item statuses ("on_sale,trading,sold_out" used by default).
 * @property int $marketplace Preferred marketplace for the search; defaults to Mercari.
 * @property string $sort Sort using the given field.
 * @property string $order Sorting order ('desc' by default)
 * @property int $page Starting page index (1 by default).
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
}
