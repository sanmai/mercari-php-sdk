# Mercari API PHP SDK 

So, I wanted to snipe up a rare item from Mercari. If you ever tried to buy something rare, you know items go away in literal seconds. Almost inhumanly fast. And you guessed it, there's API to enable bots for sophisticated buyers. 

Mercari's API credentials are hard to come by, as Mercari is quite selective about who they grant it to. However, they also have a somewhat naive approach to internet security, requiring users to establish a local authenticated proxy server to allow their engineers to work with the API from the comfort of their homes. That's how yours truly gets to play with the API and buy something nice. In return, you get an API client, but please don't ask me for credentials or accesses. You'll need to figure that out through Mercari if you want to use this client.

This is a pretty complete Mercari API client, whipped up over a few holiday cocktails. It's working well for me right now, but hey, I'm just one person. Feel free to jump in and contribute! I'll probably tinker with it as I need stuff from Mercari, but this is open-source, baby! Add new methods, tweak things, make it your own. So I expect you, the user of this API client, to take the duty to be responsible for adding new methods, etc., with dignity and grace.

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
  
If you're not a fan of Mercari's IP restrictions (and honestly, who is?), you can either set up your own proxy server or, if you're feeling adventurous, try accessing the API directly. Your call!

### Proxy Server

Let's face it: Mercari's API security is a bit of a buzzkill. Their "one IP address per customer" rule is about as flexible as a brick wall, making life difficult for teams or anyone who needs to switch things up. Imagine trying to change your IP (like when you're spinning up new servers in the cloud) and having to wait weeks for Mercari to catch up. Yikes!

That's where a proxy server swoops in to save the day. It's like your own personal API bouncer, letting you change your IP address as often as you want without Mercari even noticing. Plus, you can share access with your team without jumping through hoops. Talk about a win-win for collaboration and sanity!

Mercari's lack of visibility into who's actually using their API is also a bit of a concern. It's like throwing a party with no guest list – anyone could show up. While probably unintentional, this lack of oversight on Mercari's part emphasizes the need for a proxy server with robust authentication on our end, especially for teams collaborating on projects.

Let's start this party with nginx, the proxy server extraordinaire. Setting it up is a breeze – just add a new location block to your config file:

```nginx
location / {
    proxy_pass http://actual-api-host.example.jp/; 
    # Lock it down with an IP allow list
    allow 192.168.1.0/24; # Allow a specific subnet
    allow 10.0.0.1;      # Allow a specific IP
    deny all;             # Deny all other IPs
}
```

This tells nginx to forward requests to your `actual-api-host.example.jp` server, but only from the IPs you've specified. You're in control, deciding who gets access and what they can do. Feel free to get fancy with your access restrictions – it's your party!

### SSH Tunnel

If you don't want to set up a dedicated proxy, fear not! SSH's dynamic tunnel feature is your secret weapon for secure access to the Mercari API. Just run this command:

```bash
ssh -fCND 1080 my-server.example.com
```

This opens a tunnel through your `my-server.example.com` server (replace with your actual server address), granting you access to the Mercari API as if you were right there on the server. It's a convenient way to bypass those pesky IP restrictions, especially when a full-fledged proxy setup isn't in the cards.

While an nginx proxy is great for basic IP allowlisting, an SSH tunnel takes it a step further. Your traffic is encrypted end-to-end, adding an extra layer of security compared to a plain proxy setup. This makes it a great choice for situations where you need to protect your API credentials or when you're working on a public or less secure network.

However, SSH tunnels have their quirks. Unstable connections can make them a bit finicky, and you'll need to configure your API client to use the tunnel in development but not in production. It's a trade-off between security and convenience.

So, if you're okay with a bit of extra configuration and don't mind the occasional connection hiccup, SSH tunnels offer a secure and flexible way to access the API from anywhere. 

