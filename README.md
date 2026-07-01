# Mercari API PHP SDK

A pretty complete PHP client for the Mercari API. Feel free to jump in and contribute!

```bash
composer require sanmai/mercari-php-sdk
```

Please note this is _not_ an official SDK. It is an independent, community-maintained
client, so Mercari most likely won't be able to answer questions about it. **Something
amiss?** [Open an issue](https://github.com/sanmai/mercari-php-sdk/issues/new), or, even
better, send a PR!

## Overview

There are three kinds of objects you work with:

- **Clients.** `MercariAuthClient` acquires access tokens; `MercariClient` sends every
  other request. You build each with a static `createInstance()` factory.
- **Requests.** Objects like `SearchRequest`, `PurchaseRequest`, and `TokenRequest`
  carry the parameters of a call. They expose properties directly and, where it helps,
  named constructors and a fluent interface.
- **Responses.** Every call returns a typed response or DTO. List responses
  (`SearchResponse`, `ItemsResponse`, `MessagesResponse`, and so on) are both iterable
  and countable, so you can `foreach` over them or pass them to `count()`.

## What You Need

Before you start messing with this API client, here's the lowdown on what you gotta have:

- **Authority Hostname.** In the examples, we'll pretend it's `proxy-auth.example.com`.
- **"Open API" Hostname.** This is where you actually talk to the Mercari API. We'll
  call it `proxy-api.example.com`.
- **API Credentials.** You'll need a `client_id` and `client_secret` from Mercari.

Next, you can either set up your own proxy server or resort to a dynamic SSH tunnel.
Your call!

### Proxy Server

Just add a new location block to your config file:

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

This tells nginx to forward requests to your `actual-api-host.example.jp` server, but
only from the IPs you've specified. You're in control, deciding who gets access and what
they can do.

### SSH Tunnel

If you don't want to set up a dedicated proxy, fear not! Just run this command to set up
a dynamic SSH tunnel:

```bash
ssh -fCND 1080 my-server.example.com
```

This opens a tunnel through your `my-server.example.com` server (replace with your
actual server address), granting you access to the Mercari API as if you were right
there on the server.

## Usage

### Authentication

Every API call needs an access token. The simplest way to get one is the
**client-credentials** flow, which is enough for read-only, unauthenticated calls:

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

To act on behalf of a user, use the **authorization-code** flow. First, send the user to
Mercari's login page, then exchange the returned code for a token pair:

```php
// 1. Build a login URL and redirect the user to it
$request = Mercari\TokenRequest::loginUrl(
    'https://your-app.example.com/callback', // your redirect URL
    'csrf-token',                            // state, echoed back to you
    'nonce'
);

header('Location: ' . $authClient->getAuthUrl($request));

// 2. On your callback, after validating the state, exchange the code for a token pair
$request = Mercari\TokenRequest::authorizationCode(
    'https://your-app.example.com/callback',
    $_GET['code']
);
$userToken = $authClient->getToken($request);

// 3. Refresh the token pair whenever it expires
$userToken = $authClient->getToken(
    Mercari\TokenRequest::refreshToken($userToken)
);
```

The rest of the examples assume you have a `$client` built from an access token.

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

Searches cover both the flea market and Mercari Shops by default. Narrow it down with the
fluent helpers:

```php
$request = (new Mercari\SearchRequest())->searchShopsOnly();
// or ->searchMercariOnly(), or ->searchBothMarketplaces()
```

### Fetching Items

Fetch a single item by ID. When the item does not exist, `item()` returns `null`:

```php
$item = $client->item('m1234567890');

if ($item === null) {
    echo "Item not found\n";
} else {
    echo "{$item->name}: {$item->status}\n";
}
```

Fetch several items at once, or find items similar to a given one:

```php
$items = $client->items(['m1111111111', 'm2222222222']);

$similar = $client->similarItems('m1234567890');
```

### Purchasing an Item

Build a `PurchaseRequest` from an item you've fetched, fill in the buyer and delivery
details, then submit it. Constructing the request from an `ItemDetail` copies over the
item ID, checksum, and — where applicable — the sole variant, coupon, and shipping fee:

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

$response = $client->purchase($request);

if ($response->isSuccess()) {
    echo "Purchased\n";
}
```

### Transactions and Messaging

Look up a transaction by its own ID or by the item ID, read and post messages, and leave
a review:

```php
$transaction = $client->transaction('t1234567890');
// or by item: $client->itemTransaction('m1234567890');

foreach ($client->transactionMessages('t1234567890') as $message) {
    echo "{$message->body}\n";
}

$client->transactionMessage('t1234567890', 'Thank you, shipping today!');

// Leave a review; the default rating is "good"
$client->transactionReview('t1234567890', 'Great buyer!');
```

Your outstanding to-dos (items awaiting shipment, unread messages, and so on) come from
`todoList()`:

```php
foreach ($client->todoList() as $todo) {
    echo "{$todo->message}\n";
}
```

### Comments and Categories

```php
foreach ($client->itemComments('m1234567890') as $comment) {
    echo "{$comment->comments}\n";
}

$client->addComment('m1234567890', 'Is this still available?');

$categories = $client->categories();
```

### Not-Found Handling

Methods that fetch a single resource — `item()`, `user()`, `transaction()`,
`itemTransaction()` — return `null` when the resource is not found rather than throwing.
List methods return an empty, iterable response in the same situation. Genuine transport
or server errors surface as Guzzle `RequestException`s, and failed reviews throw a
`Mercari\DTO\Exception`.

### Debug Logging

Both clients accept any PSR-3 logger through `setLogger()`, which logs full request and
response bodies — handy while you're experimenting:

```php
$client->setLogger($psrLogger);
```

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

## Development

To run all tests:

```bash
make -j -k
```
