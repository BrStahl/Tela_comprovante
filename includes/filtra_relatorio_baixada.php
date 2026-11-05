<?


include('../class/Dados.php');

$filtra_relatorio_baixada = new Dados();

$sistema = '2';  // Rodopar
$tipo_documento_id = 'CTE';  // CTE

// VALORES DO FORMULÁRIO (podem variar)
$num_documento = isset($_POST["num_documento"]) ? $_POST["num_documento"] : '';
$status_id = isset($_POST["status_id"]) ? $_POST["status_id"] : '';
$data_ini = isset($_POST["data_ini"]) ? $_POST["data_ini"] : '';
$data_fim = isset($_POST["data_fim"]) ? $_POST["data_fim"] : '';

// VALORES REMOVIDOS (não usados mais, enviados vazios)
$ponto_operacao_id = '';
$po_rodo = '';

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
	$dados_datas = $filtra_relatorio_baixada->comparaDatas($data_inicial, $data_final);
}


if ($dados_datas != 'maior') {

	$dados = $filtra_relatorio_baixada->exibeRelatorioBaixada(
		$ponto_operacao_id, 
		$tipo_documento_id, 
		$num_documento, 
		$status_id, 
		$data_inicial, 
		$data_final, 
		$sistema, 
		$po_rodo);

	//print $dados;
	print $dados->relatorio;


// DEBUG
/*echo "<div style='background: yellow; padding: 10px; margin: 10px; border: 2px solid orange;'>";
echo "🔍 DEBUG: Sistema = " . $sistema . "<br>";
echo "🔍 Data inicial = " . $data_inicial . "<br>";
echo "🔍 Registros retornados = " . (isset($dados->relatorio) ? "✅ OK" : "❌ VAZIO") . "<br>";
echo "</div>";

print $dados->relatorio; */




} else
	print 'data_ini_maior';
