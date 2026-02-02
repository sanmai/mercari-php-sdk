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

use Countable;
use IteratorAggregate;
use Traversable;
use Override;

/**
 * @template T
 * @template-implements IteratorAggregate<array-key, T>
 */
abstract class ListResponse implements IteratorAggregate, Countable
{
    #[Override]
    abstract public function getIterator(): Traversable;

    #[Override]
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
