<?php namespace Onmeda\Model;

use RushCon\Model\Model as BaseModel;
use RushCon\Core\Camelizer as camelizer;

class ArticlesTable extends BaseModel {
    
    public function __construct($connection) {
        parent::__construct($connection);
    }
    
    public function find($additionals) {
        $this->parseTableClassName(__CLASS__);
        return $this->findAll($this->parseTableClassName(__CLASS__), $additionals);
    }
    
}

