<?php
session_name("covre_ti");
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php");

$con = new ConexaoSCI;

echo "<h1>TESTE BÁSICO</h1>";

// Teste 1: Conexão funciona?
$result = $con->executar("SELECT GETDATE() AS AGORA");
if ($result && odbc_fetch_row($result)) {
    echo "✅ Conexão OK: " . odbc_result($result, 'AGORA') . "<br>";
} else {
    echo "❌ Conexão FALHOU<br>";
    die();
}

// Teste 2: Linked Server existe?
$result = $con->executar("SELECT * FROM sys.servers WHERE name = 'BDRODOPAR'");
if ($result && odbc_fetch_row($result)) {
    echo "✅ Linked Server OK<br>";
} else {
    echo "❌ Linked Server NÃO EXISTE<br>";
    die();
}

// Teste 3: OPENQUERY funciona?
$result = $con->executar("SELECT TOP 1 * FROM OPENQUERY([BDRODOPAR], 'SELECT TOP 1 * FROM db_visual_covre_teste..RODCON')");
if ($result && odbc_fetch_row($result)) {
    echo "✅ OPENQUERY funcionou!<br>";
    echo "Primeiro registro: " . odbc_result($result, 1) . "<br>";
} else {
    echo "❌ OPENQUERY FALHOU: " . odbc_errormsg() . "<br>";
}

echo "<h2>FIM DO TESTE</h2>";
?>