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

use BackedEnum;
use JsonSerializable;
use Override;

use function array_map;
use function implode;
use function is_array;

abstract class GenericRequest implements JsonSerializable
{
    private array $data;

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function __get(string $name)
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    public function getRequestParams(): array
    {
        return array_map(self::toParam(...), $this->data);
    }

    /**
     * An enum becomes its backing value; an array becomes a comma-separated list.
     */
    private static function toParam(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if (is_array($value)) {
            return implode(',', array_map(self::toParam(...), $value));
        }

        return $value;
    }

    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->getRequestParams();
    }
}
