<?php
/**
 * Mercari PHP SDK
 * Copyright 2024 Alexey Kopytko
 *
 * Mercari PHP SDK is free software: you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 2 of the License, or (at your option) any later version.
 *
 * Mercari PHP SDK is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Mercari PHP SDK. If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace Mercari;

use GuzzleHttp\Client;

/**
 * Main API Client.
 */
class MercariClient
{
    private const ITEM_PREFIX = 'm';

    private const SEARCH_ITEMS_V3 = '/v3/items/search';

    private const ITEMS = '/v1/items/fetch';

    private const ITEM = '/v1/items/%s';

    private const ITEM_COMMENTS = '/v1/items/%s/comments';

    private const USER = '/v1/users/%s';

    private const SIMILAR_ITEMS = '/v1/similar_items/%s';

    private const TODO_LIST = '/v1/todolist';

    private const PURCHASE = '/v1/items/purchase';

    private const TRANSACTION = '/v1/transactions/%s';

    private const TRANSACTION_ITEM = '/v2/transactions/%s';

    private const TRANSACTION_MESSAGES = '/v2/transactions/%s/messages';

    private const TRANSACTION_REVIEW = '/v1/transactions/%s/post_review';

    private const CATEGORIES = '/v1/master/item_categories';

    public const MARKETPLACE_MERCARI = 1;

    public const MARKETPLACE_SHOP = 2;

    public const MARKETPLACE_ALL = 3;
}
