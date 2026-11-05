<?
/*
ini_set('display_errors',1);
ini_set('display_startup_erros',1);
error_reporting(E_ALL);
*/
include('../class/Dados.php');

$filtra_relatorio = new Dados();

$ponto_operacao_id	= $_POST["ponto_operacao_id"]; 
$tipo_documento_id	= $_POST["tipo_documento_id"]; 
$num_documento		= $_POST["num_documento"]; 
$status_id			= $_POST["status_id"]; 
$data_ini			= $_POST["data_ini"]; 
$data_fim			= $_POST["data_fim"]; 


$data_inicial = 
implode(preg_match("~\/~", $data_ini) == 0 ? "/" : "-", 
array_reverse(explode(preg_match("~\/~", $data_ini) == 0 ? "-" : "/", $data_ini)));

$data_final = 
implode(preg_match("~\/~", $data_fim) == 0 ? "/" : "-", 
array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim)));



if (($data_ini != '') && ($data_fim != ''))
{
	$dados_datas = $filtra_relatorio->comparaDatas($data_inicial,$data_final);  
}


if ($dados_datas != 'maior')
{
	
	$dados = $filtra_relatorio->exibeRelatorio($ponto_operacao_id,$tipo_documento_id,$num_documento,$status_id,$data_inicial,$data_final);  
	
	
	print $dados->relatorio;   
}
else
	print 'data_ini_maior';
