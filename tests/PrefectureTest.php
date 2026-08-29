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

use Mercari\Enum\Prefecture;

use function count;

/**
 * @covers \Mercari\Enum\Prefecture
 */
class PrefectureTest extends TestCase
{
    public static function provideCodes(): iterable
    {
        yield 'first' => [1, Prefecture::Hokkaido, '北海道'];

        yield 'the one shipping_from_area documents' => [7, Prefecture::Fukushima, '福島県'];

        yield 'the capital' => [13, Prefecture::Tokyo, '東京都'];

        yield 'a fu, not a ken' => [27, Prefecture::Osaka, '大阪府'];

        yield 'last' => [47, Prefecture::Okinawa, '沖縄県'];
    }

    /**
     * @dataProvider provideCodes
     */
    public function testCode(int $id, Prefecture $prefecture, string $name)
    {
        $this->assertSame($name, $prefecture->value);

        $this->assertSame($id, $prefecture->id());

        $this->assertSame($prefecture, Prefecture::fromId($id));
    }

    public function testAllFortySeven()
    {
        $this->assertCount(47, Prefecture::cases());
    }

    public function testEveryCodeRoundTrips()
    {
        foreach (Prefecture::cases() as $prefecture) {
            $this->assertSame($prefecture, Prefecture::fromId($prefecture->id()));
        }

        $this->assertSame(count(Prefecture::cases()), Prefecture::Okinawa->id());
    }

    public function testUnknownCode()
    {
        $this->assertNull(Prefecture::fromId(0));

        $this->assertNull(Prefecture::fromId(48));

        $this->assertNull(Prefecture::tryFrom('メルカリ県'));
    }
}
