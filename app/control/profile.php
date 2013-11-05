<?php

/**
 * Project: KIDU
 * profile class
 * 
 * @copyright RFTI www.rfti.com.br
 * @author Rafael Franco <rafael@rfti.com.br>
 * @package profile
 */
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
            } else {
                //user data
                $this->keys['name_user'] = $_SESSION['name'];
                $this->keys['avatar'] = $_SESSION['avatar_url'];
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

            

        }

        public function _actionStart() {
            //get user answers
            $answers = $this->core->callWs('file.get_files',array('context'=>'user','username'=>$_SESSION['username']));
           
            foreach ($answers->result as $answer) {
                if($answer->access_id == 1) {
                    $answers_html .= '<figure class="oculto">
                                        <img onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                       <figcaption>
                                        <strong>Conteúdo oculto</strong>
                                        <a href="">Por quê?</a>
                                        </figcaption>
                                    </figure>';
                } else {
                    $answers_html .= '<figure>
                                        <img onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                        <figcaption>
                                            <span><img src="/images/ico_curtir.gif" class="likeButton" width="36" height="36">'.$answer->likes.'</span>
                                            <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                            <strong>'.$answer->owner->name.'</strong>
                                        </figcaption>
                                    </figure>';
                }
                
                $allFiles_html .= $answers_html;
            }

            if($answers_html == '') {
                $answers_html = $this->html->div('Você ainda não respondeu desafios :(',array('class'=>'noAswers noneAnswers'));
            }

            $this->keys['answers'] = $answers_html;

            return $this->keys;
        }
        
               
}
?>
