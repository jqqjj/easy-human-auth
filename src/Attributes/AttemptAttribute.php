<?php

namespace Jqqjj\EasyHumanAuth\Attributes;

use Jqqjj\EasyHumanAuth\Attributes\AbstractAttributes;
use Jqqjj\EasyHumanAuth\Attributes\AttributesInterface;

class AttemptAttributes extends AbstractAttributes implements AttributesInterface
{
    protected $id;
    protected $handshake_id;
    protected $add_time;
    protected $ip;
}