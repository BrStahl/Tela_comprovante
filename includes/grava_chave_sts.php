<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;


$chave 			= $_POST["chave"];
$protocolo_id	= $_POST["protocolo_id"];
$sistema = $_POST['sistema'];
$tipo_documento = $_POST['tipo_documento'];
$ponto_operacao_id = $_POST['ponto_operacao_id']; // <- ADICIONAR

if ($sistema == 1) {
	$gravacao = $dados->gravaComprovanteEnvio($chave, $protocolo_id);
} else {
	//$gravacao = $dados->gravaComprovanteEnvioRodoparSts($chave, $protocolo_id, $tipo_documento);
	$gravacao = $dados->gravaComprovanteEnvioRodoparSts($chave, $protocolo_id, $tipo_documento, $ponto_operacao_id);

}
$retorno = $gravacao->ret_gravacao;

print $retorno;
