<?php
/**
 * Project: Fashionera
 * 
 * @copyright Fashionera - www.fashionera.com.br
 * @author Rafael Franco <rfranco@steam.br.inter.net>
 * @package painel
 * */
class core extends simplePHP {
        
        private $model;
        private $html;

        #initialize vars
        public function __construct() {    
        	$this->model = $this->loadModule('model');
            $this->html = $this->loadModule('html');
        }
        
        /**
         *  isLogged
         * @return boolean
         * */
        public function isLogged() {
            if(empty($_SESSION['usuario_id'])) {
                return false;
            } else {
                return true;
            }
        }

        public function getUserData($user_id) {
            $res = $this->model->getData('usuario','a.*, UNIX_TIMESTAMP(tempo) as time ', array('id' =>$user_id));
            return $res[0];
        }

        public function getCurrentUserData() {
            return $this->getUserData($_SESSION['usuario_id']);
        }
}
?>
