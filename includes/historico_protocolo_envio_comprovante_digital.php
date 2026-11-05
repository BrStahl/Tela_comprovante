<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ERROR);

session_name("covre_ti");
session_start();

// POST, SESSION, GET E REQUEST

$id_protocolo = intval(trim($_REQUEST['id_protocolo']));
$logado    = $_SESSION["usuario_logado"];

// POST, SESSION, GET E REQUEST


include $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php"; //Classe de Controle SCI
require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/class/Autentica.php";

$autenticacao_sci = new Autentica();
$con = new ConexaoSCI();

$localItem = "../comprovante_entrega/envio_comprovante_dig.php";
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
$acesso  = $dados_autenticacao->permissao;

//$acesso = "permitido";
if ($acesso != "permitido") {
    print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";

    $dados_gravacao = $autenticacao_sci->redirecionaLogin();
    $local  = $dados_gravacao->local;

    print "<script language = 'JavaScript'>window.location='../$local';</script/>";

    die();
}

$SQL = "SELECT 
            ISNULL(LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.ID, null) AS 'codigo log',
            ISNULL(LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.OPERACAO, null) AS 'operacao realizada',
            ISNULL(FORMAT(LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.MOMENTO, 'dd/MM/yyyy HH:mm:ss'), null) AS 'data e hora',
            ISNULL(USUARIO.USUARIO, null) AS 'usuario sci',
            ISNULL(USUARIO.NOME, null) AS 'nome usuario'
        FROM
            dbGerenciamento..LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG JOIN
            dbGerenciamento..USUARIO ON USUARIO.ID = LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.ID_USUARIO
        WHERE 
            LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.ID_PROTOCOLO_ENVIO_COMPROVANTE_DIG = $id_protocolo
        ORDER BY LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG.ID ASC;";

$resultado = $con->select($SQL);

echo json_encode($resultado);
