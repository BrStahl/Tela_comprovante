<?php

session_name("covre_ti");
session_start();



include("../SCA/includes/conect_sqlserver.php");

$data_ini = $_GET["data_ini"];
$data_fim = $_GET["data_fim"];

$ip = $_SERVER['HTTP_HOST'];


$data_inicial = 
implode(preg_match("~\/~", $data_ini) == 0 ? "/" : "-", 
array_reverse(explode(preg_match("~\/~", $data_ini) == 0 ? "-" : "/", $data_ini)));

$data_final = 
implode(preg_match("~\/~", $data_fim) == 0 ? "/" : "-", 
array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim)));


if ($ip != '192.168.10.148')
	$ip = '192.168.10.126';
	
	
	shell_exec("wget -O /tmp/edi.php http://$ip/comprovante_entrega/edi.php?di=$data_inicial&df=$data_final");
	
	print "wget -O /tmp/edi.php http://$ip/comprovante_entrega/edi.php?di=$data_inicial&df=$data_final";

	print 'FIM DA EXECUCAO';

?>
