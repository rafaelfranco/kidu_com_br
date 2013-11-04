<?php
/**
 * Project: KIDU
 * 
 * @copyright KIDU - www.kidu.com.br
 * @author Rafael Franco <rfranco@rfti.com.br>
 * @package core
 * */
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
                $return['nivel'] = ($response->result->nivel != '') ? $response->result->nivel : 1;
                
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

      $myVideoEntry->SetVideoPrivate('unlisted');

      // set some developer tags -- this is optional
      // (see Searching by Developer Tags for more details)
      $myVideoEntry->setVideoDeveloperTags(array('userid', $_SESSION['username']));

      // set the video's location -- this is also optional
      $yt->registerPackage('Zend_Gdata_Geo');
      $yt->registerPackage('Zend_Gdata_Geo_Extension');
      $where = $yt->newGeoRssWhere();
      $position = $yt->newGmlPos('37.0 -122.0');
      $where->point = $yt->newGmlPoint($position);
      $myVideoEntry->setWhere($where);

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


      pre($file);
    }
}
?>
