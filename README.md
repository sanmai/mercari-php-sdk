# Mercari API PHP SDK 

This is a pretty complete Mercari API client. Feel free to jump in and contribute!

```
composer require sanmai/mercari-php-sdk
```

## Status of Implementation

 - [ ] Search Items (v3)
 - [ ] Get Item By ID
 - [ ] Get User By ID
 - [ ] Fetch Bulk Items by ID
 - [ ] Purchase Item
 - [ ] Similar Items
 - [ ] Get Transaction By TransactionID
 - [ ] Get Transaction By ItemID
 - [ ] Get Transaction Messages
 - [ ] Post Transaction Messages
 - [ ] Post Transaction Review
 - [ ] Get Todo List
 - [ ] Get Comments
 - [ ] Post Comment
 - [ ] Get Item Categories
 - [ ] Get Item Brands

## What You Need

Before you start messing with this API client, here's the lowdown on what you gotta have:

- Authority Hostname. In the examples, we'll pretend it's `proxy-auth.example.com` .
- "Open API" Hostname. This is where you actually talk to the Mercari API. We'll call it `proxy-api.example.com`. 
- API Credentials: You'll need a `client_id` and `client_secret` from Mercari.
  
Next, you can either set up your own proxy server or resort to a dynamic SSH tunnel. Your call!

### Proxy Server

Just add a new location block to your config file:

```nginx
location / {
    proxy_pass https://actual-api-host.example.jp/;
    proxy_ssl_server_name on;

    # Lock it down with an IP allow list
    allow 192.168.1.0/24; # Allow a specific subnet
    allow 10.0.0.1;      # Allow a specific IP
    deny all;             # Deny all other IPs
}
```

This tells nginx to forward requests to your `actual-api-host.example.jp` server, but only from the IPs you've specified. You're in control, deciding who gets access and what they can do.

### SSH Tunnel

If you don't want to set up a dedicated proxy, fear not! Just run this command to set up a dynamic SSH tunnel:

```bash
ssh -fCND 1080 my-server.example.com
```

This opens a tunnel through your `my-server.example.com` server (replace with your actual server address), granting you access to the Mercari API as if you were right there on the server.
