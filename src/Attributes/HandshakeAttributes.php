<?php

namespace Jqqjj\EasyHumanAuth\Attributes;

use Jqqjj\EasyHumanAuth\Attributes\AbstractAttributes;
use Jqqjj\EasyHumanAuth\Attributes\AttributesInterface;

class HandshakeAttributes extends AbstractAttributes implements AttributesInterface
{
    protected $handshake_id;
    protected $remaining;
    protected $expired_time;
}