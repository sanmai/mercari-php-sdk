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
 * Shipping status of a flea-market transaction.
 *
 * The last four apply to the Mercari shipping services only. The API does not
 * publish the full set, so use tryFrom() and expect a null for a status that
 * is not listed here.
 */
enum ShippingStatus: string
{
    case WaitShipping = 'wait_shipping';

    case Shipping = 'shipping';

    case Shipped = 'shipped';

    case Done = 'done';

    case Publish = 'publish';

    case Filling = 'fillin';

    case FixSize = 'fix_size';

    case WaitPickup = 'wait_pickup';

    case HandOver = 'hand_over';
}
