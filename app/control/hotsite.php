<?php

/**
 * Project: Inter.net
 * 
 * @copyright Inter.net  www.br.inter.net
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
			

            #load tdd module
            #$this->keys['tests'] = '';
            #$tests = $this->loadModule('tests');
            #$this->keys['tests'] = $tests->loadTests();
            
            $this->keys['search'] = $_GET['search']; 
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
            //caso ja esteja logado manda pro perfil
            if(!empty($_SESSION['usuario_id'])){
              $this->redirect('/perfil');
            }
            $this->keys['dia_nascimento'] = $this->html->select('true',$this->days(),'dia_nascimento',1,0);
            $this->keys['mes_nascimento'] = $this->html->select('true',$this->mounths(),'mes_nascimento',1,0);
            $this->keys['ano_nascimento'] = $this->html->select('true',$this->years(1920,date('Y')),'ano_nascimento',1980,0);
            
            $this->keys['nome'] = (isset($_GET['nome'])) ? $_GET['nome'] : '' ; 
            $this->keys['email'] = (isset($_GET['email'])) ? $_GET['email'] : '' ; 

            return $this->keys;
        }
        
       /**
        * _actionLocais function
        * @return array
        * */
       public function _actionLogoff() {
          unset($_SESSION['user_token']);
          $this->redirect('/');
       }     
            
          
             
        
}
?>
