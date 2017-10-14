## Easy Human Auth

Simple apis for identifying human which protects your application far away from robots.

## Installation

Run the following to include this via Composer
```
composer require jqqjj/easy-human-auth
```
# Usage

Create mysql tables(when using  DBTableGateway adapter)
```sql
CREATE TABLE `handshake` (
  `handshake_id` varchar(32) NOT NULL,
  `remaining` int(10) unsigned DEFAULT NULL,
  `expired_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`handshake_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE `attempt` (
  `handshake_id` varchar(32) NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  `add_time` timestamp NULL DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```
Code:
```php
use Jqqjj\HumanAuthFriendly\Manager;
use Jqqjj\HumanAuthFriendly\Adapter\DBTableGateway;

//Create a PDO object
$pdo = new \PDO('mysql:host=localhost;port=3306;dbname=yourdbname','dbuser','dbpasswd');
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

//Create an adapter(Only MySQL adapter is supported Currently)
$adapter = new DBTableGateway($pdo);
$object = new Manager($adapter);

//Put the codes where application needs protection
$object->action();

//Check it, it will return false when the client is a robot
if($object->check()){
	//human
}else{
	//robot
}

//Mark humans if you make sure the client is not a robot
$object->humanAction();
```

### License
This package is licensed under the [MIT license](http://opensource.org/licenses/MIT).
