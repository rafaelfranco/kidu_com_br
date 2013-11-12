<?php

/**
 * Project: KIDU
 * 
 * @copyright RFTI www.rfti.com.br
 * @author Rafael Franco <rfranco@team.br.inter.net>
 */
/**
 *  hotsite class
 *
 * @package admin
 * @author Rafael Franco
 * */
class hotsite extends simplePHP {
        
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
            if($this->core->isLogged()) {
                //user data
                
                $this->keys['loggeduser'] = $this->keys['name_user']  = $_SESSION['username'];
                $this->keys['avatar'] = $_SESSION['avatar_url'];
            }

            #load tdd module
            #$this->keys['tests'] = '';
            #$tests = $this->loadModule('tests');
            #$this->keys['tests'] = $tests->loadTests();
            
            $this->keys['search'] = $this->getParameter(2); 
            #set global keys

            #include system globals
            $this->keys['head'] = $this->includeHTML('../view/hotsite/global/head.html');

            #header
            $this->keys['header'] = $this->includeHTML('../view/hotsite/global/header.html');

            #footer
            $this->keys['footer'] = $this->includeHTML('../view/hotsite/global/footer.html');

            #topo
            $this->keys['top'] = $this->includeHTML('../view/hotsite/global/top.html');


        }

        public function _actionStart() {
            //caso ja esteja logado manda pra home logada

            if(!empty($_SESSION['username'])){
              $this->redirect('/home');
            } 

            return $this->keys;
        }

        public function _actionHome() {
            //busca  a lista de comunidades
            if(isset($_POST['searchInput']) && $_POST['searchInput']!= '') {
                $this->redirect('/home/'.$_POST['searchInput']);
            }
            $this->keys['search'] = $this->getParameter(2);

            if(!empty($_SESSION['username'])){
              $this->keys['footer'] = '';
            } 
            return $this->keys;
        }

        public function _actionFathers() {
            //busca  a lista de comunidades
            return $this->keys;
        }

        public function _actionTeachers() {
            //busca  a lista de comunidades
            return $this->keys;
        }

        public function _actionHelp() {
            //busca  a lista de comunidades
            return $this->keys;
        }

        public function _actionTerms() {
            //busca  a lista de comunidades
            return $this->keys;
        }

        public function _actionContact() {
            //busca  a lista de comunidades
            return $this->keys;
        }
        
       /**
        * _actionLocais function
        * @return array
        * */
       public function _actionLogoff() {
          unset($_SESSION['username']);
          $this->redirect('/');
       }                
        
}
?>
