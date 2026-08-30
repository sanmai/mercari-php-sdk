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

/**
 * @covers \Mercari\Enum\Prefecture
 */
class PrefectureTest extends TestCase
{
    public static function provideCodes(): iterable
    {
        yield 1 => [1, Prefecture::Hokkaido, '北海道'];
        yield 2 => [2, Prefecture::Aomori, '青森県'];
        yield 3 => [3, Prefecture::Iwate, '岩手県'];
        yield 4 => [4, Prefecture::Miyagi, '宮城県'];
        yield 5 => [5, Prefecture::Akita, '秋田県'];
        yield 6 => [6, Prefecture::Yamagata, '山形県'];
        yield 7 => [7, Prefecture::Fukushima, '福島県'];
        yield 8 => [8, Prefecture::Ibaraki, '茨城県'];
        yield 9 => [9, Prefecture::Tochigi, '栃木県'];
        yield 10 => [10, Prefecture::Gunma, '群馬県'];
        yield 11 => [11, Prefecture::Saitama, '埼玉県'];
        yield 12 => [12, Prefecture::Chiba, '千葉県'];
        yield 13 => [13, Prefecture::Tokyo, '東京都'];
        yield 14 => [14, Prefecture::Kanagawa, '神奈川県'];
        yield 15 => [15, Prefecture::Niigata, '新潟県'];
        yield 16 => [16, Prefecture::Toyama, '富山県'];
        yield 17 => [17, Prefecture::Ishikawa, '石川県'];
        yield 18 => [18, Prefecture::Fukui, '福井県'];
        yield 19 => [19, Prefecture::Yamanashi, '山梨県'];
        yield 20 => [20, Prefecture::Nagano, '長野県'];
        yield 21 => [21, Prefecture::Gifu, '岐阜県'];
        yield 22 => [22, Prefecture::Shizuoka, '静岡県'];
        yield 23 => [23, Prefecture::Aichi, '愛知県'];
        yield 24 => [24, Prefecture::Mie, '三重県'];
        yield 25 => [25, Prefecture::Shiga, '滋賀県'];
        yield 26 => [26, Prefecture::Kyoto, '京都府'];
        yield 27 => [27, Prefecture::Osaka, '大阪府'];
        yield 28 => [28, Prefecture::Hyogo, '兵庫県'];
        yield 29 => [29, Prefecture::Nara, '奈良県'];
        yield 30 => [30, Prefecture::Wakayama, '和歌山県'];
        yield 31 => [31, Prefecture::Tottori, '鳥取県'];
        yield 32 => [32, Prefecture::Shimane, '島根県'];
        yield 33 => [33, Prefecture::Okayama, '岡山県'];
        yield 34 => [34, Prefecture::Hiroshima, '広島県'];
        yield 35 => [35, Prefecture::Yamaguchi, '山口県'];
        yield 36 => [36, Prefecture::Tokushima, '徳島県'];
        yield 37 => [37, Prefecture::Kagawa, '香川県'];
        yield 38 => [38, Prefecture::Ehime, '愛媛県'];
        yield 39 => [39, Prefecture::Kochi, '高知県'];
        yield 40 => [40, Prefecture::Fukuoka, '福岡県'];
        yield 41 => [41, Prefecture::Saga, '佐賀県'];
        yield 42 => [42, Prefecture::Nagasaki, '長崎県'];
        yield 43 => [43, Prefecture::Kumamoto, '熊本県'];
        yield 44 => [44, Prefecture::Oita, '大分県'];
        yield 45 => [45, Prefecture::Miyazaki, '宮崎県'];
        yield 46 => [46, Prefecture::Kagoshima, '鹿児島県'];
        yield 47 => [47, Prefecture::Okinawa, '沖縄県'];
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

    public function testUnknownCode()
    {
        $this->assertNull(Prefecture::fromId(0));

        $this->assertNull(Prefecture::fromId(48));

        $this->assertNull(Prefecture::tryFrom('メルカリ県'));
    }
}
