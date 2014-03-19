<?php

/**
 * Project: KIDU
 * theme class
 * 
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
            $this->keys['image'] = str_replace("medium", "large", $theme->result->avatar_url);
            $this->keys['members'] = $theme->result->members_count;
            $this->keys['disclaimmer'] = $theme->result->fields->briefdescription->value;

            //get challenges from this theme
            $challenges = $this->core->getWs('group.get_groups',array('context'=>'sub-groups','guid'=>$this->getParameter(3)));
            $this->keys['num_subgroups'] = count($challenges->result);

            $conta_respostas_geral = 0;
            $allFiles_html = '';
            $challenge_html = '';
            foreach ($challenges->result as $challenge) {
            $conta_respostas_desafio = 0;
            $answers_html = '';
            $desafio_texto = false;
            
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$challenge->guid));
                if($answers->status == -20){header('Location: /logoff'); exit;};
            
            $imagem_primeira_resposta = false;
            $imagem_resposta = "<img src='/images/sem_resposta.gif' width='200' height='200' alt='" . $challenge->description . "'>\n";
            
                if(isset($answers->result)){
                    if($challenge->text_enabled == 'no'){//dentro do if estava perdendo a var...                    
                        foreach ($answers->result as $answer) {
                            if((gettype($answer->tags) == 'string' && $answer->tags == 'aprovado') || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
                            $conta_respostas_desafio++;
                                if(!$imagem_primeira_resposta || ((gettype($answer->tags) == 'array' && in_array('escolhido', $answer->tags)) || (gettype($answer->tags) == 'string' && strpos('escolhido',$answer->tags)))){
                                $imagem_resposta = "<img src='" . $answer->file_icon . "' width='200' height='200' alt='" . $challenge->description . "'>\n";
                                $imagem_primeira_resposta = true;
                                } else {
                                $imagem_primeira_resposta = false;
                                }
                            }
                        }
                    } else {
                        foreach ($answers->result as $answer) {
                            if((gettype($answer->tags) == 'string' && $answer->tags == 'aprovado') || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
                            $conta_respostas_desafio++;    
                                if(!$imagem_primeira_resposta || ((gettype($answer->tags) == 'array' && in_array('escolhido', $answer->tags)) || (gettype($answer->tags) == 'string' && strpos('escolhido',$answer->tags)))){
                                $imagem_resposta = "<div><p><span>" . strip_tags($answer->description) . "</span></p></div>";
                                $imagem_primeira_resposta = true;
                                } else {
                                $imagem_primeira_resposta = false;
                                }
                            }    
                        }
                    }       
                };
            //get answers for this challenge
            

                //echo json_encode($answers, true);
                // if(isset($answers->result)){
                // var_dump($desafio_texto);
                // $imagem_primeira_resposta = false;
                // $imagem_resposta = "<img src='/images/sem_resposta.gif' width='200' height='200' alt='" . $challenge->description . "'>\n";
                //     foreach ($answers->result as $answer) {
                //             if((gettype($answer->tags) == 'string' && $answer->tags == 'aprovado') || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
                //             $conta_respostas_desafio++;
                //             $conta_respostas_geral++;
                //                 if($desafio_texto === false){//se o tipo de resposta não for texto
                //                 echo("imagem");
                //                     if(!$imagem_primeira_resposta || ((gettype($answer->tags) == 'array' && in_array('escolhido', $answer->tags)) || (gettype($answer->tags) == 'string' && strpos('escolhido',$answer->tags)))){
                //                     $imagem_resposta = "<img src='" . $answer->file_icon . "' width='200' height='200' alt='" . $challenge->description . "'>\n";
                //                     $imagem_primeira_resposta = true;
                //                     } else {
                //                     $imagem_primeira_resposta = false;
                //                     }
                //                 } else {//se o tipo de resposta for texto
                //                 //echo("texto");
                //                     if(!$imagem_primeira_resposta || (((gettype($answer->tags) == 'array' && in_array('escolhido', $answer->tags)) || (gettype($answer->tags) == 'string' && strpos('escolhido',$answer->tags))))){
                //                     $imagem_resposta = "<div><p><span>" . strip_tags($answer->description) . "</span></p></div>";
                //                     $imagem_primeira_resposta = true;
                //                     } else {
                //                     $imagem_primeira_resposta = false;
                //                     }
                //                 }
                //         }
                //     }
                // }

            $allFiles_html .= $answers_html;
                
                // if($answers_html == '') {
                // $answers_html = $this->html->div('Não existem respostas para esse desafio ainda :(',array('class'=>'noAswers'));
                // }

                $challenge_html .= "<li>\n";
                $challenge_html .= "<a title='" . $challenge->description . "' href='/theme/challenge/" . $this->getParameter(3) . "/" . $challenge->guid . "'>\n";
                $challenge_html .= "<figure>\n";
                $challenge_html .= $imagem_resposta . "\n";
                $challenge_html .= "<figcaption><strong>" . $challenge->briefdescription . "</strong><br>\n";
                    if ($conta_respostas_desafio == 0){
                        $challenge_html .= "<small>Nenhuma resposta...</small>\n";
                    } else if ($conta_respostas_desafio == 1) {
                        $challenge_html .= "<small>" . $conta_respostas_desafio . " resposta</small>\n";
                    } else {
                        $challenge_html .= "<small>" . $conta_respostas_desafio . " respostas</small>\n";
                    }
                $challenge_html .= "</figcaption>\n";
                $challenge_html .= "</figure></a>\n";
                $challenge_html .= "</li>\n";

            //     <h3>'.$challenge->briefdescription.'</h3>
            //     <div>
            //     <span>
            //     <!--a href="/theme/challenges/'.$this->getParameter(3).'/'.$challenge->guid.'" -->
            //     <a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'" >
            //     <img src="/images/bot_faca_voce.gif" alt="Faça você!" width="110" height="40"></a>
            //     </span><!--br>
            //     <a href="/theme/challenge-answers/'.$challenge->guid.'">Ver mais respostas a esta questão.</a-->
            //     </div>
            //     <p>'.$challenge->description.'</p>
            //     </dt>';
                
            //     if($conta_respostas_desafio <= 3  && $conta_respostas_desafio > 0){
            //     $challenge_html .= '<dd class="sem_scroll">'. $answers_html .'</dd>';
            //     } else if ($conta_respostas_desafio > 3) {
            //     $challenge_html .= '<dd class="sombra_esquerda"><div onclick="rola_esquerda(this)"></div></dd><dd onscroll="aciona_sombra(this)" class="caixa"><div style="width: ' . 315 * $conta_respostas_desafio . 'px">'. $answers_html .'</div></dd><dd class="sombra_direita"><div onclick="rola_direita(this)"></div></dd>';
            //     } else {
            //     $challenge_html .= '<dd class="sem_scroll">'. $answers_html .'</dd>';
            //     }
            }

            // if($conta_respostas_geral <= 3  && $conta_respostas_geral > 0){
            // $todas_respostas = '<dd class="sem_scroll">'. $allFiles_html .'</dd>';
            // } else if ($conta_respostas_geral > 3) {
            // $todas_respostas = '<dd class="sombra_esquerda"><div onclick="rola_esquerda(this)"></div></dd><dd onscroll="aciona_sombra(this)" class="caixa"><div style="width: ' . 315 * $conta_respostas_geral . 'px">'. $allFiles_html .'</div></dd><dd class="sombra_direita"><div onclick="rola_direita(this)"></div></dd>';
            // } else {
            // $todas_respostas = '<dd class="sem_scroll">'. $allFiles_html .'</dd>';
            // }

            $this->keys['challenges'] = $challenge_html;
            //$this->keys['allFiles'] = $todas_respostas;
            
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
                    $challenge_list .= '<li><a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'"><img src="/images/estrela-cheia.gif" width="33" height="30" alt="estrela">'.$challenge->name.'</a></li>';
                } else {
                    $challenge_list .= '<li><a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenge->guid.'"><img src="/images/estrela-vazia.gif" width="33" height="30" alt="estrela">'.$challenge->name.'</a></li>';    
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

            if($challenge->result->text_enabled == 'yes') {
                $this->redirect('/theme/challengeText/'.$this->getParameter(3).'/'.$this->getParameter(4));
            }

            $this->keys['challenge_id'] = $this->getParameter(4);
            $this->keys['theme'] = $theme->result->name;
            $this->keys['icone_tema'] = '<a href="/theme/view/' . $this->getParameter(3) . '" id="icone_tema"><img src=' . $theme->result->avatar_url . ' width="100" height="100"></a>';
            $this->keys['challenge'] = $challenge->result->name;   
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$this->getParameter(4)));

            if($answers->status == -20){header('Location: /logoff');};

            $this->keys['answers'] = $this->core->pega_todas_respostas($answers,true);
            $this->keys['primeira_resposta'] = $this->core->pega_resposta_escolhida($answers);            
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
                $this->keys['back'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge-1].'" id="botao_desafio_anterior"><img src="/images/bot_desafio_anterior.gif" height="28" width="10"><span>Desafio<br>anterior</span></a>';
            }
            
            if(isset($challenges_list[$current_challenge+1]) && $challenges_list[$current_challenge+1] != '') {
                 $this->keys['next'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge+1].'" id="botao_proximo_desafio"><span>Próximo<br>desafio</span><img src="/images/bot_proximo_desafio.gif" height="28" width="10"></a>';
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
            $this->keys['icone_tema'] = '<a href="/theme/view/' . $this->getParameter(3) . '" id="icone_tema"><img src=' . $theme->result->avatar_url . ' width="100" height="100"></a>';
            $this->keys['theme'] = '<a href="/theme/view/' . $this->getParameter(3) . '">' . $theme->result->name . '</a>';
            $this->keys['challenge'] = $challenge->result->name;   
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$this->getParameter(4)));

            if($answers->status == -20){header('Location: /logoff');};

            $this->keys['answers'] = $this->core->pega_todas_respostas($answers,true);

            $this->keys['primeira_resposta'] = $this->core->pega_resposta_escolhida($answers);
            
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
                $this->keys['back'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge-1].'" id="botao_desafio_anterior"><img src="/images/bot_desafio_anterior.gif" height="28" width="10"><span>Desafio<br>anterior</span></a>';
            }
            
            //get other themes
            if(isset($challenges_list[$current_challenge+1]) && $challenges_list[$current_challenge+1] != '') {
                 $this->keys['next'] = '<a href="/theme/challenge/'.$this->getParameter(3).'/'.$challenges_list[$current_challenge+1].'" id="botao_proximo_desafio"><span>Próximo<br>desafio</span><img src="/images/bot_proximo_desafio.gif" height="28" width="10"></a>';
            } else {
                $this->keys['next'] = '';
            }

            return $this->keys;
        }

        public function _actionChallengeAnswers() {

            //get challenge details
            $challenge = $this->core->getWs('group.get',array('guid'=>$this->getParameter(3)));

            $this->keys['challenge_id'] = $this->getParameter(3);
            $this->keys['challenge'] = $challenge->result->name;   
            $this->keys['description'] = $challenge->result->fields->description->value;

            //get answers for this challenge
            $answers = $this->core->callWs('file.get_files',array('context'=>'group','group_guid'=>$this->getParameter(3)));
            if($answers->status == -20){header('Location: /logoff');};

            $this->keys['answers'] = $this->core->pega_todas_respostas($answers,true);
            
            //get other themes
            return $this->keys;
        }
               
}
?>
