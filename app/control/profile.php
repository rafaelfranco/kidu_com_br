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
                $this->keys['loggeduser'] = $this->keys['name_user']  = $_SESSION['username'];
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
        $answers_html = '';
        $allFiles_html = '';
        $conta_respostas_desafio = 0;
        
        $answers = $this->core->callWs('file.get_files',array('context'=>'user','username'=>$_SESSION['username']));
            if($answers->status == -20){header('Location: /logoff');};

            foreach ($answers->result as $answer) {
                $answers_html .= $this->core->answerHtml($answer);
                $allFiles_html .= $answers_html;
                $conta_respostas_desafio++;
            }

            if($answers_html == '') {
                $answers_html = $this->html->div('Você ainda não respondeu desafios :(',array('class'=>'noAswers noneAnswers'));
            }

            if($conta_respostas_desafio < 3  && $conta_respostas_desafio > 0){
            $respostas = '<dd class="sem_scroll">'. $answers_html .'</dd>';
            } else if ($conta_respostas_desafio > 3) {
            $respostas = '<dd><div style="width: ' . 315 * $conta_respostas_desafio . 'px">'. $answers_html .'</div></dd>';
            } else {
            $respostas = '<dd class="sem_scroll">'. $answers_html .'</dd>';
            }

            $this->keys['answers'] = $respostas ;

            return $this->keys;
        }
            

         public function _actionView() {
            $username = $this->getParameter(3);

            $this->keys['name_user'] = $username;

            //get user answers
            $answers = $this->core->callWs('file.get_files',array('context'=>'user','username'=>$username));
           if($answers->status == -20){header('Location: /logoff');};

            $answers_html = '';
            $conta_respostas_desafio = 0;
            foreach ($answers->result as $answer) {
                if($answer->tags != 'aprovado') {
                    $answers_html .= '<figure class="resposta oculto">
                                        <img onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                       <figcaption>
                                        <strong>Conteúdo oculto</strong>
                                        <a href="" onclick="this.parentNode.getElementsByTagName(\'span\')[0].style.display = \'inline\'; return false;">Por quê?</a>
                                        <span class="aviso" onclick="this.style.display = \'none\';"><strong>Fechar</strong><br><br>Este conteúdo ainda não pode ser exibido porque não foi avaliado pelos educadores do Kidu. Aguarde.</span>
                                        </figcaption>
                                    </figure>';
                } else {
                    $answers_html .= '<figure class="resposta">
                                        <img onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                        <figcaption>
                                            <span  onclick="likeItem('.$answer->guid.');" ><img src="/images/ico_curtir.gif" class="likeButton" width="36" height="36"><b id="likes-'.$answer->guid.'" >'.$answer->likes.'</b></span>
                                            <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                            <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>
                                        </figcaption>
                                    </figure>';
                }
                
            $conta_respostas_desafio++;
            }

            if($answers_html == '') {
                $answers_html = $this->html->div('Você ainda não respondeu desafios :(',array('class'=>'noAswers noneAnswers'));
            }

            if($conta_respostas_desafio < 3  && $conta_respostas_desafio > 0){
            $respostas = '<dd class="sem_scroll">'. $answers_html .'</dd>';
            } else if ($conta_respostas_desafio > 3) {
            $respostas = '<dd><div style="width: ' . 315 * $conta_respostas_desafio . 'px">'. $answers_html .'</div></dd>';
            } else {
            $respostas = '<dd class="sem_scroll">'. $answers_html .'</dd>';
            }

            $this->keys['answers'] = $respostas ;

            return $this->keys;
        }
        
}
?>
