<?php

/**
 * Project: KIDU
 * profile class
 * 
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
                $this->keys['avatar'] = str_replace("medium", "small", $_SESSION['avatar_url']);
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

        public function _actionStart() {//meu proprio perfil
        $answers_html = '';
        $conta_respostas_desafio = 0;
        
        $answers = $this->core->callWs('file.get_files',array('context'=>'user','username'=>$_SESSION['username']));

            if($answers->status == -20){header('Location: /logoff');}

        $this->keys['answers'] = $this->core->pega_todas_respostas($answers,false);
        $this->keys['avatar_url'] = str_replace("medium", "large", $_SESSION['avatar_url']);

            return $this->keys;
        }
            

         public function _actionView() {//perfil de outro
            $username = $this->getParameter(3);

            $this->keys['name_user'] = $username;
            $user = $this->core->getWs('user.get_profile',array('username'=>$username));

            if($user->status != 0) {
                $this->keys['answers'] = 'Usuário não encontrado';
                return $this->keys;   
            }

            //get user answers
            $answers = $this->core->callWs('file.get_files',array('context'=>'user','username'=>$username));
            if($answers->status == -20){header('Location: /logoff');};

            $this->keys['answers'] = $this->core->pega_todas_respostas($answers,true);

            return $this->keys;
        }
        
        public function _actionAvatar() {//perfil de outro
            $username = $this->getParameter(3);

            $this->keys['name_user'] = $username;
            $user = $this->core->getWs('user.get_profile',array('username'=>$username));

            if($user->status != 0) {
                $this->keys['answers'] = 'Usuário não encontrado';
                return $this->keys;   
            }

            return $this->keys;
        }

        public function _actionsalvarAvatar() {//perfil de outro
        //echo $_POST['draw-image-result'];
        $answers = $this->core->callWs('user.save_avatar',array('draw-image-result'=>$_POST['draw-image-result']));
        var_dump($answers);
        }

}
?>
