<?php

namespace yidas\rest;

use yidas\http\Request;
use yidas\http\Response;

/**
 * RESTful API Controller
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @version 1.1.5
 * @link    https://github.com/yidas/codeigniter-rest/
 * @see     https://github.com/yidas/codeigniter-rest/blob/master/examples/RestController.php
 * 
 * Controller extending:
 * ```php
 * class My_controller extends yidas\rest\Controller {}
 * ```
 * 
 * Route setting:
 * ```php
 * $route['resource_name'] = '[Controller]/route';
 * $route['resource_name/(:num)'] = '[Controller]/route/$1';
 * ```
 */
class Controller extends \CI_Controller
{
    /**
     * @var array Standard format
     */
    protected $responseFormat = [
        'status_code' => 'code',
        'status_text' => 'message',
        'body' => 'data',
    ];

    /**
     * RESTful API resource routes
     * 
     * public function index() {}
     * protected function store($requestData=null) {}
     * protected function show($resourceID) {}
     * protected function update($resourceID, $requestData=null) {}
     * protected function delete($resourceID=null) {}
     * 
     * @var array RESTful API table of routes & actions
     */
    protected $routes = [
        'index' => 'index',
        'store' => 'store',
        'show' => 'show',
        'update' => 'update',
        'delete' => 'delete',
    ];

    /**
     * Pre-setting format
     * 
     * @var string yidas\http\Response format
     */
    protected $format;

    /**
     * Body Format usage switch
     * 
     * @var bool Default $bodyFormat for json()
     */
    protected $bodyFormat = false;

    /**
     * @var object yidas\http\Request;
     */
    protected $request;

    /**
     * @var object yidas\http\Response;
     */
    protected $response;
    
    
    function __construct() 
    {
        parent::__construct();
        
        // Request initialization
        $this->request = new Request;
        // Response initialization
        $this->response = new Response;

        // Response setting
        if ($this->format) {
		    $this->response->setFormat($this->format);
        }
    }

    /**
     * Route bootstrap
     * 
     * For Codeigniter route setting to implement RESTful API
     * 
     * @param int|string Resource ID
     */
    public function route($resourceID=NULL)
    {
        switch ($this->request->getMethod()) {
            case 'POST':
                if (!$resourceID) {
                    return $this->_action(['store', $this->request->getBodyParams()]);
                }
                break;
            case 'PUT':
            case 'PATCH':
                if ($resourceID) {
                    return $this->_action(['update', $resourceID, $this->request->getBodyParams()]);
                }
                break;
            case 'DELETE':
                if ($resourceID) {
                    return $this->_action(['delete', $resourceID]);
                } else {
                    return $this->_action(['delete']);
                }
                break;
            case 'GET':
            default:
                if ($resourceID) {
                    return $this->_action(['show', $resourceID]);
                } else {
                    return $this->_action(['index']);
                }
                break;
        }
    }

    /**
     * Alias of route()
     */
    public function ajax($resourceID=NULL)
    {
        return $this->route($resourceID);
    }

    /**
     * Output by JSON format with optinal body format
     * 
     * @param array|mixed Callback data body, false will remove body key
     * @param bool Enable body format
     * @param int Callback status code
     * @param string Callback status text
     * @return string Response body data
     */
    protected function json($data=[], $bodyFormat=null, $statusCode=null, $statusText=null)
    {
        // Check default Body Format setting if not assigning
        $bodyFormat = ($bodyFormat!==null) ? $bodyFormat : $this->bodyFormat;
        
        if ($bodyFormat) {
            // Pack data
            $data = $this->_format($statusCode, $statusText, $data);
        } else {
            // JSON standard of RFC4627
            $data = is_array($data) ? $data : [$data];
        }

        return $this->response->json($data, $statusCode);
    }

    /**
     * Format Response Data
     * 
     * @param int Callback status code
     * @param string Callback status text
     * @param array|mixed|bool Callback data body, false will remove body key 
     * @return array Formated array data
     */
    protected function _format($statusCode=null, $statusText=null, $body=false)
    {
        $format = [];
        // Status Code setting
        if ($statusCode) {
            $this->response->setStatusCode($statusCode);
        }
        // Status Code field is necessary
        $format[$this->responseFormat['status_code']] = ($statusCode) 
            ?: $this->response->getStatusCode();
        // Status Text field
        if ($statusText) {
            $format[$this->responseFormat['status_text']] = $statusText;
        }
        // Body field
        if ($body !== false) {
            $format[$this->responseFormat['body']] = $body;
        }
        
        return $format;
    }

    /**
     * Default Action
     */
    protected function _defaultAction()
    {
        /* Response sample code */
        // $response->data = ['foo'=>'bar'];
		// $response->setStatusCode(401);
        
        // Codeigniter 404 Error Handling
        show_404();
    }

    /**
     * Action processor for route
     * 
     * @param array Elements contains method for first and params for others 
     */
    private function _action($params)
    {
        // Shift and get the method
        $method = array_shift($params);

        if (!isset($this->routes[$method])) {
            $this->_defaultAction();
        }

        // Get corresponding method name
        $method = $this->routes[$method];

        if (!method_exists($this, $method)) {
            $this->_defaultAction();
        }

        return call_user_func_array([$this, $method], $params);
    }
}
