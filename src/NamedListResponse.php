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
     * @return T|null
     */
    public function get(int|string $id): ?NamedItem
    {
        if (!isset($this->index)) {
            $this->indexById();
        }

        return $this->index[$id] ?? null;
    }

    private function indexById(): void
    {
        $this->index = [];

        foreach ($this as $item) {
            $this->index[$item->getId()] = $item;
        }
    }
}
