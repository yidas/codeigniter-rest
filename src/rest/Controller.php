<?php

namespace yidas\rest;

use yidas\http\Request;
use yidas\http\Response;

/**
 * RESTful API Controller
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @version 1.0.0
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
     * @var bool Close the default actions without implement
     */
    const CLOSE_DEFAULT_ACTIONS = true;

    /**
     * @var array Standard format
     */
    protected $responseFormat = [
        'status_code' => 'code',
        'status_text' => 'message',
        'body' => 'data',
    ];

    /**
     * @var string yidas\http\Response format
     */
    protected $format;

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
                    $this->store($this->request->getRawBody());
                }
                break;
            case 'PUT':
            case 'PATCH':
                if ($resourceID) {
                    $this->update($this->request->getRawBody(), $resourceID);
                }
                break;
            case 'DELETE':
                if ($resourceID) {
                    $this->delete($resourceID);
                } else {
                    $this->deleteAll();
                }
                break;
            case 'GET':
            default:
                if ($resourceID) {
                    $this->show($resourceID);
                } else {
                    $this->index();
                }
                break;
        }
    }

    /**
     * Output by JSON format with optinal body format
     * 
     * @param array|mixed Callback data body
     * @param bool Enable body format
     * @param int Callback status code
     * @param string Callback status text
     * @return string Response body data
     */
    protected function json($data, $bodyFormat=false, $statusCode=null, $statusText=null)
    {
        $this->response->setFormat(Response::FORMAT_JSON);
        
        if ($bodyFormat) {
            // Pack data
            $data = $this->format($statusCode, $statusText, $data);
        }
        
        return $this->response
            ->setData($data)
            ->send();
    }

    /**
     * Format Response Data
     * 
     * @param int Callback status code
     * @param string Callback status text
     * @param array|mixed Callback data body
     * @return array Formated array data
     */
    protected function format($statusCode=null, $statusText=null, $body=null)
    {
        $format = [];
        // Status Code
        $format[$this->responseFormat['status_code']] = ($statusCode) ?: $this->response->getStatusCode();
        // Status Text
        if ($statusText) {
            $format[$this->responseFormat['status_text']] = $statusText;
        }
        // Body
        if ($body) {
            $format[$this->responseFormat['body']] = $body;
        }
        
        return $format;
    }

    /**
     * Default Action
     */
    private function _defaultAction()
    {
        /* Response sample code */
        // $response->data = ['foo'=>'bar'];
		// $response->setStatusCode(401);
        
        if (static::CLOSE_DEFAULT_ACTIONS) {
            // Codeigniter 404 Error Handling
            show_404();
        }
    }

    /**
     * Action: Index
     */
    public function index()
    {
        $this->_defaultAction();
    }

    /**
     * Action: Store
     * 
     * @param array $requestData
     */
    public function store($requestData=null)
    {
        $this->_defaultAction();
    }

    /**
     * Action: Show
     * 
     * @param int|string $resourceID
     */
    public function show($resourceID)
    {
        $this->_defaultAction();
    }

    /**
     * Action: Update
     * 
     * @param int|string $resourceID
     * @param array $requestData
     */
    public function update($resourceID, $requestData=null)
    {
        $this->_defaultAction();
    }

    /**
     * Action: Delete
     * 
     * @param int|string $resourceID
     */
    public function delete($resourceID)
    {
        $this->_defaultAction();
    }

    /**
     * Action: Delete All
     */
    public function deleteAll()
    {
        $this->_defaultAction();
    }
}
