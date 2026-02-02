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

use JMS\Serializer\Annotation\PostDeserialize;
use JMS\Serializer\Annotation\Type;
use Mercari\DTO\TodoItem;
use ArrayIterator;
use Traversable;
use Override;

/**
 * @extends ListResponse<TodoItem>
 */
class TodoListResponse extends ListResponse
{
    /**
     * @var TodoItem[]
     * @Type("array<Mercari\DTO\TodoItem>")
     */
    public $data = [];

    public string $next_page_token;

    /**
     * @PostDeserialize
     */
    private function normalizeData(): void
    {
        $this->data ??= [];
    }

    /**
     * @return ArrayIterator<array-key, TodoItem>
     */
    #[Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->data);
    }
}
