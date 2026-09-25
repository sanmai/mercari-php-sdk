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
 * This enum defines the item statuses that items report and that the status
 * search filter accepts.
 */
enum ItemStatus: string
{
    case OnSale = 'on_sale';

    /** A buyer purchased the item. */
    case Trading = 'trading';

    /** The transaction for the item is complete. */
    case SoldOut = 'sold_out';

    /** The item is suspended. */
    case Stop = 'stop';

    /** The item is deleted. */
    case Cancel = 'cancel';

    /** An administrator deleted the item. */
    case AdminCancel = 'admin_cancel';
}
