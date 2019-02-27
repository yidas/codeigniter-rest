<?php

namespace yidas\http;

/**
 * Request Component
 * 
 * @author  Nick Tsai <myintaer@gmail.com>
 * @since   1.7.0
 * @todo    Psr\Http\Message\RequestInterface
 */
class Request
{
    /**
     * @var string raw HTTP request body
     */
    private $_rawBody;

    /**
     * @var array Body params
     */
    private $_bodyParams;
    
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
     * Returns request content-type
     * The Content-Type header field indicates the MIME type of the data
     * contained in [[getRawBody()]] or, in the case of the HEAD method, the
     * media type that would have been sent had the request been a GET.
     * For the MIME-types the user expects in response, see [[acceptableContentTypes]].
     * 
     * @return string request content-type. Null is returned if this information is not available.
     * @link https://tools.ietf.org/html/rfc2616#section-14.17
     * HTTP 1.1 header field definitions
     */
    public function getContentType()
    {
        return isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;
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
     * Returns the request parameters given in the request body.
     *
     * Request parameters are determined using the parsers depended on [[contentType]].
     * If no parsers are configured for the current [[contentType]] it uses the PHP function `mb_parse_str()`
     * to parse the [[rawBody|request body]].
     * 
     * @todo   Cache
     * @return array the request parameters given in the request body.
     */
    public function getBodyParams()
    {
        if ($this->_bodyParams === null) {
        
            $contentType = $this->getContentType();

            if (strcasecmp($contentType, 'application/json') == 0) {
                // JSON content type
                $this->_bodyParams = json_decode($this->getRawBody(), true);
            } elseif ($this->getMethod() === 'POST') {
                // PHP has already parsed the body so we have all params in $_POST
                $this->_bodyParams = $_POST;
            } else {
                $this->_bodyParams = [];
                mb_parse_str($this->getRawBody(), $this->_bodyParams);
            }
        }

        return $this->_bodyParams;
    }

    /**
     * Alias of getRawBody()
     */
    public function input()
    {
        return $this->getBodyParams();
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

            if (strpos(strtolower($_SERVER['HTTP_AUTHORIZATION']), 'bearer ')===0) {

                $b64token = substr($_SERVER['HTTP_AUTHORIZATION'], 7);
            }
        }

        return $b64token;
    }
}
