<?php

/**
 * Project: SIMPLE PHP - Framework 
 * 
 * @copyright RFTI  www.rfti.com.br
 * @author Rafael Franco <rafael@rfti.com.br>
 */

/**
 * model module
 *
 * @package model
 * @author Rafael Franco
 * */
class model {

        public $debug = 0;

        public function __construct() {
                
        }
    
        /**
         * get list create lists for key => $value
         *
         * @return array
         * @author Rafael Franco
         * */
        public function getList($table, $key='id', $value='name', $filters='', $function_key='_void', $function_value='_void') {

                $data = $this->getData($table, "a.$key,a.$value", $filters, '', "a.$value asc");
                foreach ($data as $item) {
                        if ($function_key == '_void') {
                                $return[$item[$key]] = $item[$value];
                        } else {
                                $return[$function_key($item[$key])] = $item[$value];
                        }
                }
                return $return;
        }

        public function enableDebug() {
                $this->debug = 1;
        }

        public function disableDebug() {
                $this->debug = 0;
        }

        public function _void($var) {
                return($var);
        }

        /**
         * get data from database
         * @param $table string, the nao of db table
         * @param $values string, list of values
         * @param $filters array list of filters
         * @param $limits array - start | limit
         * @param $orderby string
         * @param $additional string
         */
        public function getData($table, $values='a.*', $filters = '', $limits='', $orderby='a.ID DESC', $join='', $groupby='') {

                global $mdb2;

                $sql = "SELECT $values "
                        . "FROM $table as a "
                        . " $join "
                        . "WHERE a.id >= 1 ";

                #makeFilters
                $sql .= $this->makeFilters($filters);


                if ($groupby != '') {
                        $sql .= " group by $groupby ";
                }

                $sql .= " ORDER BY " . $orderby . " ";

                if ($limits != '') {
                        $sql .= "Limit $limits[start], $limits[limit];";
                }
                if ($this->debug == 1) {
                        echo "<br><b>$sql</b><br>";
                }
                
                $res = $mdb2->loadModule('Extended')->getAll($sql, null, array(), '', MDB2_FETCHMODE_ASSOC);

                if (count($res) == 0) {
                        $res[0]['result'] = 'empty';
                }
  
                #$arq = fopen("../logs/query-select-errors.txt",'a+');
                #fwrite($arq,$sql." - ".@date('d/m/Y h:i:s').'/n');
                return $res;
        }

        
        
