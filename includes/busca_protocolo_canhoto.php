<?php
session_name("covre_ti");
session_start();


require_once "../class/DadosCanhoto.php";

$dados = new DadosCanhoto;	


	$protocolo_id		 = $_POST['id'];

	$relatorio_protocolo = $dados->buscaProtocoloCanhoto($protocolo_id);  
	
	$rel_protocolo = $relatorio_protocolo->protocolo;
	
	print $rel_protocolo;



?>

