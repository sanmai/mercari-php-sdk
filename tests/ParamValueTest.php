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

namespace Tests\Mercari;

use Mercari\Enum\Fame;
use Mercari\ParamValue;

/**
 * @covers \Mercari\ParamValue
 */
class ParamValueTest extends TestCase
{
    public static function provideValues(): iterable
    {
        yield 'null' => [null, null];

        yield 'string' => ['on_sale', 'on_sale'];

        yield 'int' => [2, 2];

        yield 'enum' => [Fame::Bad, 'bad'];

        yield 'list of enums' => [[Fame::Good, Fame::Bad], 'good,bad'];

        yield 'list of scalars' => [[1, 'two'], '1,two'];

        yield 'mixed list' => [[Fame::Good, 'bad'], 'good,bad'];

        yield 'empty list' => [[], ''];
    }

    /**
     * @dataProvider provideValues
     */
    public function testOf(mixed $value, mixed $expected)
    {
        $this->assertSame($expected, ParamValue::of($value));
    }
}
