<?php
/**
 * Project: KIDU
 * 
 * @copyright KIDU - www.kidu.com.br
 * @author Rafael Franco <rfranco@rfti.com.br>
 * @package core
 * 
 * corrigidos por XTO
 */
class core extends simplePHP {
        
        private $model;
        private $html;

        #initialize vars
        public function __construct() {    
        	$this->model = $this->loadModule('model');
            $this->html = $this->loadModule('html');
        } 


        /**
         * callWS function, resposible for ws comunication to function who need to use POST calls
         * @param $action string The name of ws method who to call
         * @param $data array List of parameters will be sent to ws
         * @return array
         * */
        public function callWs($action,$data){
            $nvp = array(
                'api_key'           => ELGG_WS_API_KEY,
                'auth_token'        => $_SESSION['user_token'],
             );
            
            $nvp = array_merge($nvp,$data);

            //open connection
            $curl = curl_init();

            //set curl
            curl_setopt( $curl , CURLOPT_URL , ELGG_WS.'?method='.$action);
            curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
            curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
            curl_setopt( $curl , CURLOPT_POST , 1 );
            curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

            //do action
            $res = urldecode(curl_exec($curl)); 
            $response = json_decode($res);        
            return $response;
        }

        /**
         * getWs function, resposible for ws comunication to function who need to use GET calls
         * @param $action string The name of ws method who to call
         * @param $data array List of parameters will be sent to ws
         * @return array
         * */
        public function getWS($action,$data) {

            $query  = http_build_query($data);

            $url = ELGG_WS.'?method='.$action.'&'. $query;

            $res = file_get_contents($url);

            $response = json_decode($res);  

            return $response;
        }

        /** 
        * doLogin function 
        * do login on Elgg system and create local session
        * @param $username string
        * @param $password string
        * @return boolean
        **/
        public function doLogin($username,$password) {
            $response = $this->callWs('auth.gettoken',array('username'=>$username,'password'=>$password));
            

            if($response->status == -1) {
              return false;
            } else {
              //get user data 
              $_SESSION = $this->getUserData($username);
              $_SESSION['user_token'] = $response->result;
              return true;
            }
        }
        
        /**
        * getUserData
        * function to get basic user data
        * @param $username string username on the system
        * */
        public function getUserData($username) {
            $response = $this->getWS('user.get_profile',array('username'=>$username));

            if($response->status == 0) {
                $return['name'] = $response->result->core->name;
                $return['username'] = $username;
                $return['avatar_url'] = $response->result->avatar_url;
                $return['authorized'] = $response->result->authorized;
                $return['guid'] = $response->result->guid;
                //$return['nivel'] = ($response->result->nivel != '') ? $response->result->nivel : 1;
                
                return $return;
            } else {
                return false;
            }
        }

        /**
         *  isLogged
         * @return boolean
         * */
        public function isLogged() {
            if(empty($_SESSION['username'])) {
                return false;
            } else {
                return true;
            }
        }

    public function getDoneChallenges() {
      $answers = $this->callWs('file.get_files',array('context'=>'user','username'=>$_SESSION['username']));
      foreach ($answers->result as $answer) {
        if($answer->access_id == 2) {
          $doneChallenges[$answer->container_guid] = $answer->container_guid;
        }
      }
      return $doneChallenges;
    }

    public function youtubePost($file,$challenge) {
      
      include 'Zend/Gdata/YouTube.php';
      include 'Zend/Gdata/ClientLogin.php';

      $authenticationURL= 'https://www.google.com/accounts/ClientLogin';
      $httpClient = Zend_Gdata_ClientLogin::getHttpClient(
                  $username = 'kidu@kidu.com.br',
                  $password = 'kiduthefuture7',
                  $service = 'youtube',
                  $client = null,
                  $source = 'Kidu', // a short string identifying your application
                  $loginToken = null,
                  $loginCaptcha = null,
                  $authenticationURL);


      $developerKey = 'AI39si494VNAUgM0l2nziccttjVPhpqxg1RDmpQnh4i5K95_ezNax-KfYtSf5UQcThldcxKYs0Qe2w06NtaDi_zZXCkiy7rcWw';
      $applicationId = 'Kidu';
      $clientId = 'Kidu v1';

      $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);

      // create a new VideoEntry object
      $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();

      // create a new Zend_Gdata_App_MediaFileSource object
      $filesource = $yt->newMediaFileSource('tmp/'.$file);
      $filesource->setContentType('video/quicktime');
    
      // set slug header
      $filesource->setSlug('Video Kidu');

      // add the filesource to the video entry
      $myVideoEntry->setMediaSource($filesource);

