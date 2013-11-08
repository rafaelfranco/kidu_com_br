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
                $this->keys['loggeduser'] = $_SESSION['username'];
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
                $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$challenge->guid));
                foreach ($answers->result as $answer) {
                    if($answer->tags == 'aprovado') {
                        $likeIcon = $this->core->likeIcon($answer->guid,$answer->likes);
                        $answers_html .= '<figure ">
                                    <img onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                        '.$likeIcon.'
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>
                                        </figcaption>
                                    </figure>';
                    }

                    
                }
                $allFiles_html .= $answers_html;
                if($answers_html == '') {
                    $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
                }

                $challenge_html .= '<dt>
                                <h3>'.$challenge->briefdescription.'</h3>
                                <div>
                                    <span>
                                    <!--a href="/theme/challenges/'.$this->getParameter(3).'/'.$challenge->guid.'" -->
                                    <a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'" >
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
            for($i=1;$i <= 5;$i++) {
                if($i <= $_SESSION['nivel']) {
                    $this->keys['nivel-'.$i] = '';
                } else {
                    $this->keys['nivel-'.$i] = 'class="fechado"';
                }
            }
            
            $this->keys['theme'] = $theme->result->name;
            $this->keys['theme_id'] = $this->getParameter(3);
            $this->keys['challenge_id'] = $this->getParameter(4);
            $this->keys['nivel'] = $_SESSION['nivel'];
            
            
            $doneChallenges = $this->core->getDoneChallenges();

            //get challeges for this theme and nivel
            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
            foreach ($challenges->result as $challenge) {
                if(in_array($challenge->guid, $doneChallenges)) {
                    $challenge_list .= '<li><img src="/images/estrela-cheia.gif" width="33" height="30" alt="estrela"><a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'">'.$challenge->name.'</a></li>';
                } else {
                    $challenge_list .= '<li><img src="/images/estrela-vazia.gif" width="33" height="30" alt="estrela"><a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'">'.$challenge->name.'</a></li>';    
                }
                
            }
            $this->keys['challengeList'] = $challenge_list;
            $x = 0;
            $a = 1;
            //apply challenge guide rules
            for($i=1;$i<=count($doneChallenges);$i++) {
                $x++;
                if($x == 1) {
                    $stars_list .= '<li>'; 
                }
                $stars_list .= '<img src="/images/estrela.gif" width="22" height="22" alt="estrela">';
                if($x == 3) {
                    $a++;
                    $stars_list .= '</li>'; 
                    $x = 0;
                    $this->keys['nivel-'.$a] = '';
                }
            }
            $this->keys['stars'] = $stars_list;

            //get other themes
            return $this->keys;
        }


        public function _actionChallenge() {

            //get theme details
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            //get challenge details
            $challenge = $this->core->getWs('group.get',array('guid'=>$this->getParameter(4)));

            if($challenge->result->text_enabled == 1) {
                $this->redirect('/theme/challengeText/'.$this->getParameter(3).'/'.$this->getParameter(4));
            }

            $this->keys['challenge_id'] = $this->getParameter(4);
            $this->keys['theme'] = $theme->result->name;
            $this->keys['challenge'] = $challenge->result->name;   
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$this->getParameter(4)));

            foreach ($answers->result as $answer) {
                if($answer->tags == 'aprovado') {
                    $likeIcon = $this->core->likeIcon($answer->guid,$answer->likes);
                    $answers_html .= '<figure>
                                    <img  onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                       '.$likeIcon.'
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>
                                        </figcaption>
                                    </figure>';
                    }     
            }
            if($answers_html == '') {
                $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
            }

                
            $this->keys['challenges'] = $challenge_html;
            $this->keys['answers'] = $answers_html ;
            
            //ADD USER TO THOSE GROUPS
            #username
            
            #groupid group.join
            $res = $this->core->callWs('group.join',array('username'=>$_SESSION['username'],'groupid'=>$this->getParameter(3)));
            $res = $this->core->callWs('group.join',array('username'=>$_SESSION['username'],'groupid'=>$this->getParameter(4)));


            #get all challenges on this theme to make arrows links
            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
            $x = 0;
            foreach ($challenges->result as $challenge) {
                $challenges_list[$x] = $challenge->guid;
                if($challenge->guid == $this->getParameter(4)) {
                    $current_challenge = $x;
                }
                $x++;
            }
            
            if($current_challenge == 0) {
                $this->keys['back'] = '';
            } else {
                $this->keys['back'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge-1].'" id="botao_desafio_anterior"><span>Desafio<br>anterior</span><img src="/images/bot_desafio_anterior.gif" height="110" width="17"></a>';
            }
            
            if($challenges_list[$current_challenge+1] != '') {
                 $this->keys['next'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge+1].'" id="botao_proximo_desafio"><img src="/images/bot_proximo_desafio.gif" height="110" width="17"><span>Próximo<br>desafio</span></a>';
            } else {
                $this->keys['next'] = '';
            }
            //get other themes
            return $this->keys;
        }
        

        public function _actionChallengeText() {

            //get theme details
            $theme = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            //get challenge details
            $challenge = $this->core->getWs('group.get',array('guid'=>$this->getParameter(4)));
            
            $this->keys['challenge_id'] = $this->getParameter(4);
            $this->keys['theme'] = $theme->result->name;
            $this->keys['challenge'] = $challenge->result->name;   
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$this->getParameter(4)));

            foreach ($answers->result as $answer) {
                if($answer->tags == 'aprovado') {
                    $likeIcon = $this->core->likeIcon($answer->guid,$answer->likes);
                    $answers_html .= '<figure>
                                    <img  onclick="showModal('.$answer->guid.')" src="'.$answer->file_icon.'" height="285" width="285" alt="Kidu">
                                    <figcaption>
                                       '.$likeIcon.'
                                        <img src="/images/ico_usuario.gif" width="36" height="36" alt="User">   
                                        <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>
                                        </figcaption>
                                    </figure>';
                    }     
            }
            if($answers_html == '') {
                $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
            }

                
            $this->keys['challenges'] = $challenge_html;
            $this->keys['answers'] = $answers_html ;
            
            //ADD USER TO THOSE GROUPS
            #username
            
            #groupid group.join
            $res = $this->core->callWs('group.join',array('username'=>$_SESSION['username'],'groupid'=>$this->getParameter(3)));
            $res = $this->core->callWs('group.join',array('username'=>$_SESSION['username'],'groupid'=>$this->getParameter(4)));


            #get all challenges on this theme to make arrows links
            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
            $x = 0;
            foreach ($challenges->result as $challenge) {
                $challenges_list[$x] = $challenge->guid;
                if($challenge->guid == $this->getParameter(4)) {
                    $current_challenge = $x;
                }
                $x++;
            }
            
            if($current_challenge == 0) {
                $this->keys['back'] = '';
            } else {
                $this->keys['back'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge-1].'" id="botao_desafio_anterior"><span>Desafio<br>anterior</span><img src="/images/bot_desafio_anterior.gif" height="110" width="17"></a>';
            }
            
            if($challenges_list[$current_challenge+1] != '') {
                 $this->keys['next'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge+1].'" id="botao_proximo_desafio"><img src="/images/bot_proximo_desafio.gif" height="110" width="17"><span>Próximo<br>desafio</span></a>';
            } else {
                $this->keys['next'] = '';
            }
            //get other themes
            return $this->keys;
        }
               
}
?>
