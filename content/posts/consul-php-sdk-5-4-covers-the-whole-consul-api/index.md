---
title: 'consul-php-sdk 5.4 covers the whole Consul API'
description: 'Almost twelve years after I started it, consul-php-sdk covers the whole Consul CE HTTP API: from 6 services to 17, and from 34 public methods to 141.'
date: '2026-09-29'
habitat: [PHP, Consul]
latin: 'Consul universalis'
style: branch
signoff: "And that's all!"
---

I started consul-php-sdk in November 2014. Almost twelve years later, version 5.4 finally covers the whole Consul CE HTTP API: it went from 6 services to 17, and from 34 public methods to 141.

Why now? I don't use Consul in production anymore, but I have an Ultra Claude plan to do open source.

{{< note >}}Yes, an SDK for a tool I don't run anymore.{{< /note >}}

## What is consul-php-sdk?

It's a thin PHP wrapper around the Consul HTTP API, built on top of Symfony HttpClient. No magic here:

```php
$kv = new Consul\Services\KV();

$kv->put('config/feature-flag', 'enabled');

echo $kv->get('config/feature-flag', ['raw' => true])->getBody(); // enabled
```

Until now, it covered `Agent`, `Catalog`, `Health`, `KV`, `Session` and `TXN`. So you had the key/value store and the service discovery. For anything else, you were on your own.

Version 5.4 adds 11 services. Let's see some of them.

## How to back up a cluster?

`Snapshot` saves the whole state of your cluster, and restores it:

```php
$snapshot = new Consul\Services\Snapshot();

file_put_contents('backup.snap', $snapshot->save()->getBody());

// Later...
$snapshot->restore(file_get_contents('backup.snap'));
```

## How to manage ACLs?

`ACL` handles the tokens, the policies and the roles. For example, to create a token that can only read your configuration:

```php
$acl = new Consul\Services\ACL();

$policy = $acl->createPolicy([
    'Name' => 'read-config',
    'Rules' => 'key_prefix "config/" { policy = "read" }',
])->json();

$token = $acl->createToken([
    'Description' => 'Read the configuration',
    'Policies' => [['ID' => $policy['ID']]],
])->json();

echo $token['SecretID'];
```

## How to query your services?

`PreparedQuery` stores a query in Consul, and you run it by its name:

```php
$query = new Consul\Services\PreparedQuery();

$query->create([
    'Name' => 'api',
    'Service' => ['Service' => 'api', 'OnlyPassing' => true],
]);

$nodes = $query->execute('api')->json()['Nodes'];
```

## What about the service mesh?

`Connect` manages the intentions, and `Config` the configuration entries:

```php
$connect = new Consul\Services\Connect();

$connect->upsertIntention('web', 'api', ['Action' => 'allow']);

$connect->checkIntention('web', 'api')->json(); // ['Allowed' => true]

$config = new Consul\Services\Config();

$config->apply(['Kind' => 'service-defaults', 'Name' => 'api', 'Protocol' => 'http']);
```

## And the rest?

`Event` fires custom events through your cluster:

```php
$event = new Consul\Services\Event();

$event->fire('deploy', 'v1.2.3');
```

`Status` gives you the leader and the peers, and `Operator` covers Raft, Autopilot and the keyring. `Coordinate`, `DiscoveryChain` and `Peering` complete the list.

And `Agent`, `Catalog` and `Health` got all their missing endpoints.

## What else changed?

All the documented options are now allowed: blocking queries, consistency modes, filtering, and so on. Before, an option missing from the list of a method was silently dropped. And you can pass many values for an option:

```php
$health = new Consul\Services\Health();

$health->service('api', ['tag' => ['v1', 'primary']]);
```

The `dc` option now works on all endpoints that are not local to the agent. And the ACL token can be read from the `CONSUL_HTTP_TOKEN` environment variable, like the Consul CLI does.

I also fixed a few bugs:

- `MultiSemaphore` reused the metadata of a previous resource when acquiring many resources
- `Session::create()`, and so `LockHandler`, failed when called without arguments
- `Agent::join()` and `Agent::forceLeave()` used `GET` instead of `PUT`

## How to upgrade?

Version 5.4 requires PHP 8.2, Symfony HttpClient 6.4, 7.4 or 8.1+, and psr/log 2 or 3.

## Conclusion

The library stays thin: it still only depends on `symfony/http-client` and `psr/log`. But now, if Consul can do it, you can do it from PHP.
