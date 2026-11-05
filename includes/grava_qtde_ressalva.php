<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$qtde		= $_POST["qtde"];
$id			= $_POST["id"];
$tela		= $_POST["tela"];


	
	$gravacao = $dados->gravaQtdeRessalva($qtde, $id, $tela);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

