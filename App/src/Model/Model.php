<?php namespace RushCon\Model;

class Model {
    
    private $__dbLink;
    
    public function __construct($database) {
        //var_dump($database);
         
             
            $dbName = $database->dbCredentials['database'];
            $host = $database->dbCredentials['host'];
            $userName = $database->dbCredentials['user'];
            $password = $database->dbCredentials['password'];
        
            $dsn = "mysql:dbname=".$dbName.";host=" . $host;
        
            
            try {
                   $dbh = new \PDO($dsn, $userName, $password);
		} catch (PDOException $e) {
                    echo 'Connection failed: ' . $e->getMessage();
             }
                $this->db_link = $dbh;
           
    }
    
    public function insertQuery($data,$tablename) {
		
		$complete_query = "";
		
                $str_fields="";
                $str_val="(NULL ,";
                $c=0;
		$str = "";
		if(is_array($data)){
                    $size=sizeof($data)-1;
                    $str.="INSERT INTO ".$tablename." (id, ";

                     while(list($key,$val)=each($data)){

                           $str_fields.=$key . ($c < $size ? "," : ")");
                           $str_val.="'".$val."'" .($c < $size ? "," : ")");
                           $c++;
                     }

                     $complete_query = $str .$str_fields ." VALUES ". $str_val;
                     //echo $complete_query . "\n";
		}
                
		$this->db_link->query($complete_query);
	}
	
	public function count_rows($tablename, $keyname, $where = array()) {
            
		$sql = "SELECT count(*) AS " . $keyname . " FROM " . $tablename;
		
		
		if(!empty($where)){ 
			$sql .=" WHERE ";  
			$sz = sizeof($where) - 1;
			$counter = 0;
			
			while(list($field,$condition) = each($where)){
                            if (!strpos($field, "OR") && !strpos($field, "AND") && !strpos($field, "IS") && !strpos($condition, "IS") && !strpos($field, "IN") && !strpos($condition,"LIKE")) {
                                
                                $sql .= $field."=".$condition;
                            } else {                                
                                $sql .= $field."".$condition;
                            }
				
                            $sql .= ($counter < $sz ? " AND " : "");
                            $counter++;
				
			}
		}
		//echo $sql;
		$row = $this->db_link->query($sql);
		$result = $row->fetch(PDO::FETCH_OBJ);
                
		return $result->$keyname;
		
	}
	
	public function delete_record($id = array(), $tablename){
		
		$success = false;
		
		if(!empty($id)){
                    
			list($key,$val) = each($id);
                        
                        if (!is_array($val)) {
                            if (count($id) === 1) {
                                $del =" DELETE FROM " . $tablename . " WHERE ". $key ."=" . $val . "";
                            } else {
                                //$del =" DELETE FROM " . $tablename . " WHERE ". $key ."=" . $val . "";
                                $del = " DELETE FROM " . $tablename. " WHERE " ;
                                $s = 0;
                                foreach ($id as $field => $value) {
                                    $del .= $field ."=". $value;
                                    $del .= $s < (count($id) - 1) ? " AND " : ""; 
                                    $s++;
                                }
                            }
                        } else {
                            $del = " DELETE FROM " . $tablename . " WHERE ";
                            $idcount  = count($id) - 1;
                            $top = 0;
                            
                           foreach ($id as $key => $val) {
                               $del .=  "" . $key . " IN ";
                               $cntVal = count($val) - 1;
                               $inStr = "";
                                $inStr .= "(";
                            
                            for ($i=0; $i<count($val); $i++) {
                                //echo $val[$i] . "\n";
                                $inStr .= $val[$i];
                                $inStr .= $i < $cntVal ? "," : "";
                            }
                            
                             $inStr .= ")";
                             $del .= $inStr;
                             $del .= $top < $idcount ? " AND " : "";
                             $top++;
                             
                           }
                            
                            
                        }
		}
                //echo $del;
		return $this->db_link->query($del);

	}
	
