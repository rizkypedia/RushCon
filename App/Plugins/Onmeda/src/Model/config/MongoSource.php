<?php namespace Onmeda\Model\Config;

class MongoSource {
    public function getSource() {
       $connection = new \MongoClient();
       return $connection;
    }
    
}
