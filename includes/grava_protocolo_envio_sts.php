<?php
session_name("covre_ti");
session_start();

include("../../SCA/includes/conect_sqlserver.php");
require_once "../class/GravaDados.php";

$dados = new GravaDados;


$id		 			= $_POST["id"];
$ponto_operacao_id 	= $_POST["ponto_operacao_id"];
$tipo_documento_id	= $_POST["tipo_documento_id"];
$status_id			= $_POST["status_id"];
$sistema			= $_POST["sistema"];



$gravacao = $dados->gravaProtocoloEnvioSts($id, $ponto_operacao_id, $tipo_documento_id, $status_id, $sistema);

$retorno = $gravacao->ret_gravacao;

print $retorno;
