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

use JMS\Serializer\Annotation\Exclude;
use Mercari\DTO\NamedItem;

/**
 * A list of named items that the API gives in no particular order.
 *
 * @template-covariant T of NamedItem
 * @extends ListResponse<T>
 */
abstract class NamedListResponse extends ListResponse
{
    /**
     * @var array<int|string, T>
     */
    #[Exclude]
    private array $index;

    /**
     * Find an item by its ID. Gives null when the list has no such item.
     *
     * The index is built on the first lookup, from the list as it is at that moment.
     *
     * @return T|null
     */
    public function get(int|string $id): ?NamedItem
    {
        if (!isset($this->index)) {
            $this->indexById();
        }

        return $this->index[$id] ?? null;
    }

    /**
     * @return array<int|string, T>
     */
    private function indexById(): array
    {
        $this->index = [];

        foreach ($this as $item) {
            $this->index[$item->getId()] = $item;
        }

        return $this->index;
    }
}
