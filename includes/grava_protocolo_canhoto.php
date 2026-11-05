<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDadosCanhoto.php";

$dados = new GravaDadosCanhoto;	


$id		 				= $_POST["id"];
$data	 				= $_POST["data"];
$cliente_grupo_id		= $_POST["cliente_grupo_id"];
$nome_cliente			= $_POST["nome_cliente"];
$cliente_id				= $_POST["cliente_id"];	
$grupo_cliente_id		= $_POST["grupo_cliente_id"];		
$responsavel_cliente	= $_POST["responsavel_cliente"];
$depto_cliente			= $_POST["depto_cliente"];			
$status_id				= $_POST["status_id"];


$data_prot = 
implode(preg_match("~\/~", $data) == 0 ? "/" : "-", 
array_reverse(explode(preg_match("~\/~", $data) == 0 ? "-" : "/", $data)));

	
	$gravacao = $dados->gravaProtocoloCanhoto($id, $data_prot, $cliente_grupo_id, $cliente_id, $grupo_cliente_id, $responsavel_cliente, $depto_cliente, $status_id);  
	
	$retorno = $gravacao->ret_gravacao;
	
	print $retorno;


?>

