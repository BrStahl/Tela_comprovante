<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;	


$ressalva		= $_POST["ressalva"];
$protocolo_id	= $_POST["protocolo_id"];
$tela			= $_POST["tela"];
$aux			= $_POST["aux"];

	
	$gravacao = $dados->gravaRessalvaTodos($ressalva, $protocolo_id, $tela, $aux);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

