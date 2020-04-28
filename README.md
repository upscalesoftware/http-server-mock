[![Build Status](https://api.travis-ci.org/upscalesoftware/http-server-mock.svg?branch=master)](https://travis-ci.org/upscalesoftware/http-server-mock)

HTTP Server Mock for REST API Prototyping
=========================================

This project implements an HTTP server that responds to recognized requests with static body and headers.
Virtually any part of a request, including headers, can be configured to influence a response being returned.
Mapping between requests and responses is declared in a JSON configuration file.

While being a general-purpose server, the project is particularly geared towards REST APIs.
Operational fake API server can be stood up in a matter of minutes.


## Features

**The server allows to:**

* Serve static response body and headers for requests with matching properties

* Match requests by: HTTP method, URI path, URI query params, HTTP headers (including cookies), body contents

* Configure requests and responses in the JSON configuration file

* Factor out request/response body from the configuration file to an external file


## Installation

### System Requirements

* Interpreter [PHP](http://www.php.net/) 5.6 or newer

* Dependency manager [Composer](https://getcomposer.org/)

### Installation via CLI

Install the _latest stable version_ to a directory of your choice (say, `/tmp/http-server-mock`):

```shell
composer create-project --no-dev upscale/http-server-mock /tmp/http-server-mock
```

**Note:** The instructions above assume the [global Composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` for your setup.


## Running the Server

The easiest way to launch the server is via the PHP's built-in web server:
```shell
php -S 127.0.0.1:8080 /tmp/http-server-mock/server.php
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080) in a browser to confirm the server is running.

Alternative approach is to use the HTTP server [Apache](https://httpd.apache.org/) 2.x with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) installed.
The project comes pre-configured to be deployed anywhere under the Document Root to start serving incoming requests.


## Configuration

The configuration defines rules of matching responses to requests by their properties.
The rules are declared in a JSON file.

### Configuration Location

The server looks for the configuration file in the following places:

1. Filename in environment variable `HTTP_SERVER_MOCK_CONFIG_FILE`, if defined

2. File `config.json` in the project root directory, if exists

3. File `config.json.dist` provided out of the box


### Configuration Format

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

Virtually all of the properties are optional and can be omitted to not participate in matching.

#### External Sources

In the configuration example above request and response bodies are embedded into the configuration file.
While useful in some cases, it may bloat the file and create additional formatting challenges, such as escaping.

The configuration supports [references to external sources](http://www.php.net/wrappers), for instance:
```json
{
    "body": "file://%base_dir%/data/blog/posts/1/response.json"
}
```

The placeholder `%base_dir%` represents an absolute path to the project root directory.

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

#### Delaying Response

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


## Request Handling Algorithm

For each incoming request the server reads the rules from the configuration file and evaluates them.
Rules are being evaluated against the request one by one in a _declared order_ until the first match is found.
Once matched successfully, a response from the matched rule is returned and further processing stops.

Rule evaluation is done by checking an incoming request for presence of declared request properties.
In order to match, the request needs to contain matching values for _at least all_ of the properties.
For key-value properties presence of all of the declared pairs in the request is necessary.
Presence of additional data not mentioned in the configuration is permitted and has no effect.

**Note:** Rules with specific properties need to precede generic ones sharing the same subset of properties.
Otherwise, a generic rule will match before evaluation of a specific rule occurs, effectively suppressing the latter.


## License

Licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).
