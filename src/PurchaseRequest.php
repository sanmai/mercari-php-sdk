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

namespace Mercari;

use Mercari\DTO\ItemDetail;

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
 *
 * @psalm-suppress RedundantCondition
 */
class PurchaseRequest extends GenericRequest
{
    public function __construct(ItemDetail $item = null)
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
