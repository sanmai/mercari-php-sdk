# Mercari API PHP SDK

A pretty complete PHP client for the Mercari API. Feel free to jump in and contribute!

```bash
composer require sanmai/mercari-php-sdk
```

Requires PHP 8.2 or newer.

Please note that this is not an official SDK but rather an independent, community-maintained client, so Mercari folks most likely won't be able to answer questions about it.

**Something amiss?** [Open an issue](https://github.com/sanmai/mercari-php-sdk/issues/new), or, even better, send a PR!

## Status of Implementation

- [x] Search Items (v3)
- [x] Get Item By ID
- [x] Fetch Bulk Items By ID
- [x] Similar Items
- [x] Get User By ID
- [x] Purchase Item
- [x] Get Transaction By Transaction ID
- [x] Get Transaction By Item ID
- [x] Get Transaction Messages
- [x] Post Transaction Message
- [x] Post Transaction Review
- [x] Get Todo List
- [x] Get Comments
- [x] Post Comment
- [x] Get Item Categories
- [x] Webhook Signature Validation
- [ ] Get Item Brands
- [ ] Accept Transaction
- [ ] Reject Transaction
- [ ] Return Transaction
- [ ] Get Shops Order
- [ ] Get Partner Offers
- [ ] Update Additional Service Status

## Overview

There are three kinds of objects you work with:

- **Clients.** `MercariAuthClient` acquires access tokens; `MercariClient` sends every other request. You build each with a static `createInstance()` factory.
- **Requests.** Objects like `SearchRequest`, `PurchaseRequest`, and `TokenRequest` carry the parameters of a call. They expose properties directly and, where it helps, named constructors and a fluent interface.
- **Responses.** Most calls return a typed response or DTO (data transfer object). List responses (`SearchResponse`, `ItemsResponse`, `MessagesResponse`, and so on) are both iterable and countable, so you can `foreach` over them or pass them to `count()`. The DTO and response classes under `src/` are the reference for the fields each response carries.

## What You Need

Before you start messing with this API client, here's the lowdown on what you gotta have:

- **Authority Hostname.** In the examples, we'll pretend it's `proxy-auth.example.com`.
- **"Open API" Hostname.** This is where you actually talk to the Mercari API. We'll call it `proxy-api.example.com`.
- **API Credentials.** You'll need a `client_id` and `client_secret` from Mercari.

Next, you can either set up your own proxy server or resort to a dynamic SSH tunnel. Your call!

### Proxy Server

Add a new location block to your config file:

```nginx
location / {
    proxy_pass https://actual-api-host.example.jp/;
    proxy_ssl_server_name on;

    # Lock it down with an IP allow list
    allow 192.168.1.0/24; # Allow a specific subnet
    allow 10.0.0.1;       # Allow a specific IP
    deny all;             # Deny all other IPs
}
```

This block tells nginx to forward requests to your `actual-api-host.example.jp` server, but only from the IPs you've specified. You're in control, deciding who gets access and what they can do.

### SSH Tunnel

If you don't want to set up a dedicated proxy, fear not! Run this command to set up a dynamic SSH tunnel:

```bash
ssh -vCND 1080 my-server.example.com
```

This command opens a tunnel through your `my-server.example.com` server (replace with your actual server address), granting you access to the Mercari API as if you were on the server itself.

## Usage

The API has two tiers, each unlocked by a separate authentication flow. Start with **client credentials** to browse and read; then use the **user flow** to make purchases or act as a specific Mercari user. Everything in Part 1 works without ever having to build the OAuth2 redirect dance.

## Part 1: Client Credentials

The client-credentials flow needs only your `client_id` and `client_secret`. It unlocks searching, item and user lookups, similar items, reading comments, and categories. No user login, no redirects.

### Building a Client

```php
$authClient = Mercari\MercariAuthClient::createInstance(
    'proxy-auth.example.com',
    'client_id',
    'client_secret'
);

// Client-credentials flow (the default when no request is given)
$token = $authClient->getToken();

$client = Mercari\MercariClient::createInstance(
    'proxy-api.example.com',
    $token->access_token
);
```

The rest of Part 1 assumes you have this `$client`.

### Searching for Items

Set the properties you care about on a `SearchRequest`, then iterate the response:

```php
$request = new Mercari\SearchRequest();
$request->keyword = 'Nintendo Switch';
$request->price_min = 10000;
$request->price_max = 30000;
$request->limit = 20;

$response = $client->search($request);

echo count($response), " items found\n";

foreach ($response as $item) {
    echo "{$item->id}\t{$item->name}\n";
}
```

Searches use Mercari's flea market by default. Use the fluent helpers to search Shops or both marketplaces:

```php
$request = (new Mercari\SearchRequest())->searchShopsOnly();
// or ->searchMercariOnly(), or ->searchBothMarketplaces()
```

Results are paginated. `->meta` reports the total and whether more pages exist; advance by raising the request's `page`, which is zero-indexed (the first page is `0`):

```php
$request = new Mercari\SearchRequest();
$request->keyword = 'Nintendo Switch';
$request->page = 0;

do {
    $response = $client->search($request);

    foreach ($response as $item) {
        echo "{$item->id}\t{$item->name}\n";
    }

    $request->page++;
} while ($response->meta->has_next);

echo "{$response->meta->num_found} items in total\n";
```

### Fetching Items

Fetch a single item by ID. When the item does not exist, `item()` returns `null`:

```php
$item = $client->item('m1234567890');

if ($item === null) {
    echo "Item not found\n";
    return;
}

echo "{$item->name}: {$item->status}\n";
```

Fetch several items at once, or find items similar to a given one:

```php
$items = $client->items(['m1111111111', 'm2222222222']);

$similar = $client->similarItems('m1234567890');
```

### Looking Up a User

`user()` returns a `Seller` profile, or `null` if there's no such user:

```php
$seller = $client->user('123456');

if ($seller === null) {
    echo "Seller not found\n";
    return;
}

echo "{$seller->name}: {$seller->num_sell_items} items, {$seller->num_ratings} ratings\n";
```

### Reading Comments

```php
foreach ($client->itemComments('m1234567890') as $comment) {
    echo "{$comment->comments}\n";
}
```

### Categories

```php
$categories = $client->categories();
```

## Part 2: Acting as a User

Purchasing, your transactions and their messages, reviews, your todo list, and posting comments all act in the context of a specific Mercari user, so they need a user access token from the OAuth2 **authorization-code** flow rather than client credentials. Purchasing in particular requires it.

### The Authorization-Code Flow

Send the user to Mercari's login page, then exchange the returned code for a token pair:

```php
// 0. Make sure a session is running before touching $_SESSION (both requests need this).
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// 1. Generate a random state (CSRF guard), persist it in the session, and redirect.
$expectedState = bin2hex(random_bytes(16));

$_SESSION['mercari_oauth_state'] = $expectedState;

$request = Mercari\TokenRequest::loginUrl(
    'https://your-app.example.com/callback', // your redirect URL
    $expectedState,                          // state, echoed back to you
    bin2hex(random_bytes(16))                // nonce, required by the endpoint
);

header(sprintf('Location: %s', $authClient->getAuthUrl($request)));
return;
```

The callback is a separate request.

```php
// 2. On your callback, confirm the returned state matches the one you issued
//    before trusting the code. Use a constant-time comparison to avoid timing leaks:
$expectedState = $_SESSION['mercari_oauth_state'];
unset($_SESSION['mercari_oauth_state']); // single use

if (!hash_equals($expectedState, $_GET['state'] ?? '')) {
    throw new RuntimeException('State mismatch: possible CSRF, discard this callback');
}

$request = Mercari\TokenRequest::authorizationCode(
    'https://your-app.example.com/callback',
    $_GET['code']
);

// 3. Persist this token for the next request.
$userToken = $authClient->getToken($request);

// 4. Refresh the token pair whenever it expires
$userToken = $authClient->getToken(
    Mercari\TokenRequest::refreshToken($userToken)
);
```

A `TokenResponse` carries everything you need to keep a session alive: `access_token`, `refresh_token`, `expires_in` (seconds), and `ts` (when the token was issued). Persist it, and refresh only once it's about to expire rather than on every request:

```php
if ($savedToken->ts + $savedToken->expires_in <= time() + 60) {
    $savedToken = $authClient->getToken(
        Mercari\TokenRequest::refreshToken($savedToken)
    );
    // persist $savedToken for next time
}
```

Build a `$client` from the user's `access_token`, exactly as in Part 1:

```php
$client = Mercari\MercariClient::createInstance('proxy-api.example.com', $userToken->access_token);
```

### Purchasing an Item

Build a `PurchaseRequest` from an item you've just fetched, fill in the buyer and delivery details, then submit it. Constructing the request from an `ItemDetail` copies over the item ID, checksum, and - where applicable - the sole variant, coupon, and shipping fee:

```php
$item = $client->item('m1234567890');

$request = new Mercari\PurchaseRequest($item);
$request->family_name      = 'Yamada';
$request->first_name       = 'Taro';
$request->family_name_kana = 'ヤマダ';
$request->first_name_kana  = 'タロウ';
$request->telephone        = '09012345678';
$request->zip_code1        = '100';
$request->zip_code2        = '0001';
$request->prefecture       = 'Tokyo';
$request->city             = 'Chiyoda';
$request->address1         = '1-1';
$request->address2         = 'Mercari Heights 101';
$request->delivery_identifier = "purchase-{$item->id}";

$response = $client->purchase($request);

if ($response->isSuccess()) {
    echo "Purchased\n";
}
```

The constructor only auto-selects a variant when the item has exactly one. For an item with several variants, set `$request->variant_id` yourself (Mercari Shops purchases also expect `$request->shops_shipping_fee`). `delivery_identifier` is an optional identifier included with the delivery address; the example above tags it with the item ID. The checksum ties the request to a specific item snapshot, so fetch the item immediately before purchasing.

### Transactions and Messaging

Look up a transaction by its own ID or by the item ID, read and post messages, and leave a review:

```php
$transaction = $client->transaction('t1234567890');
// or by item: $client->itemTransaction('m1234567890');

foreach ($client->transactionMessages('t1234567890') as $message) {
    echo "{$message->body}\n";
}

$client->transactionMessage('t1234567890', 'Thank you, shipping today!');

// Leave a review; the rating is "good" (default) or "bad"
$client->transactionReview('t1234567890', 'Great buyer!');
```

### Your Todo List

Your outstanding todos (items awaiting shipment, unread messages, and so on) come from `todoList()`, which pages through a `next_page_token`:

```php
$pageToken = '';

do {
    $response = $client->todoList(limit: 50, page_token: $pageToken);

    foreach ($response as $todo) {
        echo "{$todo->message}\n";
    }

    $pageToken = $response->next_page_token;
} while ($pageToken !== '');
```

### Posting a Comment

A comment is posted as the signed-in user:

```php
$client->addComment('m1234567890', 'Is this still available?');
```

## Working With Any Call

These apply whichever flow built your `$client`.

### Errors and Missing Resources

Methods that fetch a single resource - `item()`, `user()`, `transaction()`, `itemTransaction()` - return `null` when it isn't found rather than throwing; list methods return an empty, iterable response.

Write actions report problems in two ways. `purchase()` returns a `PurchaseResponse` even when the purchase is declined, so check `isSuccess()` and inspect `transaction_status`. A rejected review instead throws `Mercari\DTO\Exception`. Genuine transport or server errors surface as Guzzle `RequestException`s. The `Failure` and `FailureDetails` DTOs describe the error payload the API returns.

Catch the two throwing paths separately: a `Mercari\DTO\Exception` means the API accepted the request but refused the action (its message holds the reason), while a Guzzle `RequestException` is a transport- or HTTP-level failure you can interrogate for a status code:

```php
use GuzzleHttp\Exception\RequestException;
use Mercari\DTO\Exception as MercariException;

try {
    $client->transactionReview('t1234567890', 'Great buyer!');
} catch (MercariException $e) {
    // Accepted by the API, but the action itself was rejected
    echo "Review rejected: {$e->getMessage()}\n";
} catch (RequestException $e) {
    // Transport or HTTP-level failure
    echo "Request failed (HTTP {$e->getResponse()?->getStatusCode()})\n";
}
```

### Client Configuration

Both `createInstance()` factories take optional `$extraHeaders` and `$retryOptions` arrays after their required arguments - use them to send extra headers (a custom User-Agent, say) or tune the bundled retry middleware. The factory signatures in `src/` list the defaults.

### Debug Logging

`MercariClient` accepts any PSR-3 logger through `setLogger()`, which logs full request and response bodies - handy while you're experimenting:

```php
$client->setLogger($psrLogger);
```


## Development

To run all tests:

```bash
make -j -k
```
