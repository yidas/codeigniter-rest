<?php

namespace yidas\http;

/**
 * Request Component
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @since   0.1.0
 * @todo    Psr\Http\Message\RequestInterface
 */
class Request
{
    /**
     * @var array raw HTTP request body
     */
    private $_rawBody;
    
    /**
     * Retrieves the HTTP method of the request.
     *
     * @return string Returns the request method.
     */
    public function getMethod()
    {
        if (isset($_SERVER['HTTP_X-Http-Method-Override'])) {
            return strtoupper($_SERVER['HTTP_X-Http-Method-Override']);
        }
        if (isset($_SERVER['REQUEST_METHOD'])) {
            return strtoupper($_SERVER['REQUEST_METHOD']);
        }
        return 'GET';
    }

    /**
     * Returns the raw HTTP request body.
     * @return string the request body
     */
    public function getRawBody()
    {
        if ($this->_rawBody === null) {
            $this->_rawBody = file_get_contents('php://input');
        }
        return $this->_rawBody;
    }

    /**
     * Alias of getRawBody()
     */
    public function input()
    {
        return $this->getRawBody();
    }
    
    /**
     * Get Credentials with HTTP Basic Authentication 
     *
     * @return array that contains exactly two elements:
     * - 0: the username sent via HTTP authentication, `null` if the username is not given
     * - 1: the password sent via HTTP authentication, `null` if the password is not given
     * 
     * @example
     *  list($username, $password) = $request->getAuthCredentialsWithBasic();
     */
    public function getAuthCredentialsWithBasic()
    {
        if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
            
            return [$_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']];
        } 
        
        $authToken = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : null;
        $authToken = (!$authToken && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) 
            ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
            : $authToken;
        
        if ($authToken !== null && strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'basic ')===0) {
            
            $parts = array_map(function ($value) {
                return strlen($value) === 0 ? null : $value;
            }, explode(':', base64_decode(mb_substr($authToken, 6)), 2));
            
            if (count($parts) < 2) {
                return [$parts[0], null];
            }
            
            return $parts;
        }

        return [null, null];
    }

    /**
     * Get Credentials with OAuth 2.0 Authorization Framework: Bearer Token Usage
     *
     * @return string b64token
     */
    public function getAuthCredentialsWithBearer()
    {
        $b64token = null;
        
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {

            if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'Bearer ')===0) {

                $b64token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }

        return $b64token;
    }
}
