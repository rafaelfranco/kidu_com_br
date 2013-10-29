<?php
	/**
	 * Project: SIMPLE PHP - Framework 
	 * 
	 * @copyright Rafael Franco  www.rfranco.org
	 * @author Rafael Franco <rafaelfranco@me.com>
	 */

	/**
	 * test module, this  is a module tho create and show automated tests 
	 * Started at 2012 june 29
	 * @package tests
	 * @author Rafael Franco
	 **/
	class tests
	{
		private $controler;
		private $action;
		private $sp;

		public function __construct($controler,$action) 
		{
			$this->sp = new simplePHP();	

			$this->controler = $controler;
			$this->action = $action;


		}
		
		/**
		 * loadTests function
		 * @return array
		 * */
		public function loadTests() {
			#load test file
			$test = file_get_contents('../tests/'.$this->controler.'/'.$this->action.'.test');
			
			#load xml module
			$xml = $this->sp->loadModule('xml');
			
			$testData = $xml->xml2array($test);
			foreach($testData['root']['test'] as $test) {
				$combo[$test['name']['value']] = $test['name']['value'];
				$js[] = $this->makeTests($test); 
			}

			/**TODO**
			Criar a rotina que exibe o combo de testes e o js que executa os testes
			**/
			$html = $this->sp->loadModule('html');
			$testList = $html->select(true,$combo,'tests');
			
			$tests = $testList;
			$tests .= $html->input('button','Executar Teste',array('id'=>'makeTests'));

			$tests .= $this->printJs($js);

			return $tests;	

		} 	
		    

	     /**
	      * makeTests function
	      * @return array
	      * */
	     public function makeTests($test) {
	     	
	     	$js = "case '".$test['name']['value']."' : ";
	     	
	     	foreach ($test['actions']['action'] as $action) {
	    		$act = $action['attr'];
	     		switch ($act['name']) {
	     			case 'add':
	     				$js .= "$('#".$act['target']."').attr('value','".$act['value']."');";
	     				break;
     				case 'call':
	     				$js .= $act['target']."();";
	     				break;
	     			case 'select' :
	     				$js .= "$(\"#".$act['target']." option[value='".$act['value']."']\").attr(\"selected\", true);";
	     				break;
	     			case 'check':
	     				$js .= "$('#".$act['target']."').attr('checked',true);";
	     				break;
	     			case 'set':
	     				$js .= "$('#".$act['target']."').attr('".$act['key']."','".$act['value']."');";
	     				break;		
	     			default:
	     				# code...
	     				break;
	     		}
			}	
	     	$js .= "break; "; 
	     	
	     	return $js;
	     } 	


	     /**
	      * printJs function
	      * @return array
	      * */
	     public function printJs($js) {
	     	$testList = "";
	     	foreach ($js as $test) {
	     		$testList .= $test;
	     	}

	     	$js = "<script>";
	     	$js .= "$(document).ready(function() {
	     				$('#makeTests').click(function() {
	     					doTest($('#tests').val());
	     				});
	     			});
	     			";
	     	$js .= "function doTest(selectedTest) {
	     				switch(selectedTest)
						{
						$testList	
						default:
						  alert('Teste '+selectedTest+' nao definido');
						}
	     			}
	     	";		
	     	$js .= "</script>";
	       	
	       	return $js;
	     } 	
	          
	          	     

	}
?>