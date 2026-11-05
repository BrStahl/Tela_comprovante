<?php
session_name("covre_ti");
session_start();


require_once "../class/GravaDados.php";

$dados = new GravaDados;


$chave 			= $_POST["chave"];
$protocolo_id	= $_POST["protocolo_id"];
$sistema = $_POST['sistema'];
$tipo_documento = $_POST['tipo_documento'];

if ($sistema == 1) {
	$gravacao = $dados->gravaComprovanteEnvioDig($chave, $protocolo_id);
} else {

	$gravacao = $dados->gravaComprovanteEnvioDigRodopar($chave, $protocolo_id, $tipo_documento);
}
$retorno = $gravacao->ret_gravacao;

print $retorno;
