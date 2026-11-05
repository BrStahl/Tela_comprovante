<?
include('../class/DadosCanhoto.php');


$filtra_grupo = new DadosCanhoto();

//$tipo_documento_id	= $_POST["tipo_documento_id"]; 


$dados = $filtra_grupo->exibeGrupoCliente();  


print $dados->relatorio;  

