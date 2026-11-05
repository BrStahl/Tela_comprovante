<?php

session_name("covre_ti");
session_start();

require_once "../../class/consultaProtocolosDigitalizacao.php";


$action = $_GET['action'];

switch ($action) {
    case 'busca-protocolos':
        echo json_encode(listarProtocolosFinalizados());
        exit;
    default:
        die("ERRO");
}