	public function update_single_record($condition = array(), $sets = array(), $tablename) {
		
		$success = false;
		$update = "UPDATE " . $tablename. " SET ";
		
		/*loop through set array*/
		$sz_sets = sizeof($sets) - 1;
		$counter = 0;
		
		while(list($key,$val)=each($sets)){
                    
                        if (is_string($val)) {
                            $v = "'" . $val . "'";
                        } else {
                            if (empty($val)) {
                                $v = 'NULL';
                            } else {
                                $v = $val;
                            }
                        }
			$update .= $key."=".$v;
			$update .=($counter < $sz_sets ? "," : "");
			$counter++;
		}
		
		$update .=" WHERE ";
		
		$counter = 0;
		
		/*loop through condition*/
		$sz_condition = sizeof($condition) - 1;
		
		while(list($where,$to)=each($condition)){
                        if (!is_array($to)) {
                            $update .=$where. "=" . $to;
                        } else {
                            
                            $update .= $where . " IN ";
                            $cntTo = count($to) - 1;
                            $insStr = "";
                            $insStr .= "(";
                            
                            for ($i=0; $i<count($to);$i++) {
                                
                                $insStr .= $to[$i];
                                $insStr .= $i < $cntTo ? "," : "";
                            }
                            $insStr .= ")";
                            $update .= $insStr;
                        }
			$update .=($counter < $sz_condition ? " AND " : "");
			$counter++;
		}
		
		//$update.="";
		//echo $update;
		return $this->db_link->query($update);

	}
        
        public function findAll($tablename = "", $additionals = array()) {
            $finalResults = array();
            
            if (!empty($tablename)) {
                $sql = "SELECT ";
                if (empty($additionals)) {
                    $sql .= "* ";
                } else {
                    
                    if (isset($additionals['fields'])) {
                        $ffields = $this->__prepareFields($additionals['fields']);
                        $szFields = count($additionals['fields']) - 1;
                        $s = 0;
                        foreach ($additionals['fields'] as $fieldIdx => $field) {
                            
                            $sql.= $field;
                            $sql.=($s < $szFields ? ", " : " ");
                            $s++;
                        }
                    } else {
                        $sql .= "* ";
                    }
                }
                
                
                /*DO JOIN IF EXISTS*/
                if (isset($additionals['join'])) {
                    $type = isset($additionals['join']['type']) ? $additionals['join']['type'] : "LEFT JOIN";
                    list($startTable, $joins) = each($additionals['join']['tables']);
                     $sql .= "FROM " . $startTable . " ";
                     reset($additionals['join']['tables']);
                     //var_dump($additionals['join']['tables']);
                    foreach ($additionals['join']['tables'] as $tables => $table) {                   
                        
                        if (is_array($table)) {
                         
                           foreach ($table as $tbls => $tbl) {
                               $sql .= $type . " " .$tbls . " ON ";
                               list($left, $right) = each($tbl);
                               $sql.=$left ." = " . $right . " ";
                           }
                        }
                    }
                   
                    
                    
                    
                } else {
                    $sql .= "FROM " . $tablename;
                }
                
                //conditions
                
                if (isset($additionals['conditions']) && !empty($additionals['conditions'])) {
                    $sql .= " WHERE ";
                    $szConditions = count($additionals['conditions']) - 1;
                    $b = 0;
                    foreach ($additionals['conditions'] as $idxKey => $val) {
                        if (!strpos($idxKey, "OR") && !strpos($idxKey, "AND") && !strpos($idxKey, "IS") && !strpos($idxKey, "IN")) {
                            
                            $sql.=$idxKey . "=" . $val;
                        } else {
                            $sql.=$idxKey . "" . $val;
                        }
                        $sql .= ($b < $szConditions ? " AND " : " ");
                        $b++;
                    }
                }
                
                //order by
                if (isset($additionals['order'])) {
                    $sql .= " " . $additionals['order'];
                }
                
                if (isset($additionals['limit'])) {
                    $sql .= " " . $additionals['limit'];
                }
                
                //check if * is selected
                if (strpos($sql, "*") === 7) {
                    $ffields = $this->__getColumnNames($tablename);
                }
                
                //do the query
                $result = $this->db_link->query($sql);
               
                
                foreach ($result as $row) {
                    //additionally loop through prepared fields
                    $tmp = array();
                    foreach ($ffields as $ffield) {
                        
                        //if ($row[$ffield]) {
                            $tmp[$ffield] = $row[$ffield];
                        //}
                    }
                    
                    array_push($finalResults, $tmp);
                }
                
            }
            
            return $finalResults;
        }
        
