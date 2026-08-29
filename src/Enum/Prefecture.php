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

namespace Mercari\Enum;

use function array_search;

/**
 * Japanese prefecture, as accepted by the prefecture parameter when fetching an item.
 *
 * Cases follow the JIS X 0401 order, so their position is the prefecture code
 * reported by shipping_from_area.
 */
enum Prefecture: string
{
    case Hokkaido = '北海道';

    case Aomori = '青森県';

    case Iwate = '岩手県';

    case Miyagi = '宮城県';

    case Akita = '秋田県';

    case Yamagata = '山形県';

    case Fukushima = '福島県';

    case Ibaraki = '茨城県';

    case Tochigi = '栃木県';

    case Gunma = '群馬県';

    case Saitama = '埼玉県';

    case Chiba = '千葉県';

    case Tokyo = '東京都';

    case Kanagawa = '神奈川県';

    case Niigata = '新潟県';

    case Toyama = '富山県';

    case Ishikawa = '石川県';

    case Fukui = '福井県';

    case Yamanashi = '山梨県';

    case Nagano = '長野県';

    case Gifu = '岐阜県';

    case Shizuoka = '静岡県';

    case Aichi = '愛知県';

    case Mie = '三重県';

    case Shiga = '滋賀県';

    case Kyoto = '京都府';

    case Osaka = '大阪府';

    case Hyogo = '兵庫県';

    case Nara = '奈良県';

    case Wakayama = '和歌山県';

    case Tottori = '鳥取県';

    case Shimane = '島根県';

    case Okayama = '岡山県';

    case Hiroshima = '広島県';

    case Yamaguchi = '山口県';

    case Tokushima = '徳島県';

    case Kagawa = '香川県';

    case Ehime = '愛媛県';

    case Kochi = '高知県';

    case Fukuoka = '福岡県';

    case Saga = '佐賀県';

    case Nagasaki = '長崎県';

    case Kumamoto = '熊本県';

    case Oita = '大分県';

    case Miyazaki = '宮崎県';

    case Kagoshima = '鹿児島県';

    case Okinawa = '沖縄県';

    /**
     * The prefecture code, from 1 for Hokkaido to 47 for Okinawa.
     */
    public function id(): int
    {
        return array_search($this, self::cases(), true) + 1;
    }

    public static function fromId(int $id): ?self
    {
        return self::cases()[$id - 1] ?? null;
    }
}
