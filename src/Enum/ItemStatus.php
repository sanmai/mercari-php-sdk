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
 * Item status, as reported by items and accepted by the status search filter.
 */
enum ItemStatus: string
{
    /** The item is on sale. */
    case OnSale = 'on_sale';

    /** The item has been purchased. */
    case Trading = 'trading';

    /** The trading for this item has ended. */
    case SoldOut = 'sold_out';

    /** The item has been suspended. */
    case Stop = 'stop';

    /** The item has been deleted. */
    case Cancel = 'cancel';

    /** The item has been deleted by an administrator. */
    case AdminCancel = 'admin_cancel';
}
