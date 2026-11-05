<?php
session_name("covre_ti");
session_start();


require_once "../class/DadosCanhoto.php";

$dados = new DadosCanhoto;	


	$protocolo_id		 	= $_POST['protocolo_id'];

	
	$relatorio_canhoto = $dados->exibeNotaFiscal($protocolo_id);  
	
	print $rel_canhoto;



?>

