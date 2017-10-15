<?php

namespace Jqqjj\EasyHumanAuth\Adapter;

use Jqqjj\EasyHumanAuth\Adapter\AdapterInterface;

class DBTableGateway implements AdapterInterface
{
    protected $handshake_table = 'handshake';
    protected $attempt_table = 'attempt';
    protected $pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }
    
    public function getHandshake($handshake_id)
    {
        if(empty($handshake_id)){
            return null;
        }
        
        $statement = $this->pdo->prepare("select * from {$this->handshake_table} where handshake_id=:handshake_id");
        $statement->bindParam('handshake_id', $handshake_id, \PDO::PARAM_STR);
        
        $statement->execute();
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }

    public function addHandshake($handshake_id,$remaining,$expired_time)
    {
        $datetime = date("Y-m-d H:i:s",$expired_time);
        $statement = $this->pdo->prepare("insert into {$this->handshake_table} (handshake_id,remaining,expired_time)"
        . " values (:handshake_id,:remaining,:expired_time)");
        $statement->bindParam('handshake_id', $handshake_id, \PDO::PARAM_STR);
        $statement->bindParam('remaining', $remaining, \PDO::PARAM_INT);
        $statement->bindParam('expired_time', $datetime, \PDO::PARAM_STR);
        
        $statement->execute();
        if($statement->rowCount()){
            return $handshake_id;
        }else{
            return null;
        }
    }
    
    public function updateHandshake($handshake_id,array $data)
    {
        $where = array('handshake_id'=>$handshake_id);
        $where_keys = array();
        foreach (array_keys($where) AS $value)
        {
            $where_keys[] = "`$value`=?";
        }
        $params_keys = array();
        foreach (array_keys($data) AS $val)
        {
            $params_keys[] = "`{$val}`=?";
        }
        $sql = "update {$this->handshake_table} set ". implode(',', $params_keys) . " where ".  implode(' AND ', $where_keys);
        
        $bind_values = array_merge(array_values($data), array_values($where));
        
        $statement = $this->pdo->prepare($sql);
        for ($index = 0; $index < count($bind_values); ++$index) {
            if (is_string($bind_values[$index])) {
                $statement->bindParam($index + 1, $bind_values[$index], \PDO::PARAM_STR);
            }
            elseif (is_bool($bind_values[$index])) {
                $statement->bindParam($index + 1, $bind_values[$index], \PDO::PARAM_BOOL);
            }
            elseif (is_int($bind_values[$index])) {
                $statement->bindParam($index + 1, $bind_values[$index], \PDO::PARAM_INT);
            }
            else {
                $statement->bindParam($index + 1, $bind_values[$index], \PDO::PARAM_NULL);
            }
        }
        return $statement->execute();
    }
    
    public function getAttempts($handshake_id,$num)
    {
        $statement = $this->pdo->prepare("select * from {$this->attempt_table} where handshake_id=:handshake_id order by add_time desc limit {$num}");
        $statement->bindParam('handshake_id', $handshake_id, \PDO::PARAM_STR);
        
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getIPAttempts($ip,$num)
    {
        $statement = $this->pdo->prepare("select * from {$this->attempt_table} where ip=:ip order by add_time desc limit {$num}");
        $statement->bindParam('ip', $ip, \PDO::PARAM_STR);
        
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function addAttempt($handshake_id,$status,$add_time,$ip)
    {
        $datetime = date("Y-m-d H:i:s",$add_time);
        $statement = $this->pdo->prepare("insert into {$this->attempt_table} (handshake_id,status,add_time,ip)"
        . " values (:handshake_id,:status,:add_time,:ip)");
        $statement->bindParam('handshake_id', $handshake_id, \PDO::PARAM_STR);
        $statement->bindParam('status', $status, \PDO::PARAM_INT);
        $statement->bindParam('add_time', $datetime, \PDO::PARAM_STR);
        $statement->bindParam('ip', $ip, \PDO::PARAM_STR);
        
        $statement->execute();
        if($statement->rowCount()){
            return $handshake_id;
        }else{
            return null;
        }
    }
}