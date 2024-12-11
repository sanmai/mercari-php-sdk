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
use Mercari\PurchaseRequest;

class ItemDetail
{
    public const SHOPS_ID_LENGTH = 22;

    public const ITEM_TYPE_MERCARI = 'mercari';

    /**
     * The item is on sale
     */
    public const ON_SALE = 'on_sale';

    /**
     * The item has been purchased
     */
    public const TRADING = 'trading';

    /**
     * The trading for this item has ended
     */
    public const SOLD_OUT = 'sold_out';

    /**
     * The item has been suspended
     */
    public const STOP = 'stop';

    /**
     * The item has been deleted
     */
    public const CANCEL = 'cancel';

    /**
     * The item has been deleted by admin
     */
    public const ADMIN_CANCEL = 'admin_cancel';

    public string $id;

    public string $status;

    public string $name;

    public string $thumbnail;

    public int $price;

    /**
     * ItemType to distinguish mercari or shops item ("mercari", "shops")
     */
    public string $item_type;

    /**
     * @internal Please use getDescription()
     */
    public string $description;

    public bool $has_promotions;

    public int $updated;

    public int $created;

    public SellerLatest $seller;

    /**
     * @Type("array<string>")
     */
    public array $photos;

    /**
     * @Type("Mercari\DTO\ItemCategory")
     */
    public ItemCategory $item_category;

    /**
     * @Type("Mercari\DTO\ItemCondition")
     */
    public ItemCondition $item_condition;

    /**
     * @Type("Mercari\DTO\ItemDiscount")
     */
    public ItemDiscount $item_discount;

    /**
     * @Type("Mercari\DTO\ItemSize")
     */
    public ItemSize $item_size;

    /**
     * @Type("Mercari\DTO\ItemBrand")
     */
    public ItemBrand $item_brand;

    /**
     * @Type("Mercari\DTO\ShippingPayer")
     */
    public ShippingPayer $shipping_payer;

    /**
     * @Type("Mercari\DTO\ShippingMethod")
     */
    public ShippingMethod $shipping_method;

    /**
     * @Type("Mercari\DTO\ShippingFromArea")
     */
    public ShippingFromArea $shipping_from_area;

    /**
     * @Type("Mercari\DTO\ShippingDuration")
     */
    public ShippingDuration $shipping_duration;

    /**
     * @Type("Mercari\DTO\ShippingClass")
     */
    public ShippingClass $shipping_class;

    /**
     * @Type("Mercari\DTO\CatalogDetails")
     */
    public CatalogDetails $catalog_details;

    /**
     * @Type("Mercari\DTO\AnshinAuthentication")
     */
    public AnshinAuthentication $anshin_item_authentication;

    /**
     * @Type("array<Mercari\DTO\ItemVariant>")
     * @var ItemVariant[]
     */
    public array $item_variants;

    public int $num_comments;

    public int $num_likes;

    public string $checksum;

    public function getUrl(): string
    {
        if ($this->isMercariC2C()) {
            return sprintf('https://jp.mercari.com/item/%s', rawurlencode($this->id));
        }

        return sprintf('https://jp.mercari.com/shops/product/%s', rawurlencode($this->id));
    }

    public function isAvailable(): bool
    {
        return $this->status === self::ON_SALE;
    }

    public function getPurchaseRequest(): PurchaseRequest
    {
        if (!$this->isAvailable()) {
            throw new Exception('Item is not available for sale');
        }

        return new PurchaseRequest($this);
    }

    public function isMercariC2C(): bool
    {
        if (isset($this->item_type)) {
            return $this->item_type === self::ITEM_TYPE_MERCARI;
        }

        return !isset($this->seller->shop_id) && strlen($this->id) !== self::SHOPS_ID_LENGTH;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
