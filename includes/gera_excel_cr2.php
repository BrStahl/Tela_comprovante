<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_name("covre_ti");
session_start();

require_once "../class/Dados.php";

require_once '../PHPExcel/PHPExcel.php';

$protocolo_id = $_GET['protocolo'];
//$sistema = $_GET['sistema'];
$dados = new Dados();
//if ($sistema == 2)
$relatorio_comprovante = $dados->exibeComprovanteEnvioDig($protocolo_id, 1);
//else {
//	$relatorio_comprovante = $dados->exibeComprovanteEnvioRodopar($protocolo_id);
//}

$objPHPExcel = new PHPExcel();

$objPHPExcel->getProperties()->setCreator("Covre")
	->setTitle("Relatório de Documentos");

$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setTitle('Dados');
$contador = 1;

$objPHPExcel->getActiveSheet()
	->setCellValue('A1', 'ITEM')
	->setCellValue('B1', 'TIPO')
	->setCellValue('C1', 'FILIAL')
	->setCellValue('D1', 'SERIE')
	->setCellValue('E1', 'DOC')
	->setCellValue('F1', 'EMISSAO')
	->setCellValue('G1', 'CLIENTE')
	->setCellValue('H1', 'RESSALVA')
	->setCellValue('I1', 'CHAVE');

while (odbc_fetch_row($relatorio_comprovante)) {
	$documento				= odbc_result($relatorio_comprovante, 1);

	$cont_a = $contador + 1;


	$data_emissao			= odbc_result($relatorio_comprovante, 2);
	$ponto_operacao			= odbc_result($relatorio_comprovante, 3);
	$cliente				= odbc_result($relatorio_comprovante, 4);
	$docto_transporte_id	= odbc_result($relatorio_comprovante, 5);
	$id						= odbc_result($relatorio_comprovante, 6);
	$checked				= odbc_result($relatorio_comprovante, 7);
	//$status_protocolo		= odbc_result($result,8);
	$qtde_ressalva			= odbc_result($relatorio_comprovante, 9);
	$tipo					= odbc_result($relatorio_comprovante, 10);
	$serie					= odbc_result($relatorio_comprovante, 11);
	$chave					= odbc_result($relatorio_comprovante, 13);
	/*
	$c_aux = $contador + 1;
	$objPHPExcel->getActiveSheet()
		->setCellValue('A' . $c_aux, $contador)
		->setCellValue('B' . $c_aux + 1, $tipo)
		->setCellValue('C' . $c_aux + 1, $ponto_operacao)
		->setCellValue('D' . $c_aux + 1, $serie)
		->setCellValue('E' . $c_aux + 1, $documento)
		->setCellValue('F' . $c_aux + 1, $data_emissao)
		->setCellValue('G' . $c_aux + 1, $cliente)
		->setCellValue('H' . $c_aux + 1, $qtde_ressalva)
		->setCellValue('I' . $c_aux + 1, $chave);*/

	$objPHPExcel->getActiveSheet()
		->setCellValue('A' . $cont_a, $contador)
		->setCellValue('B' . $cont_a, $tipo)
		->setCellValue('C' . $cont_a, $ponto_operacao)
		->setCellValue('D' . $cont_a, $serie)
		->setCellValue('E' . $cont_a, $documento)
		->setCellValue('F' . $cont_a, $data_emissao)
		->setCellValue('G' . $cont_a, $cliente)
		->setCellValue('H' . $cont_a, $qtde_ressalva)
		->setCellValue('I' . $cont_a, $chave)
	;
	echo $documento;
	//break;

	$contador++;
}


// Configurar cabeçalhos para download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="dados_excel.xls"');
header('Cache-Control: max-age=0');

// Criar o writer para Excel5 (formato .xls)
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

// Salvar para a saída do PHP
ob_end_clean(); // Limpar o buffer de saída
$objWriter->save('php://output');
exit;
