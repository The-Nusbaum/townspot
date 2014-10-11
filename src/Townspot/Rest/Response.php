<?php
/**
 * Created by IntelliJ IDEA.
 * User: ian
 * Date: 10/4/14
 * Time: 4:40 PM
 */

namespace Townspot\Rest;


class Response {
    protected $_success = false;
    protected $_data = array();
    protected $_count = 0;
    protected $_message;
    protected $_code = '200';
    protected $_codeMsg = 'OK';

    protected $_codeMsgs = array(
        // INFORMATIONAL CODES
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        // SUCCESS CODES
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        // REDIRECTION CODES
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy', // Deprecated
        307 => 'Temporary Redirect',
        // CLIENT ERROR
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        // SERVER ERROR
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    );

    public function __construct($success = true, $data = null, $message = null, $code = 200) {
        if($success) $this->setSuccess((bool) $success);
        if($data) {
            $this->setData($data);
            $this->setCount(count($data));
        }
        if($message) $this->setMessage($message);
        $this->setCode($code);
        $this->setCodeMsg($this->getResponseMessage($code));
    }

    public function build() {
        return array(
            'success' => $this->getSuccess(),
            'code' => $this->getCode(),
            'codeMsg' => $this->getCodeMsg(),
            'message' => $this->getMessage(),
            'data' => $this->getData(),
            'count' => $this->getCount()
        );
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->_code = $code;
        $this->_codeMsg = $this->getResponseMessage($code);
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeMsg()
    {
        return $this->_codeMsg;
    }

    /**
     * @param string $codeMsg
     */
    public function setCodeMsg($codeMsg)
    {
        $this->_codeMsg = $codeMsg;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->_message;
    }

    /**
     * @return mixed
     */
    public function getResponseMessage($code)
    {
        return $this->_codeMsgs[$code];
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->_message = $message;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCount()
    {
        return $this->_count;
    }

    /**
     * @param mixed $count
     */
    public function setCount($count)
    {
        $this->_count = $count;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->_data = $data;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * @param mixed $success
     */
    public function setSuccess($success)
    {
        $this->_success = (bool) $success;
        return $this;
    }




} 