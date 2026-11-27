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

namespace Tests\Mercari\DTO;

use Mercari\DTO\AuctionDetail;
use Mercari\DTO\AuctionSnapshot;
use Mercari\Enum\AuctionStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Mercari\TestCase;

#[CoversClass(AuctionDetail::class)]
#[CoversClass(AuctionSnapshot::class)]
class AuctionTest extends TestCase
{
    public function testOmittedZeroValues(): void
    {
        /** @var AuctionDetail $auction */
        $auction = $this->deserializeFile(__DIR__ . '/../data/auction_detail_min.json', AuctionDetail::class);

        $this->assertSame(0, $auction->bid_count);
        $this->assertFalse($auction->is_winner);
        $this->assertFalse($auction->is_top_bidder);
        $this->assertSame(AuctionStatus::Scheduled, $auction->getStatus());
    }

    public function testUnknownStatus(): void
    {
        $auction = new AuctionSnapshot();
        $auction->status = 'UNKNOWN';

        $this->assertNull($auction->getStatus());
    }
}
