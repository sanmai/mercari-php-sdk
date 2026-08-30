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

use Mercari\Enum\ShippingStatus;

/**
 * @covers \Mercari\Enum\ShippingStatus
 */
class ShippingStatusTest extends BackedEnumTestCase
{
    public function enumClass(): string
    {
        return ShippingStatus::class;
    }

    public function expectedValues(): array
    {
        return [
            'WaitShipping' => 'wait_shipping',
            'Shipping' => 'shipping',
            'Shipped' => 'shipped',
            'Done' => 'done',
            'Publish' => 'publish',
            'Filling' => 'fillin',
            'FixSize' => 'fix_size',
            'WaitPickup' => 'wait_pickup',
            'HandOver' => 'hand_over',
        ];
    }
}
