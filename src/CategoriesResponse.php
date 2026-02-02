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

use JMS\Serializer\Annotation\Type;
use Mercari\DTO\Category;
use ArrayIterator;
use Override;
use Traversable;

use function iterator_count;

/**
 * @extends ListResponse<Category>
 * @template-implements \Countable<Category>
 */
class CategoriesResponse extends ListResponse
{
    /**
     * @var Category[]
     * @Type("array<Mercari\DTO\Category>")
     */
    public array $master_categories = [];

    /**
     * @return ArrayIterator<array-key, Category>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->master_categories);
    }

    #[Override]
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
