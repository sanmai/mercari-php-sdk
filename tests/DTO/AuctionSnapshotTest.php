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

use Mercari\DTO\AuctionSnapshot;
use Mercari\Enum\AuctionStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(AuctionSnapshot::class)]
class AuctionSnapshotTest extends TestCase
{
    public static function provideStatuses(): iterable
    {
        yield 'known' => ['WINNING_PERIOD', AuctionStatus::WinningPeriod];
        yield 'unknown' => ['UNKNOWN', null];
    }

    #[DataProvider('provideStatuses')]
    public function testGetStatus(string $status, ?AuctionStatus $expected): void
    {
        $auction = new AuctionSnapshot();
        $auction->status = $status;

        $this->assertSame($expected, $auction->getStatus());
    }

    public function testNoBids(): void
    {
        $auction = new AuctionSnapshot();
        $this->assertSame(0, $auction->bid_count);
    }
}
