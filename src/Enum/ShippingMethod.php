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

enum ShippingMethod: int
{
    /** 未定 (着払い) */
    case UndecidedBuyerPays = 1;

    /** ポスパケット */
    case PosPacket = 2;

    /** クロネコヤマト (着払い) */
    case YamatoBuyerPays = 3;

    /** ゆうパック (着払い) */
    case YuPackBuyerPays = 4;

    /** 未定 (送料込み) */
    case Undecided = 5;

    /** ゆうメール (送料込み) */
    case YuMail = 6;

    /** ゆうパケット */
    case YuPacket = 7;

    /** レターパック */
    case LetterPack = 8;

    /** 郵便（定型、定形外、書留など） */
    case Post = 9;

    /** クロネコヤマト (送料込み) */
    case Yamato = 10;

    /** ゆうパック (送料込み) */
    case YuPack = 11;

    /** はこBOON */
    case HakoBoon = 12;

    /** クリックポスト */
    case ClickPost = 13;

    /** らくらくメルカリ便 */
    case RakurakuMercari = 14;

    /** ゆうメール (着払い) */
    case YuMailBuyerPays = 15;

    /** 梱包・発送たのメル便 */
    case Tanomeru = 16;

    /** ゆうゆうメルカリ便 */
    case YuyuMercari = 17;

    /**
     * らくらくメルカリ便
     * @deprecated an older ID from 2017
     */
    case RakurakuMercariLegacy = 18;

    /** あとよろメルカリ便 */
    case AtoyoroMercari = 19;

    /** エコメルカリ便 */
    case EcoMercari = 20;

    /** おまかせクルマ取引 */
    case CarTrade = 21;
}
