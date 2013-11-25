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

        //send email
        #load email module
        $this->email = $this->loadModule('email');

        $html = file_get_contents('../view/email/convite.html');

        $keys['guid'] = $_SESSION['guid'];
        $html = $this->applyKeys($html,$keys);

        $this->email->send($data['father-email'],'Seu filho que brincar no KIDU',$html,'Kidu','cadastro@kidu.com.br');

        
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
    if(isset($_POST['text']) && $_POST['text'] != '') {
      $res = $this->core->getWs('group.get_groups',array('context'=>'search','find'=>$_POST['text']));
    } else {
      $res = $this->core->getWs('group.get_groups',array('context'=>'featured'));  
    }
    $x = 0;
    echo "<ul>\n";
    foreach ($res->result as $group) {
      echo '<li><a href="/theme/view/'.$group->guid.'">'.$group->name.'</a></li>';
      $x++;
    }

    //search
    if ($_POST['text'] != '') {
      $x = $this->core->search($_POST['text']);
    }
    
    if($x==0) {
      echo '<li>Não foram encontrados resultados</li>';
    }
    echo "</ul><br class='tudo'>\n";
    exit;
    
  }
  public function _actionPostfile() {

    #save file local
    $file = $this->loadModule('file');
    $file_name = $file->uploadFile($_FILES['upload'],APP_PATH.'/public/tmp/');

    #if is a video, save on youtube and get the youtube key
    $type = explode('/',$_FILES['upload']['type']);
    if($type[0] == 'video') {
      $youtubeCode = $this->core->youtubePost($file_name,$_POST['challenge_id']);
    }
    
    $link =  'http://'.$_SERVER['HTTP_HOST'].'/tmp/'.$file_name;
    //save file on ELGG
    $res = $this->core->callWs('file.upload',array('filepath'=>$link,'container_guid'=>$_POST['challenge_id'],'user_guid'=>$_SESSION['guid'],'access'=>2,'description'=>$youtubeCode));

    //redirect to profile
    if($res->status == 0) {
      $this->redirect('/profile');
    } else {
      $this->redirect('/logoff');
    }
  }


  public function _actionPostfiletext() {

    $link =  'http://'.$_SERVER['HTTP_HOST'].'/tmp/tmp.txt';

    //save file on ELGG
    $res = $this->core->callWs('file.upload',array('filepath'=>$link,'container_guid'=>$_POST['challenge_id'],'user_guid'=>$_SESSION['guid'],'access'=>2,'description'=>$_POST['textAnswer']));

    //redirect to profile
    if($res->status == 0) {
      $this->redirect('/profile');
    } else {
      $this->redirect('/logoff');
    }
  }

  public function _actionGetFile() {
        $file_id = $this->getParameter(3);

        $file = $this->core->callWs('file.get_files',array('guid'=>$file_id,'context'=>'one'));
        if($file->status == -20){
        echo 'erro_deslogado';
        return;
        }

        $img = str_replace('medium', 'large', $file->result[0]->file_icon);
       
        $challenge = $this->core->getWs('group.get',array('guid'=>$file->result[0]->container_guid));
       
        $theme =  $this->core->getWs('group.get',array('guid'=>$challenge->result->container_guid));
       
        $likeIcon = $this->core->likeIcon($file_id,$file->result[0]->likes);

        if($file->result[0]->MIMEType == 'text/plain') {
          $center = '<blockquote><div><p>'.$file->result[0]->description.'</p></div></blockquote>';
        } else {
          if($file->result[0]->description != '') {
            $center = '<iframe width="700" height="500" src="//www.youtube.com/embed/'.$file->result[0]->description.'?showinfo=0&rel=0" frameborder="0" allowfullscreen></iframe><br class="tudo">';
          } else {
            $center = '<img src="'.$img.'"><br class="tudo">';
          }
        }
        
        echo '<dl>
                <dt>
                  <span class="fechar" onclick="fecha_modal()">Fechar | X</span>
                  <h4>Tema</h4>
                  <p><a href="/theme/view/'.$challenge->result->container_guid.'">'.$theme->result->name.'</a></p>
                  
                  <h4>Desafio</h4>
                  <p><a href="/theme/challenge/'.$challenge->result->container_guid.'/'.$file->result[0]->container_guid.'">'.$challenge->result->name.'</a></p>
                  <p><br></p>
                  <p>Postado em<br><time>'.date('d.m.Y - h:m',$file->result[0]->time_created).'</time></p>
                  ' . $likeIcon . '
                  <br class="tudo">
                </dt>
                <dd>
                '.$center.'
                </dd>
              </dl>';
              exit;
  }

  public function _actionSendMessage() {
    #envia o email
    $headers  = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    $headers .= 'From:contato@kidu.com.br<contato@kidu.com.br>' . "\r\n";

    extract($_POST);

    $emailTPL = "Novo contato recebido.<br/><br/>";
    $emailTPL .= "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ";
    $emailTPL .= "Nome = $pessoa <br/>";
    $emailTPL .= "E-mail = $unidade <br/>";
    $emailTPL .= "Mensagem = $valor <br/>";
    $emailTPL .= "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - ";

    mail('info@kidu.com.br','Novo contato recebido',$emailTPL,$headers);
    mail('rafaelfranco@me.com','Novo contato recebido',$emailTPL,$headers);
    echo 'sucesso;';
    exit;
  }

  public function _actionAddlike() {
    $entity_guid = $_POST['item_id'];
    $res = $this->core->callWs('file.add_like',array('entity_guid'=>$entity_guid));
    echo ($res->result);
    exit;
  }

  public function _actionSearchThemes()
  {
    //receive search
    $search = $_POST['search'];

    //search themes
    $res = $this->core->getWs('group.get_groups',array('context'=>'search','find'=>$search));
    $x = 0;
    foreach ($res->result as $theme) {
      echo '<div><a href="/theme/view/'.$theme->guid.'">'.$theme->name.'</a></div>';
      $x++;
    }
    
    $x = $this->core->search($search);

    if($x==0) {
      echo '<p>Não foram encontrados resultados</p>';
    }
    exit;
  }

}

?>