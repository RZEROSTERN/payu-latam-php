<?php

namespace Rzerostern\Payu\Api;

class PayuHttpRequestInfo
{
    /** the http method to the request */
    var $method;

    /** the environment to the request*/
    var $environment;

    /** the segment to add the url to the request*/
    var $segment;

    /** the user for Basic Http authentication */
    var $user;

    /** the password for Basic Http authentication */
    var $password;

    /** the language to be include in the header request */
    var $lang;



    /**
     * 
     * @param string $environment
     * @param string $method
     * @param string $segment
     */
    function __construct($environment, $method, $segment = null)
    {
        $this->environment = $environment;
        $this->method = $method;
        $this->segment = $segment;
    }


    /**
     * Builds the url for the environment selected
     */
    public function getUrl()
    {
        if (isset($this->segment)) {
            return Environment::getApiUrl($this->environment) . $this->segment;
        } else {
            return Environment::getApiUrl($this->environment);
        }
    }
}
