<?php

namespace Jqqjj\HumanAuthFriendly\Attributes;

use Jqqjj\HumanAuthFriendly\Attributes\AbstractAttributes;
use Jqqjj\HumanAuthFriendly\Attributes\AttributesInterface;

class HandshakeAttributes extends AbstractAttributes implements AttributesInterface
{
    protected $handshake_id;
    protected $remaining;
    protected $expired_time;
}