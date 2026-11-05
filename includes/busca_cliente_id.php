<?
include('../class/DadosCanhoto.php');

$busca_cliente = new DadosCanhoto();



$cliente	 	 	= $_POST['cliente'];


$dados = $busca_cliente->buscaCliente($cliente);  

$rel_dados = $dados->resultado;   
	
print $rel_dados;

