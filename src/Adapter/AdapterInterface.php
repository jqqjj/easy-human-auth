<?php

namespace Jqqjj\EasyHumanAuth\Adapter;

interface AdapterInterface
{
    public function getHandshake($handshake_id);
    public function addHandshake($handshake_id,$remaining,$expired_time);
    public function updateHandshake($handshake_id,array $data);
    public function getAttempts($handshake_id,$num);
    public function getIPAttempts($ip,$num);
    public function addAttempt($handshake_id,$status,$add_time,$ip);
}

