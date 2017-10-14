<?php

namespace Jqqjj\HumanAuthFriendly;

use Jqqjj\HumanAuthFriendly\Attributes\HandshakeAttributes;
use Jqqjj\HumanAuthFriendly\Adapter\AdapterInterface;
use Jqqjj\HumanAuthFriendly\Exception\RuntimeException;

class Handshake
{
    private $adapter;
    protected $attributes;
    protected $original_attributes;
    protected $duration = 600;
    protected $max_attack_num = 5;
    
    public function __construct($handshake_id,AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
        $attributes = $adapter->getHandshake($handshake_id);
        $remaining = $this->_getIPRemaining($_SERVER['REMOTE_ADDR']);
        if(empty($attributes) || strtotime($attributes['expired_time'])<time()){
            $handshake_id = md5(uniqid("", true).mt_rand(100, 999));
            $this->adapter->addHandshake($handshake_id, $remaining, time());
            $attributes = $this->adapter->getHandshake($handshake_id);
            if(empty($attributes)){
                throw new RuntimeException("Add Handshake Error.");
            }
            $attributes['remaining'] = max([$remaining,$attributes['remaining']]);
            $this->_loadAttributes($attributes);
            $this->_freshenOriginalAttributes();
            $this->addAttempt(0);
        }else{
            $attributes['remaining'] = max([$remaining,$attributes['remaining']]);
            $this->_loadAttributes($attributes);
            $this->_freshenOriginalAttributes();
        }
    }
    
    public function getId()
    {
        return $this->attributes->handshake_id;
    }
    
    public function getRemaining()
    {
        return $this->attributes->remaining;
    }
    
    public function getAttempts()
    {
        return $this->adapter->getAttempts($this->getId(), $this->max_attack_num);
    }
    
    public function addAttempt($status)
    {
        if($status){
            $this->resetRemaining();
        }else{
            $this->adapter->addAttempt($this->getId(), 0, time(), $_SERVER['REMOTE_ADDR']);
            $this->attributes->remaining = max([$this->attributes->remaining - 1,0]);
        }
    }
    
    public function resetRemaining()
    {
        $this->attributes->remaining = $this->max_attack_num;
    }
    
    public function save()
    {
        $data = $this->attributes->toArray();
        $original_data = $this->original_attributes->toArray();
        $save_data = array_diff_assoc($data, $original_data);
        if(empty($save_data)){
            return true;
        }
        if($this->adapter->updateHandshake($this->getId(), $save_data)){
            $this->_freshenOriginalAttributes();
            return true;
        }else{
            return false;
        }
    }
    
    private function _loadAttributes(array $attributes)
    {
        $this->attributes = new HandshakeAttributes();
        foreach ($attributes as $key=>$value){
            if(isset($this->attributes->$key)){
                $this->attributes->$key = $value;
            }
        }
    }
    
    private function _freshenOriginalAttributes()
    {
        $this->original_attributes = clone $this->attributes;
    }
    
    private function _getIPRemaining($ip)
    {
        $attempts = $this->adapter->getIPAttempts($ip, $this->max_attack_num);
        if(empty($attempts)){
            return $this->max_attack_num;
        }
        
        $use = 0;
        foreach ($attempts as $value){
            if($value['status'] || time() - strtotime($value['add_time']) > $this->duration){
                break;
            }
            $use++;
        }
        return max([$this->max_attack_num - $use , 0]);
    }
    
    public function __set($name, $value)
    {
        $this->attributes->$name = $value;
    }
    
    public function __get($name)
    {
        return $this->attributes->$name;
    }
    
    public function __destruct()
    {
        $this->save();
    }
}