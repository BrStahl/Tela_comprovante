<?
include('../class/Dados.php');


$filtra_tabelas = new Dados();

$tipo_documento_id    = $_POST["tipo_documento_id"];
$sistema = $_POST['sistema'];


$dados = $filtra_tabelas->exibePo($tipo_documento_id, $sistema);


print $dados->relatorio;
