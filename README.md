HTTP Server Mock for REST API Prototyping
=========================================

This project implements a HTTP server that responds to recognized requests with static body and headers.
Virtually any part of a request, including headers, can be configured to influence a response being returned.
Mapping rules between requests and responses is declared in a JSON configuration file.

While being a general-purpose server, the project is particularly geared towards REST APIs.
REST API of any complexity can be declared in the configuration with no programming involved.


## Features

**The server allows to:**

* Serve static response body and headers for requests with matching properties

* Match requests by the following properties: HTTP method, URI path, URI query params, HTTP headers, body contents

* Configure requests and responses in the JSON configuration file

* Factor out request/response body from the configuration file to an external file


## Installation

**System requirements:**

* Interpreter [PHP](http://www.php.net/) 5.4 or newer

* Dependency manager [Composer](https://getcomposer.org/)

**Installation commands:**

1. Download the source code:

    ```shell
    cd /tmp
    curl -sL https://github.com/upscalesoftware/http-server-mock/archive/latest.tar.gz | tar xz
    cd http-server-mock-latest
    ```

2. Install dependencies:

    ```shell
    php composer.phar install
    ```


## Running the Server

The easiest way to launch the server is via the PHP built-in web server:
```shell
php -S 127.0.0.1:8080 server.php
```

Open [http://127.0.0.1:8080](http://127.0.0.1:8080) in a browser to confirm the server is running.

Alternative approach is to use the HTTP server [Apache](https://httpd.apache.org/) 2.x with [mod_rewrite](http://httpd.apache.org/docs/current/mod/mod_rewrite.html) installed.
The project comes pre-configured to be deployed anywhere under the Document Root to start serving incoming requests.


## Configuration

The server looks for the configuration file `config.json` in the project root directory.
If it does not exist, the server falls back to `config.json.dist` provided out of the box.
The configuration defines rules of matching responses to requests by their properties.

**Configuration format:**
```json
[
    {
        "request": {
            "method": "POST",
            "path": "/blog/posts",
            "params": {},
            "headers": {"Accept": "application/json", "Content-Type": "application/json"},
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

Majority of the properties are scalar, `params` and `headers` are key-value pairs.

Virtually all of the properties are optional and can be omitted to not participate in matching.

### External Sources

In the configuration example above request and response bodies are embedded into the configuration file.
While useful in some cases, it may bloat the file and create additional formatting challenges, such as escaping.

The configuration supports [references to external sources](http://www.php.net/wrappers), for instance:
```json
{
    "body": "file://%base_dir%/data/blog/posts/new/response.json"
}
```

The placeholder `%base_dir%` represents an absolute path to the project root directory.


## Request Handling Algorithm

For each incoming request the server reads the rules from the configuration file and evaluates them.
Rules are being evaluated against the request one by one in a _declared order_ until the first match is found.
Once matched successfully, a response from the matched rule is returned and further processing stops.

Rule evaluation is done by checking an incoming request for presence of _all_ of declared request properties.
In order to match, the request needs to contain _at least_ all of the declared request properties.
For key-value properties presence of all of the declared values in the request is necessary.
Presence of additional values not mentioned in the configuration is permitted and has no effect.

**Note:** Rules with specific properties need to precede generic one having the same subset of properties.
Otherwise, a generic rule will match before evaluation of a specific rule, effectively suppressing the latter.


## License

Licensed under the [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0).
