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

use Mercari\DTO\PartnerOfferStatus;
use Mercari\PartnerOffersResponse;

/**
 * @covers \Mercari\DTO\PartnerOffer
 * @covers \Mercari\PartnerOffersResponse
 */
class PartnerOffersResponseTest extends TestCase
{
    public function testDeserialize()
    {
        $file = __DIR__ . '/data/partner_offers.json';

        $response = $this->deserializeFile($file, PartnerOffersResponse::class);

        $this->assertCount(2, $response);

        foreach ($response as $offer) {
            $this->assertSame(PartnerOfferStatus::Pending, $offer->getStatus());
            break;
        }

        $this->assertDeserializedSame($file, $response, false);
    }

    public function testDeserializeEmpty()
    {
        $file = __DIR__ . '/data/partner_offers_empty.json';

        $response = $this->deserializeFile($file, PartnerOffersResponse::class);

        $this->assertCount(0, $response);
    }
}
