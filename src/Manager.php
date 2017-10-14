<?php

namespace Jqqjj\HumanAuthFriendly;

use Jqqjj\HumanAuthFriendly\Adapter\AdapterInterface;
use Jqqjj\HumanAuthFriendly\Handshake;

class Manager
{
    public $cookie_key = "_humanAuthFriendly";
    public $lifttime = 2592000;//3600 * 24 * 30
    public $path = '/';
    public $domain;
    public $secure = false;
    public $httponly = true;
    
    private $handshake;
    
    public function __construct(AdapterInterface $adapter)
    {
        $handshake_id = !empty($_COOKIE) && !empty($_COOKIE[$this->cookie_key]) ? $_COOKIE[$this->cookie_key] : "";
        $this->handshake = new Handshake($handshake_id,$adapter);
        $this->handshake->expired_time = date("Y-m-d H:i:s",time()+$this->lifttime);
    }
    
    public function attemptFailure()
    {
        $this->handshake->addAttempt(0);
    }
    
    public function attemptSuccess()
    {
        $this->handshake->addAttempt(1);
    }

    public function check()
    {
        return $this->handshake->getRemaining() > 0;
    }
    
    public function getHandshake()
    {
        return $this->handshake;
    }
    
    public function getCookieString()
    {
        $str = "{$this->cookie_key}={$this->handshake->getId()}";
        $str .= ";expires=".gmdate('D, d-M-Y H:i:s T', $this->lifttime + time());
        $str .= ";Max-Age={$this->lifttime}";
        $str .= ";path={$this->path}";
        if(!empty($this->domain)){
            $str .= ";domain={$this->domain}";
        }
        if($this->secure){
            $str .= ";secure";
        }
        if($this->httponly){
            $str .= ";HttpOnly";
        }
        return $str;
    }
    
    public function outputCookie()
    {
        setcookie($this->cookie_key, $this->handshake->getId(), $this->lifttime + time(), $this->path, $this->domain, $this->secure, $this->httponly);
    }
}