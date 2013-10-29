<?php
/**
 * Project: KIDU
 * 
 * @copyright KIDU - www.kidu.com.br
 * @author Rafael Franco rafael@rfti.com.br
 * @package action
 * 
 * * Classe responsavel pelo controle dos sub módulos do perfil action no painel de controle
 * */

class action extends simplePHP {
  private $core;
  private $keys;
  private $model;
  private $html;

  public function __construct() {  

      #load core module
      $this->core = $this->loadModule('core','',true);

      #load model module
      $this->model = $this->loadModule('model');

      #load html module
      $this->html = $this->loadModule('html');
  }

  public function _actionStart() {  
    
  }


  /**
   * _actionSignup function
   * @return array
   * */
  public function _actionSignup() {
      $data = $_POST;
      
      //do signup
      $response = $this->core->getWs('user.register',$data);

      if($response->status == -1) {
        echo 'erro;';
        echo $response->message;
      } else {
        //do login
        $this->core->doLogin($data['username'],$data['password']);
        echo 'success;';
      }
      exit;
  }   

  /**
    * _actionSignin function
    * @return string
    * */
  public function _actionSignin() {
      if($this->core->doLogin($_POST['username'],$_POST['password'])) {
        echo 'success;';
      } else {
        echo 'erro;login ou senha incorretos';
      }
      exit;
   }

  /**
  * _actionAuthorize function
  * #father authorize user to enter the site
  */
  public function _actionAuthorize() {
    $res = $this->core->getWs('user.authorize',array('guid'=>$_SESSION['guid'],'facebook_id'=>$_POST['facebook_id'],'fb_access_token'=>$_POST['fb_access_token']));
    if($res->result == 1) {
      $_SESSION['authorized'] = 'true';
      echo 'success';
      exit;
    }
  }

  public function _actionGetgroups() {
    $res = $this->core->getWs('group.get_groups',array('context'=>'featured'));
    
    foreach ($res->result as $group) {
      echo '<a href="/theme/view/'.$group->guid.'"><div>'.$group->name.'</div></a>';
    }
    exit;
    
  }
  public function _actionPostfile() {
    
    #save file local
    $file = $this->loadModule('file');
    $file_name = $file->uploadFile($_FILES['upload'],APP_PATH.'/public/tmp/');

    $link =  'http://'.$_SERVER['HTTP_HOST'].'/tmp/'.$file_name;

    $res = $this->core->callWs('file.upload',array('filepath'=>$link,'container_guid'=>$_POST['challenge_id'],'user_guid'=>$_SESSION['guid'],'access_id'=>1));
    
    //redirect to profile
    if($res->status == 0) {
      $this->redirect('/profile');
    } else {
      $this->redirect('/logoff');
    }
  }

  public function _actionGetFile() {
        $file_id = $this->getParameter(3);
        $file = $this->core->getWs('file.get_files',array('guid'=>$file_id,'context'=>'one'));

        $img = str_replace('small', 'full', $file->result[0]->file_icon);
       
       # pre($file->result[0]);
       echo '<dl style="left: 213px;">
        <dt>
        <span onclick="fecha_modal()">Fechar | X</span>
        <h4>'.$file_id .'</h4>
        <p>Nome do tema</p>

        <h4>Desafio</h4>
        <p>Nome do desafio pra onde esta resposta foi postada</p>
        <p><br></p>
        <p>Postado em<br><time>12.12.2013 - 17h45</time></p>

        <div><img src="imagens/ico_curtir.gif" width="36" height="36"> 12</div>
        <br class="tudo">
        </dt>
        <dd>
        <img src="'.$img.'" width="700" height="700" alt="Menininha meu amor"><br class="tudo">
        </dd>
        </dl>';
        exit;
  }
}

?>