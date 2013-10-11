<?php

/**
 * Project: Inter.net
 * 
 * @copyright Inter.net  www.br.inter.net
 * @author Rafael Franco <rfranco@team.br.inter.net>
 */
/**
 *  teste class
 *	Class to automated tests
 * @package admin
 * @author Rafael Franco
 * */
class teste extends simplePHP {

        public function __construct() {    
        }

        /**
         * _actionCriaconta function
         * @return html
         * */
        public function _actionCriaconta() {

        	/**
        	*930;"Inter Speedy on - Trimestral"
    			*931;"Inter Speedy on - Semestral"
    			*932;"Inter Speedy on - Anual"
    			**/
        	$testData['CodPlano'] = '930-5';
	  		  $testData['NomeCliente'] = 'Rafael Marcos de Sousa Franco';
      		$testData['Sexo'] = 'M';
      		$testData['DataNascimento'] = '23/07/1982';
      		$testData['CodProfissao'] = '27';
      		$testData['Profissao'] = '';
      		$testData['CPF'] = '288.479.872-29';
      		$testData['CEP'] = '05265040';
      		$testData['Bairro'] = 'Jardim Jaragua';
      		$testData['Cidade'] = 'Sao Paulo';
      		$testData['Estado'] = 'SP';
      		$testData['Endereco'] = 'Rua Francisco Bellazzi';
      		$testData['Numero'] = '120';
      		$testData['Complemento'] = 'Casa 81';
      		$testData['DDDResidencial'] = '11';
      		$testData['TelefoneResidencial'] = '39164002';
      		$testData['DDDCelular'] = '11';
      		$testData['TelefoneCelular'] = '94623779';
      		$testData['DDDComercial'] = '11';
      		$testData['TelefoneComercial'] = '35233312';
      		$testData['EmailAlternativo'] = 'rafaelfranco@me.com';
      		$testData['Login'] = 'rfrancotdd2';
      		$testData['Senha'] = 'oXP3mmK4etHkLgaa';
      		$testData['TipoPagamento'] = '2';
      		$testData['BandeiraCartao'] = '1';
      		$testData['NumeroCartao'] = '4005400440044004';
      		$testData['CodigoSegurancaCartao'] = '333';
      		$testData['ValidadeCartao'] = '12/14';
      		$testData['BinCartao'] = '';
      		$testData['Banco'] = '';
      		$testData['AgenciaBanco'] = '';
      		$testData['ContaBanco'] = '';
      		$testData['CodigoFuncionario'] = '0';
      		$testData['CodigoOrigem'] = '497';
     		
     		  

     		  $cadastro = $this->loadModule('cadastro','',true);

        	$res = $cadastro->_actionCriaconta($testData);

        	pre($res);
        } 

        public function _actionValidalogin() {

     		   $data['VerificaLogin'] = array('login','rfranco');

     		   $cadastro = $this->loadModule('cadastro','',true);
     		
        	 $res = $cadastro->_actionValidalogin($data);

        	 #pre($res);

           echo date('H:i',1341435049);
        } 

        public function _actionGetxmlmoip() {
          $moip = $this->loadModule('moip','',true);


          
          $testData['CodPlano'] = '930-5';
          $testData['NomeCliente'] = 'Rafael Marcos de Sousa Franco';
          $testData['Sexo'] = 'M';
          $testData['DataNascimento'] = '23/07/1982';
          $testData['CodProfissao'] = '27';
          $testData['Profissao'] = '';
          $testData['CPF'] = '288.479.872-29';
          $testData['CEP'] = '05265040';
          $testData['Bairro'] = 'Jardim Jaragua';
          $testData['Cidade'] = 'Sao Paulo';
          $testData['Estado'] = 'SP';
          $testData['Endereco'] = 'Rua Francisco Bellazzi';
          $testData['Numero'] = '120';
          $testData['Complemento'] = 'Casa 81';
          $testData['DDDResidencial'] = '11';
          $testData['TelefoneResidencial'] = '39164002';
          $testData['DDDCelular'] = '11';
          $testData['TelefoneCelular'] = '94623779';
          $testData['DDDComercial'] = '11';
          $testData['TelefoneComercial'] = '35233312';
          $testData['EmailAlternativo'] = 'rafaelfranco@me.com';
          $testData['Login'] = 'rfrancotdd2';
          $testData['Senha'] = 'oXP3mmK4etHkLgaa';
          $testData['TipoPagamento'] = '2';
          $testData['BandeiraCartao'] = '1';
          $testData['NumeroCartao'] = '4005400440044004';
          $testData['CodigoSegurancaCartao'] = '333';
          $testData['ValidadeCartao'] = '12/14';
          $testData['BinCartao'] = '';
          $testData['Banco'] = '';
          $testData['AgenciaBanco'] = '';
          $testData['ContaBanco'] = '';
          $testData['CodigoFuncionario'] = '0';
          $testData['CodigoOrigem'] = '497';
          
          $testData['periodicidade'] = 'Trimestral';
          $testData['valor'] = '47.90';    


          $testData['CobrancaID'] = '6636754';
          $testData['ClienteID'] = '1121076';


          $xml = $moip->_actionGetxml($testData);

          return $xml;
        }

        public function _actionGettokenmoip() {

            $moip = $this->loadModule('moip','',true);


          
          $testData['CodPlano'] = '930-5';
          $testData['NomeCliente'] = 'Rafael Marcos de Sousa Franco';
          $testData['Sexo'] = 'M';
          $testData['DataNascimento'] = '23/07/1982';
          $testData['CodProfissao'] = '27';
          $testData['Profissao'] = '';
          $testData['CPF'] = '288.479.872-29';
          $testData['CEP'] = '05265040';
          $testData['Bairro'] = 'Jardim Jaragua';
          $testData['Cidade'] = 'Sao Paulo';
          $testData['Estado'] = 'SP';
          $testData['Endereco'] = 'Rua Francisco Bellazzi';
          $testData['Numero'] = '120';
          $testData['Complemento'] = 'Casa 81';
          $testData['DDDResidencial'] = '11';
          $testData['TelefoneResidencial'] = '39164002';
          $testData['DDDCelular'] = '11';
          $testData['TelefoneCelular'] = '94623779';
          $testData['DDDComercial'] = '11';
          $testData['TelefoneComercial'] = '35233312';
          $testData['EmailAlternativo'] = 'rafaelfranco@me.com';
          $testData['Login'] = 'rfrancotdd2';
          $testData['Senha'] = 'oXP3mmK4etHkLgaa';
          $testData['TipoPagamento'] = '2';
          $testData['BandeiraCartao'] = '1';
          $testData['NumeroCartao'] = '4005400440044004';
          $testData['CodigoSegurancaCartao'] = '333';
          $testData['ValidadeCartao'] = '12/14';
          $testData['BinCartao'] = '';
          $testData['Banco'] = '';
          $testData['AgenciaBanco'] = '';
          $testData['ContaBanco'] = '';
          $testData['CodigoFuncionario'] = '0';
          $testData['CodigoOrigem'] = '497';
          
          $testData['periodicidade'] = 'Trimestral';
          $testData['valor'] = '47.90';    


          $testData['CobrancaID'] = '6636754-3';
          $testData['ClienteID'] = '1121076';


          $xml = $moip->_actionGetxml($testData);

          $res = $moip->_actionGettoken($xml);
            
          pre($res);

        }








}