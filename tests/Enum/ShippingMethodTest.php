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

namespace Tests\Mercari\Enum;

use Mercari\Enum\ShippingMethod;

/**
 * @covers \Mercari\Enum\ShippingMethod
 */
class ShippingMethodTest extends BackedEnumTestCase
{
    public function enumClass(): string
    {
        return ShippingMethod::class;
    }

    public function expectedValues(): iterable
    {
        yield 'UndecidedBuyerPays' => 1;
        yield 'PosPacket' => 2;
        yield 'YamatoBuyerPays' => 3;
        yield 'YuPackBuyerPays' => 4;
        yield 'Undecided' => 5;
        yield 'YuMail' => 6;
        yield 'YuPacket' => 7;
        yield 'LetterPack' => 8;
        yield 'Post' => 9;
        yield 'Yamato' => 10;
        yield 'YuPack' => 11;
        yield 'HakoBoon' => 12;
        yield 'ClickPost' => 13;
        yield 'RakurakuMercari' => 14;
        yield 'YuMailBuyerPays' => 15;
        yield 'Tanomeru' => 16;
        yield 'YuyuMercari' => 17;
        yield 'RakurakuMercariLegacy' => 18;
        yield 'AtoyoroMercari' => 19;
        yield 'EcoMercari' => 20;
        yield 'CarTrade' => 21;
    }
}
