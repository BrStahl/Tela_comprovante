<?php
session_name("covre_ti");
session_start();

require_once "../class/GravaDados.php";

$dados = new GravaDados;


$chave 			= $_POST["chave"];
$protocolo_id	= $_POST["protocolo_id"];


$gravacao = $dados->gravaComprovanteEnvioDigRodopar($chave, $protocolo_id);

print $gravacao;
exit();
$retorno = $gravacao->ret_gravacao;

print $retorno;
