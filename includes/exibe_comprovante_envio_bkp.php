<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$protocolo_id		 	= $_POST['protocolo_id'];
	$tela				 	= $_POST['tela'];
	
	if ($tela == 1)
		$relatorio_comprovante = $dados->exibeComprovanteEnvio($protocolo_id);  
	else
		$relatorio_comprovante = $dados->exibeComprovanteEnvioDig($protocolo_id);  
	
	$rel_comprovante = $relatorio_comprovante->comprovante;
	
	print $rel_comprovante;



?>