      $myVideoEntry->setVideoTitle('Resposta de '.$_SESSION['name'].' ao desafio '.$challenge);
      $myVideoEntry->setVideoDescription('Para maiores informações acesse www.kidu.com.br');
     
      // The category must be a valid YouTube category!
      $myVideoEntry->setVideoCategory('Autos');

      // Set keywords. Please note that this must be a comma-separated string
      // and that individual keywords cannot contain whitespace
      $myVideoEntry->SetVideoTags('kidu, desafio');

      // abaixo, pego em http://stackoverflow.com/questions/11188557/is-it-possible-to-upload-to-youtube-with-the-zend-library-but-delay-listing-it  
      $unlisted = new Zend_Gdata_App_Extension_Element( 'yt:accessControl', 'yt','http://gdata.youtube.com/schemas/2007', '' );
      $unlisted->setExtensionAttributes(array(array('namespaceUri' => '', 'name' => 'action', 'value' => 'list'), array('namespaceUri' => '', 'name' => 'permission', 'value' => 'denied')));
      $myVideoEntry->setExtensionElements(array($unlisted));

      // set some developer tags -- this is optional
      // (see Searching by Developer Tags for more details)
      $myVideoEntry->setVideoDeveloperTags(array('userid', $_SESSION['username']));

      // set the video's location -- this is also optional
      // $yt->registerPackage('Zend_Gdata_Geo');
      // $yt->registerPackage('Zend_Gdata_Geo_Extension');
      // $where = $yt->newGeoRssWhere();
      // $position = $yt->newGmlPos('37.0 -122.0');
      // $where->point = $yt->newGmlPoint($position);
      // $myVideoEntry->setWhere($where);

      // upload URI for the currently authenticated user
      $uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

