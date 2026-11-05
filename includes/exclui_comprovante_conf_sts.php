<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id 	= $_POST["id"];


	$gravacao = $dados->excluiComprovanteConfSts($id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;




?>

