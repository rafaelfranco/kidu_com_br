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
    $res = $this->core->getWs('group.get_groups');
    
    pre($res);
    
  }
}

?>