<?php namespace Onmeda\Model;

use RushCon\Model\Model as BaseModel;
use RushCon\Core\Camelizer as camelizer;

class CompoProduitsPaTable extends BaseModel {
    private $__tableName;
    
    public function __construct($connection) {
        parent::__construct($connection);
        $this->__tableName = $this->parseTableClassName(__CLASS__);
    }
    
     public function find($additionals = array()) {
        $decamelized = camelizer::decamelize($this->__tableName);
        return $this->findAll($decamelized, $additionals);
    }
    
    public function getIngredients($drugId) {
        $joins = array("type" => "LEFT JOIN","tables" => array(
                "compo_produits_pa" => array(
                    "compo_pa" => array("compo_produits_pa.CODE_PA" => "compo_pa.CODE_PA")
                ), 
            ));
        $fields = array("compo_produits_pa.CODE_CIP", "compo_produits_pa.CODE_PA","compo_pa.CODE_PA", "compo_pa.LIBELLE_PA");
        
        $decamelized = camelizer::decamelize($this->__tableName);
        $conditions = array("compo_produits_pa.CODE_CIP" => $drugId);
        $additionals = array(
            "fields" => $fields,
            "joins" => $joins,
            "conditions" => $conditions
            );
        return $this->findAll($decamelized, $additionals);
    }
}



