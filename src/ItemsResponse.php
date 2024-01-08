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

use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\Type;
use Mercari\DTO\ItemDetail;
use ArrayIterator;
use ReturnTypeWillChange;

/**
 * @extends ListResponse<ItemDetail>
 */
class ItemsResponse extends ListResponse
{
    /**
     * @var ItemDetail[]
     * @Type("array<string, Mercari\DTO\ItemDetail>")
     */
    public ?array $items = [];

    public SearchResponseMeta $meta;

    /**
     * @PostDeserialize
     */
    private function normalizeItems(): void
    {
        $this->items ??= [];
    }

    /**
     * @return ArrayIterator<array-key, ItemDetail>
     */
    #[ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->items ?? []);
    }

}
