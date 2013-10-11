<?php
   	/**
	 * Project: SIMPLE PHP - Framework 
	 * 
	 * @copyright RFTI  www.rfti.com.br
	 * @author Rafael Franco <rafael@rfti.com.br>
	 */

	/**
	 * util module
	 *
	 * @package util
	 * @author Rafael Franco
	 **/
	class util 
	{
		public function __construct() 
		{

		} 
		
		public function cutEnd($txt,$qtd) {
			return substr($txt,0,strlen($txt)-$qtd);
		}
		
		public function days($days = 31) {
			for($x=1;$x<=$days;$x++) {
				$return[$x] = $x;
			}
			return $return;
		}

		public function hours() {
			for($x=0;$x<=23;$x++) {
				$return[$x.':00'] = $x.':00';
				$return[$x.':30'] = $x.':30';
			}
			return $return;
		}
		
		public function mounths() {
			
			$return['01'] = '01';
			$return['02'] = '02';
			$return['03'] = '03';
			$return['04'] = '04';
			$return['05'] = '05';
			$return['06'] = '06';
			$return['07'] = '07';
			$return['08'] = '08';
			$return['09'] = '09';
			$return['10'] = '10';
			$return['11'] = '11';
			$return['12'] = '12';
			
			return $return;
		}

		public function states() {
			 $estado['Acre'] = utf8_decode('Acre');
			 $estado['Alagoas'] = utf8_decode('Alagoas');
			 $estado['Amapa'] = utf8_decode('Amapa');
			 $estado['Amazonas'] = utf8_decode('Amazonas');
			 $estado['Bahia'] = utf8_decode('Bahia');
			 $estado['Ceará'] = utf8_decode('Ceará');
			 $estado['Distrito Federal'] = utf8_decode('Distrito Federal');
			 $estado['Espírito Santo'] = utf8_decode('Espírito Santo');
			 $estado['Goiás'] = utf8_decode('Goiás');
			 $estado['Maranhão'] = utf8_decode('Maranhão');
			 $estado['Mato Grosso'] = utf8_decode('Mato Grosso');
			 $estado['Mato Grosso do Sul'] = utf8_decode('Mato Grosso do Sul');
			 $estado['Minas Gerais'] = utf8_decode('Minas Gerais');
			 $estado['Pará'] = utf8_decode('Pará');
			 $estado['Paraíba'] = utf8_decode('Paraíba');
			 $estado['Paraná'] = utf8_decode('Paraná');
			 $estado['Pernambuco'] = utf8_decode('Pernambuco');
			 $estado['Piauí'] = utf8_decode('Piauí');
			 $estado['Rio de Janeiro'] = utf8_decode('Rio de Janeiro');
			 $estado['Rio Grande do Norte'] = utf8_decode('Rio Grande do Norte');
			 $estado['Rio Grande do Sul'] = utf8_decode('Rio Grande do Sul');
			 $estado['Rondônia'] = utf8_decode('Rondônia');
			 $estado['Roraima'] = utf8_decode('Roraima');
			 $estado['Santa Catarina'] = utf8_decode('Santa Catarina');
			 $estado['São Paulo'] = utf8_decode('São Paulo');
			 $estado['Sergipe'] = utf8_decode('Sergipe');
			 $estado['Tocantins'] = utf8_decode('Tocantins');
			return $estado;
		}
		public function ufs() {
			 $estado['AC'] = utf8_decode('AC');
			 $estado['AL'] = utf8_decode('AL');
			 $estado['AP'] = utf8_decode('AP');
			 $estado['AM'] = utf8_decode('AM');
			 $estado['BA'] = utf8_decode('BA');
			 $estado['CE'] = utf8_decode('CE');
			 $estado['DF'] = utf8_decode('DF');
			 $estado['ES'] = utf8_decode('ES');
			 $estado['GO'] = utf8_decode('GO');
			 $estado['MA'] = utf8_decode('MA');
			 $estado['MT'] = utf8_decode('MT');
			 $estado['MS'] = utf8_decode('MS');
			 $estado['MG'] = utf8_decode('MG');
			 $estado['PA'] = utf8_decode('PA');
			 $estado['PB'] = utf8_decode('PB');
			 $estado['PA'] = utf8_decode('PA');
			 $estado['PB'] = utf8_decode('PB');
			 $estado['PI'] = utf8_decode('PI');
			 $estado['RJ'] = utf8_decode('RJ');
			 $estado['RN'] = utf8_decode('RN');
			 $estado['RS'] = utf8_decode('RS');
			 $estado['RO'] = utf8_decode('RO');
			 $estado['RR'] = utf8_decode('RR');
			 $estado['SC'] = utf8_decode('SC');
			 $estado['SP'] = utf8_decode('SP');
			 $estado['SE'] = utf8_decode('SE');
			 $estado['TO'] = utf8_decode('TO');
			return $estado;
		}
		public function clubs() {
			$clube['Atlético-MG'] = utf8_decode('Atlético-MG');
			$clube['Atlético-PR'] = utf8_decode('Atlético-PR');
			$clube['Bahia'] = utf8_decode('Bahia');
			$clube['Botafogo'] = utf8_decode('Botafogo');
			$clube['Corinthians'] = utf8_decode('Corinthians');
			$clube['Coritiba'] = utf8_decode('Coritiba');
			$clube['Criciúma'] = utf8_decode('Criciúma');
			$clube['Cruzeiro'] = utf8_decode('Cruzeiro');
			$clube['Flamengo'] = utf8_decode('Flamengo');
			$clube['Fluminense'] = utf8_decode('Fluminense');
			$clube['Goiás'] = utf8_decode('Goiás');
			$clube['Grêmio'] = utf8_decode('Grêmio');
			$clube['Internacional'] = utf8_decode('Internacional');
			$clube['Náutico'] = utf8_decode('Náutico');
			$clube['Ponte Preta'] = utf8_decode('Ponte Preta');
			$clube['Portuguesa'] = utf8_decode('Portuguesa');
			$clube['Santos'] = utf8_decode('Santos');
			$clube['São Paulo'] = utf8_decode('São Paulo');
			$clube['Vasco'] = utf8_decode('Vasco');
			$clube['Vitória'] = utf8_decode('Vitória');
			$clube['ABC'] = utf8_decode('ABC');
			$clube['América-MG'] = utf8_decode('América-MG');
			$clube['América-RN'] = utf8_decode('América-RN');
			$clube['ASA'] = utf8_decode('ASA');
			$clube['Atlético-GO'] = utf8_decode('Atlético-GO');
			$clube['Avaí'] = utf8_decode('Avaí');
			$clube['Boa Esporte'] = utf8_decode('Boa Esporte');
			$clube['Bragantino'] = utf8_decode('Bragantino');
			$clube['Ceará'] = utf8_decode('Ceará');
			$clube['Chapecoense'] = utf8_decode('Chapecoense');
			$clube['Figueirense'] = utf8_decode('Figueirense');
			$clube['Guaratinguetá'] = utf8_decode('Guaratinguetá');
			$clube['Icasa'] = utf8_decode('Icasa');
			$clube['Joinville'] = utf8_decode('Joinville');
			$clube['Oeste'] = utf8_decode('Oeste');
			$clube['Palmeiras'] = utf8_decode('Palmeiras');
			$clube['Paraná'] = utf8_decode('Paraná');
			$clube['Paysandu'] = utf8_decode('Paysandu');
			$clube['São Caetano'] = utf8_decode('São Caetano');
			$clube['Sport'] = utf8_decode('Sport');
			ksort($clube);
			return $clube;
		}
		public function years($start,$end) {
			while($start <= $end) {
				$return[$start] = $start;
				$start++;
			}
			return $return;
		}

		public function getCurrentUrl() {
			return 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		}
		
		public function getAppUrl() {
			return 'http://'.$_SERVER['HTTP_HOST'].'/';
		}   
		
		public function utf8decodeArray($array) {
			foreach($array as $key => $value) {
				$return[$key] = utf8_decode($value);
			}
			return $return;
		} 
		public function utf8encodeArray($array) {
			foreach($array as $key => $value) {
				$return[$key] = utf8_encode($value);
			}
			return $return;
		}
		public function error($msg='') {
			$msg = utf8_decode($msg);
			echo "<script>alert('$msg');history.back(-1);</script>";
			exit;
		}

		public function success($msg='',$url = '') {
			$msg = utf8_decode($msg);
			echo "<script>alert('$msg');
			window.location='$url'</script>";
			exit;
		}

		public function noresult($data,$message = 'Não foram encontrados resultados') {
			if($data[0]['result'] == 'empty') {
				echo $message;
				exit;
			}
		}
		              
	}
	
	#developer functions
	function pr($data) 
	{
		echo '<pre>';
		print_r($data);
	} 
	function pre($data) 
	{
		pr($data);
		exit;
	}
	function reais($value) {
		return 'R$ '.number_format($value,2,',','.');
	}
	
	function idade($birthday){
		list($year,$month,$day) = explode("-",$birthday);
		$year_diff  = date("Y") - $year;
		$month_diff = date("m") - $month;
		$day_diff   = date("d") - $day;
		if ($day_diff < 0 || $month_diff < 0)
		$year_diff--;
		return $year_diff;
	}
	
?>
