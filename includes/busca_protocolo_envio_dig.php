<?php
session_name("covre_ti");
session_start();


require_once "../class/Dados.php";

$dados = new Dados;	


	$protocolo_id		 	= $_POST['id'];

	$relatorio_protocolo = $dados->buscaProtocoloEnvioDig($protocolo_id);  
	
	$rel_protocolo = $relatorio_protocolo->protocolo;
	
	print $rel_protocolo;



?>

