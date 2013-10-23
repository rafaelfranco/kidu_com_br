<?php

/**
 * Project: KIDU
 * theme class
 * 
 * @copyright RFTI www.rfti.com.br
 * @author Rafael Franco <rafael@rfti.com.br>
 * @package theme
 */
class theme extends simplePHP {
        
        #initialize vars
        private $model;
        private $html;
        
        
        public function __construct() {    
			global $keys;

            #load model module
            $this->model = $this->loadModule('model');

            #load html module
            $this->html = $this->loadModule('html');
            
            #load core module
            $this->core = $this->loadModule('core','',true);
      			
            #if not logged in redirect to hotsite
            if(!$this->core->isLogged()) {
              $this->redirect('/');
            } else {
                //user data
                $this->keys['name_user'] = $_SESSION['name'];
                $this->keys['avatar'] = $_SESSION['avatar_url'];
            }

            #if father's user not authorized that use, redirect
            if((CURRENT_ACTION != 'unauthorized' ) && (CURRENT_ACTION != 'authorize' ) && ($_SESSION['authorized'] != 'true')) {
              $this->redirect('/theme/unauthorized');
            }

            #set global keys
            $this->keys['head'] = $this->includeHTML('../view/theme/global/head.html');

            #header
            $this->keys['header'] = $this->includeHTML('../view/profile/global/header.html');

            #footer
            $this->keys['footer'] = $this->includeHTML('../view/profile/global/footer.html');

            #topo
            $this->keys['top'] = $this->includeHTML('../view/profile/global/top.html');

            

        }

        public function _actionStart() {
            
            return $this->keys;
        }
        
               
}
?>
