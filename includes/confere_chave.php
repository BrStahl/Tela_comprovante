<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;


$chave 			= $_POST["chave"];
$protocolo_id	= $_POST["protocolo_id"];
$sistema = $_POST['sistema'];
$tipo_documento = $_POST["tp_doc"];


if ($sistema == 1) {
	$gravacao = $dados->gravaComprovanteRecebimento($chave, $protocolo_id);
} else {
	$gravacao = $dados->gravaComprovanteRecebimentoRodopar($chave, $protocolo_id, $tipo_documento);
}

$retorno = $gravacao->ret_gravacao;

print $retorno;