        private function makeFilters($filters){ 
				$sql = ''; 
                if ($filters != '') {
                        foreach ($filters as $key => $value) {
                               if (substr(trim($key), 0, 2) != 'in') {
                                $key    = addslashes($key);
                                $value  = addslashes($value);
                               }
                                
                                if (substr_count($key, 'like') == 1) {
                                        if (substr(trim($key), 0, 9) == 'likeAfter') {
                                             $key = str_replace('likeAfter', '', $key);
                                             $sql .= "AND $key like '$value%' ";   
                                        } else {
                                            if (substr(trim($key), 0, 10) == 'likeBefore') {
                                                $key = str_replace('likeBefore', '', $key);
                                                $sql .= "AND $key like '%$value' ";   
                                            } else {
                                                $key = str_replace('like ', '', $key);
                                                $sql .= "AND $key like '%$value%' ";   
                                            }     
                                        }
                                } else if (substr(trim($key), 0, 5) == 'orlik') {
                                        $key = str_replace('orlik ', '', $key);
                                        $sql .= "OR  $key like '%$value%' ";
                                } else if (substr(trim($key), 0, 4) == 'orin') {
                                        $key = str_replace('orin ', '', $key);
                                        $sql .= "OR $key in $value ";
                                } else if (substr(trim($key), 0, 2) == 'or') {
                                        $key = str_replace('or ', '', $key);
                                        $sql .= "OR $key = '$value' ";
                                } else if (substr(trim($key), 0, 2) == 'in') {
                                        $key = str_replace('in ', '', $key);
                                        $sql .= "AND $key in $value ";
                                } else if (substr(trim($key), 0, 5) == 'notin') {
                                        $key = str_replace('notin ', '', $key);
                                        $sql .= "AND $key not in $value ";
                                } else if (substr(trim($key), 0, 3) == 'dif') {
                                        $key = str_replace('dif', '', $key);
                                        $sql .= "AND $key != '$value' ";
                                } else if (substr(trim($key), 0, 1) == '<') {
                                        $key = str_replace('<', '', $key);
                                        $sql .= "AND $key < $value ";
                                } else if (substr(trim($key), 0, 1) == '>') {
                                        $key = str_replace('>', '', $key);
                                        $sql .= "AND $key > $value ";
                                } else {
                                        if (is_string($value)) {
                                                $sql .= "AND $key = '$value' ";
                                        } else {
                                                $sql .= "AND $key = $value ";
                                        }
                                }
                        }
                }
              
                return $sql;
        }
        
        
        /**
         * Add data to database
         * @global <object> $mdb2
         * @param <string> $table
         * @param <array> $data
         * @return <boolean>
         */
        public function addData($table, $data,$saveIp = false) {
                global $mdb2;

                if($saveIp) {
                    $data['ip'] = $_SERVER['REMOTE_ADDR'];
                }

                $sql = "INSERT INTO $table ( `id` ";
                extract($data);

                $id = "NULL";

                foreach ($data as $key => $value) {
                        $sql .= ",`$key`";
                }
                $sql .= ") VALUES ( " . $id . " ";
                foreach ($data as $key => $value) {
                        $key    = addslashes($key);
                        $value  = addslashes($value);
                                
                        if ($value == 'NULL') {
                                $sql .= ", $value";
                        } else if (is_string($value)) {
                                $sql .= ", '$value'";
                        } else {
                                if ($value) {
                                        $sql .= ", $value";
                                } else {
                                        $sql .= ", ''";
                                }
                        }
                }
                $sql .= ");";
                
                if ($this->debug == 1) {
                        echo "<br><b>$sql</b><br>";
                }        
                $mdb2->query($sql);
                if (mysql_insert_id() == 0) {
                        $arq = fopen("../logs/query-insert-errors.txt", 'a+');
                        fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
				');
                        return array('status'=>'erro','sql'=>$sql);
                } else {
                        return mysql_insert_id();
                }
        }              


		 public function replaceData($table, $data,$saveIp = false) {
	                global $mdb2;

	                if($saveIp) {
	                    $data['ip'] = $_SERVER['REMOTE_ADDR'];
	                }

	                $sql = "REPLACE INTO $table ( `id` ";
	                extract($data);

	                $id = "NULL";

	                foreach ($data as $key => $value) {
	                        $sql .= ",`$key`";
	                }
	                $sql .= ") VALUES ( " . $id . " ";
	                foreach ($data as $key => $value) {
	                        $key    = addslashes($key);
	                        $value  = addslashes($value);

	                        if ($value == 'NULL') {
	                                $sql .= ", $value";
	                        } else if (is_string($value)) {
	                                $sql .= ", '$value'";
	                        } else {
	                                if ($value) {
	                                        $sql .= ", $value";
	                                } else {
	                                        $sql .= ", ''";
	                                }
	                        }
	                }
	                $sql .= ");";
	                if ($this->debug == 1) {
	                        echo "<br><b>$sql</b><br>";
	                }        
	                $mdb2->query($sql);
	                if (mysql_insert_id() == 0) {
	                        $arq = fopen("../logs/query-insert-errors.txt", 'a+');
	                        fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
					');
	                        return array('status'=>'erro','sql'=>$sql);
	                } else {
	                        return mysql_insert_id();
	                }
	        }

        public function countData($table, $filters = '', $additional = '', $distinct='', $join = "") {
                global $mdb2;

                if ($distinct == '') {
                        $sql = "SELECT count(a.id) as qtd ";
                } else {
                        $sql = "SELECT count(distinct($distinct)) as qtd ";
                }
                
                $t = explode(' ',$table);

                $sql    .= "FROM $t[0] as a  "
                        . " $join "
                        . "WHERE a.id >= 1 ";

                #makefilters     
                $sql .= $this->makeFilters($filters);
                
                
                if ($additional) {
                        $sql .= " " . $additional;
                }
               
                if($this->debug == 1) {
                        echo "<br><b>$sql</b><br>";
                }
               

                $res = $mdb2->loadModule('Extended')->getAll($sql, null, array(), '', MDB2_FETCHMODE_ASSOC);
                return $res[0]['qtd'];
        }

