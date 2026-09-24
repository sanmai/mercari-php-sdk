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

use function implode;
use function is_iterable;
use function Pipeline\take;

/**
 * @internal
 */
final class ParamValue
{
    /**
     * Converts enums into backing values, lists of enums into comma-separated list of backing values.
     * @param BackedEnum|iterable<BackedEnum> $value
     */
    public static function of(mixed $value): mixed
    {
        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        if (is_iterable($value)) {
            return implode(',', take($value)->cast(self::of(...))->toList());
        }

        return $value;
    }
}
