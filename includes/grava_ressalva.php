<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$ressalva	= $_POST["ressalva"];
$id			= $_POST["id"];
$tela		= $_POST["tela"];


	
	$gravacao = $dados->gravaRessalva($ressalva, $id, $tela);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

