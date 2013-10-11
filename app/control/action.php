<?php
/**
 * Project: Footbooking
 * 
 * @copyright Footbooking - www.footbooking.com.br
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
            $data['nome'] = utf8_decode($_POST['nome']);
            $data['email'] = $_POST['email'];
            $data['senha'] = md5($_POST['senha']);    
            $data['nascimento'] = $_POST['ano_nascimento'].'-'.$_POST['mes_nascimento'].'-'.$_POST['dia_nascimento'];
            $data['sexo'] = $_POST['sexo'];
            if($_POST['facebook_id'] != '') {
              $data['facebook_id'] =   $_POST['facebook_id'];
              $data['AccessToken'] = $_POST['AccessToken'];
            }

            if($_POST['foto'] != '') {
                $file = $this->loadModule('file');
                $data['avatar'] = $file->copyFile($_POST['foto'],'repository/upload/');   
            }
            
            #verifico se o usuario já estava pré-cadastrado
            $preCadastro = $this->model->getData('usuario','a.*', array('email'=>$data['email'],'precadastro'=>1));
            if($preCadastro[0]['result'] != 'empty') {
              $user_id = $preCadastro[0]['id'];
              $this->model->alterData('usuario', $data, array('id'=>$user_id));
            } else {
              $user_id = $this->model->addData('usuario',$data,true);  
            }

            if($user_id != 0) {
                echo 'sucesso;Usuario cadastrado com sucesso;';

                //start session
                $this->logUser($user_id,utf8_encode($_POST['nome']),'jogador');

                //cria o diretorio do usuario
                mkdir('/repository/'.$_SESSION['usuario_id']);
            } else {
                echo 'erro;Ocorreu um erro;';
            }
        }   

        /**
          * _actionLoginfb function
          * @return string
          * */
        public function _actionLogin() {
            
            $data['email'] = $_POST['login'];
            $data['senha'] = trim(md5($_POST['senha']));    

            #get user
            $res = $this->model->getData('usuario','a.*',$data);

            //if return error array
            if(@$res[0]['result'] == 'empty') {
                echo "erro;Email ou senha incorretos;";
            } else {
                echo "sucesso;";
                //start session
                $this->logUser($res[0]['id'],$res[0]['nome'],$res[0]['tipo']);
            }
         }


          /**
          * _actionLoginfb function
          * @return string
          * */
        public function _actionLoginfb() {
            
            $data['facebook_id'] = $_POST['facebook_id'];

            #get user
            $res = $this->model->getData('usuario','a.*',$data);

            //if return error array
            if(@$res[0]['result'] == 'empty') {
                echo "erro;Email ou senha incorretos;";
            } else {
                echo "sucesso;";
                //start session
                $this->logUser($res[0]['id'],$res[0]['nome'],$res[0]['tipo']);
            }
         }

         /**
          * logUser function
          * @return void
          *  
          * */
         private function logUser($usuario_id,$usuario,$tipo) {
            $_SESSION['usuario_id'] = $usuario_id;
            $_SESSION['tipo'] = $tipo;
            $_SESSION['start'] = time();
            $_SESSION['usuario'] = $usuario;

            //caso o tipo de usuario seja uma quadra busca o id da quadra e coloca na sessao
            if($tipo == 'quadra') {
              $quadra = $this->model->getData('quadras','a.*', array('usuario_id'=>$_SESSION['usuario_id']));
              $_SESSION['quadra_id'] = $quadra[0]['id'];
            }

         }  

        /**
         * _actionreenviaSenha function
         * @return array
         * */
        public function _actionreenviaSenha() {
            #busca se o email já esta na base 
            $data  = $this->model->getData('usuario','a.*', array('email'=>$_POST['emailforget']));

            #se não esta exibe a mensagem de erro
            if($data[0]['result'] == 'empty') {
               $this->error('O email informado não esta cadastrado','/'); 
            } else {
                #se tem gera uma nova senha
                $newPass = substr(time(),0,8);
                
                #insere a senha no banco
                $this->model->alterData('usuario', array('senha' => md5($newPass)), array('email'=>$_POST['emailforget']));

                #envia mensagem por email
                $email = $this->loadModule('email');

                $mensagem = "Você solicitou sua senha, a mesma alterada, segue abaixo os novos dados de acesso </br></br>";
                $mensagem .= "Usuário: " .$_POST['emailforget']." </br></br>";
                $mensagem .= "Senha: " .$newPass." </br></br>";
                $mensagem .= "Time Footbooking";

                $email->send($_POST['emailforget'],'Senha alterada com sucesso',$mensagem,'Time Footbooking','time@footbooking.com.br');
                $this->success('A nova senha foi enviada para o email cadastrado','/');
            }            
        } 

    /**
    * _actionLogout function
    * @return array
    * */
    public function _actionLogout() {
      unset($_SESSION['usuario_id']);
      $this->redirect('/');
    } 


    /**
     * _actionChangePreferenceDay function
     * @return array
     * */
    public function _actionChangePreferenceDay() {
      //verifica se o usuário já possui alguma preferencia cadastrada se não tem cria
      $res = $this->model->getData('dias_preferencia','a.*', array('usuario_id'=>$_SESSION['usuario_id']));
      if($res[0]['result'] == 'empty') {
        $this->model->addData('dias_preferencia',array('usuario_id'=>$_SESSION['usuario_id']));
      }

      //altera a preferencia de horario de um dia
      $this->model->alterData('dias_preferencia',array($_POST['day'] => $_POST['hour']),array('usuario_id' => $_SESSION['usuario_id']));
    } 

    /**
     * _actionChangePreferenceDay function
     * @return array
     * */
    public function _actionChangePreferenceDayTeam() {
      //verifica se a equipe já possui alguma preferencia cadastrada se não tem cria
      $res = $this->model->getData('dias_preferencia','a.*', array('equipe_id'=>$_POST['equipe_id']));
      if($res[0]['result'] == 'empty') {
        $this->model->addData('dias_preferencia', array('equipe_id'=>$_POST['equipe_id']));
      }

      //altera a preferencia de horario de um dia
      $this->model->alterData('dias_preferencia',array($_POST['day'] => $_POST['hour']), array('equipe_id'=>$_POST['equipe_id']));
    } 

      
     /**
    * _actionpostInWall function
    * @return array
    * Insere a mensagem no banco e inicia uma conversa
    * */
    public function _actionPostInWall() {
      $data['equipe_id'] = ($_POST['equipe_id'] == '') ? 0 : $_POST['equipe_id'];
      $data['jogo_id'] = ($_POST['jogo_id'] == '') ? 0 : $_POST['jogo_id']; 
      $data['texto'] = $_POST['message'];
      $data['usuario_id'] =$_SESSION['usuario_id'];
      $data['quadra_id'] = ($_POST['quadra_id'] == '') ? 0 : $_POST['quadra_id']; 

      #inicia a conversa
      $conversa_id = $this->model->addData('mural',$data);

      #coloca o criador da conversa como parte interessada na conversa
      $this->model->addData('mural_participantes',array('conversa_id'=>$conversa_id,'usuario_id'=>$_SESSION['usuario_id']));

    } 


  /**
   * _actionloadWall function
   * @return array
   * */
  public function _actionloadWall() {    
    $origin = false;

    $filters['pai_id'] = 0;
    $filters['a.jogo_id'] = ($_POST['game_id'] == '') ? 0 : $_POST['game_id'] ;
    $filters['a.quadra_id'] = ($_POST['quadra_id'] == '') ? 0 : $_POST['quadra_id'] ;

    if($filters['jogo_id'] == 0) {
      //alterado para exibir todas as mensagens dos amigos do usuario

      //busca todos os amigos do usuario
      $amigos = $this->core->getFriends('ids');
      if($amigos != '') {
        $filters['in a.usuario_id'] = "($_POST[usuario_id],$amigos)";  
      } else {
        $filters['in a.usuario_id'] = "($_POST[usuario_id])";
      }
      



      //busca as quadras que o usuario curte
      $quadras = $this->core->getuserlikes();
      if($quadras != '') {
        $filters['orin a.quadra_id'] = "($quadras)";  
      }
      
    }

    if($_POST['equipe_id'] != '') {
      unset($filters['usuario_id']);
      unset($filters['pai_id']);
      unset($filters['jogo_id']);
      unset($filters['in a.usuario_id']);
      
      $filters['a.equipe_id'] = $_POST['equipe_id'];
    }

    //caso seja mural de quadra
    if($filters['a.quadra_id'] != '') {
      unset($filters['in a.usuario_id']);
    }

    
    $limits['start'] = $_POST['inicio'];
    $limits['limit'] = 10;

    #Quando algum conteúdo é escrito no mural do time, 
    #os participantes do mesmo recebem a informação no mural deles
    if(($_POST['equipe_id'] == '') && ($_POST['game_id'] == '') && ($_POST['quadra_id'] == '')) {
      #busco os times do usuario
      $times = $this->core->userTeams($_POST['usuario_id']);
      $times = $this->cutEnd($times,1);
      $filters['orin a.equipe_id'] = "($times)";


      //busco os jogos que o usuario participa
      $jogos = $this->core->userGames($_POST['usuario_id']);
      $jogos = $this->cutEnd($jogos,1);
      $filters['orin a.jogo_id'] = "($jogos)";


      $origin = true;
     
    }

    //lista as mensagens
    $saida = $this->core->listMessages($filters,$limits,$origin);

    echo $saida;
    exit;
  }   


  /**
   * _actionloadMessageComments function
   * @return array
   * */
  public function _actionloadMessageComments() {
      
      $filters['pai_id'] = $_POST['message_id'];
      $saida = $this->includeHTML('../view/perfil/snippets/comentar.html');
      $saida = $this->applyKeys($saida,$_POST); 
      $saida .= $this->core->listMessages($filters,'',false,true,$_POST['marcaLido']);
      echo $saida;

      //removo todas as notificacoes dessa conversa para este usuario
      $this->model->removeData('mural_notificacoes',array('usuario_id'=>$_SESSION['usuario_id'],'conversa_id'=>$_POST['message_id']));
      exit;
  }   

  /**
   * _actionremoveMessage function
   * @return array
   * */
  public function _actionremoveMessage() {
      $filters['id'] = $_POST['message_id'];
      $this->model->removeData('mural',$filters);
      exit;
  }  

    /**
   * _actionremoveTalkMessage function
   * @return array
   * */
  public function _actionremoveTalkMessage() {
      $filters['id'] = $_POST['message_id'];
      $this->model->removeData('mensagem',$filters);
      exit;
  }   

  /**
   * _actionpostComment function
   * @return array
   * */
  public function _actionPostComment() {
      $data['pai_id'] = $_POST['message_id'];
      $data['texto'] = $_POST['comment'];
      if($_POST['quadra_id'] != '') {
        $data['quadra_id'] = $_POST['quadra_id'];
      } else {
        //verifico se a mensagem pai é de uma quadra
        $pai = $this->model->getData('mural','a.*',array('id'=>$data['pai_id']));
        $data['quadra_id'] = $pai[0]['quadra_id'];
      }
      
      $data['usuario_id'] = $_SESSION['usuario_id'];
      
      $this->model->addData('mural',$data);

      #coloca quem respondeu a conversa como parte interessada na conversa
      $this->model->addData('mural_participantes',array('conversa_id'=>$_POST['message_id'],'usuario_id'=>$_SESSION['usuario_id']));

      #busco os outros participantes da conversa
      $participantes = $this->model->getData('mural_participantes','usuario_id as participante',array('conversa_id'=>$_POST['message_id'],'dif usuario_id'=>$_SESSION['usuario_id']));
    
      #insere a notificacao para os outros participantes dessa conversa
      foreach ($participantes as $participante) {
        $this->model->addData('mural_notificacoes',array('conversa_id'=>$_POST['message_id'],'usuario_id'=>$participante['participante']));
      }
  }        

  /**
   * _actionpostComment function
   * @return array
   * */
  public function _actioncountUnreadMessages() {
    
    $filters['usuario_id'] = $_SESSION['usuario_id'];
    echo $this->model->countData('mural_notificacoes',$filters);
    exit;
  }


  /**
   * _actionsalvaAvatar function
   * @return array
   * */
  public function  _actionsalvaAvatar() {
    $file = $this->loadModule('file');
    $image = $this->loadModule('image');
    
    $data['avatar'] = $file->uploadFile($_FILES['imagem'],'repository/upload/');

    //salva a foto de perfil no banco
    $this->model->alterData('usuario', $data, array('id'=>$_SESSION['usuario_id']));

    //copia o avatar no repositorio
    @unlink('repository/'.$_SESSION['usuario_id'].'/avatar.jpg');
    $file->copyFile('repository/upload/'.$data['avatar'],'repository/'.$_SESSION['usuario_id'],'avatar.jpg');

    //reduz a foto para 170 px
    $image->reduceImage('repository/upload/'.$data['avatar'],300);

    //reduz o avatar para 60 px
    $image->reduceImage('repository/'.$_SESSION['usuario_id'].'/avatar.jpg',70);

    $this->redirect('/perfil');

  }   

  /**
   * _actionalteraPerfil function
   * @return array
   * */
  public function _actionalteraPerfil() {
    
    $data = $_POST;
    $data['nascimento'] = $_POST['anos'].'-'.$_POST['meses'].'-'.$_POST['dias'];
    unset($data['dias']);
    unset($data['meses']);
    unset($data['anos']);
    unset($data['anos']);
    if($data['email'] == ''){
      unset($data['email']);  
    }
    if($data['senha'] == ''){
      unset($data['senha']);  
    } else {
      $data['senha'] = md5($data['senha']);  
    }

    //caso não tenha selecionado posicao muda para  - - - 
    $data['campo'] = ($data['campo'] == '0') ? '- - -' : $data['campo'];
    $data['society'] = ($data['society'] == '0') ? '- - -' : $data['society'];
    $data['salao'] = ($data['salao'] == '0') ? '- - -' : $data['salao'];
    $data['clube'] = utf8_decode($data['clube']);

    unset($data['reemail']);
    unset($data['resenha']);
    $this->model->alterData('usuario', $data, array('id'=>$_SESSION['usuario_id']));  
    $this->redirect('/perfil');
  }   
    


  /**
  * _actionloadtalk function
  * @return array
  * */
  public function _actionloadtalk() {
    #busca as mensagem postadas pelo usuario alvo
    $filters['remetente'] = $_POST['user_id'];
    $filters['usuario_id'] = $_SESSION['usuario_id'];
    $talk1 = $this->model->getData('mensagem','a.*,u.nome,UNIX_TIMESTAMP(a.tempo) as tempo', $filters, '','a.ID DESC','inner join usuario as u on a.remetente = u.id');

    #busca as mensagens postadas pelo usuario logado
    $filters['usuario_id'] = $_POST['user_id'];
    $filters['remetente'] = $_SESSION['usuario_id'];
    $talk2 = $this->model->getData('mensagem','a.*,u.nome,UNIX_TIMESTAMP(a.tempo) as tempo', $filters, '','a.ID DESC','inner join usuario as u on a.remetente = u.id');
    
    //junta a conversa
    if($talk1[0]['result'] != 'empty') {
      foreach ($talk1 as $message) {
        $talk[$message['id']] = $message;
        //marca a mensagem como lida
        $this->model->alterData('mensagem', array('unread'=>1), array('id'=>$message['id']));
      }
    }
    
    if($talk2[0]['result'] != 'empty') {
      foreach ($talk2 as $message) {
       $talk[$message['id']] = $message;
      }
    }
    //ordena a conversa
    sort($talk);

    foreach ($talk as $message) {
      
      $user = $this->model->getData('usuario','*',array('id'=>$message['remetente']));
      $avatar = $this->core->avatar($user[0]);

      $remetente = $this->html->span(utf8_encode($message['nome']));
      $mensagem = $this->html->label($message['texto']);

      $linkRemover = $this->html->div('EXCLUIR '.$this->html->img('/images/icones/remover.jpg'),array('class'=>'linkRemover removeMessage','id'=>'removeMessage-'.$message['id']));
      
      $data = $this->html->div(date('d/m/Y - H:i',$message['tempo']).$linkRemover,array('class'=>'date'));
      

      $timeline  .= $this->html->div($avatar.$remetente.$mensagem.$data,array('id'=>'message-'.$message['id'],'class'=>'talkedMessage')); 
      
    }
    echo $timeline;

    exit;
  }   


  /**
  * _actionAddmessage function
  * @return array
  * */
  public function _actionAddmessage() {
   $data = $_POST;
   $data['remetente'] = $_SESSION['usuario_id'];
   $this->model->addData('mensagem',$data);
   exit;
  }   

  /**
   * _actionCriarRecreativo function
   * @return array
   * */
  public function _actionCriarRecreativo() {
    //trata a data
    $d = explode('/',$_POST['data']);

    //recebe os amigos 
    $amigos = $_POST['amigo'];

    //recebe os de fora da rede
    $foradarede = $_POST['foradarede'];

    //seta dados
    $_POST['tipo'] = 'recreativo';
    $h = explode(':', $_POST['horainicio']);
    $_POST['tempo'] = mktime($h[0],$h[1],0,$d[1],$d[0],$d[2]);
    $_POST['criador_id'] = $_SESSION['usuario_id'];
    $_POST['nivel_mercado'] = ($_POST['mercado'] == 'on') ? 'publico' : 'privado';
    $_POST['rateio'] = ($_POST['rateio'] == '') ? '0' : $_POST['rateio'];

    //limpa dados ja usados
    unset($_POST['amigo']);
    unset($_POST['foradarede']);
    unset($_POST['data']);
    unset($_POST['mercado']);
    unset($_POST['busca']);
    
    $_POST['participantes'] = count($amigos);

    #regra de local
    $_POST['local_id'] = $this->core->buscaLocal($_POST['local'],$_POST['endereco']);

    unset($_POST['endereco']);
    #pre($_POST);

    //cria a partida
    $jogo_id = $this->model->addData('jogos',$_POST);

    //addiciona o usuario ao jogo
    $this->core->addtogame($jogo_id,$_SESSION['usuario_id'],'Presente');

    #envia convites aos amigos para o jogo e adiciona ao jogo
    foreach ($amigos as $key => $amigo) {
      if($amigo!= '') {
        $this->core->addtogame($jogo_id,$amigo,'Pendente',true);
      }
    }

    //precadastra e envia o convite e adiciona a lista de amigos
    foreach ($foradarede as $key => $value) {
      $fora = explode('|', $value);
      $nome = $fora[0];
      $email = $fora[1];
      $amigoDeFora = $this->core->precadastra($nome,$email);
      $this->core->addtogame($jogo_id,$amigoDeFora,'Pendente',true);
    }

    $this->redirect('/jogos/ver/'.$jogo_id);
  }   


  /**
   * _actionCriarContra function
   * @return array
   * */
  public function _actionCriarContra() {

    //seta dados
    $_POST['tipo'] = 'contra';
    $_POST['criador_id'] = $_SESSION['usuario_id'];
    $_POST['nivel_mercado'] = ($_POST['mercado'] == 'on') ? 'publico' : 'privado';
    $_POST['rateio'] = ($_POST['rateio'] == '') ? '0.00' : $_POST['rateio']  ;
  
    $d = explode('/',$_POST['data']);
    $h = explode(':', $_POST['horainicio']);
    $_POST['tempo'] = mktime($h[0],$h[1],0,$d[1],$d[0],$d[2]);
    
    $dataDoJogo = $_POST['data'];
    //limpa dados ja usados
    unset($_POST['amigos']);
    unset($_POST['foradarede']);
    unset($_POST['data']);
    unset($_POST['endereco']);


    #regra de local
    $_POST['local_id'] = $this->core->buscaLocal($_POST['local']);

    //cria a partida
    $jogo_id = $this->model->addData('jogos',$_POST);

    //busco os dados do admin do time 2
    $desafiado = $this->model->getData('equipes','a.*', array('id'=>$_POST['adversario_id']));

    //envia o convite para o admin do time 2
    $data['usuario_id'] = $desafiado[0]['usuario_id'];
    $data['remetente_id'] = $_SESSION['usuario_id'];
    $data['tipo'] = 'jogo';
    $data['jogo_id'] = $jogo_id;
    $this->model->addData('convites',$data);

    $time1 = $this->model->getData('equipes','a.*',array('id'=>$_POST['equipe_id']));
    $time2 = $this->model->getData('equipes','a.*',array('id'=>$_POST['adversario_id']));

    //envia email para todos os jogadores do time que esta desafiando ou seja criando o jogo
    //busca os jogadores do time que esta desafiando
    $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$_POST['equipe_id'].')'));
    foreach ($jogadores as $jogador) {
      //envia o lembrete por email
      $mensagem = 'Dia: '.$dataDoJogo.'. <br>
                   Horário: '.$_POST['horainicio'].'. <br>
                   Local: '.$_POST['local'].' - '.$_POST['endereco'].'. <br>
                   Status: Aguardando que o time '.$time2[0]['nome'].'  aceite o desafio. <br>';

      $assunto = 'O seu time '.$time1[0]['nome'].' esta desafiando para um contra o time '.$time2[0]['nome'];                
      $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
    }


    //envia email para todos os jogadores do time que esta sendo desafiado
    //busca os jogadores do time que esta desafiando
    $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$_POST['adversario_id'].')'));
    foreach ($jogadores as $jogador) {
      //envia o lembrete por email
      $mensagem = 'Dia: '.$dataDoJogo.'. <br>
                   Horário: '.$_POST['horainicio'].'. <br>
                   Local: '.$_POST['local'].' - '.$_POST['endereco'].'. <br>
                   Status: Aguardando que seu time aceite o desafio. <br>';
                  
                  
      $assunto = 'O seu time '.$time2[0]['nome'].' esta sendo desafiado pelo time '.$time1[0]['nome'];                
      $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
    }

      



    //envia uma notificacao para todos os usuarios que participam do time desafiado
    #busca os participantes do time desafiado
    $jogadores = $this->model->getData('equipe_participante','a.*',array('equipe_id'=>$desafiado[0]['id']));
    foreach ($jogadores  as $jogador) {
      //notifica toodos menos o administrador que ja foi convidado
      if($jogador['usuario_id'] != $data['usuario_id']) {
        $msg = $desafiado[0]['nome'] . ' foi desafiado';
        $link = '/jogos/contra/'.$jogo_id;
        $this->core->addNotification($jogador['usuario_id'],$msg,$link);
      }
    }
   

    $this->redirect('/jogos/contra/'.$jogo_id);
  }   

  
  /**
   * _actionMassa function
   * @return array
   * */
  public function _actionMassa() {
    
    for($x=1;$x <100;$x++) {
      $data['nome'] = 'Usuario' .$x;  
      $data['email'] = 'usuario'.$x.'@rfti.com.br'; 
      $data['senha'] = 'f3d2426e52be7c11edd6099fa01714c6'; 
      $data['avatar'] = '8a8cef584040cf08c5d428e14cdbd06c427.png'; 
      $data['tipo'] = 'jogador';
      $this->model->addData('usuario',$data);
    }
    
  }   

  /**
   * _actionfindTeams function
   * @return array
   * */
  public function _actionfindTeams() {
    #busca times que não sejam criados pelo usuario atual 
    $res = $this->model->getData('equipes','a.*', array('like nome' => $_POST['search'],'dif usuario_id' => $_SESSION['usuario_id']),'','a.nome asc');
    $equipe  = '';
    foreach ($res as $equipe) {
      #$avatar = $this->html->span($this->html->img('/repository/upload/'.$equipe['escudo']),array('class'=>'avatarThumb')); 
      #$avatar = $this->html->link($avatar,'/equipes/ver/'.$equipe['id']);
      $avatar = $this->core->avatarEquipe($equipe);
      $ver = $this->html->link('Ver','/equipes/ver/'.$equipe['id'],'_blank','miniButton'); 
      $desafiar = $this->html->link('Desafiar','javascript:desafiar('.$equipe['id'].')','_blank','miniButton2'); 
      
      $participantes = $this->model->countData('equipe_participante',array('equipe_id'=>$equipe['id']));

      $equipes .= '<table cellpadding="3" id="equipe'.$equipe['id'].'">
                  <tr>
                      <td>'.$avatar.'</td>
                      <td>'.$equipe['nome'].' <br> <span class="f12">'.$participantes.' participante(s)</span></td>
                  </tr>
                  <tr>
                    <td>'.$ver.'</td>
                    <td>'.$desafiar.'</td>
                  </tr>
                </table>';
    }
    if($res[0]['result'] == 'empty') {
      echo 'Não foram encontradas equipes'; 
    } else {
      echo $equipes;   
    }
    exit;
  }   
       

  /**
   * _actioncolocaEquipeNoMercado function
   * @return array
   * */ 
  public function _actioncolocaEquipeNoMercado() {
    $equipe_id = $this->getParameter(3);
    $this->model->alterData('equipes', array('mercado'=>1), array('id'=>$equipe_id));
  }
  /**
   * _actionremoveEquipeNoMercado function
   * @return array
   * */ 
  public function _actionremoveEquipeNoMercado() {
    $equipe_id = $this->getParameter(3);
    $this->model->alterData('equipes', array('mercado'=>0), array('id'=>$equipe_id));
  }  

 /**
   * _actioncolocaUsuarioNoMercado function
   * @return array
   * */ 
  public function _actioncolocaUsuarioNoMercado() {
    $this->model->alterData('usuario', array('mercado'=>1), array('id'=>$_SESSION['usuario_id']));
  }
  /**
   * _actionremoveUsuarioNoMercado function
   * @return array
   * */ 
  public function _actionremoveUsuarioNoMercado() {
    $this->model->alterData('usuario', array('mercado'=>0), array('id'=>$_SESSION['usuario_id']));
  }


   /**
    * _actionCriarEquipe function
    * @return array
    * */
   public function _actionCriarEquipe() {
     
    $file = $this->loadModule('file');
    $image = $this->loadModule('image');
    
    $data['escudo'] = $file->uploadFile($_FILES['escudo'],'repository/upload/');
    $data['usuario_id'] = $_SESSION['usuario_id']; 
    $data['nome'] = $_POST['nome']; 
    $data['local'] = $_POST['local']; 
    $data['bairro'] = $_POST['bairro']; 

    #crio a equipe
    $equipe_id = $this->model->addData('equipes',$data);

    #coloco o usuario como administrador
    $this->model->addData('equipe_administrador',array('usuario_id'=>$_SESSION['usuario_id'],'equipe_id'=>$equipe_id));

    #coloco o usuario como parte da equipe
    $this->model->addData('equipe_participante',array('usuario_id'=>$_SESSION['usuario_id'],'equipe_id'=>$equipe_id));

    //adiciona os amigos ao time
    foreach ($_POST['convite'] as $key => $convidado) {
      #coloco o usuario como parte da equipe
      $this->model->addData('equipe_participante',array('usuario_id'=>$convidado,'equipe_id'=>$equipe_id));
    }


    //adiciona os amigos que foram colocados como administradores
    foreach ($_POST['admin'] as $key => $admin) {
      #coloco o usuario como administrador
      $this->model->addData('equipe_administrador',array('usuario_id'=>$admin,'equipe_id'=>$equipe_id));
    }

    
    //precadastra e envia o convite e adiciona ao time
    foreach ($_POST['foradarede'] as $key => $value) {
      $fora = explode('|', $value);
      $nome = $fora[0];
      $email = $fora[1];
      $amigoDeFora = $this->core->precadastra($nome,$email);
      $this->model->addData('equipe_participante',array('usuario_id'=>$amigoDeFora,'equipe_id'=>$equipe_id));
    }

    $this->redirect('/equipes/ver/'.$equipe_id);
     
   }  

   /**
    * _actionCriarQuadra function
    * @return array
    * */
   public function _actionCriarQuadra() {
     
    $file = $this->loadModule('file');
    $image = $this->loadModule('image');

    unset($_POST['resenha']);
    
    foreach ($_POST as $key => $value) {
      if($value == 'on') {
        $_POST['infra'] .= $key.',';
        unset($_POST[$key]);
      }
    }

    $data = $_POST;
    $data['logo'] = $file->uploadFile($_FILES['logotipo'],'repository/upload/');
    #cria o usuario quadra e recebe o id
    $userdata['nome'] = $_POST['nome'];
    $userdata['tipo'] = 'quadra';
    $userdata['email'] =  $_POST['email'];
    $userdata['senha'] =  md5($_POST['senha']);


    $data['usuario_id'] = $this->model->addData('usuario',$userdata);

    if($data['usuario_id']['status'] == 'erro') {
      $this->error('Este email já possui um cadastro no site');
    }

    #crio a equipe
    $quadra_id = $this->model->addData('quadras',$data);

    #logo o usuario
    $this->logUser($quadra_id,$userdata['nome'],'quadra');

    $this->redirect('/quadras/ver/'.$quadra_id);
     
   }


   /**
    * _actionfindplayers function
    * @return array
    * */  
   public function _actionfindplayers() {
     
    /**
    *"Quando o usuário procura um jogador pela barra de busca, os jogadores deverão aparecer nessa ordem: Primeiro os amigos, depois os amigos dos seus amigos e terceiro os usuários com mais amigos.
    *Exemplo: Se eu procuro por Rafael, apareceram todos os meus amigos Rafael, depois todos os amigos dos meus amigos Rafael e por último os Rafaeis com mais amigos.
    **/

    //busca os amigos que estiverem nessa busca
    $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['find'],'in id'=>'(select distinct(amigo_id) from amizades where usuario_id = '.$_SESSION['usuario_id'].')'));
    if($res[0]['result'] != 'empty') {
        foreach ($res as $user) {
            $usersList .= $user['id'].',';
        }
    }
    
    //busca os amigos de amigos 
    $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['find'],'in id'=>'(select distinct(amigo_id) from amizades where usuario_id in (select amigo_id from amizades where usuario_id = '.$_SESSION['usuario_id'].'))'));
    if($res[0]['result'] != 'empty') {
        foreach ($res as $user) {
            $usersList .= $user['id'].',';
        }
    }

    //busca os outros
    $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['find'],'notin id'=>"($usersList 0)"));
    if($res[0]['result'] != 'empty') {
        foreach ($res as $user) {
            $usersList .= $user['id'].',';
        }
    }
    
    $usersList = $this->core->cutEnd($usersList,1);

    $players = $this->model->getData('usuario','a.*', array('in id'=>"($usersList)",'dif id'=>$_SESSION['usuario_id']),array('start'=>0,'limit'=>30));

    $resultado = '';
    foreach ($players as $amigo) {
        $amigoHtml = $this->core->avatar($amigo);

        $amigoDados = $this->html->link($this->html->b(utf8_encode($amigo['nome'])),'/perfil/ver/'.$amigo['id']);
        $amigoDados .= $this->html->span($this->core->friendsInCommon($amigo['id'],$_SESSION['usuario_id']).' amigos em comum');

        $amigo['equipes'] = $this->model->countData('equipe_participante', array('usuario_id'=>$amigo['id'])) ;
        $amigoDados .= $this->html->span($amigo['equipes'].' equipes<br/><br/>');

        #caso ja seja amigo nao exibe o botao
        if($this->core->isFriend($amigo['id'])==false) {
          if($this->core->isInvited($amigo['id'])) {
            $amigoDados .= $this->html->span('Convite já enviado',array('class'=>'red'));
          } else {
            $amigoDados .= $this->html->span('Adicionar',array('class'=>'adicionarSugerido miniButton arial','id'=>$amigo['id']));
          }
        }
       
        $amigoHtml .= $this->html->div($amigoDados,array('class'=>'amigoDados')); 

        $resultado .= $this->html->div($amigoHtml,array('id'=>'amigo-'.$amigo['id'],'class'=>'amigoSugerido')); 
    }
    
    if (($players[0]['result'] != 'empty') && ($_POST['find'] != '')) {
      echo $resultado;  
    } else {
      echo '<br><br>Não foram encontrados jogadores<br><br><br>';
    }
    exit;
    
   }


      /**
       * _actionaddfriendinvite function
       * @return array
       * */
      public function _actionaddfriendinvite() {
        
        $data['usuario_id'] = $_POST['user_id'];
        $data['remetente_id'] = $_SESSION['usuario_id'];
        $data['tipo'] = 'amizade';
        
        $this->model->addData('convites',$data);

        #envia mensagem por email
        $email = $this->loadModule('email');
   
        $mensagem = "Você recebeu uma solicitação de amizade no Futbooking para aceitar acesse agora o seu perfil  </br></br>";

        $email->send('rafaelfranco@me.com','Você recebeu uma solicitação de amizade no Futbooking',$mensagem,'Time Footbooking','amizade@footbooking.com.br');

        exit;
      }   
              
      public function _actionremovefriend() {
        $data['amigo_id'] = $_POST['user_id'];
        $data['usuario_id'] = $_SESSION['usuario_id'];
        
        $this->model->removeData('amizades',$data);

        $data['usuario_id'] = $_POST['user_id'];
        $data['amigo_id'] = $_SESSION['usuario_id'];
        
        $this->model->removeData('amizades',$data);

        //quando remove a amizade remove também os convites de amizade que já fora feitos
        $this->model->removeData('convites',array('remetente_id'=>$_SESSION['usuario_id'],'usuario_id'=>$_POST['user_id']));
        exit;
      }    


      /**
       * _actionaceptInvite function
       * @return array
       * */
      public function _actionaceptInvite() {
         
        $invite_id = $this->getParameter(3);
        $invite = $this->model->getData('convites','a.*', array('id'=>$invite_id)); 
        
        switch ($invite[0]['tipo']) {
          case 'jogo':
            //confirma a participacao no jogo
            $this->model->alterData('jogo_participantes', array('status' =>'Confirmado'), array('jogo_id' => $invite[0]['jogo_id'] , 'participante_id'=>$_SESSION['usuario_id']));
            $url = '/jogos/ver/'. $invite[0]['jogo_id'];  
            
            //verifico se o jogo em questão não é um contra
            $jogo = $this->model->getData('jogos','a.*', array('id'=>$invite[0]['jogo_id']));
            if($jogo[0]['tipo'] == 'contra') {
              //confirma a participacao no contra
              $url = '/action/aceitar-contra/'. $invite[0]['jogo_id'];
            } 
          break;
          case 'amizade':
            //cria a amizade
            $data['usuario_id'] = $invite[0]['usuario_id'];
            $data['amigo_id'] = $invite[0]['remetente_id'];
            $this->model->addData('amizades',$data);

            $data['usuario_id'] = $invite[0]['remetente_id'];
            $data['amigo_id'] = $invite[0]['usuario_id'];
            $this->model->addData('amizades',$data);   
            $url = '/jogadores';         
            break;
            case 'equipe':
              //coloco o usuario na equipe
              $data['usuario_id'] = $invite[0]['usuario_id'];
              $data['equipe_id'] = $invite[0]['mensagem'];
              $this->model->addData('equipe_participante',$data);
              $url = '/equipes';         
            break;
          
          default:
            # code...
            break;
        }
        
        
        $this->model->alterData('convites', array('aceito' =>1), array('id'=>$invite_id)); 
        $this->success('Convite aceito com sucesso',$url);
        
      }

      /**
       * _actionaceptInvite function
       * @return array
       * */
      public function _actiondenyInvite() {
         
        $invite_id = $this->getParameter(3);
        
        $invite = $this->model->getData('convites','a.*', array('id'=>$invite_id)); 
        switch ($invite[0]['tipo']) {
          case 'jogo':
            //nega a participacao no jogo
            $this->model->alterData('jogo_participantes', array('status' =>'Negado'), array('jogo_id' => $invite[0]['jogo_id'] , 'participante_id'=>$_SESSION['usuario_id']));
            $url = '/jogos';  
          break;
          case 'equipe':
            $url = '/equipes';  
          break;
          default :
            $url = '/jogadores';
          break;
        }
        $this->model->alterData('convites', array('aceito' =>2), array('id'=>$invite_id)); 
        $this->success('Convite negado com sucesso',$url);
      } 

       
     /**
      * _actionisFutureDate function
      * @return array
      * */
     public function _actionisFutureDate() {
        
        $time = mktime(0,0,0,$_POST['m'],$_POST['d'],$_POST['y']);  
      
        if($time > time()) {
          echo 'yes';
        } else {
          echo 'not';
        }
        exit;
     }  


      /**
       * _actiongetGames function
       * @return array
       * */
      public function _actiongetGames() {
        echo $this->core->buscaRecreativos($_POST);
        exit;
      }   
            
                
 
     /**
      * _actionconfirmaParticipantes function
      * @return array
      * */
     public function _actionconfirmaParticipantes() {
        
        $game_id = $_POST['currentGame'];
        unset($_POST['currentGame']);
        //removo todos os participantes atualmente cadastrados no jogo
       # $this->model->removeData('jogo_participantes',array('jogo_id'=>$game_id));


        //adiciono os participantes recebidos com os status recebidos
        foreach ($_POST as $key=>$value) {
          $u = explode('-', $key);
          $data['participante_id'] = $participante_id = $u[1];
          //caso seja o criador do jogo o status é sempre presente
          $data['status'] = ($data['participante_id'] == $_SESSION['usuario_id']) ? 'Presente' : $value;
          $data['jogo_id'] = $game_id;

          $this->model->alterData('jogo_participantes',$data,array('participante_id'=>$data['participante_id'],'jogo_id'=>$game_id));

          //coloco no perfil do usuario se ele participou ou foi wo
          #busca os dados do usuario
          $participante = $this->model->getData('usuario','a.*', array('id'=>$participante_id));
          $jogos_recreativos = $participante[0]['jogos_recreativos'] + 1;
          $wo = $participante[0]['wo'] + 1;

          if($data['status'] == 'Wo') { 
            $this->model->alterData('usuario', array('wo' => $wo), array('id'=>$participante_id));
          } 
          if($data['status'] == 'Presente') { 
            $this->model->alterData('usuario', array('jogos_recreativos' => $jogos_recreativos), array('id'=>$participante_id));
          }
        }

        //mudo o status do jogo como confirmado os participantes 
        $this->model->alterData('jogos', array('participantes_confirmados'=>'1'), array('id'=>$game_id));


        #redireciono o criador do jogo pra tela do jogo [pos-jogo]
        $this->redirect('/jogos/ver/'.$game_id);
        
      
     }   


     /**
      * _actiongravaGols function
      * @return array
      * */
    public function _actiongravaGols() {
        
        $game_id = $_POST['game_id'];

        unset($_POST['game_id']);
        foreach ($_POST as $participante_id => $gols) {
          $data['jogo_id'] = $game_id;
          $data['participante_id'] = $participante_id;
          $data['quantidade'] = $gols;
          $data['fato'] = 'gol';
          
          //grava os gols da partida
          $this->model->addData('jogos_fatos',$data);

          #busca os dados do usuario
          $participante = $this->model->getData('usuario','a.*', array('id'=>$participante_id));
          $gols = $participante[0]['gols_recreativos'] + $gols;

          //adiciona os gols ao perfil do usuario
          $this->model->alterData('usuario', array('gols_recreativos' => $gols), array('id'=>$participante_id));
        }

        //marca que o admin já lancou os gols
        $this->model->alterData('jogos', array('gols_confirmados'=>'1','lembrete_enviado'=>1 , 'hora_lancamento' => time()), array('id'=>$game_id));
       
        #busca os dados do jogo
        $res = $this->model->getData('jogos','a.*', array('id' => $game_id));
        $jogo = $res[0];

        //avisa a todos os participantes presentes que é hora de avaliar  
        $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo['id'],'status'=>'Presente'),'','a.ID DESC', 'inner join usuario as u on u.id = a.participante_id ') ;

        #itera jogadores
        foreach ($jogadores as $jogador) {
          //envia as notificacoes de sistema aos jogadores
          $this->core->addNotification($jogador['participante_id'],'Voc&ecirc; tem um jogo para avaliar','/jogos/ver/'.$jogo['id']);

          //envia o lembrete por email
          $mensagem = $jogador['nome'] .' <br><br>
                      A partida do dia  '.date('d/m/Y',$jogo['tempo']).' às '.$jogo['horainicio'] .' no '.utf8_encode($jogo['local']).' já foi jogada. Agora é hora de avaliar esse jogo, melhorar seu histórico e de seus amigos. Ver quem participou do jogo, quem marcou os gols, marcar os craques e muito mais. Você tem 48h para avaliar e garantir suas avaliações.<br><br>
                      É rapido! Mas você só tem <b>48 horas</b> para avaliar! <br><br>
                      Segue o jogo...<br>';
          $this->core->enviaEmail($jogador['nome'] .' VAMOS AOS RESULTADOS DO JOGO!',$jogador['nome'] .' VAMOS AOS RESULTADOS DO JOGO!',$mensagem,$jogador['participante_id']);
        } #fecha jogadores

        #redireciono o criador do jogo pra tela do jogo [pos-jogo]
        $this->redirect('/jogos/pos/'.$game_id);
     }   
         

          /**
      * _actiongravaCraque function
      * @return array
      * */
    public function _actiongravaCraque() {
        
        $game_id = $_POST['game_id'];

        $data['jogo_id'] = $game_id;
        $craque = explode('-', $_POST['craque']);
        $data['participante_id'] = $craque[1];
        $data['quantidade'] = 1;
        $data['fato'] = 'craque';
        $data['usuario_id'] = $_SESSION['usuario_id'];


        //grava ocraque da partida
        $this->model->addData('jogos_fatos',$data);

        #busca os dados do usuario
        $_POST['craque'] = str_replace('participante-', '', $_POST['craque']);

        $participante = $this->model->getData('usuario','a.*', array('id'=>$_POST['craque']));
        $craque = $participante[0]['craque'] + 1;

        //adiciona o craque ao perfil do usuario
        $this->model->alterData('usuario', array('craque' => $craque), array('id'=>$_POST['craque']));      
        
        #redireciono o criador do jogo pra tela do jogo [pos-jogo]
        $this->redirect('/jogos/ver/'.$game_id);
     }    


     public function _actiongravaFairPlay() {
        $p = explode(',', $_POST['fairplay']);
        foreach ($p as $participante_id) {
          if($participante_id != '') {
            $data['jogo_id'] = $_POST['game_id'];
            $participante = explode('-',$participante_id);
            $data['participante_id'] = $participante[1];
            $data['quantidade'] = 1;
            $data['fato'] = 'fairplay';
            $data['usuario_id'] = $_SESSION['usuario_id'];
            
            //grava ocraque da partida
            $this->model->addData('jogos_fatos',$data);

            #busca os dados do usuario
            $participante = $this->model->getData('usuario','a.*', array('id'=>$data['participante_id']));
            $fairplay = $participante[0]['fairplay'] + 1;

            //adiciona o fairplay ao perfil do usuario
            $this->model->alterData('usuario', array('fairplay' => $fairplay), array('id'=>$data['participante_id']));
          }
        }
       
        #redireciono o criador do jogo pra tela do jogo [pos-jogo]
        $this->redirect('/jogos/ver/'.$_POST['game_id']);
     }

  
       /**
        * _actiongravaFundamentos function
        * @return array
        * */
       public function _actiongravaFundamentos() {
          
          $game_id = $_POST['game_id'];
       
          unset($_POST['game_id']);

          foreach ($_POST as $fundamento) {
            foreach ($fundamento as $jogador => $tipo_fundamento) {

              $data['jogo_id'] = $game_id;
              $data['participante_id'] = $jogador;
              $data['quantidade'] = 1;
              $data['fato'] = $tipo_fundamento;
              $data['gerador'] = $_SESSION['usuario_id'];
              $data['usuario_id'] = $_SESSION['usuario_id'];

              #armazena que o fundamento veio de 1 jogo e jogador
              $this->model->addData('jogos_fatos',$data);
              $data['fato'] = 'fundamento';
              $this->model->addData('jogos_fatos',$data);

              #grava o fundamento no perfil do jogador
              $this->core->adicionaFundamento($jogador,$tipo_fundamento);
            }
          }
        //marca que o admin já lancou o fairplay
        $this->model->alterData('jogos', array('fundamentos_confirmado'=>'1'), array('id'=>$game_id));

        #CASO TODOS OS PARTICIPANTES DO JOGO Ja TENHAM FEITO AVALIACAO FECHA O JOGO
        #CONTA PARTICIPANTES
        $participantes = $this->model->countData('jogo_participantes', array('jogo_id'=>$game_id,'status'=>'Presente'));
       
        #CONTA AVALIADORES
        $avaliadores = $this->model->getData('jogos_fatos','count(distinct(usuario_id)) as qtd', array('jogo_id'=>$game_id));
     
        if($participantes == $avaliadores[0]['qtd']) {
          #redireciono o criador do jogo pra tela do jogo [pos-jogo]
          $this->redirect('/jogos/fechado/'.$game_id);
        } else {
          #redireciono o criador do jogo pra tela do jogo [pos-jogo]
          $this->redirect('/jogos/ver/'.$game_id);
        }
       }   


       /**
        * _actionconvidarforaredejogo function
        * Convida de fora da rede no jogo
        * @return array
        * */
       public function _actionconvidarforaredejogo() {
        
        extract($_POST);
       
        $amigoDeFora = $this->core->precadastra($nome,$email);
        $this->core->addtogame($game_id,$amigoDeFora,'Pendente',true);
        
        $this->redirect('/jogos/ver/'.$game_id);
       
       }

       /**
        * _actionconvidarforaredejogo function
        * Convida de fora da rede no jogo
        * @return array
        * */
       public function _actionconvidarforaredecontra() {
        
        extract($_POST);
       
        $amigoDeFora = $this->core->precadastra($nome,$email);
        $this->core->addtogame($jogo_id,$amigoDeFora,'Confirmado',false);
        $this->core->addtoteam($_POST['equipe_id'],$amigoDeFora); 

        $this->redirect('/jogos/ver/'.$jogo_id);
       
       }
       /**
        * _actionconvidarnojogo function
        * Convida da rede no jogo
        * @return array
        * */
       public function _actionconvidarNoJogo() {
          foreach ($_POST['amigo'] as $key => $amigo) {
           $this->core->addtogame($_POST['jogo_id'],$amigo,'Pendente',true); 
          }
          $this->redirect('/jogos/ver/'.$_POST['jogo_id']);
       }   



