<?php

/**
 * Project: SIMPLE PHP - Framework 
 * 
 * @copyright RFTI  www.rfti.com.br
 * @author Rafael Franco <rafael@rfti.com.br>
 */

/**
 *  admin class
 *
 * @package admin
 * @author Rafael Franco
 * */
class master extends simplePHP {
        public $session;

        public function __construct() {
         	global $keys;
                //load modules
                $this->session = $this->loadModule('session');

         	$this->keys['header'] = file_get_contents(SIMPLEPHP_PATH.'/app/code/view/master/header.php');
            
        }
        public function _actionStart() {
         	return $this->keys;
        }
        /**
         * Do login on Simple PHP Master area
         * */
        public function _actionLogin() {
        	if((MASTER_LOGIN == $_POST['login']) && (MASTER_PASSWD == $_POST['pass'])) {
                        $this->session->add('master','logged');
                        pr($this->session->values());
                } else {
                        $this->showError('Login e senha incorretos','/master');
                }
        }
}
?>
