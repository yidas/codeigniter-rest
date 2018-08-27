<p align="center">
    <a href="https://codeigniter.com/" target="_blank">
        <img src="https://codeigniter.com/assets/images/ci-logo-big.png" height="100px">
    </a>
    <h1 align="center">CodeIgniter RESTful API</h1>
    <br>
</p>

CodeIgniter 3 RESTful API Controller

[![Latest Stable Version](https://poser.pugx.org/yidas/codeigniter-rest/v/stable?format=flat-square)](https://packagist.org/packages/yidas/codeigniter-rest)
[![Latest Unstable Version](https://poser.pugx.org/yidas/codeigniter-rest/v/unstable?format=flat-square)](https://packagist.org/packages/yidas/codeigniter-rest)
[![License](https://poser.pugx.org/yidas/codeigniter-rest/license?format=flat-square)](https://packagist.org/packages/yidas/codeigniter-rest)

This RESTful API extension is collected into [yidas/codeigniter-pack](https://github.com/yidas/codeigniter-pack) which is a complete solution for Codeigniter framework.

Features
--------

- ***PSR-7** standardization*

- ***RESTful API** implementation*

- ***Laravel Resource Controllers** pattern like* 

---

OUTLINE
-------

- [Demonstration](#demonstration)
    - [RESTful Create Callback](#restful-create-callback)
    - [Packed Standard Format](#packed-standard-format)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
    - [Routes Setting](#routes-setting)
- [Resource Controllers](#resource-controllers)
    - [Build Methods](#build-methods)
    - [Custom Routes & Methods](#custom-routes--methods)
    - [Usage](#usage)
- [HTTP Request](#http-request)
    - [Usage](#usage-1)
- [HTTP Response](#http-response)
    - [Usage](#usage-2)
- [Reference](#reference)

---

DEMONSTRATION
-------------

```php
class ApiController extends yidas\rest\Controller
{
    public function index()
    {
        return $this->response->json(['bar'=>'foo']);
    }
}
```

Output with status `200 OK`:

```json
{"bar":"foo"}
```

### RESTful Create Callback

```php
public function store($requestData=null) {

    $this->db->insert('mytable', $requestData);
    $id = $this->db->insert_id();
    
    return $this->response->json(['id'=>$id], 201);
}
```

Output with status `201 Created`:

```json
{"id":1}
```

### Packed Standard Format

```php
try {
    throw new Exception("API forbidden", 403);
} catch (\Exception $e) {
    // Pack data into a standard format
    $data = $this->pack(['bar'=>'foo'], $e->getCode(), $e->getMessage());
    return $this->response->json($data, $e->getCode());
}

```

Output with status `403 Forbidden`:

```json
{"code":403,"message":"API forbidden","data":{"bar":"foo"}}
```

---

REQUIREMENTS
------------
This library requires the following:

- PHP 5.4.0+
- CodeIgniter 3.0.0+

---

INSTALLATION
------------

Run Composer in your Codeigniter project under the folder `\application`:

    composer require yidas/codeigniter-rest
    
Check Codeigniter `application/config/config.php`:

```php
$config['composer_autoload'] = TRUE;
```
    
> You could customize the vendor path into `$config['composer_autoload']`

---

CONFIGURATION
-------------

1. Create a controller to extend `yidas\rest\Controller`, 

```php
class ResourceController extends yidas\rest\Controller {}
```

2. Add and implement action methods referring by [Build Methods](#build-methods).

Then you could access RESTful API:

```
https://yourname.com/resources/api
https://yourname.com/resources/api/123
```

You could also use `/ajax` instead of `/api` if you like:

```
https://yourname.com/resources/ajax
https://yourname.com/resources/ajax/123
```

> `resources` is Controller name, if you don't want to have `/api` or `/ajax` in URI you could set Routes Setting as below.

### Routes Setting

If you want to have the standard RESTful URI pattern, which defines controller as resource for URI, for example:

```
https://yourname.com/resources
https://yourname.com/resources/123
```

You could add a pair of routes for this controller into `\application\config\routes.php` to enable RESTful API url:

```php
$route['resource_name'] = '[Controller]/route';
$route['resource_name/(:num)'] = '[Controller]/route/$1';
```

---

RESOURCE CONTROLLERS
--------------------
 
The base RESTful API controller is `yidas\rest\Controller`, the following table is the actions handled by resource controller, the `action` refers to `CI_Controller`'s action name which you could override:

|HTTP Method|URI (Routes Setting) |Action   |Description                                    |
|:----------|:--------------------|:--------|:----------------------------------------------|
|GET        |/photos              |index    |List the collection's members.                 |
|POST       |/photos              |store    |Create a new entry in the collection.          |
|GET        |/photos/{photo}      |show     |Retrieve an addressed member of the collection.|
|PUT/PATCH  |/photos/{photo}      |update   |Update the addressed member of the collection. |
|PUT        |/photos              |update   |Update the entire collection.                  |
|DELETE     |/photos/{photo}      |delete   |Delete the addressed member of the collection. |
|DELETE     |/photos              |delete   |Delete the entire collection.                  |

> Without Routes Setting, the URI is like `/photos/ajax` & `/photos/ajax/{photo}`.


### Build Methods:

You could make a resource controller by referring the [Template of Resource Controller](https://github.com/yidas/codeigniter-rest/blob/dev/examples/RestController.php).

The following RESTful controller methods could be add by your need. which each method refers to the action of Resource Controller table by default, and injects required arguments:

```php
public function index() {}
protected function store($requestData=null) {}
protected function show($resourceID) {}
protected function update($resourceID=null, $requestData=null) {}
protected function delete($resourceID=null, $requestData=null) {}
```

> `$requestData` (array) is the array input data parsed from request raw body
> 
> `$resourceID` (string) is the addressed identity of the resource from request

### Custom Routes & Methods

The default routes for mapping the same action methods of Resource Controller are below:

```php
protected $routes = [
    'index' => 'index',
    'store' => 'store',
    'show' => 'show',
    'update' => 'update',
    'delete' => 'delete',
];
```

You could override it to define your own routes while creating a resource controller:

```php
class ApiController extends yidas\rest\Controller {

    protected $routes = [
        'index' => 'find',
        'store' => 'save',
        'show' => 'display',
        'update' => 'edit',
        'delete' => 'destory',
    ];
}
```

After reseting routes, each RESTful method (key) would enter into specified controller action (value). For above example, while access `/resources/ajax/` url with `GET` method would enter into `find()` action. However, the default route would enter into `index()` action.

> The keys refer to the actions of Resource Controller table, you must define all methods you need. 


### Usage

#### pack()

Pack array data into body format

You could override this method for your application standard.

```php
protected array pack(array|mixed $data, integer $statusCode=200, string $message=null)
````

*Example:*
```php
$data = $this->pack(['bar'=>'foo'], 403, 'Forbidden');
return $this->response->json($data, 403);
```

JSON Result:

```
{
    "code": 403,
    "message": "Forbidden",
    "data": {
        "bar": "foo"
    }
}
```

---

HTTP REQUEST
------------

The PSR-7 request component `yidas\http\request` is loaded with `yidas\rest\Controller`, which provides input handler and HTTP Authentication.

### Usage

#### getAuthCredentialsWithBasic()

Get Credentials with HTTP Basic Authentication 

```php
public array getAuthCredentialsWithBasic()
```

*Example:*
```php
list($username, $password) = $this->request->getAuthCredentialsWithBasic();
```

#### getAuthCredentialsWithBearer()

Get Credentials with OAuth 2.0 Authorization Framework: Bearer Token Usage

```php
public string getAuthCredentialsWithBearer()
```

*Example:*
```php
$b64token = $this->request->getAuthCredentialsWithBearer();
```

---

HTTP RESPONSE
-------------

The PSR-7 response component `yidas\http\response` is loaded with `yidas\rest\Controller`, which provides output handler and formatter.

### Usage

#### json()

JSON output shortcut

```php
public void json(array|mixed $data, integer $statusCode=null)
```

*Example:*
```php
$this->response->json(['bar'=>'foo'], 201);
```

#### setFormat()

Set Response Format into CI_Output

```php
public self setFormat(string $format)
```

*Example:*
```php
$this->response->setFormat(\yidas\http\Response::FORMAT_JSON);
```

#### setData()

Set Response Data into CI_Output

```php
public self setData(mixed $data)
```

*Example:*
```php
$this->response->setData(['foo'=>'bar']);
```

#### send()

Sends the response to the client.

```php
public void send()
```

*Example:*
```php
$this->response->send();
```

---

REFERENCE
---------

- [HTTP authentication by MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication)

- [RFC7617 - The 'Basic' HTTP Authentication Scheme](https://tools.ietf.org/html/rfc7617)

- [RFC6750 - The OAuth 2.0 Authorization Framework: Bearer Token Usage](https://tools.ietf.org/html/rfc6750)

- [REST Relationship between URL and HTTP methods](https://en.wikipedia.org/wiki/Representational_state_transfer#Relationship_between_URL_and_HTTP_methods)

