<?php

/**
 * Project: Inter.net
 * 
 * @copyright Inter.net  www.br.inter.net
 * @author Rafael Franco <rfranco@team.br.inter.net>
 */
/**
 *  profile class
 *
 * @package admin
 * @author Rafael Franco
 * */
class profile extends simplePHP {
        
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
            }

            #if father's user not authorized that use, redirect
            if((CURRENT_ACTION != 'unauthorized' ) && (CURRENT_ACTION != 'authorize' ) && ($_SESSION['authorized'] != 'true')) {
              $this->redirect('/profile/unauthorized');
            }

            #set global keys
            $this->keys['head'] = $this->includeHTML('../view/profile/global/head.html');

            #header
            $this->keys['header'] = $this->includeHTML('../view/profile/global/header.html');

            #footer
            $this->keys['footer'] = $this->includeHTML('../view/profile/global/footer.html');

            #topo
            $this->keys['top'] = $this->includeHTML('../view/profile/global/top.html');

            //user data
            $this->keys['name_user'] = $_SESSION['name'];
            $this->keys['avatar'] = $_SESSION['avatar_url'];

        }

        public function _actionStart() {
            
            return $this->keys;
        }
        
               
}
?>
