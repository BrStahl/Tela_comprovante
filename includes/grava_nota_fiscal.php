<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDadosCanhoto.php";

$dados = new GravaDadosCanhoto;	


$nota_fiscal	= $_POST["nota_fiscal"];
$protocolo_id	= $_POST["id"];


	
	$gravacao = $dados->gravaNotaFiscal($protocolo_id, $nota_fiscal);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

