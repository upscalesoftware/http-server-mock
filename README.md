HTTP Server Mock of REST API [![Build Status](https://api.travis-ci.org/upscalesoftware/http-server-mock.svg?branch=master)](https://travis-ci.org/upscalesoftware/http-server-mock)
============================

This project implements an HTTP server that responds to recognized requests with static body and headers.
Virtually any part of a request, including headers, can be configured to influence a response being returned.
Mapping between requests and responses is declared in a JSON configuration file.

While being a general-purpose server, the project is particularly geared towards REST APIs.
It allows to stand up a fully operational fake API server for prototyping purposes in a matter of minutes.


**Features:**
- Match requests by method, URL, headers, body
- Respond with static headers and body
- Request/response body in external files
- JSON configuration file


## Installation

Install the project to a directory of your choice via [Composer](https://getcomposer.org/):
```bash
composer create-project --no-dev upscale/http-server-mock /tmp/http-server-mock
```


## Usage

The easiest way to launch the server is via the PHP's built-in web server:
```shell
php -S 127.0.0.1:8080 /tmp/http-server-mock/server.php
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080) in a browser to confirm the server is running.

Alternatively, use the HTTP server [Apache](https://httpd.apache.org/) 2.x with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) installed.
The project comes pre-configured to be deployed anywhere under the Document Root to start serving incoming requests.


## Configuration

Configuration defines rules of matching responses to requests by their properties, in the JSON format.

The server looks for the configuration file in the following locations:
1. Filename in environment variable `HTTP_SERVER_MOCK_CONFIG_FILE`
2. File `config.json` in the project root directory
3. File `config.json.dist` provided out of the box


### Format

The JSON configuration file has the following format:
```json
[
    {
        "request": {
            "method": "POST",
            "path": "/blog/posts",
            "params": {},
            "headers": {"Accept": "application/json", "Content-Type": "application/json"},
            "cookies": {},
            "body": "{\"title\":\"Hello World!\",\"body\":\"This is my first post!\"}"
        },
        "response": {
            "status": 201,
            "reason": "Created",
            "headers": {"Content-Type": "application/json"},
            "body": "{\"id\":\"1\",\"title\":\"Hello World!\",\"body\":\"This is my first post!\"}"
        }
    }
]
```

Majority of the properties are scalar. Properties `params`, `headers`, `cookies` are key-value pairs.

Virtually all of the properties are optional and can be omitted to not participate in the matching.

#### External Sources

In the configuration example above both request and response bodies are embedded into the configuration file.
While useful in some cases, it may bloat the file and create additional formatting challenges, such as escaping.

The configuration supports [references to external sources](http://www.php.net/wrappers), for instance:
```json
{
    "body": "file://%base_dir%/data/blog/posts/1/response.json"
}
```

The placeholder `%base_dir%` represents an absolute path to the server installation directory.

#### Request Format

The server takes into account format of a request body when matching requests.
Human-readable formats allow reasonable degree of freedom in the syntax to improve contents readability.
Variations in things like whitespaces and indentation are typically permitted by most formats.
Request body will be matched successfully regardless of the syntax variation in use.

The format is determined automatically from the request header `Content-Type`.
Mapping of common MIME types to supported formats is declared in `mime-types.php`.
Should the automatic detection not suffice, the format can be enforced in the configuration.

The configuration syntax to enforce the body format:
```json
{
    "request": {
        "format": "json"
    }
}
```

Supported formats: `binary`, `text`, `xml`, `html`, `json`.

The format `binary` is assumed by default.

#### Response Delay

Time needed for a real server to compute a response varies dramatically from one request to another.
For testing purposes it may be desired to emulate realistic response times for heavyweight operations.

The configuration allows to specify a fixed response delay, for instance:
```json
{
    "response": {
        "delay": 2000
    }
}
```

The delay duration is in milliseconds (1 second = 1000 milliseconds).


## Algorithm

For each incoming request the server reads the rules from the configuration file and evaluates them.
Rules are being evaluated against the request one by one in a declared order until the first match is found.
Once matched successfully, a response from the matched rule is returned and further processing stops.

Rule evaluation is done by checking an incoming request for presence of declared request properties.
In order to match, a request needs to contain matching values for all the declared properties.
For key-value properties, presence of all declared pairs is necessary.
Additional data not mentioned in the configuration is permitted.

**Note:** Rules with specific properties need to precede generic ones sharing the same subset of properties.
Otherwise, a generic rule will match before evaluation of a specific rule occurs, effectively suppressing the latter.

## Contributing

Pull Requests with fixes and improvements are welcome!

## License

Copyright © Upscale Software. All rights reserved.

Licensed under the [Apache License, Version 2.0](https://github.com/upscalesoftware/http-server-mock/blob/master/LICENSE.txt).
