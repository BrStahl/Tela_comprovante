<?

include('../class/DadosCanhoto.php');


$filtra_canhoto = new DadosCanhoto();

$nota_fiscal		= $_POST["nota_fiscal"]; 
$cliente_grupo_id	= $_POST["cliente_grupo_id"];
$cliente_id			= $_POST["cliente_id"];
$grupo_cliente_id	= $_POST["grupo_cliente_id"];
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
	$dados_datas = $filtra_canhoto->comparaDatas($data_inicial,$data_final);  
}


if ($dados_datas != 'maior')
{
	
	$dados = $filtra_canhoto->pesquisaCanhotoPendente($nota_fiscal,$cliente_grupo_id,$cliente_id,$grupo_cliente_id,$status_id,$data_inicial,$data_final);  
	
	
	print $dados->relatorio;  

}
else
	print 'data_ini_maior';





