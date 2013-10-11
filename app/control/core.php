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
         * getEnabledModules function
         * @return array
         * */
        public function getEnabledModules() {
        	
        	#get modules enables for this kind of user
        	$modules = $this->model->getData('regra_acesso','*,m.id as modulo_id', array('tipo_usuario'=>$_SESSION['tipo']),'','a.ID DESC','INNER JOIN modulo AS m ON m.id = a.modulo');

            foreach ($modules as $module) {
                unset($module['id']);
                $module['tag'] = str_replace(' ', '-', $module['nome']);
                $return[] = $module;
            }
        	return $return;
        }

        /**
         * getUser function
         * @return array
         * */
        public function getUser($user_id,$pass=false) {
            
            #get modules enables for this kind of user
            $user_data = $this->model->getData('usuario','a.*', array('id'=>$_SESSION['usuario_id']));

            if(!$pass) {
                unset($user_data[0]['senha']);
            }
            $return = $user_data[0];
            return $return;
        }

        
        /**
         *  isLogged
         * @return boolean
         * */
        public function isLogged() {
            if(empty($_SESSION['usuario_id'])) {
                return false;
            } else {
                return true;
            }
        }

        public function getUserData($user_id) {
            $res = $this->model->getData('usuario','a.*, UNIX_TIMESTAMP(tempo) as time ', array('id' =>$user_id));
            return $res[0];
        }

        public function getCurrentUserData() {
            return $this->getUserData($_SESSION['usuario_id']);
        }

        public function getUserGames($limit = 10) {
            
            //busca os jogos recreativos que o usuario participara com status confirmado
            $res = $this->model->getData('jogo_participantes','a.jogo_id', array('participante_id'=>$_SESSION['usuario_id'],'status'=>'Confirmado'));
            if($res[0]['result'] != 'empty') {
                foreach ($res as $jogo) {
                    $jogos_ids .= $jogo['jogo_id'].',';
                }
            }
            $filtros['>a.tempo'] = time();
            if($jogos_ids!='') {
                $jogos_ids = $this->cutEnd($jogos_ids,1);
                $filtros['in a.id'] = "($jogos_ids)";    
            } else {
                $filtros['a.id'] = 0;
            }
            $data = $this->model->getData('jogos','a.*,c.nome as criador',$filtros, array('start'=>0,'limit' => $limit),'a.tempo asc','left join jogo_participantes as b on b.jogo_id = a.id inner join usuario as c on c.id = a.criador_id ', 'a.id');

            foreach ($data as $jogo) {
                if($jogo['tipo'] == 'contra') {
                    $time1 = $this->model->getData('equipes','a.nome', array('id'=>$jogo['equipe_id']));
                    $jogo['time1'] = $time1[0]['nome']; 

                    $time2 = $this->model->getData('equipes','a.nome', array('id'=>$jogo['adversario_id']));
                    $jogo['time2'] = $time2[0]['nome']; 
                }


                $retorno[] = $jogo;
            }
            return $retorno;
        }

        public function getTeamGames($equipe_id) {
            $data = $this->model->getData('jogos','a.*,c.nome as criador', array('equipe_id'=>$equipe_id,'or adversario_id'=>$equipe_id ), '','a.ID DESC','left join jogo_participantes as b on b.jogo_id = a.id inner join usuario as c on c.id = a.criador_id ', 'a.id');
            return $data;
        }

        public function basicUserData($user_id) {
            $userData = $this->getUserData($user_id);

            #totaliza dados do usuario
            $userData['jogadores'] =  $this->model->countData('amizades', array('usuario_id'=>$user_id));
            $userData['equipes'] =  $this->model->countData('equipe_participante', array('usuario_id'=>$user_id));
            $userData['fotos'] =  $this->model->countData('midia', array('usuario_id'=>$user_id,'tipo' => 'foto','equipe_id'=>0,'quadra_id'=>0));
            $userData['videos'] =  $this->model->countData('midia', array('usuario_id'=>$user_id,'tipo' => 'video','equipe_id'=>0,'quadra_id'=>0));

            $keys = $userData;

            $keys['idade'] = idade($userData['nascimento']); 
            $keys['jogos_total'] = $userData['jogos_recreativos']+$userData['jogos_contras']+$userData['jogos_campeonato'];
            $keys['totalGols'] = $userData['gols_recreativos']+$userData['gols_contras']+$userData['gols_campeonato']; 
            $keys['mediaGols'] = number_format(@($keys['totalGols']/$keys['jogos_total']),2); 
            $keys['apelido'] =  $userData['apelido'];

            #avatar
            if($userData['avatar'] == '') {
                $keys['avatar'] = '/images/avatar_padrao.jpg'; 
            } else {
                if(file_exists($_SERVER['DOCUMENT_ROOT'].$keys['avatar'])) {
                    $keys['avatar'] = '/images/avatar_padrao.jpg'; 
                } else {
                    $keys['avatar'] = '/repository/upload/'.$keys['avatar'];    
                }
            }
            
            #seletor de horas dias de preferencia
            $horarios = $this->hours();
 
            #busco dias de preferencia do usuario
            $data = $this->model->getData('dias_preferencia','a.*', array('usuario_id'=>$user_id));

            #exibo o seletor de horario dos dias de preferencia
            $keys['hourSelector-segunda'] = $this->html->select(true,$horarios,'horario-segunda',$data[0]['segunda'],0,'','hourSelector',"salvaHorario('segunda')");
            $keys['hourSelector-terca'] = $this->html->select(true,$horarios,'horario-terca',$data[0]['terca'],0,'','hourSelector',"salvaHorario('terca')");
            $keys['hourSelector-quarta'] = $this->html->select(true,$horarios,'horario-quarta',$data[0]['quarta'],0,'','hourSelector',"salvaHorario('quarta')");
            $keys['hourSelector-quinta'] = $this->html->select(true,$horarios,'horario-quinta',$data[0]['quinta'],0,'','hourSelector',"salvaHorario('quinta')");
            $keys['hourSelector-sexta'] = $this->html->select(true,$horarios,'horario-sexta',$data[0]['sexta'],0,'','hourSelector',"salvaHorario('sexta')");
            $keys['hourSelector-sabado'] = $this->html->select(true,$horarios,'horario-sabado',$data[0]['sabado'],0,'','hourSelector',"salvaHorario('sabado')");
            $keys['hourSelector-domingo'] = $this->html->select(true,$horarios,'horario-domingo',$data[0]['domingo'],0,'','hourSelector',"salvaHorario('domingo')");

            #exibe o dia como ativo ou inativo)
            $keys['segunda'] = ($data[0]['segunda'] == 0) ? 'day' : 'dayActive'; 
            $keys['terca'] = ($data[0]['terca'] == 0) ? 'day' : 'dayActive'; 
            $keys['quarta'] = ($data[0]['quarta'] == 0) ? 'day' : 'dayActive'; 
            $keys['quinta'] = ($data[0]['quinta'] == 0) ? 'day' : 'dayActive'; 
            $keys['sexta'] = ($data[0]['sexta'] == 0) ? 'day' : 'dayActive'; 
            $keys['sabado'] = ($data[0]['sabado'] == 0) ? 'day' : 'dayActive'; 
            $keys['domingo'] = ($data[0]['domingo'] == 0) ? 'day' : 'dayActive'; 
            
            #exibe a hora de preferencia
            $keys['hora-segunda'] = ($data[0]['segunda'] == 0) ? '--' : $data[0]['segunda'] ; 
            $keys['hora-terca'] = ($data[0]['terca'] == 0) ? '--' : $data[0]['terca'] ; 
            $keys['hora-quarta'] = ($data[0]['quarta'] == 0) ? '--' : $data[0]['quarta'] ; 
            $keys['hora-quinta'] = ($data[0]['quinta'] == 0) ? '--' : $data[0]['quinta'] ; 
            $keys['hora-sexta'] = ($data[0]['sexta'] == 0) ? '--' : $data[0]['sexta'] ; 
            $keys['hora-sabado'] = ($data[0]['sabado'] == 0) ? '--' : $data[0]['sabado'] ; 
            $keys['hora-domingo'] = ($data[0]['domingo'] == 0) ? '--' : $data[0]['domingo'] ; 

            return $keys;
        }

        public function posicoes() {
            $return['Goleiro'] = 'Goleiro';
            $return['Defesa'] = 'Defesa';
            $return['Meio-campo'] = 'Meio-campo';
            $return['Ataque'] = 'Ataque';
            return $return;
        }

        public function modalidades() {
            $return['Campo'] = 'Campo';
            $return['Society'] = 'Society';
            $return['Salão'] = 'Sal&atilde;o';
            return $return;
        }

        public function listMessages($filters,$limits,$showOrigin = false,$showcomments = false,$markRead = 1) {
            
            //caso um filtro de juncao esteja vazio, remove ele
            if($filters['orin a.equipe_id'] == '()') {
                unset($filters['orin a.equipe_id']);
            }

            if($filters['orin a.jogo_id'] == '()') {
                unset($filters['orin a.jogo_id']);
            }
            if($filters['a.jogo_id'] != '') {
                unset($filters['in a.usuario_id']);
            }

            if($filters['pai_id'] == 0) {
                $data = $this->model->getData('mural','a.*,u.nome,u.avatar,UNIX_TIMESTAMP(a.tempo) AS time,e.nome as equipe', $filters, $limits,'a.ID DESC','inner join usuario as u on u.id = a.usuario_id left join equipes as e on e.id = a.equipe_id left join jogos as j on j.id = a.jogo_id');
            } else {
                $data = $this->model->getData('mural','a.*,u.nome,u.avatar,UNIX_TIMESTAMP(a.tempo) AS time,e.nome as equipe', $filters, $limits,'a.ID ASC','inner join usuario as u on u.id = a.usuario_id left join equipes as e on e.id = a.equipe_id left join jogos as j on j.id = a.jogo_id');
            }
            
            if($data[0]['result'] != 'empty') {
                foreach ($data as $message) {
                    if(($message['pai_id'] == 0) or ($showcomments)) {

                    #caso seja o mural do usuario mostra a origem da mensagem
                    if($showOrigin) {
                        if($message['equipe_id'] != 0) {
                            $message['texto'] = $message['texto'] . '</br><b style="font-size:9px;"> Publicada em: '.$this->html->link($message['equipe'],'/equipes/ver/'.$message['equipe_id']).'</b>';
                        }

                        if($message['jogo_id'] != 0) {
                            $message['texto'] = $message['texto'] . '</br><b style="font-size:9px;"> Publicada em: '.$this->html->link('Jogo','/jogos/ver/'.$message['jogo_id']).'</b>';
                        }
                    }    

                    $markRead = ($markRead == '') ? 1 : $markRead;
                    #marca uma mensagem como lida uma mensagem
                    $this->model->alterData('mural', array('unread'=>$markRead), array('id'=>$message['id']));

                    if($message['avatar'] =='') {   
                        $avatar = $this->html->div($this->html->img('/images/avatar_padrao.jpg'),array('class'=>'avatarThumb')); 
                    } else {
                        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/repository/upload/'.$message['avatar'])) {
                            $avatar = $this->html->div($this->html->img('/repository/upload/'.$message['avatar']),array('class'=>'avatarThumb'));     
                        } else {
                            $avatar = $this->html->div($this->html->img('/images/avatar_padrao.jpg'),array('class'=>'avatarThumb')); 
                        }
                        
                    }
                    $avatar = $this->html->link($avatar,'/perfil/ver/'.$message['remetente']);
                    $remetente = $this->html->span(utf8_encode($message['nome']));
                    $mensagem = $this->html->label(utf8_encode($message['texto']));
                  
                    $linkRemover = '';
                    $linkComentar = '';
                    #monta o link de remover a mensagem
                    if(($message['pai_id'] == 0)||($message['quadra_id'] != 0)) {
                        //caso o usuario logado seja o remetende ou destinatario coloca o remover
                        if(($message['usuario_id'] == $_SESSION['usuario_id']) or ($message['remetente'] == $_SESSION['usuario_id']) or ( ($message['quadra_id'] == $_SESSION['quadra_id'])  && ($_SESSION['tipo']=='quadra')  )) {
                            $linkRemover = $this->html->div('EXCLUIR '.$this->html->img('/images/icones/remover.jpg'),array('class'=>'linkRemover','id'=>'removeMessage-'.$message['id']));
                        }
                        
                        #monta o link para comentar um post apenas se não é comentario
                        #contar os comentarios
                        $qtdComentarios = $this->model->countData('mural',array('pai_id'=>$message['id']));
                        if($qtdComentarios > 0){
                            // verifica se essa conversa tem notificacoes pra este usuario
                            $notificacoes = $this->model->countData('mural_notificacoes',array('conversa_id'=>$message['id'],'usuario_id' =>$_SESSION['usuario_id']));
                            if($notificacoes > 0) {
                                $linkComentar = $this->html->div('Comentarios: '.$qtdComentarios,array('id'=>'comentar-'.$message['id'],'class'=>'linkComentar red')); 
                            } else {
                                $linkComentar = $this->html->div('Comentarios: '.$qtdComentarios,array('id'=>'comentar-'.$message['id'],'class'=>'linkComentar'));     
                            }
                            
                        } else {
                            $linkComentar = $this->html->div('Comentar ',array('id'=>'comentar-'.$message['id'],'class'=>'linkComentar'));   
                        }
                        
                    } else {
                        $linkRemover = $this->html->div('EXCLUIR '.$this->html->img('/images/icones/remover.jpg'),array('class'=>'linkRemover removeComment','id'=>'removeMessage-'.$message['id']));
                        $linkComentar = '';
                    }
                     
                    
                    
                    //se a mensagem tem pai id é pq é um comentario
                    if($message['pai_id'] != 0) {
                        $linkComentar = '';
                    }
                   
                    #box onde serao carregados os comentarios sobre o post apenas se não é comentario
                    $comentsBox = ($message['pai_id'] == 0) ? $this->html->div('',array('id'=>'comentsBox-'.$message['id'])) : ''; 

                    #monta a ultima linha do post
                    $horario = $this->html->div(date('d/m/Y - H:i',$message['time']).$linkComentar.$linkRemover,array('class'=>'date'));

                    #define a class de acordo com o tipo de mensagm
                    $class = ($message['pai_id'] == 0) ? 'talkedMessage' : 'comentedMessage';
                    
                    $mensagem = utf8_decode($mensagem);

                    $saida .= $this->html->div($avatar.$remetente.$mensagem.$horario.$comentsBox,array('id'=>'message-'.$message['id'],'class'=>$class)); 
                    }
                }
            } else {
                $saida = '';
            }
            return $saida;
        }

        public function addtogame($game_id,$user_id,$status = 'pendente',$sendInvite = false) {
            $this->model->addData('jogo_participantes',array('participante_id'=>$user_id,'jogo_id' =>$game_id,'status'=>$status));

            #envia notificacao ao usuario
            if($sendInvite) {
                
                $data['usuario_id'] = $user_id;
                $data['remetente_id'] = $_SESSION['usuario_id'];
                $data['tipo'] = 'jogo';
                $data['jogo_id'] = $game_id;
                
                $this->model->addData('convites',$data);

                $game = $this->model->getData('jogos','a.*', array('id'=>$game_id));
                $convidado = $this->model->getData('usuario','a.*', array('id'=>$user_id));
                #envia mensagem por email
                $mensagem = $convidado[0]['nome'] ."<br><br>";
                $mensagem = "<b>".$_SESSION['usuario']." </b> convocou você para uma partida no ".utf8_encode($game[0]['local']).", no dia ".date('d/m/Y',$game[0]['tempo'])." às ".$game[0]['horainicio'].".</br>
                                                 Maiores informações sobre o jogo podem ser encontradas na página do evento no <a href=\"http://futbooking.com\" style=\"color:#f37d37;font-weigth:bold\">Futbooking.com</a>. <br><br>Segue o jogo...";
                
                $this->enviaEmail($_SESSION['usuario'].' convocou você para uma partida de futebol',$_SESSION['usuario'].' convocou você para uma partida de futebol',$mensagem,$user_id);
            }

        }

        public function addteamtogame($game_id,$team_id,$status = 'pendente' ) {
            //busca os jogadores do time
            $jogadores = $this->model->getData('equipe_participante','a.*', array('equipe_id' => $team_id));

            foreach ($jogadores as $jogador) {
                $this->addtogame($game_id,$jogador['usuario_id'],$status,true);
            }
        }

        public function getFriends($result='', $usuario_id = '') {
            if($usuario_id == '') {
                $usuario_id = $_SESSION['usuario_id'];
            }
            $res = $this->model->getData('amizades','b.id,b.nome,equipes,avatar', array('usuario_id'=>$usuario_id),'','a.ID DESC','inner join usuario as b on b.id = a.amigo_id');
            foreach ($res as $amigo) {
                
                #adiciona a quantidade de amigos em comum
                $amigo['amigos_em_comum'] = $this->friendsInCommon($amigo['id'],$_SESSION['usuario_id']);

                #adiciona a quantidade de equipes que o usuario faz parte
                $amigo['equipes'] = $this->model->countData('equipe_participante', array('a.usuario_id'=>$amigo['id']));

                $return[] = $amigo;

                $onlyids .=$amigo['id'].",";
            }
            if($res[0]['result'] == 'empty') {
                $return = '';
            }
            if($result == 'onlyids') {
                $onlyids = $this->cutEnd($onlyids,1);
                $return = "($onlyids)";
            }

             if($result == 'ids') {
                $onlyids = $this->cutEnd($onlyids,1);
                $return = "$onlyids";
            }
            
            return $return;
        }

        public function friendsInCommon($user1,$user2) {
            $res =  $this->model->getData('usuario', 'a.id, count(*) as qtd', array('in amigo_id'=>"($user1,$user2)"),'','a.id','inner join amizades as z on a.id = z.usuario_id','a.id');
          
            $count = 0;
            foreach ($res as $item) {
                if($item['qtd'] == 2) {
                    $count++;
                }
            }
            return $count;
        }
        public function loadNotifications() {
            #verifica notificacoes
            $keys['messageNotification'] = '';
            $unreadMessages = $this->model->countData('mensagem',array('usuario_id'=>$_SESSION['usuario_id'],'unread'=>0));
            $keys['messageNotification'] = ($unreadMessages != 0) ? $this->html->div($unreadMessages,array('id'=>'messageNotification')) : '';


            //busca as notificacoes de mensagems
            $res = $this->model->getData('mensagem','*',array('usuario_id'=>$_SESSION['usuario_id'],'unread'=>0),'','a.ID DESC', 'inner join usuario as u on u.id = a.remetente');
            if($res[0]['result'] != 'empty') {
                foreach ($res as $msg) {
                    $messageNotification .= "<span><b>".$msg['nome'].'</b> te enviou uma mensagem <a href="/perfil/mensagens/'.$msg['id'].' " class="g12">[Ver]</a></span></hr>';
                }
            }
            if($messageNotification != '') {
                $messageNotification .= '<a href="/perfil/mensagens" class="showAllmessages">Ver todas as mensagens</a>';  
            }

            $keys['messageNotification'] .= $this->html->div($messageNotification,array('id'=>'messageboxNotification'));

            //conta os convites para jogos
            $keys['gamesNotification'] = '';
            $gameUnreadMessages = $this->model->countData('convites',array('usuario_id'=>$_SESSION['usuario_id'],'tipo'=>'jogo','aceito'=>0));

            $keys['playersNotification'] = '';
            $unreadMessages = $this->model->countData('convites',array('usuario_id'=>$_SESSION['usuario_id'],'tipo'=>'amizade','aceito'=>0));
            $keys['playersNotification'] = ($unreadMessages != 0) ? $this->html->div($unreadMessages,array('id'=>'messageNotification')) : '';

            //get friendship invites
            $data = $this->model->getData('convites','a.*', array('usuario_id'=>$_SESSION['usuario_id'],'tipo'=>'amizade','aceito'=>0));
            foreach ($data as $invite) {
                $remetente = $this->model->getData('usuario','a.*', array('id'=>$invite['remetente_id']));
                $frindshipsInvites .= $this->html->link($remetente[0]['nome'],'/perfil/ver/'.$invite['remetente_id'],'','g12') .' te adicionou como amigo <br> ';
                $frindshipsInvites .= $this->html->link('[ACEITAR]','/action/aceptInvite/'.$invite['id'],'','o12') .'  ';
                $frindshipsInvites .= $this->html->link('[RECUSAR] ','/action/denyInvite/'.$invite['id'],'','o12') .' <br><br> ';
            }

            if($data[0]['result'] == 'empty') {
                $frindshipsInvites = '';
            } else { 
                $frindshipsInvites = $this->html->div($frindshipsInvites,array('id'=>'boxNotification')); 
            }
            $keys['frindshipsInvites'] = $frindshipsInvites;


            //busca os convites para entrar em times 
            $keys['teamNotification'] = '';
            $unreadMessages = $this->model->countData('convites',array('usuario_id'=>$_SESSION['usuario_id'],'tipo'=>'equipe','aceito'=>0));
            $keys['teamNotification'] = ($unreadMessages != 0) ? $this->html->div($unreadMessages,array('id'=>'messageNotification','style'=>'margin-top:0px;')) : '';
    

            //Busca convite para jogos
            $data = $this->model->getData('convites','a.*', array('usuario_id'=>$_SESSION['usuario_id'],'tipo'=>'jogo','aceito'=>0));
            foreach ($data as $invite) {

                //busca os dados do remetente
                $remetente = $this->model->getData('usuario','a.*', array('id'=>$invite['remetente_id']));
                
                //busca os dados do jogo
                $game = $this->model->getData('jogos','a.*', array('id'=>$invite['jogo_id']));
                if($game[0]['fechado'] == 0 ) {
                    $gamesInvites .= $this->html->link($remetente[0]['nome'],'/perfil/ver/'.$invite['remetente_id'],'','g12') .' <br> te convidou para participar do evento <br> ';
                    $gamesInvites .= $this->html->link($game[0]['tipo'],'/jogos/ver/'.$invite['jogo_id'],'','r12') .' <br>';
                    $gamesInvites .= $this->html->link('Ver mais','/jogos/ver/'.$invite['jogo_id'],'','g12') .' <br><hr>';
                } else {
                    $gameUnreadMessages--;
                }
            }

            $gamesInvites = ($data[0]['result'] == 'empty') ? '' : $gamesInvites;


            //busca as notificacoes avulsas que sao inseridas pelo sistema
            $notificacoes = $this->model->getData('notificacoes','a.*', array('usuario_id'=>$_SESSION['usuario_id'],'status' =>0));
            if($notificacoes[0]['result'] != 'empty') {
                foreach ($notificacoes as $notificacao) {
                    switch ($notificacao['posicao']) {
                        case 'mensagem':       
                            
                            break;
                        case 'jogos':       
                                $gamesInvites .= $this->html->link($notificacao['texto'].'<br><hr>','/action/notificacao/'.$notificacao['id']);
                                $gameUnreadMessages++;
                            break;
                        case 'jogadores':       
                            
                            break;
                    }
                }
            }
            if($gamesInvites != '') {
                $gamesInvites = $this->html->div($gamesInvites,array('id'=>'boxNotification'));     
            }
            $keys['gamesInvites'] = $gamesInvites;

            //atribui o balao vermelho com a quantidade de notificacoes pendentes
            $keys['gamesNotification'] = ($gameUnreadMessages != 0) ? $this->html->div($gameUnreadMessages,array('id'=>'messageNotification')) : '';

            return $keys;
        }

        /**
         * buscaRecreativos $filtrosfunction
         * @return array    
         * */
        public function buscaRecreativos($filtros='') {

            #monta os filtros
            $filters['mercado'] ='sim';
            $filters['tipo'] = $filtros['kind'];

            if($filtros['start_date'] != '') {
                $d = explode('/', $filtros['start_date']);
                $start = mktime(0,0,0,$d[1],$d[0],$d[2]);
                $filters['>tempo'] = $start;
            } 
            if($filtros['end_date'] != '') {
                $d = explode('/', $filtros['end_date']);
                $start = mktime(0,0,0,$d[1],$d[0],$d[2]);
                $filters['<tempo'] = $start;
            }  
            //getfriends list
            $friends_list = $this->getFriends('onlyids');

            
            if($filtros['onlyfriends'] == 'checked') {
                $filters['in criador_id'] = $friends_list;
            }

            #busca os recreativos
            $res = $this->model->getData('jogos','a.*', $filters,array('start'=>0,'limit'=>20),'a.tempo asc');
            foreach ($res as $recreativo) {
                $data = $this->html->span(date('d/m/Y',$recreativo['tempo']).' - '.$recreativo['horainicio'],array('class'=>'g14'));
                $local = $this->html->span('<br>Jogo recreativo em '.$recreativo['local'].'<br>',array('class'=>'o14')); 
                $ver = $this->html->link('Ver','/jogos/ver/'.$recreativo['id'],'','minibutton'); 
                $listaRecreativos .= $this->html->div($data.$local.$ver,array('id'=>'recreativo-'.$recreativo['id'],'class'=>"recreativoMarcado")); 
            }
            if($res[0]['result'] =='empty') {
                $listaRecreativos = 'Não foram encontrados jogos';
            }
            return $listaRecreativos;
        }     
       
        public function listaParticipantes($jogo_id,$status) {
            #busca os participantes
            $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo_id,'status' =>$status),'','a.ID DESC', 'inner join usuario as u on u.id = a.participante_id ')  ;
            foreach ($jogadores as $jogador) {
                $htmlParticipate = $this->avatar($jogador);
                $htmlParticipate .= $this->html->span($jogador['nome']);
                $listaParticipantes .= $this->html->div($htmlParticipate,array('id'=>'participante-'.$jogador['id'],'class' =>'participanteJogo')); 
            }
            if($jogadores[0]['result'] == 'empty') {
                $listaParticipantes = '<br> &nbsp;&nbsp;&nbsp;N&atilde;o existem jogadores pendentes';   
            }
            return $listaParticipantes; 
        }

        public function listaemcampo($jogo_id,$status) {
            #busca os participantes
            $x = 1;
            $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo_id,'status' =>$status),'','a.ID DESC', 'inner join usuario as u on u.id = a.participante_id ')  ;
            foreach ($jogadores as $jogador) {
                $htmlParticipate = $this->avatar($jogador);
                $listaParticipantes .= $this->html->li($htmlParticipate,array('id'=>'participante-'.$x,'class' =>'participanteEmCampo')); 
                $x++;
            }
            while ($x <= 16) {
                $selo = $this->html->div($x,array('class'=>'selo')); 
                $listaParticipantes .= $this->html->li($selo,array('id'=>'participante-'.$x,'class' =>'participanteEmCampo')); 
                $x++;
            }
            return $listaParticipantes; 
        }

        public function listaTime($time_id,$jogo_id ='' ) {
            #busca os participantes
            if($jogo_id != '') {
                $jogadores = $this->model->getData('equipe_participante','*', array('equipe_id' => $time_id,'in usuario_id' => "(select participante_id from jogo_participantes where jogo_id = $jogo_id)"),'','a.ID DESC', 'inner join usuario as u on u.id = a.usuario_id ')  ;
            } else {
                $jogadores = $this->model->getData('equipe_participante','*', array('equipe_id' => $time_id),'','a.ID DESC', 'inner join usuario as u on u.id = a.usuario_id ')  ;
            }
            
            $listaParticipantes = '';
            foreach ($jogadores as $jogador) {
                $htmlParticipate = $this->avatar($jogador);
                $htmlParticipate .= $this->html->span($jogador['nome']);
                $listaParticipantes .= $this->html->div($htmlParticipate,array('id'=>'participante-'.$jogador['id'],'class' =>'participanteJogo')); 
            }
            return $listaParticipantes; 
        }

        

         public function contaParticipantes($jogo_id,$status) {
            #busca os participantes
            $jogadores = $this->model->getData('jogo_participantes','*', array('jogo_id' => $jogo_id,'status' =>$status),'','a.ID DESC', 'inner join usuario as u on u.id = a.participante_id ')  ;
            $x = 0;
            foreach ($jogadores as $jogador) {
                $x++;
            }
            if($jogadores[0]['result'] == 'empty') {
                $x = 0;
            }
            return $x; 
        }

        public function adicionaFundamento($jogador,$fundamento) {
            #busca os dados do usuario

            $participante = $this->model->getData('usuario','a.*', array('id'=>$jogador));
            
            $qtd = $participante[0][$fundamento] + 1;

            //adiciona o fairplay ao perfil do usuario
            $this->model->alterData('usuario', array($fundamento => $qtd), array('id'=>$jogador));

        }


        /**
         * _actionprecadastra function
         * @return array
         * */
        public function precadastra($nome,$email) {
            
            // verifica se ja nao esta cadastrado se estiver devolve o id
            $data = $this->model->getData('usuario','a.*', array('email'=>$email));

            if($data[0]['result'] != 'empty') {
                $return =  $data[0]['id'];
            } else {
                $user['nome'] = $nome;
                $user['apelido'] = $nome;
                $user['email'] = $email;
                $user['tipo'] = 'jogador';
                $user['avatar'] = 'precadastrado.png';
                $user['precadastro'] = '1';
                $return =  $this->model->adddata('usuario',$user);

                $link = "http://futbooking.com/hotsite/start/?email=$email&nome=$nome";
                $mensagem = "Você foi convidado pelo <b>".$_SESSION['usuario']."</b> para participar da rede <a href=\"$link\" style=\"color:#f37d37;font-weigth:bold\">Futbooking.com</a>. <br/><br/>
                            O Futbooking é um espaço virtual para organizar partidas de futebol.<br/><br/>
                                Cadastrando-se no <a href=\"$link\" style=\"color:#f37d37;font-weigth:bold\">Futbooking.com</a>, você poderá:<br/>
                                <ul>
                                 <li>Organizar jogos recreativos (seja no campo, society ou salão) <br/></li>
                                 <li>Procurar locais para os jogos<br/></li>
                                 <li>Avaliar os jogadores <br/></li>
                                 <li>Montar times <br/></li>
                                 <li>Encontrar outras equipes para desafiar<br/></li>
                                 <li>Organizar campeonatos<br/></li>
                                 <li>E muito mais!<br/><br/></li>
                                 </ul>
                                Acesse agora <a href=\"$link\" style=\"color:#f37d37;font-weigth:bold\">Futbooking.com</a> e monte seu perfil. <br/><br/>
                                O pontapé inicial já foi dado.
                                <br><br>Segue o jogo...";
                
                $this->enviaEmail($_SESSION['usuario'].' convocou você para o Futbooking',$_SESSION['usuario'].' convocou você para o Futbooking',$mensagem,$return);

            }
            

            return $return;
        } 


        /**
         * _actionAmigosForaDoJogo function
         * @return array
         * */
        public function amigosForaDoJogo($jogo_id,$query = '',$nivel = '') {
         #   $filters['b.usuario_id']= $_SESSION['usuario_id'];
            $query = ($query != 'buscar jogador') ? $query : '';
            $filters['notin a.id'] = "(select participante_id from `jogo_participantes` where jogo_id = $jogo_id)";
            if($query != '') {
                $filters['like a.nome'] = $query;   
            }

            if($nivel == 'amigos') {
                $filters['in id'] = '(select amigo_id from amizades where usuario_id = '.$_SESSION['usuario_id'].')';
            }
            
            $filters['tipo'] = 'jogador';
            $filters['precadastro'] = 0;
            $amigos = $this->model->getData('usuario','a.*', $filters, '','a.ID DESC');

            foreach ($amigos as $amigo) {
                $keys['avatar'] =   $this->avatar($amigo);
                $keys['amigo']  =   $this->html->link($this->html->b(utf8_encode($amigo['nome'])),'/perfil/ver/'.$amigo['id']);
                $keys['amigo_id'] = $amigo['id'];
              
                $amigoHtml = $this->includeHTML('../view/jogos/snippets/convidarNoJogo.html');
                $amigoHtml = $this->applyKeys($amigoHtml,$keys);    

                $listaAmigos .= $this->html->div($amigoHtml,array('id'=>'amigo-'.$amigo['id'],'class'=>'amigosListAmigo')); 
            }
            if($amigos[0]['result'] == 'empty') {
                $listaAmigos = ' Todos os seus amigos já foram convidados para este jogo! :)'; 
            }
            return  $listaAmigos; 
        }     
        
        public function trataResultado($dados,$dadosTratados,$frase = 'Não foram encontrados reultados') {
            if($dados[0]['result'] == 'empty') {
                $retorno = $frase;
            } else {
                $retorno = $dadosTratados;
            }
            return $retorno;
        }   

        public function avatarTime($img,$class = 'avatarThumb') {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].$img)) {
                $avatar = $this->html->span($this->html->img($img),array('class'=>$class));     
            } else {
                $avatar = $this->html->span($this->html->img('/images/avatar_padrao_equipe.jpg'),array('class'=>$class)); 
            }
            return $avatar;

        }

        public function enviaEmail($assunto,$titulo,$texto,$usuario) {

            //busca o email do usuario
            $user = $this->model->getData('usuario','email', array('id'=>$usuario));

            $keys['titulo'] = $titulo;
            $keys['texto'] = nl2br($texto);

            $emailTPL = file_get_contents('../view/hotsite/email.html');
            $emailTPL = $this->applyKeys($emailTPL,$keys);   
            


            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
            $headers .= 'From: contato@footbooking.com.br<contato@footbooking.com.br>' . "\r\n";

            mail($user[0]['email'],$assunto,$emailTPL,$headers);
           
        }

        public function userTeams($user_id) {
            $filters['a.usuario_id'] = $user_id;
            $filters['or e.usuario_id'] = $user_id;
            $res = $this->model->getData('equipe_participante','distinct(a.equipe_id)',$filters,'','a.equipe_id DESC','inner join equipes as e on e.id = a.equipe_id' );
            foreach ($res as $time) {
                $return .= $time['equipe_id'].',';
            }
            return $return;
        }

        public function userGames($user_id) {
            $res = $this->model->getData('jogo_participantes','a.*', array('participante_id'=>$user_id));
            foreach ($res as $jogo) {
                $return .= $jogo['jogo_id'].',';
            }
            return $return;
        }

        public function avatar($userdata) {
            if($userdata['avatar']!='') {
                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/repository/upload/'.$userdata['avatar'])) {
                    $avatar = $this->html->link($this->html->div($this->html->img('/repository/upload/'.$userdata['avatar']),array('class'=>'avatarThumb')),'/perfil/ver/'.$userdata['id']);
                } else {
                     $avatar = $this->html->link($this->html->div($this->html->img('/images/avatar_padrao.jpg'),array('class'=>'avatarThumb')),'/perfil/ver/'.$userdata['id']);
                }
            } else {
                $avatar = $this->html->link($this->html->div($this->html->img('/images/avatar_padrao.jpg'),array('class'=>'avatarThumb')),'/perfil/ver/'.$userdata['id']);
            }
            return $avatar;
        }

        public function avatarEquipe($data) {

            if($data['escudo']!='') {
                if(file_exists($_SERVER['DOCUMENT_ROOT'].'/repository/upload/'.$data['escudo'])) {
                    $avatar = $this->html->link($this->html->div($this->html->img('/repository/upload/'.$data['escudo']),array('class'=>'avatarThumb')),'/equipes/ver/'.$data['id']);
                } else {
                     $avatar = $this->html->link($this->html->div($this->html->img('/images/avatar_padrao_equipe.jpg'),array('class'=>'avatarThumb')),'/equipes/ver/'.$data['id']);
                }
            } else {
                $avatar = $this->html->link($this->html->div($this->html->img('/images/avatar_padrao_equipe.jpg'),array('class'=>'avatarThumb')),'/equipes/ver/'.$data['id']);
            }
            return $avatar;
        }
        
        /**
         * Verifica se o usuario é amigo do usuario logado
         * */
        public function isFriend($friend_id) {
            $res = $this->model->countData('amizades',array('usuario_id'=>$_SESSION['usuario_id'],'amigo_id'=>$friend_id));
            return ($res == 1) ? true : false;
        }

        /**
         * Verifica se o usuario já foi convidado pelo usuario logado
         * */
        public function isInvited($friend_id) {
            $res = $this->model->countData('convites',array('remetente_id'=>$_SESSION['usuario_id'],'usuario_id'=>$friend_id,'tipo'=>'amizade','aceito'=>0 ));
            return ($res > 0) ? true : false;
        }
           

        /**
        * _actionbuscaLocal function
        * @return array
        * */
        public function buscaLocal($local,$endereco) {

          $res = $this->model->getData('locais','a.*', array('like name'=>$local));
          if($res[0]['result'] != 'empty' ) {
            $return = $res[0]['id'];
          } else {
            $return = $this->model->addData('locais',array('name'=>$local,'endereco'=>$endereco));
          }

          return $return;
        }     
            
        public function isTeamAdmin($equipe_id) {
            //verifica se foi esse usuario que criou o time
            $res = $this->model->countData('equipes', array('usuario_id'=>$_SESSION['usuario_id'],'id'=>$equipe_id));
            if($res == 1) {
                $return = true;
            } else {
                //se ele não é o criador verifica se esta entre os administradores da equipes
                $res = $this->model->countData('equipe_administrador', array('usuario_id'=>$_SESSION['usuario_id'],'id'=>$equipe_id));
                if($res == 1) {
                    $return = true;
                } else {
                    $return = false;
                }
            }
            return $return;
        }

        public function isTeamMember($equipe_id) {
            //verifica se esse usuario faz parte do time
            $res = $this->model->countData('equipe_participante', array('usuario_id'=>$_SESSION['usuario_id'],'equipe_id'=>$equipe_id));
            if($res == 1) {
                $return = true;
            } else {
                $return = false;
            }
        
            return $return;
        }

        public function isQuadraAdmin($quadra_id) {
            if(($_SESSION['tipo'] == 'quadra') && ($_SESSION['usuario_id'] == $quadra_id)) {
                return true;
            } else {
                return false;    
            }
        }

        public function getGameData($game_id) {
            return $this->model->getData('jogos','a.*,u.nome  ', array('a.id'=>$game_id),'','a.ID DESC','inner join usuario as u on u.id = a.criador_id' );
        }

        public function addNotification($usuario,$msg,$link,$tipo = 'jogos') {
          //adiciona a notificacao
          $data['usuario_id'] = $usuario;
          $data['texto'] = $msg;
          $data['link'] = $link;
          $data['posicao'] = $tipo;
          $this->model->addData('notificacoes',$data);

          return true;
        }

        public function getUserWidget($user_id) {
            $userdata = $this->getUserData($user_id);

            $content = $this->avatar($userdata);
            $content .= $this->html->link($userdata['nome'],'/perfil/ver/'.$user_id);
            $widget = $this->html->div($content,array('id'=>'userWidget'));

            return $widget;
        }

        public function curteQuadra($quadra_id) {
            if($this->model->countData('curtidas',array('usuario_id' => $_SESSION['usuario_id'],'quadra_id' => $quadra_id)) == 1) {
                return true;
            } else {
                return false;
            }
        } 

        public function getuserlikes() {
            $res = $this->model->getData('curtidas','a.*',array('usuario_id'=>$_SESSION['usuario_id']));
            if($res[0]['result'] == 'empty') {
                return '';
            } else {
                foreach ($res as $item) {
                    $quadras = $item['quadra_id'] .',';
                }
            }
            return $this->cutEnd($quadras,1);
        }


        public function inGame($user_id,$game_id){
            if($this->model->countData('jogo_participantes',array('jogo_id' => $game_id,'participante_id' => $user_id)) == 1){
                return true;
            } else {
                return false;
            }
        }

        public function addtoteam($equipe_id,$user_id) {
            $data['usuario_id'] = $user_id;
            $data['remetente_id'] = $_SESSION['usuario_id'];
            $data['tipo'] = 'equipe';
            $data['mensagem'] = $equipe_id;
            $this->model->addData('convites',$data);

            #envia mensagem por email
            $email = $this->loadModule('email');
            $mensagem = "Voc&ecirc; recebeu um convite para entrar em um time no Futbooking para aceitar acesse agora o seu perfil  </br></br>";
            $email->send('rafaelfranco@me.com','Você recebeu uma solicitação de time no Futbooking',$mensagem,'Time Footbooking','amizade@footbooking.com.br');

            

        }


}
?>