        public function removeData($table, $filters) {
                global $mdb2;
                $sql = "Delete  "
                        . "FROM $table "
                        . "WHERE id >= 1 ";
                if ($filters != '') {
                        if ($filters != '') {
                                foreach ($filters as $key => $value) {
                                        $key    = addslashes($key);
                                        $value  = addslashes($value);
                                
                                        if (substr_count($value, 'like') == 1) {
                                                $value = str_replace('like ', '', $value);
                                                $sql .= "AND $key like '%$value%' ";
                                        } else if (substr(trim($value), 0, 2) == 'in') {
                                                $value = str_replace('in ', '', $value);
                                                $sql .= "AND $key in $value ";
                                        } else if (substr(trim($value), 0, 2) == '<=') {
                                                $value = str_replace('<= ', '', $value);
                                                $sql .= "AND $key <= '$value' ";
                                        } else if (substr(trim($value), 0, 2) == '>=') {
                                                $value = str_replace('>= ', '', $value);
                                                $sql .= "AND $key >= '$value' ";
                                        } else {
                                                if (is_string($value)) {
                                                        $sql .= "AND $key = '$value' ";
                                                } else {
                                                        $sql .= "AND $key = $value ";
                                                }
                                        }
                                }
                        }
                }
                if ($this->debug == 1) {
                        echo "<br><b>$sql</b><br>";
                }

                $res = $mdb2->query($sql);
                if (@$res->result != 1) {
                        $arq = fopen("../logs/query-remove-errors.txt", 'a+');
                        fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
										');
                        die('Error not removed data - See log file');
                } else {
                        return true;
                }
        }


        //Adicionar imagem como principal
        public function imgPrincipal($table, $filters) {

                global $mdb2;
                $sql = "UPDATE $table SET featured = '0' WHERE items_id = ".$filters['idProduto'];

                $res = $mdb2->query($sql);
                if (@$res->result != 1) {
                    $arq = fopen("../logs/query-remove-errors.txt", 'a+');
                    fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
                                    ');
                    die('Error not removed data - See log file');
                } else {

                    $sql = "UPDATE $table SET featured = '1' WHERE id = ".$filters['id'];
                    $res = $mdb2->query($sql);

                    if (@$res->result != 1) {
                        $arq = fopen("../logs/query-remove-errors.txt", 'a+');
                        fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
                                        ');
                        die('Error not removed data - See log file');
                    } else {
                        return true;
                    }
                }
        }


        /**
         * Update mysql data
         * @global  $mdb2
         * @param <string> $table
         * @param <array> $data
         * @param <array> $filter
         * @return <type>
         */
        public function alterData($table, $data, $filter) {
                global $mdb2;

                $sql = "UPDATE $table ";
                extract($data);
                $sql .= ' SET';
                foreach ($data as $key => $value) {
                        # $value = str_replace("'",'"',$value);
                        $value = addslashes($value);
                        if (is_string($value)) {
                                $sql .= " $key = '$value' ,";
                        } else {
                                if ($value) {
                                        $sql .= " $key = $value ,";
                                } else {
                                        $sql .= " $key = '' ,";
                                }
                        }
                }

                $sql = substr($sql, 0, strlen($sql) - 1);

                $sql .= "WHERE 1=1 ";
                if (is_array($filter)) {
                        foreach ($filter as $key => $value) {
                                if (is_string($value)) {
                                        $sql .= "AND $key = '$value' ";
                                } else {
                                        $sql .= "AND $key = $value ";
                                }
                        }
                }


                $res = $mdb2->query($sql);
                
                if ($this->debug == 1) {
                        echo "<br><b>$sql</b><br>";
                }  
                if (@$res->result != 1) {
                        $arq = fopen("../logs/query-update-errors.txt", 'a+');
                        fwrite($arq, $sql . " - " . @date('d/m/Y h:i:s') . '
						');
                        die('Error not updated data - See log file');
                } else {
                        return true;
                }
        }

}

?>