<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDadosCanhoto.php";

$dados = new GravaDadosCanhoto;	


$values	 				= $_POST["values"];

	
	$gravacao = $dados->gravaProtocoloCanhotoPendente($values);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