/**
        * _actionconvidarnoContra function
        * Convida da rede no Contra
        * @return array
        * */
       public function _actionconvidarNoContra() {
          foreach ($_POST['amigo'] as $key => $amigo) {
           $this->core->addtogame($_POST['jogo_id'],$amigo,'Confirmado',false); 
           $this->core->addtoteam($_POST['equipe_id'],$amigo); 
          }
          $this->redirect('/jogos/ver/'.$_POST['jogo_id']);
       }   


       /**
        * _actionbuscaAmigosForaJogo function
        * @return array
        * */
       public function _actionbuscaAmigosForaJogo() {
          echo $this->core->amigosForaDoJogo($_POST['jogo_id'],$_POST['query'],$_POST['nivel']);
          exit;
       }   

       /**
        * _actionCancelarJogo function
        * @return array
        * */
       public function _actionCancelarJogo() {
           
          $jogo_id = $this->getParameter(3);

          #marca o jogo como cancelado
          $this->model->alterData('jogos', array('cancelado'=>1,'quem_cancelou'=>$_SESSION['usuario_id']), array('id'=>$jogo_id));

          #remove os convites para o jogo
          $this->model->removeData('convites',array('jogo_id'=>$jogo_id));

          //manda notificacao e email para todos os participantes do jogo
          $jogo = $this->model->getData('jogos','a.*', array('id'=>$jogo_id));
          
          $time1 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['equipe_id']));
          $time2 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['adversario_id']));
          $msg = 'Um jogo foi cancelado';
          $link = '/jogos/contra/'.$jogo_id;

          //envia email para todos os jogadores do time que esta desafiando 
          $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['equipe_id'].')'));
          foreach ($jogadores as $jogador) {
            //envia o lembrete por email
            $mensagem = 'Dia: '.$dataDoJogo.'. <br>
                         Horário: '.$jogo[0]['horainicio'].'. <br>
                         Local: '.$jogo[0]['local'].' - '.$jogo[0]['endereco'].'. <br>
                         Status: Cancelado. <br>';
            $assunto = 'O jogo do seu time '.$time1[0]['nome'].' contra '.$time2[0]['nome'].' foi cancelado ';                
            $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
            $this->core->addNotification($jogador['id'],$msg,$link);
          }

          //envia email para todos os jogadores do time que esta sendo desafiado
          $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['adversario_id'].')'));
          foreach ($jogadores as $jogador) {
            //envia o lembrete por email
            $mensagem = 'Dia: '.$dataDoJogo.'. <br>
                         Horário: '.$jogo[0]['horainicio'].'. <br>
                         Local: '.$jogo[0]['local'].' - '.$jogo[0]['endereco'].'. <br>
                         Status: Cancelado. <br>';
                        
                        
            $assunto = 'O jogo do seu time '.$time2[0]['nome'].' contra o '.$time1[0]['nome'].' foi cancelado';                
            $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
            $this->core->addNotification($jogador['id'],$msg,$link);
          }

          #redireciona
          $this->redirect('/perfil');
      }     

      /**
       * _actionAlteraJogo function
       * @return array
       * */
      public function _actionAlteraJogo() {
        
        $d = explode('/',$_POST['data']);

        $_POST['tempo'] = mktime(0,0,0,$d[1],$d[0],$d[2]);
        $_POST['nivel_mercado'] = ($_POST['mercado'] == 'on') ? 'publico' : 'privado';

        unset($_POST['data']);
        unset($_POST['mercado']);
        
        $this->model->alterData('jogos', $_POST, array('id'=>$_POST['id']));

        $this->success('Jogo alterado com sucesso!','/jogos/ver/'.$_POST['id']);

      }   



      /**
       * _actionbuscaJogadores function
       * @return array
       * */
      public function _actionbuscaJogadores() {
        
        $_POST['query'] = ($_POST['query'] != 'buscar outros jogadores') ? $_POST['query'] : '';
    
        $filters['precadastro'] = 0;
        $filters['like nome'] = $_POST['query'];
        $filters['dif id'] = $_SESSION['usuario_id'];

        #caso receba um id de time tire da busca os jogadores desse time 
        if($_POST['equipe_id'] != '') {
          #busco os jogadores da equipe
          $jogadores = $this->model->getData('equipe_participante','a.*', array('equipe_id'=>$_POST['equipe_id']));
          foreach($jogadores as $jogador) {
            $lista .= $jogador['usuario_id'].',';
          }
          $jogadores = $this->cutEnd($lista,1);
          $filters['notin id'] = '('.$jogadores.')';
        }
        $filters['tipo'] = 'jogador';


        //busca os amigos que estiverem nessa busca
        $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['query'],'in id'=>'(select distinct(amigo_id) from amizades where usuario_id = '.$_SESSION['usuario_id'].')'));
        if($res[0]['result'] != 'empty') {
            foreach ($res as $user) {
                $usersList .= $user['id'].',';
            }
        }
        if($_POST['nivel'] != 'amigos') {
          //busca os amigos de amigos 
          $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['query'],'in id'=>'(select distinct(amigo_id) from amizades where usuario_id in (select amigo_id from amizades where usuario_id = '.$_SESSION['usuario_id'].'))'));
          if($res[0]['result'] != 'empty') {
              foreach ($res as $user) {
                  $usersList .= $user['id'].',';
              }
          }
          //busca os outros
          $res = $this->model->getData('usuario','id', array('like nome'=>$_POST['query'],'notin id'=>"($usersList 0)"));
          if($res[0]['result'] != 'empty') {
              foreach ($res as $user) {
                  $usersList .= $user['id'].',';
              }
          }
        } else {
          $filters['in id'] = '(select distinct(amigo_id) from amizades where usuario_id = '.$_SESSION['usuario_id'].')';
        }
        $usersList = $this->core->cutEnd($usersList,1);

        
        
        if($usersList == '') {

          $usuarios = $this->model->getData('usuario','a.*', $filters, array('start'=>0,'limit'=>27));
        } else {
          $filters['in id'] = "($usersList)";
          $usuarios = $this->model->getData('usuario','a.*', $filters, array('start'=>0,'limit'=>27),'"field(id,$usersList)"');
        }
        
        
        foreach ($usuarios as $amigo) {
            $keys['avatar'] =   $this->core->avatar($amigo);
            $keys['amigo']  =   $this->html->link($this->html->b(utf8_encode($amigo['nome'])),'/perfil/ver/'.$amigo['id']);
            $keys['amigo_id'] = $amigo['id'];
          
            $amigoHtml = $this->includeHTML('../view/equipes/snippets/convidar.html');
            $amigoHtml = $this->applyKeys($amigoHtml,$keys);    

            $listaAmigos .= $this->html->div($amigoHtml,array('id'=>'amigo-'.$amigo['id'],'class'=>'amigosListAmigo')); 
        }
        
        $this->noresult($usuarios,'Não foram encontrados usuarios');
        
        echo $listaAmigos; 
        exit;
      }   
           

      /**
       * _actionaceitarContra function
       * @return array
       * */
      public function _actionaceitarContra() {
        $game_id = $this->getParameter(3);

        //marca que o desafiado aceitou o contra
        $this->model->alterData('jogos', array('desafiado_confirmado' => 1), array('id'=>$game_id));
        
        ######adiciona os jogadores dos dois times ao jogo#####
        //busca os dados do jogo
        $jogo = $this->model->getData('jogos','a.*', array('id'=>$game_id));

        //addiciona os jogadores do time 1 ao jogo
        $this->core->addteamtogame($game_id,$jogo[0]['equipe_id'],'Confirmado');

        //addiciona os jogadores do time 2 ao jogo
        $this->core->addteamtogame($game_id,$jogo[0]['adversario_id'],'Confirmado');

        //se aceitou o contra marca como aceito todos os convites relativos a este jogo pois quem tem que aceitar é o admin do time
        $this->model->alterData('convites', array('aceito'=>1), array('jogo_id'=>$game_id));



        //manda email e notificação de que foi aceito
        $time1 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['equipe_id']));
        $time2 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['adversario_id']));
        $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['equipe_id'].')'));
        foreach ($jogadores as $jogador) {
          //envia o lembrete por email
          $mensagem = 'O desafio para o contra foi aceito<br>';

          $assunto = 'O time '.$time2[0]['nome'].' aceitou o contra o '.$time1[0]['nome'];                
          $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);

          //envia uma notificacao ao jogador
          $this->core->addNotification($jogador['id'],'O desafio foi aceito','/jogos/ver/'.$game_id);

        }


      //envia email para todos os jogadores do time que esta sendo desafiado
      //busca os jogadores do time que esta desafiando
      $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['adversario_id'].')'));
      foreach ($jogadores as $jogador) {
        //envia o lembrete por email
        $mensagem = 'O desafio para o contra foi aceito<br>'; 
                    
        $assunto = 'O time '.$time2[0]['nome'].' aceitou o contra o '.$time1[0]['nome'];                
        $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
        $this->core->addNotification($jogador['id'],'O desafio  foi aceito','/jogos/ver/'.$game_id);
      }




        
        $this->redirect('/jogos/contra/'.$game_id);
        
        #return value keys to replace in html
        return $this->keys;
      }   

      /**
       * _actionaceitarContra function
       * @return array
       * */
      public function _actionRecusarContra() {
        $game_id = $this->getParameter(3);

        //marca que o desafiado aceitou o contra
        $this->model->alterData('jogos', array('desafiado_confirmado' => 2), array('id'=>$game_id));
        
        //remove todos os convites relativos a esse desafio
        $this->model->removeData('convites', array('jogo_id'=>$game_id));
        
        //remove todos os alertas relativos a esse desafio
        $this->model->removeData('notificacoes', array('link'=>'/jogos/contra/'.$game_id));

        //busca dados do jogo
        $jogo = $this->model->getData('jogos','a.*',array('id'=>$game_id));

       


        //No caso de um contra ser recusado pelo administrador do time desafiado, 
        //isso irá gerar um e-mail para todos os integrantes de ambos os times  
        //dizendo que aquele contra foi recusado. 
        $time1 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['equipe_id']));
        $time2 = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['adversario_id']));
        $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['equipe_id'].')'));
        foreach ($jogadores as $jogador) {
          //envia o lembrete por email
          $mensagem = 'O desafio para o contra foi recusado<br>';

          $assunto = 'O time '.$time2[0]['nome'].' recusou o contra o '.$time1[0]['nome'];                
          $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);

          //envia uma notificacao ao jogador
          $this->core->addNotification($jogador['id'],'O desafio n&atilde;o foi aceito','/jogos/ver/'.$game_id);

        }


      //envia email para todos os jogadores do time que esta sendo desafiado
      //busca os jogadores do time que esta desafiando
      $jogadores = $this->model->getData('usuario','a.*',array('in id'=>'(select usuario_id from equipe_participante where equipe_id = '.$jogo[0]['adversario_id'].')'));
      foreach ($jogadores as $jogador) {
        //envia o lembrete por email
        $mensagem = 'O desafio para o contra foi recusado<br>'; 
                    
        $assunto = 'O time '.$time2[0]['nome'].' recusou o contra o '.$time1[0]['nome'];                
        $this->core->enviaEmail($assunto,$assunto,$mensagem,$jogador['id']);
        $this->core->addNotification($jogador['id'],'O desafio n&atilde;o foi aceito','/jogos/ver/'.$game_id);
      }

        //redireciona
        $this->redirect('/jogos');
        
      }   
           



      /**
       * _actioncriarGaleria function
       * @return array
       * */
      public function _actioncriarGaleria() {
        
        $_POST['usuario_id'] = $_SESSION['usuario_id'];
        $galeria_id = $this->model->addData('galerias',$_POST);

        if($_POST['equipe_id'] != '') {
          $this->redirect('/equipes/vergaleria/'.$galeria_id);
        } 

        if($_POST['quadra_id'] != '') {
          $this->redirect('/quadras/vergaleria/'.$galeria_id);
        } 
        $this->redirect('/perfil/vergaleria/'.$galeria_id);

      }   
        


      /**
      * _actionCadastraFoto function
      * @return array
      * */
      public function _actionCadastrarFoto() {
        $file = $this->loadModule('file');
        $data = $_POST;  

        $data['nome'] = $file->uploadFile($_FILES['arquivo'],'repository/upload/');
        $data['tipo'] = 'foto';
        $data['usuario_id'] = $_SESSION['usuario_id'];


        #pega dados da galeria
        $galeria = $this->model->getData('galerias','a.*', array('id'=>$_POST['galeria_id']));
        $data['equipe_id'] = $galeria[0]['equipe_id'];
        $data['quadra_id'] = $galeria[0]['quadra_id'];
        

        $this->model->addData('midia',$data);

        $this->redirect('/perfil/vergaleria/'.$_POST['galeria_id']);

        #return value keys to replace in html
        return $this->keys;
      }   

      /**
      * _actionCadastraFoto function
      * @return array
      * */
      public function _actionCadastrarVideo() {
        $file = $this->loadModule('file');
        $data = $_POST;  

        $data['nome'] = $_POST['nome'];
        $data['tipo'] = 'video';
        $data['usuario_id'] = $_SESSION['usuario_id'];

        #pega dados da galeria
        $galeria = $this->model->getData('galerias','a.*', array('id'=>$_POST['galeria_id']));
        $data['equipe_id'] = $galeria[0]['equipe_id'];
        $data['quadra_id'] = $galeria[0]['quadra_id'];

        $this->model->addData('midia',$data);

        $this->redirect('/perfil/vergaleria/'.$_POST['galeria_id']);

        #return value keys to replace in html
        return $this->keys;
      }   
       

     /**
      * _actionremoveGaleria function
      * @return array
      * */
     public function _actionremoveGaleria() {
       
        $galeria_id = $this->getParameter(3);

        $url = '/perfil/galeria/fotos/'.$_SESSION['usuario_id'];
        //caso seja de uma equipe
        if($this->getParameter(4) == 'equipe') {
          $galeria = $this->model->getData('galerias','a.*', array('id'=>$galeria_id));
          $url = '/equipes/galeria/fotos/'.$galeria[0]['equipe_id'];
        }
        //caso seja de uma quadra
        if($this->getParameter(4) == 'quadra') {
          $galeria = $this->model->getData('quadras','a.*', array('id'=>$galeria_id));
          $url = '/quadras/galeria/fotos/'.$galeria[0]['equipe_id'];
        }

        $this->model->removeData('galerias',array('id'=>$galeria_id));

        $this->model->removeData('midia',array('galeria_id'=>$galeria_id));

        $this->success('Galeria removida',$url);

     }   
     /**
      * _actionremoveGaleria function
      * @return array
      * */
     public function _actionremoverMidia() {
       
        $midia_id = $this->getParameter(3);

        //antes de remover busca os dados da midia
        $midia = $this->model->getData('midia','a.*', array('id'=>$midia_id));
        
        $this->model->removeData('midia',array('id'=>$midia_id));

        $this->success('Removido com sucesso','/perfil/vergaleria/'.$midia[0]['galeria_id']);
     }  


     /**
      * _actionAlterarEquipe function
      * @return array
      * */
     public function _actionAlterarEquipe() {
       $file = $this->loadModule('file');
      if($_FILES['escudo']['name']!= '') {
         $_POST['escudo'] = $file->uploadFile($_FILES['escudo'],'repository/upload/'); 
      }
       

        $equipe_id = $_POST['currentTeam'];
        unset($_POST['currentTeam']);

       $this->model->alterData('equipes', $_POST, array('id'=>$equipe_id));

       $this->redirect('/equipes/ver/'.$equipe_id.'/alterado');
     } 

     /**
      * _actionAlterarEquipe function
      * @return array
      * */
     public function _actionAlterarGaleria() {
        $this->model->alterData('galerias', $_POST, array('id'=>$_POST['id']));
        $galeria = $this->model->getData('galerias','a.*', array('id'=>$_POST['id']));
        if($galeria[0]['equipe_id']!=0) {
          $this->success('Galeria alterada com sucesso!','/equipes/galeria/fotos/'.$galeria[0]['equipe_id']);  
        }
        if($galeria[0]['quadra_id']!=0) {
          $this->success('Galeria alterada com sucesso!','/quadras/galeria/fotos/'.$galeria[0]['quadra_id']);  
        }
        $this->success('Galeria alterada com sucesso!','/perfil/galeria/fotos/'.$_SESSION['usuario_id']);
     }   


     /**
      * _actionconvidaParaTime function
      * @return array
      * */
     public function _actionconvidaParaTime() {
        $equipe_id = $_POST['equipe_id'];
        foreach ($_POST['convite'] as $key => $convidado) {
          #coloco o usuario como parte da equipe
          #$this->model->addData('equipe_participante',array('usuario_id'=>$convidado,'equipe_id'=>$equipe_id));
          #enviar o convite para o jogador fazer parte do time
          $this->core->addtoteam($equipe_id, $convidado);

        }


        //adiciona os amigos que foram colocados como administradores
        foreach ($_POST['admin'] as $key => $admin) {
          #coloco o usuario como administrador
          $this->model->addData('equipe_administrador',array('usuario_id'=>$admin,'equipe_id'=>$equipe_id));
        }

        
        //precadastra e envia o convite e adiciona ao time
        foreach ($_POST['foradarede'] as $key => $value) {
          $fora = explode('|', $value);
          $nome = $fora[0];
          $email = $fora[1];
          $amigoDeFora = $this->core->precadastra($nome,$email);
          $this->model->addData('equipe_participante',array('usuario_id'=>$amigoDeFora,'equipe_id'=>$equipe_id));
        }

        $this->success('Amigos convidados com sucesso','/equipes/ver/'.$equipe_id);
     }   
          
  
     /**
      * _actionbuscaEndereco function
      * @return array
      * */
     public function _actionbuscaEndereco() {
        $local = $_POST['local'];

        $res = $this->model->getData('locais','a.*', array('name'=>utf8_decode($local)));
        echo utf8_encode($res[0]['endereco']);
        exit;
     }   


     /**
      * _actionfechajogo function
      * @return array
      * */
     public function _actionfechajogo() {
        //recebe o o id do jogo
        $jogo_id = $this->getParameter(3);

        //fecha o jogo
        $data['fechado'] = 1;

        //descobre quem é o craque do jogos
        $res = $this->model->getData('jogos_fatos','count(*) as qtd, participante_id', array('jogo_id'=>$jogo_id), array('start'=>0,'limit'=>1),'qtd desc','', 'participante_id');
        $craque_partida = $res[0]['participante_id'];
        
        if($res[0]['result'] != 'empty') {
          $craque = $this->model->getData('usuario','a.*', array('id' => $craque_partida));
          $data['craque_jogo'] = $craque[0]['nome'];
        
          //coloca o craque da partida no perfil do usuario
          $craque_jogo = $craque[0]['craque_jogo'] + 1;
          $this->model->alterData('usuario', array('craque_jogo' => $craque_jogo), array('id'=>$craque_partida));
        }
      
        //marca quem foi o craque do jogo
        $this->model->alterData('jogos',$data,array('id'=>$jogo_id)); 

        #adiciona notificações para todos os participantes dizendo que o jogo foi encerrado
        #busca os participantes
        $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo_id),'','a.ID DESC')  ;
        foreach ($jogadores as $jogador) {
          $this->core->addNotification($jogador['participante_id'],'Um jogo foi encerrado, confira o resultado','/jogos/ver/'.$jogo_id);
        }
        //remove todos os convites que foram feitos para esse jogo
        $this->model->removeData('convites',array('jogo_id' => $jogo_id));

        $this->redirect('/jogos/ver/'.$jogo_id);
     }  



      /**
       * _actionnotificacao function
       * @return array
       * */
      public function _actionnotificacao() {
        //marca a leitura de uma notificacao
        $notificacao_id = $this->getParameter(3);
        //busca notificiacao
        $notificacao = $this->model->getData('notificacoes','a.*', array('id' => $notificacao_id));
        //marca como lida
        $this->model->alterData('notificacoes', array('status'=>1),array('id'=>$notificacao_id));        
        //redireciona  
        $this->redirect($notificacao[0]['link']);
      }   


      /**
       * _actionIniciaAvaliacoes function
       * Processo de lembrete pós jogo
       * Este metodo vai ser chamado via cron em um intervalo de 5 minutos
       * Ele sera responsavel por enviar o email e notificacao de que o jogo deve ser avaliado aos seus participantes
       * @return array
       * */
      public function _actionIniciaAvaliacoes() {
        $players = 0;
        $games = 0;

        //busca os jogos que já aconteceram  nåo estao fechados e ainda nao foram enviados os lembretes
        $filtros['fechado'] = 0;
        $filtros['< tempo'] = time();
        $filtros['lembrete_enviado'] = 0;

        $jogos = $this->model->getData('jogos','a.*', $filtros);

        //itera jogos
        if($jogos[0]['result'] != 'empty') {
          foreach ($jogos as $jogo) {
            $games++;
            #marca o jogo como lembrete enviado 
            $this->model->alterData('jogos', array('lembrete_enviado'=>1), array('id'=>$jogo['id']));

            #busca os jogadores
            $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo['id']),'','a.ID DESC', 'inner join usuario as u on u.id = a.participante_id ')  ;
          
            #itera jogadores
            foreach ($jogadores as $jogador) {
              $players++;

              //envia as notificacoes de sistema aos jogadores
              $this->core->addNotification($jogador['participante_id'],'Voc&ecirc; tem um jogo para avaliar','/jogos/ver/'.$jogo['id']);

              //envia o lembrete por email
              $mensagem = $jogador['nome'] .' <br><br>
                              A partida do dia  '.date('d/m/Y',$jogo['tempo']).' às '.$jogo['horainicio'] .' no '.utf8_encode($jogo['local']).' já foi jogada. Agora é hora de avaliar esse jogo, melhorar seu histórico e de seus amigos. Ver quem participou do jogo, quem marcou os gols, marcar os craques e muito mais. Você tem 48h para avaliar e garantir suas avaliações.<br><br>
                             É rapido! <br><br>
                             Segue o jogo...<br>';
              $this->core->enviaEmail($jogador['nome'] .' VAMOS AOS RESULTADOS DO JOGO!',$jogador['nome'] .' VAMOS AOS RESULTADOS DO JOGO!',$mensagem,$jogador['participante_id']);
              
            } #fecha jogadores
          } #fecha jogos
        }# fecha if
        echo $games.' Games - ';
        echo $players.' Players';
      }   
    
    public function _actionSairtime() {
        $time = $this->core->getParameter(3);
        $this->model->removeData('equipe_participante',array('usuario_id'=>$_SESSION['usuario_id'],'equipe_id'=>$time));
        $this->redirect('/equipes');
    }     

    public function _actionRemoverJogadorEquipe()  { 
      $this->model->enableDebug();
      $this->model->removeData('equipe_participante',array('usuario_id'=>$_POST['jogador'],'equipe_id'=>$_POST['equipe_id']));
    } 


    public function _actionAddlike() {
      $quadra_id = $_POST['quadra_id'];
      $this->model->addData('curtidas',array('usuario_id'=>$_SESSION['usuario_id'],'quadra_id'=>$quadra_id));
      
    }
    public function _actionRemovelike() {
      $quadra_id = $_POST['quadra_id'];
      $this->model->removeData('curtidas',array('usuario_id'=>$_SESSION['usuario_id'],'quadra_id'=>$quadra_id));
    }

    /**
    * Se o jogador recusa o convite, ele vai para a tela perfil, e esse jogo sai da sua agenda e ele para de receber postagens do mural daquele jogo. 
    **/
    public function _actionRecusarConviteContra() {
      $jogo_id = $this->core->getParameter(3);

      //remove do jogo
      $this->model->removeData('jogo_participantes',array('jogo_id'=>$jogo_id,'participante_id'=>$_SESSION['usuario_id']));
      //remove o convite
      $this->model->removeData('convites',array('jogo_id'=>$jogo_id,'usuario_id'=>$_SESSION['usuario_id']));
      
      $this->redirect('/perfil');
    }

    /**
    *Marca wo para o time que cancelou o contra 
    **/
    public function _actionMarcawo() {
      $jogo_id = $this->getParameter(3);

      $jogo = $this->model->getData('jogos','a.*',array('id'=>$jogo_id));

      if(!$this->core->isTeamAdmin($jogo[0]['equipe_id'])) {
        //marca um wo pro time  
        $time = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['equipe_id']));
        $wo = $time[0]['wo']+1;
        $this->model->alterData('equipes', array('wo' => $wo), array('id'=>$time[0]['id']));
      }

      if(!$this->core->isTeamAdmin($jogo[0]['adversario_id'])) {
        //marca um wo pro time
        $time = $this->model->getData('equipes','a.*',array('id'=>$jogo[0]['equipe_id']));
        $wo = $time[0]['wo']+1;
        $this->model->alterData('equipes', array('wo' => $wo), array('id'=>$time[0]['id']));

      }

      //marca que o wo ja foi decidido
      $this->model->alterData('jogos', array('wo_decidido' => 1), array('id'=>$jogo_id));
      $this->redirect('/jogos/contra/'.$jogo_id);
    }

            
}

?>