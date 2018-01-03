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
        return $this->json(['bar'=>'foo']);
    }
}
```

Output:

```json
{"bar":"foo"}
```

### Body Formatter

```php
try {
    throw new Exception("API forbidden", 403);
} catch (\Exception $e) {
    return $this->json(['bar'=>'foo'], true, $e->getCode(), $e->getMessage());
}

```

Output:

```json
{"code":403,"message":"API forbidden","data":{"bar":"foo"}}
```

### Update Example

```php
public function update($resourceID, $requestData=null) {

    $this->db->where('id', $resourceID)
        ->update('table', $requestData);
    return $this->json(null, true);
}
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
class ApiController extends yidas\rest\Controller {}
```

2. Add a pair of routes for this controller into `\application\config\routes.php` to enable RESTful API methods:

```php
$route['resource_name'] = '[Controller]/route';
$route['resource_name/(:num)'] = '[Controller]/route/$1';
```

> You could skip this route setting if you just use `index` method of the controller.

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
|DELETE     |/photos        |deleteAll|Delete the entire collection.                  |


### Overrided Methods:

The following methods with arguments could be overrided when you need to defind response and open it:

```php
public function index() {}
public function store($requestData=null) {}
public function show($resourceID) {}
public function update($resourceID, $requestData=null) {}
public function delete($resourceID) {}
public function deleteAll() {}
```

> `$requestData` is the raw body from request
> 
> `$resourceID` is the addressed identity of the resource from request


### Usage

#### json()

Output by JSON format with optinal body format

|Item|Type|Description|
|-|-|-|
|param|array\|mixed |Callback data body|
|param| bool |Enable body format|
|param| int |Callback status code|
|param| string |Callback status text|
|return |string| Response body data|

```php
return $this->json(["bar"=>"foo"], true);
```

---

HTTP REQUEST
------------

The PSR-7 request component `yidas\http\request` is loaded with `yidas\rest\Controller`, which provides input handler and HTTP Authentication.

### Usage

#### getAuthCredentialsWithBasic()

```php
list($username, $password) = $this->request->getAuthCredentialsWithBasic();
```

#### getAuthCredentialsWithBearer()

```php
$b64token = $this->request->getAuthCredentialsWithBearer();
```

---

HTTP RESPONSE
-------------

The PSR-7 response component `yidas\http\response` is loaded with `yidas\rest\Controller`, which provides output handler and formatter.

### Usage

#### setFormat()

```php
$this->response->setFormat(\yidas\http\Response::FORMAT_JSON);
```

#### setData()

```php
$this->response->setData(['foo'=>'bar']);
```

#### send()

```php
$this->response->send();
```

---

REFERENCE
---------

- [HTTP authentication by MDN](https://developer.mozilla.org/en-US/docs/Web/HTTP/Authentication)

- [RFC7617 - The 'Basic' HTTP Authentication Scheme](https://tools.ietf.org/html/rfc7617)

- [RFC6750 - The OAuth 2.0 Authorization Framework: Bearer Token Usage](https://tools.ietf.org/html/rfc6750)

