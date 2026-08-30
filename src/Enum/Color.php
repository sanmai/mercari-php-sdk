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

/**
 * Color family, as used by the color_id search filter. Each covers a range of shades, not one exact color.
 */
enum Color: int
{
    /** ブラック系 */
    case Black = 1;

    /** ホワイト系 */
    case White = 2;

    /** グレイ系 */
    case Grey = 3;

    /** ブラウン系 */
    case Brown = 4;

    /** レッド系 */
    case Red = 5;

    /** ピンク系 */
    case Pink = 6;

    /** パープル系 */
    case Purple = 7;

    /** ブルー系 */
    case Blue = 8;

    /** ベージュ系 */
    case Beige = 9;

    /** グリーン系 */
    case Green = 10;

    /** イエロー系 */
    case Yellow = 11;

    /** オレンジ系 */
    case Orange = 12;
}
