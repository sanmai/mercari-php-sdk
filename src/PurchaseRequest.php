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

namespace Mercari;

use Mercari\DTO\ItemDetail;

use function count;

/**
 * All fields except when marked are required to be non-empty strings.
 *
 * @property string $item_id Item ID
 * @property string $variant_id Variant ID (optional for flea market)
 * @property string $checksum Checksum for the purchase request
 *
 * @property string $family_name Buyer's family name
 * @property string $first_name Buyer's first name
 * @property string $family_name_kana Buyer's family name in katakana
 * @property string $first_name_kana Buyer's first name in katakana
 * @property string $telephone Buyer's telephone number
 * @property string $zip_code1 Buyer's zip code (the first 3 digits)
 * @property string $zip_code2 Buyer's zip code (the last 4 digits)
 * @property string $prefecture Delivery address prefecture
 * @property string $city Delivery address city
 * @property string $address1 First line of the delivery address
 * @property string $address2 Second line of the delivery address
 * @property string $delivery_identifier An optional identifier to be included in the delivery address
 * @property int $coupon_id Item's coupon ID
 * @property int $shops_shipping_fee Shops buyer shipping fee
 * @property string $country_code Two-character country or region code representing the shipping address of the user
 * @property string $buyer_id Buyer's ID number
 */
class PurchaseRequest extends GenericRequest
{
    public function __construct(?ItemDetail $item = null)
    {
        parent::__construct();

        if ($item === null) {
            return;
        }

        $this->item_id = $item->id;
        $this->checksum = $item->checksum;

        if (count($item->item_variants ?? []) === 1 && $this->variant_id === null) {
            foreach ($item->item_variants as $variant) {
                $this->variant_id = $variant->id;
            }
        }

        if (isset($item->item_discount->coupon_id)) {
            $this->coupon_id = $item->item_discount->coupon_id;
        }
    }
}
