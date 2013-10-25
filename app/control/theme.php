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

        public function _actionView() {
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            $this->keys['video'] = $theme->result->fields->interests->value;
            $this->keys['theme'] = $theme->result->name;
            $this->keys['members'] = $theme->result->members_count;

            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
           
            foreach ($challenges->result as $challenge) {
                
                $desafios .= '<dt>
                                <h3>'.$challenge->briefdescription.'</h3>
                                <div>
                                    <span>
                                    <a href="/theme/challenge/'.$challenge->guid.'" >
                                    <img src="/images/bot_faca_voce.gif" alt="Faça você!" width="110" height="40"></a>
                                    </span><br>
                                    <a href="/theme/challenge-answers/'.$challenge->guid.'">Ver mais respostas a esta questão.</a>
                                </div>
                                <p>'.$challenge->description.'</p>
                            </dt>
                            <dd>
                                <figure>
                                    <img src="/images/minininha.jpg" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        <span><img src="/images/ico_curtir.gif" width="36" height="36">13</span>
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong>Nome da pessoa</strong>
                                        </figcaption>
                                </figure>
                                <figure>
                                    <img src="/images/minininha.jpg" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        <span><img src="/images/ico_curtir.gif" width="36" height="36">13</span>
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong>Nome da pessoa</strong>
                                        </figcaption>
                                </figure>

                                <figure>
                                    <img src="/images/minininha.jpg" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        <span><img src="/images/ico_curtir.gif" width="36" height="36">13</span>
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong>Nome da pessoa</strong>
                                        </figcaption>
                                </figure>
                            </dd>';
            }
            $this->keys['desafios'] = $desafios;
            
            
            return $this->keys;
        }
        
               
}
?>
