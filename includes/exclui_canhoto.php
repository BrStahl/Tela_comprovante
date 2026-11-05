<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDadosCanhoto.php";

$dados = new GravaDadosCanhoto;	


$id 	= $_POST["id"];


	$gravacao = $dados->excluiCanhoto($id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;




?>

