---
layout: post
title:  How to replace Logstash by Heka with Symfony2
---

I guess everybody know Logstash, the _L_ letter in the ELK stack.

But I SensioLabs (my company) we prefer [hekad](https://hekad.readthedocs.org/en/v0.7.1/)
See [this comment](https://blog.mozilla.org/services/2013/04/30/introducing-heka/comment-page-1/#comment-163))
to understand why.

So basically, our stack look like that:

* Some application servers (Symfony2 + nginx)
* Some Elasticsearch servers

So on each applications servers, we use a lot Symfony2 + Monolog. Each
application send logs over udp to a local daemon (heka). Then this daemon send
all logs to another heka daemon on the ES server over tcp. Finally, the last
daemon send all logs to ES. Of course, you can filter everything.

Why are we using 2 daemons instead of one? To handle failures, and to not
penalize the frontend with log.

Here is the configuration of all part:

Symfony:

```yaml
# config.yml
monolog:
    handlers:
        socket:
            type: socket
            connection_string: udp://127.0.0.1:5565
```

The default `Monolog/SocketHandler` outputs a formated string. As it's harder to
parse than a regular JSON and if you are not already relying on this feature,
your can safely change the class of this handler. (If not, just create a new
Symfony service):

```
#config.yml
parameters:
    monolog.handler.socket.class: Foo\Bar\SocketHandler
```

```php
<?php

namespace Foo\Bar

use Monolog\Handler\SocketHandler as MonologSocketHandler;

class SocketHandler extends MonologSocketHandler
{
    protected function generateDataStream($record)
    {
        $r = $record;
        unset($r['formatted']);

        return json_encode($r)."\n";
    }
}
```

Here we go, now Symfony2 is sending all log over udp.

Now, let's setup the heka agent:

```
# install that in /etc/heka/agent.toml
[hekad]
maxprocs = 4

# Input

## Application

[application_log]
type = "UdpInput"
address = ":5565"
parser_type = "token"
decoder = "monolog_decoder"

[monolog_decoder]
type = "SandboxDecoder"
script_type = "lua"
filename = "/path/to/monolog.lua"

    [monolog_decoder.config]
    # This is just some option, that will be added to the payload
    # sent to the second daemon, then to ES
    type = "application.log"
    face = "frontend"
    server = "frontend-1"

# Output

## hekad router

[aggregator_output]
type = "TcpOutput"
address = "your.elasticsearch.server:5569"
keep_alive = true
message_matcher = "Logger != 'hekad'"
```

And, because heka does not know monolog, you have to add a new monolog decoder:

```lua
# install that in /path/to/monolog.lua
-- Sample input:
-- {
--     "message": "Reloading user from user provider.",
--     "context": [
--     ],
--     "level": 100,
--     "level_name": "DEBUG",
--     "channel": "security",
--     "datetime": {
--         "date": "2014-05-23 14:19:19",
--         "timezone_type": 3,
--         "timezone": "Europe\/Paris"
--     },
--     "extra": {
--         "url": "\/app_dev.php\/",
--         "ip": "127.0.0.1",
--         "http_method": "GET",
--         "server": "connect.product.localhost",
--         "referrer": null
--     }
-- }

require "string"
require "cjson"

local msg_type = read_config("type")
local msg_hostname = read_config("hostname")
local msg_facet = read_config("facet")
local msg_server = read_config("server")

local date_pattern = '^(%d+-%d+-%d+) (%d+:%d+:%d+)'
local severity_map = {
    DEBUG = 7,
    INFO = 6,
    NOTICE = 5,
    WARNING = 4,
    ERROR = 3,
    CRITICAL = 2,
    ALERT = 1,
    EMERGENCY = 0
}

local msg = {
    Timestamp   = nil,
    Type        = msg_type,
    Payload     = nil,
    Fields      = {}
}

function process_message()
    local json = cjson.decode(read_message("Payload"))

    if not json then
        return -1
    end

    msg.Payload = cjson.encode(json)

    msg.Severity = severity_map[json.level_name]

    msg.Logger = json.channel

    -- WARNING, the TZ must be UTC
    local d, t = string.match(json.datetime.date, date_pattern)
    if d then
        msg.Timestamp = string.format("%sT%sZ", d, t)
    end

    msg.Hostname = msg_hostname

    -- In order to use native Heka Id, it should be of type "raw bytes or
    -- RFC4122 string representation"
    if json.extra.uuid then
        msg.Fields.UUID = json.extra.uuid
    end
    msg.Fields.Message = json.message
    msg.Fields.SeverityText = json.level_name
    if msg_facet then
        msg.Fields.facet = msg_facet
    end
    if msg_server then
        msg.Fields.server = msg_server
    end

    inject_message(msg)

    return 0
end
```

And finally, set-up the second daemon on the ES machine:

```
# install that in /etc/heka/router.toml
[hekad]
maxprocs = 4

# Input

## TCP

[router]
type = "TcpInput"
address = "0.0.0.0:5569"
parser_type = "message.proto"
decoder = "ProtobufDecoder"
keep_alive = true

# Output

## ElasticSearch

[ESLogstashV0Encoder]
type_name = "%{Type}"

[ElasticSearchOutput]
message_matcher = "Logger != 'hekad'"
encoder = "ESLogstashV0Encoder"
server = "http://127.0.0.1:9200"
flush_interval = 50
```

And now, you can browse your kibana dashboard and enjoy the result.
