<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$id 	= $_POST["id"];
$tela 	= $_POST["tela"];


	$gravacao = $dados->excluiComprovante($id,$tela);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;




?>

