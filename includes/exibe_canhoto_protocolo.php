<?php
session_name("covre_ti");
session_start();


require_once "../class/DadosCanhoto.php";

$dados = new DadosCanhoto;	


	$protocolo_id		 	= $_POST['protocolo_id'];

	
	$relatorio_canhoto = $dados->exibeCanhotoProtocolo($protocolo_id);  
	
	$rel_canhoto = $relatorio_canhoto->canhoto;
	
	print $rel_canhoto;



?>

