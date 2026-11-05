<?
include('../class/Dados.php');


$filtra_tabelas = new Dados();

$num_protocolo		= $_POST["num_protocolo"];
$tipo_documento_id	= $_POST["tipo_documento_id"];
$num_documento		= $_POST["num_documento"];
$status_id			= $_POST["status_id"];
$data_ini			= $_POST["data_ini"];
$data_fim			= $_POST["data_fim"];
$chave			= $_POST["chave"];
$usuario			= $_POST["usuario"];

$data_inicial =
	implode(
		preg_match("~\/~", $data_ini) == 0 ? "/" : "-",
		array_reverse(explode(preg_match("~\/~", $data_ini) == 0 ? "-" : "/", $data_ini))
	);

$data_final =
	implode(
		preg_match("~\/~", $data_fim) == 0 ? "/" : "-",
		array_reverse(explode(preg_match("~\/~", $data_fim) == 0 ? "-" : "/", $data_fim))
	);


if (($data_ini != '') && ($data_fim != '')) {
	$dados_datas = $filtra_tabelas->comparaDatas($data_inicial, $data_final);
}


if ($dados_datas != 'maior') {

	$dados = $filtra_tabelas->pesquisaProtocoloDig($num_protocolo, $tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final, $chave, $usuario);


	print $dados->relatorio;
} else
	print 'data_ini_maior';