        private function __prepareFields($fields = array()) {
            $preparedFields = array();
            $tmp = array();
            
            if (!empty($fields)) {
                foreach ($fields as $fieldIdx => $field) {
                    
                    if (strpos($field, "AS")) {
                        $tmp = explode("AS" , $field);                        
                    } else {
                        if (strpos($field,".")) {
                           $tmp = explode(".", $field);
                           
                           
                        } else {
                            $tmp[0] = $field;
                        }
                    }
                   
                    $exctractedIndex = count($tmp) - 1;
                    $preparedFields[] = trim($tmp[$exctractedIndex]);
                }
            }
            
            return $preparedFields;
        }
        
        private function __getColumnNames($tableName = ""){
            $sql = 'SHOW COLUMNS FROM ' . $tableName;
            $columnNames = array();
            $stmt= $this->db_link->prepare($sql);

            try {
                if($stmt->execute()){
                    $raw_column_data = $stmt->fetchAll();

                    foreach($raw_column_data as $outer_key => $array){
                        foreach($array as $inner_key => $value){

                            if ($inner_key === 'Field'){
                                    if (!(int)$inner_key){
                                       $columnNames[] = $value;
                                    }
                                }
                        }
                    }        
                }
                return $columnNames;
            } catch (Exception $e){
                return $e->getMessage();
            }
    }
    
    public function aggregate($aggrType = "count", $additionals = array()) {
        $allowedAggr = array("sum","count","avg","max");
        $fieldAlias = "aggregate_fieldname";
        $finalResults = array();
       
        if (isset($additionals['alias'])) {
            $fieldAlias = $additionals['alias'];
            unset($additionals['alias']);
        }
        $selectedFields = array($fieldAlias);
        
        $fieldToAggregate = "*";
        
        if (isset($additionals['field_to_aggregate'])) {
            $fieldToAggregate = $additionals['field_to_aggregate'];
            unset($additionals['field_to_aggregate']);
        }
        
        $select = "SELECT " . $aggrType ."(" . $fieldToAggregate . ") as " . $fieldAlias . " ";
        
        //additional fields?
        $fields = "";
        if (isset($additionals['fields'])) {
            $sz = count($additionals['fields']) - 1;
            for ($i=0; $i<count($additionals['fields']);$i++) {
                $fields .= $additionals['fields'][$i];
                $fields .= $i < $sz ? ", " : "";
                array_push($selectedFields, $additionals['fields'][$i]);
            }
        }
        $select .= $fields;
        $select .= " FROM ";
        
        //tables
        $sz = count($additionals['tables']) - 1;
        $tables = "";
        for ($c=0; $c<count($additionals['tables']); $c++) {
            $tables .= $additionals['tables'][$c];
            $tables .= $c < $sz ? ", " : "";
        }
        $select .= " " . $tables;
        
        //WHERE 
        $select .= " WHERE ";
        $where = "";
         if (isset($additionals['conditions']) && !empty($additionals['conditions'])) {
            $szConditions = count($additionals['conditions']) - 1;
            $b = 0;
            foreach ($additionals['conditions'] as $idxKey => $val) {
                if (!strpos($idxKey, "OR") && !strpos($idxKey, "AND") && !strpos($idxKey, "IS") && !strpos($idxKey, "IN")) {
                    $where.=$idxKey . "=" . $val;
                } else {
                    $where.=$idxKey . "" . $val;
                }
                $where .= ($b < $szConditions ? " AND " : " ");
                $b++;
            }
        }
        $select .= $where;
        
        //GROUP BY?
        if (isset($additionals['groupby'])) {
            $select .= " " . $additionals['groupby'];
        }
        //echo $select;
        $ffields = $this->__prepareFields($selectedFields);
        
        $result = $this->db_link->query($select);
        
        foreach ($result as $row) {
                    //additionally loop through prepared fields
                    $tmp = array();
                    
                    foreach ($ffields as $ffield) {
                        //var_dump($row[$ffield]);
                        if ($row[$ffield]) {
                            $tmp[$ffield] = $row[$ffield];
                        }
                    }
                    
                    array_push($finalResults, $tmp);
        }
        
        return $finalResults;
    }
    
    public function parseTableClassName($tableClass) {
        
        $classParts = explode("\\", $tableClass);
        
        $lastIndex = count($classParts) - 1;
        $tableName = str_replace(TABLECLASS_SUFFIX,"", $classParts[$lastIndex]);
        
        return $tableName;
    }
    
}