      // try to upload the video, catching a Zend_Gdata_App_HttpException, 
      // if available, or just a regular Zend_Gdata_App_Exception otherwise
      try {
        $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');
        return $newEntry->getVideoId(); 
      } catch (Zend_Gdata_App_HttpException $httpException) {
        return $httpException->getRawResponseBody();
      } catch (Zend_Gdata_App_Exception $e) {
        return $e->getMessage();
      }
    }

    public function likeIcon($guid,$likes,$iliked){//a mudança de funções quando clica acontece via javascript
      if($likes == 0) {
        $html = '<span class="curtir curtir_item_' . $guid . '" onclick="likeItem('.$guid.');" ><img src="/images/ico_curtir_cz.gif" class="likeButton" width="36" height="36"><b class="cinza">'.$likes.'</b></span>';
      } else {
        if(!$iliked) {
        $html = '<span class="curtir curtir_item_' . $guid . '" onclick="likeItem('.$guid.');" ><img src="/images/ico_curtir_cz.gif" class="likeButton" width="36" height="36"><b>'.$likes.'</b></span>';  
        } else {
        $html = '<span class="curtir curtir_item_' . $guid . '" onclick="unlikeItem('.$guid.');" ><img src="/images/ico_curtir.gif" class="likeButton" width="36" height="36"><b>'.$likes.'</b></span>';
        }
      } 
      return $html;      
    }

    public function answerHtml($answer,$onlyApproved=false) {
      $likeIcon = $this->likeIcon($answer->guid,$answer->likes,$answer->iliked);

      if($answer->MIMEType == 'text/plain') {
        $file = '<div onclick="showModal('.$answer->guid.')" ><p><span>'.strip_tags($answer->description).'</span></p></div>';
      } else {
        $img = $answer->file_icon;
        $file = '<img onclick="showModal('.$answer->guid.')" src="'.$img.'" height="200" width="200" alt="Kidu">';
      }

      $answers_html = '';
      if((gettype($answer->tags) == 'string' && $answer->tags == 'aprovado') || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
      $answers_html .= '<figure class="resposta" id="figura_'.$answer->guid.'">'.$file.'<figcaption>'.$likeIcon;
        if($onlyApproved == false){
        $answers_html .= '<strong><time>' . date('d-m-Y',$answer->time_updated) . '</time></strong>';
        } else {
        $answers_html .= '<img src="' . str_replace("medium", "small", $answer->owner->avatar_url) . '" width="40" height="40" alt="User"> <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>';
        }
      $answers_html .= '</figcaption></figure>';
        } else {
          if($onlyApproved == false) {
            $answers_html .= '<figure class="resposta oculto" id="figura_'.$answer->guid.'">
                                '.$file.'
                               <figcaption>
                                <strong>Conteúdo oculto</strong>
                                <a href="" onclick="this.parentNode.getElementsByTagName(\'span\')[0].style.display = \'inline\'; return false;">Por quê?</a>
                                <span class="aviso" onclick="this.style.display = \'none\';"><strong>Fechar</strong><br><br>Este conteúdo ainda não pode ser exibido porque não foi avaliado pelos educadores do Kidu. Aguarde.</span>
                                </figcaption>
                            </figure>';
          }
        }

        return $answers_html;
    }

    public function pega_todas_respostas($answers,$onlyApproved){
    $answers_html = '';
    $conta_respostas_desafio = 0;
    
    if(isset($answers->result)){
      foreach ($answers->result as $answer) {
      $answers_html .= $this->answerHtml($answer,$onlyApproved);
        if($onlyApproved){
          if((gettype($answer->tags) == 'string' && $answer->tags == 'aprovado') || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
          $conta_respostas_desafio++;
          }
        } else {
        $conta_respostas_desafio++;  
        }
      }
    }
    
      if($conta_respostas_desafio <= 3  && $conta_respostas_desafio > 0){
      $respostas = '<dd class="sem_scroll">'. $answers_html .'</dd>';
      } else if ($conta_respostas_desafio > 3) {
      //$respostas = '<dd class="sombra_esquerda"><div onclick="rola_esquerda(this)"></div></dd><dd onscroll="aciona_sombra(this)" class="caixa"><div style="width: ' . 315 * $conta_respostas_desafio . 'px">'. $answers_html .'</div></dd><dd class="sombra_direita"><div onclick="rola_direita(this)"></div></dd>';
      $respostas = '<dd class="caixa"><div>'. $answers_html .'</div></dd>';
      } else if($onlyApproved){
      $respostas = '<dd class="sem_scroll"><div class="noAnswers">Não existem respostas para esse desafio ainda :(</div></dd>';
      } else {
      $respostas = '<dd class="sem_scroll"><div class="primeiro_acesso">Olá! Esta é a sua Página Pública no Kidu. Aqui será possível ver todos os desafios que você realizar. Para começar, entre agora na WIKIDU, escolha seu tema favorito e mãos à obra!</div></dd>';  
      }

    return $respostas;
    }

  public function pega_resposta_escolhida($answers){//melhorar essa função pra ela fazer uma busca direta
    if(!isset($answers->result)){return '';}
  $imagem_primeira_resposta = false;
  $answer_html = '';
    foreach($answers->result as $answer){
    $likeIcon = $this->likeIcon($answer->guid,$answer->likes,$answer->iliked);

      if($answer->MIMEType == 'text/plain') {
        $file = '<div onclick="showModal('.$answer->guid.')" ><p><span>'.strip_tags($answer->description).'</span></p></div>';
      } else {
        $img = $answer->file_icon;
        $file = '<img onclick="showModal('.$answer->guid.')" src="'.$img.'" height="285" width="285" alt="Kidu">';
      }

      if((gettype($answer->tags) == 'string' && strpos('aprovado',$answer->tags) !== false) || (gettype($answer->tags) == 'array' && in_array('aprovado', $answer->tags))) {
        if(!$imagem_primeira_resposta || ((gettype($answer->tags) == 'array' && in_array('escolhido', $answer->tags)) || (gettype($answer->tags) == 'string' && strpos('escolhido',$answer->tags) !== false))){
        $imagem_primeira_resposta = true;
        $answer_html = '<figure class="resposta" id="figura_'.$answer->guid.'">'.$file . "\n";
        $answer_html .= '<img src="/images/medalha.png" width="38" height="53" alt="Escolhido pelos educadores" id="medalha">' . "\n";
        $answer_html .= '<figcaption>'.$likeIcon;
        $answer_html .= '<img src="' . str_replace("medium", "small", $answer->owner->avatar_url) . '" width="40" height="40" alt="User"> <strong><a href="/profile/view/'.$answer->owner->name.'">'.$answer->owner->name.'</a></strong>' . "\n";
        $answer_html .= '</figcaption></figure>' . "\n";;
        }
      } 
    }
  return $answer_html;
     // return json_encode($answers);
  }

  public function search($search){//???
    $html = '';
    $x = 0;
    $res = $this->getWs('user.search',array('search'=>$search));
    return $res;
    // if($res->status == 0) {
    //   foreach ($res->result as $user) {
    //     $html .='<div><a href="/profile/view/'.$user->username.'">'.$user->username.'</a></div>';
    //     $x++;
    //   }  
    // } else {
    //   // $html .= $res->status;
    //   $html .= "opa";
    //   var_dump($res);
    // }
    
    // $res = $this->getWs('group.get_groups',array('context'=>'challenge','find'=>$search));
    // if($res->status == 0) {
    //   foreach ($res->result as $group) {
    //     $html .='<div><a href="/theme/challenge/'.$group->guid_main.'/'.$group->guid.'">'.$group->name.'</a></div>';
    //     $x++;
    //   }  
    // }

    //search people
    echo $html;

    //return $x;
  }  
}
?>
