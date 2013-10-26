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
            //get theme details
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            $this->keys['video'] = $theme->result->fields->interests->value;
            $this->keys['theme'] = $theme->result->name;
            $this->keys['members'] = $theme->result->members_count;

            //get challenges from this theme
            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
            foreach ($challenges->result as $challenge) {
                $answers_html = '';
                //get answers for this challenge
                $answers = $this->core->getWs('file.get_files',array('context'=>'group','group_guid'=>$challenge->guid));
                foreach ($answers->result as $answer) {
                   $answers_html .= '<figure>
                                    <img src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        <span><img src="/images/ico_curtir.gif" width="36" height="36">0</span>
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong>'.$answer->owner->name.'</strong>
                                        </figcaption>
                                    </figure>';

                    $allFiles_html .= $answers_html;
                }
                
                if($answers_html == '') {
                    $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
                }

                $challenge_html .= '<dt>
                                <h3>'.$challenge->briefdescription.'</h3>
                                <div>
                                    <span>
                                    <a href="/theme/challenges/'.$this->getParameter(3).'/'.$challenge->guid.'" >
                                    <img src="/images/bot_faca_voce.gif" alt="Faça você!" width="110" height="40"></a>
                                    </span><br>
                                    <a href="/theme/challenge-answers/'.$challenge->guid.'">Ver mais respostas a esta questão.</a>
                                </div>
                                <p>'.$challenge->description.'</p>
                            </dt>
                            <dd>'.
                                $answers_html
                            .'</dd>';
            }
            $this->keys['challenges'] = $challenge_html;
            $this->keys['allFiles'] = $allFiles_html ;
            
            //get other themes
            return $this->keys;
        }


        public function _actionChallenges() {
            //get theme details
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            //get challenge details
            $challenge = $this->core->getWs('group.get',array('guid'=>$this->getParameter(4)));

            
            $this->keys['theme'] = $theme->result->name;
            $this->keys['challenge'] = $challenge->result->name;
            $this->keys['description'] = $challenge->result->fields->description->value; 
            $this->keys['theme_id'] = $this->getParameter(3);
            $this->keys['challenge_id'] = $this->getParameter(4);    
            
            //get other themes
            return $this->keys;
        }


        public function _actionChallenge() {
            //get theme details
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            //get challenge details
            $challenge = $this->core->getWs('group.get',array('guid'=>$this->getParameter(4)));

            
            $this->keys['theme'] = $theme->result->name;
            $this->keys['challenge'] = $challenge->result->name;
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->getWs('file.get_files',array('context'=>'group','group_guid'=>$challenge->guid));
            foreach ($answers->result as $answer) {
                   $answers_html .= '<figure>
                                    <img src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        <span><img src="/images/ico_curtir.gif" width="36" height="36">0</span>
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong>'.$answer->owner->name.'</strong>
                                        </figcaption>
                                    </figure>';

                  
            }
            if($answers_html == '') {
                $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
            }

                
            $this->keys['challenges'] = $challenge_html;
            $this->keys['answers'] = $answers_html ;
            
            //get other themes
            return $this->keys;
        }
        
               
}
?>
