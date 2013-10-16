<?php
/**
 * Project: Fashionera
 * 
 * @copyright Fashionera - www.fashionera.com.br
 * @author Rafael Franco <rfranco@steam.br.inter.net>
 * @package painel
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
                'auth_token'        => ELGG_WS_AUTH_TOKEN,
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
            if(empty($_SESSION['user_token'])) {
                return false;
            } else {
                return true;
            }
        }

       
}
?>
