<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$protocolo_id		 	= $_POST['protocolo_id'];

	$relatorio_comprovante = $dados->exibeComprovanteConferido($protocolo_id);  
	
	$rel_comprovante = $relatorio_comprovante->comprovante_conferido;
	
	print $rel_comprovante;



?>

