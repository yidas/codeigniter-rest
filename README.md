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
public function store($resourceID, $requestData=null) {

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
https://yourname.com/resources/ajax
https://yourname.com/resources/ajax/123
```

> `resources` is Controller name

### Routes Setting

If you want to define controller as resource for URI, for example:

```
https://yourname.com/resources
https://yourname.com/resources/123
```

You could add a pair of routes for this controller into `\application\config\routes.php` to enable RESTful API methods:

```php
$route['resource_name'] = '[Controller]/route';
$route['resource_name/(:num)'] = '[Controller]/route/$1';
```

> You don't need set routes if you just use `index` method of the controller.

---

RESOURCE CONTROLLERS
--------------------
 
The base RESTful API controller is `yidas\rest\Controller`, the following table is the actions handled by resource controller, the `action` is the `CI_Controller`'s action name which you could override to open it:

|HTTP Method|URI            |Action   |Description                                    |
|:----------|:--------------|:--------|:----------------------------------------------|
|GET        |/photos        |index    |List the collection's members.                 |
|POST       |/photos        |store    |Create a new entry in the collection.          |
|GET        |/photos/{photo}|show     |Retrieve an addressed member of the collection.|
|PUT/PATCH  |/photos/{photo}|update   |Update the addressed member of the collection. |
|DELETE     |/photos/{photo}|delete   |Delete the addressed member of the collection. |
|DELETE     |/photos        |delete   |Delete the entire collection.                  |


### Build Methods:

You could make a resource controller by referring the [Template of Resource Controller](https://github.com/yidas/codeigniter-rest/blob/dev/examples/RestController.php).

The following methods with arguments could be add when you need to defind response and open it:

```php
public function index() {}
protected function store($requestData=null) {}
protected function show($resourceID) {}
protected function update($resourceID, $requestData=null) {}
protected function delete($resourceID=null) {}
```

> `$requestData` is the raw body from request
> 
> `$resourceID` is the addressed identity of the resource from request

### Custom Routes & Methods

The default routing methods are below setting:

```php
protected $routes = [
    'index' => 'index',
    'store' => 'store',
    'show' => 'show',
    'update' => 'update',
    'delete' => 'delete',
];
```

You could override to defind your own routing while creating a resource controller:

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

> The keys are refered to Action of Resource Controller table, you must define all routes you need. 
>
> For example: REST list `index` action will run `find` method.


### Usage

#### `pack()`

Pack array data into body format

You could override this method for your application standard.

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

#### `getAuthCredentialsWithBasic()`

```php
list($username, $password) = $this->request->getAuthCredentialsWithBasic();
```

#### `getAuthCredentialsWithBearer()`

```php
$b64token = $this->request->getAuthCredentialsWithBearer();
```

---

HTTP RESPONSE
-------------

The PSR-7 response component `yidas\http\response` is loaded with `yidas\rest\Controller`, which provides output handler and formatter.

### Usage

#### `json()`

JSON output shortcut

```php
$this->response->json(['bar'=>'foo'], 201);
```

#### `setFormat()`

```php
$this->response->setFormat(\yidas\http\Response::FORMAT_JSON);
```

#### `setData()`

```php
$this->response->setData(['foo'=>'bar']);
```

#### `send()`

```php
$this->response->send();
```

---

REFERENCE
---------

- [HTTP authentication by MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication)

- [RFC7617 - The 'Basic' HTTP Authentication Scheme](https://tools.ietf.org/html/rfc7617)

- [RFC6750 - The OAuth 2.0 Authorization Framework: Bearer Token Usage](https://tools.ietf.org/html/rfc6750)

