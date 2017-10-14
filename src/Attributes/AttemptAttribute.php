<?php

namespace Jqqjj\HumanAuthFriendly\Attributes;

use Jqqjj\HumanAuthFriendly\Attributes\AbstractAttributes;
use Jqqjj\HumanAuthFriendly\Attributes\AttributesInterface;

class AttemptAttributes extends AbstractAttributes implements AttributesInterface
{
    protected $id;
    protected $handshake_id;
    protected $add_time;
    protected $ip;
}