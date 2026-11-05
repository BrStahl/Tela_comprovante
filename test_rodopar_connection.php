<?php
// ====================================================================
// 🔍 ARQUIVO DE TESTE - test_rodopar_connection.php
// ====================================================================

// 🌐 COMO ACESSAR: http://192.168.10.192/comprovante_entrega/test_rodopar_connection.php
// ====================================================================

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_name("covre_ti");
session_start();

include($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php");

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'></head><body>";
echo "<h1>🔍 TESTE DE CONEXÃO RODOPAR</h1>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<hr>";

$con = new ConexaoSCI;

// ====================================================================
// TESTE 1: Conexão SQL básica
// ====================================================================
echo "<div style='background: #e3f2fd; padding: 15px; margin: 10px; border-left: 5px solid #2196F3;'>";
echo "<h2>✅ TESTE 1: Conexão SQL Básica</h2>";
try {
    $query_test = "SELECT GETDATE() AS DATA_SERVIDOR, @@VERSION AS VERSAO";
    $result = $con->executar($query_test);
    
    if ($result && odbc_fetch_row($result)) {
        echo "✅ <strong>SUCESSO!</strong><br>";
        echo "📅 Data do servidor: " . odbc_result($result, 'DATA_SERVIDOR') . "<br>";
        echo "💾 Versão SQL: " . substr(odbc_result($result, 'VERSAO'), 0, 100) . "...<br>";
    } else {
        echo "❌ <strong>FALHOU</strong><br>";
        echo "Erro: " . odbc_errormsg() . "<br>";
        die("</div></body></html>");
    }
} catch (Exception $e) {
    echo "❌ <strong>EXCEÇÃO:</strong> " . $e->getMessage() . "<br>";
    die("</div></body></html>");
}
echo "</div>";

// ====================================================================
// TESTE 2: Linked Server existe?
// ====================================================================
echo "<div style='background: #fff3e0; padding: 15px; margin: 10px; border-left: 5px solid #FF9800;'>";
echo "<h2>✅ TESTE 2: Linked Server BDRODOPAR</h2>";
try {
    $query_linked = "SELECT name, provider, data_source FROM sys.servers WHERE name = 'BDRODOPAR'";
    $result = $con->executar($query_linked);
    
    if ($result && odbc_fetch_row($result)) {
        echo "✅ <strong>Linked Server ENCONTRADO!</strong><br>";
        echo "📌 Nome: " . odbc_result($result, 'name') . "<br>";
        echo "🔌 Provider: " . odbc_result($result, 'provider') . "<br>";
        echo "🌐 Data Source: " . odbc_result($result, 'data_source') . "<br>";
    } else {
        echo "❌ <strong>Linked Server NÃO ENCONTRADO</strong><br>";
        echo "⚠️ Você precisa criar o linked server no SQL Server Management Studio<br>";
        echo "<pre>
EXEC sp_addlinkedserver 
    @server = 'BDRODOPAR',
    @srvproduct = '',
    @provider = 'SQLOLEDB',
    @datasrc = 'SERVIDOR_RODOPAR'
</pre>";
        die("</div></body></html>");
    }
} catch (Exception $e) {
    echo "❌ <strong>ERRO:</strong> " . $e->getMessage() . "<br>";
}
echo "</div>";

// ====================================================================
// TESTE 3: OPENQUERY simples (SEM FILTROS)
// ====================================================================
echo "<div style='background: #e8f5e9; padding: 15px; margin: 10px; border-left: 5px solid #4CAF50;'>";
echo "<h2>✅ TESTE 3: OPENQUERY Simples (TOP 1)</h2>";
try {
    // Query SEM filtros de data
    $query_openquery = "
    SELECT TOP 1 * 
    FROM OPENQUERY([BDRODOPAR], '
        SELECT TOP 1
            CON.CODCON,
            CON.SERCON,
            CON.CODFIL,
            CON.DATEMI,
            CON.SITUAC
        FROM db_visual_covre_teste..RODCON CON (NOLOCK)
    ')";
    
    echo "📋 <strong>Query executada:</strong><br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($query_openquery) . "</pre>";
    
    $result = $con->executar($query_openquery);
    
    if ($result && odbc_fetch_row($result)) {
        echo "✅ <strong>SUCESSO! Dados retornados:</strong><br>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #4CAF50; color: white;'>";
        echo "<th>CODCON</th><th>SERCON</th><th>CODFIL</th><th>DATEMI</th><th>SITUAC</th>";
        echo "</tr>";
        echo "<tr>";
        echo "<td>" . odbc_result($result, 'CODCON') . "</td>";
        echo "<td>" . odbc_result($result, 'SERCON') . "</td>";
        echo "<td>" . odbc_result($result, 'CODFIL') . "</td>";
        echo "<td>" . odbc_result($result, 'DATEMI') . "</td>";
        echo "<td>" . odbc_result($result, 'SITUAC') . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "❌ <strong>Query executou mas NÃO retornou dados</strong><br>";
        echo "Erro ODBC: " . odbc_errormsg() . "<br>";
        die("</div></body></html>");
    }
} catch (Exception $e) {
    echo "❌ <strong>EXCEÇÃO:</strong> " . $e->getMessage() . "<br>";
    die("</div></body></html>");
}
echo "</div>";

// ====================================================================
// TESTE 4: OPENQUERY com FILTRO DE DATA (ASPAS DUPLAS)
// ====================================================================
echo "<div style='background: #f3e5f5; padding: 15px; margin: 10px; border-left: 5px solid #9C27B0;'>";
echo "<h2>✅ TESTE 4: OPENQUERY com Filtro de Data</h2>";
try {
    // ⚠️ ATENÇÃO: Dentro do OPENQUERY usa-se ASPAS DUPLAS ''
    $query_openquery_data = "
    SELECT TOP 5 * 
    FROM OPENQUERY([BDRODOPAR], '
        SELECT TOP 5
            CON.CODCON,
            CON.SERCON,
            CON.CODFIL,
            CON.DATEMI,
            CON.SITUAC
        FROM db_visual_covre_teste..RODCON CON (NOLOCK)
        WHERE CON.DATEMI >= ''2024-01-01''
        ORDER BY CON.DATEMI DESC
    ')";
    
    echo "📋 <strong>Query com filtro de data:</strong><br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($query_openquery_data) . "</pre>";
    echo "⚠️ <strong>IMPORTANTE:</strong> Repare nas <span style='background: yellow; padding: 2px;'>ASPAS DUPLAS ''2024-01-01''</span><br><br>";
    
    $result = $con->executar($query_openquery_data);
    
    if ($result) {
        echo "✅ <strong>SUCESSO! Registros encontrados:</strong><br>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #9C27B0; color: white;'>";
        echo "<th>CODCON</th><th>SERCON</th><th>CODFIL</th><th>DATEMI</th><th>SITUAC</th>";
        echo "</tr>";
        
        $count = 0;
        while (odbc_fetch_row($result)) {
            $count++;
            echo "<tr>";
            echo "<td>" . odbc_result($result, 'CODCON') . "</td>";
            echo "<td>" . odbc_result($result, 'SERCON') . "</td>";
            echo "<td>" . odbc_result($result, 'CODFIL') . "</td>";
            echo "<td>" . odbc_result($result, 'DATEMI') . "</td>";
            echo "<td>" . odbc_result($result, 'SITUAC') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total de registros:</strong> $count</p>";
        
        if ($count == 0) {
            echo "⚠️ Query executou mas não retornou registros (pode não ter dados nesse período)<br>";
        }
    } else {
        echo "❌ <strong>ERRO ao executar query</strong><br>";
        echo "Erro ODBC: " . odbc_errormsg() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>EXCEÇÃO:</strong> " . $e->getMessage() . "<br>";
}
echo "</div>";

// ====================================================================
// TESTE 5: JOIN com tabela RODFIL
// ====================================================================
echo "<div style='background: #fce4ec; padding: 15px; margin: 10px; border-left: 5px solid #E91E63;'>";
echo "<h2>✅ TESTE 5: JOIN RODCON + RODFIL</h2>";
try {
    $query_join = "
    SELECT TOP 3 * 
    FROM OPENQUERY([BDRODOPAR], '
        SELECT TOP 3
            CON.CODCON,
            CON.CODFIL,
            CON.DATEMI,
            FIL.RAZSOC
        FROM db_visual_covre_teste..RODCON CON (NOLOCK)
        JOIN db_visual_covre_teste..RODFIL FIL (NOLOCK)
            ON FIL.CODFIL = CON.CODFIL
        WHERE CON.DATEMI >= ''2024-01-01''
    ')";
    
    echo "📋 <strong>Query com JOIN:</strong><br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; overflow: auto;'>" . htmlspecialchars($query_join) . "</pre>";
    
    $result = $con->executar($query_join);
    
    if ($result) {
        echo "✅ <strong>SUCESSO!</strong><br>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr style='background: #E91E63; color: white;'>";
        echo "<th>CODCON</th><th>CODFIL</th><th>DATEMI</th><th>RAZSOC</th>";
        echo "</tr>";
        
        $count = 0;
        while (odbc_fetch_row($result)) {
            $count++;
            echo "<tr>";
            echo "<td>" . odbc_result($result, 'CODCON') . "</td>";
            echo "<td>" . odbc_result($result, 'CODFIL') . "</td>";
            echo "<td>" . odbc_result($result, 'DATEMI') . "</td>";
            echo "<td>" . odbc_result($result, 'RAZSOC') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Total:</strong> $count registros</p>";
    } else {
        echo "❌ <strong>ERRO</strong><br>";
        echo "Erro ODBC: " . odbc_errormsg() . "<br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>EXCEÇÃO:</strong> " . $e->getMessage() . "<br>";
}
echo "</div>";

// ====================================================================
// RESULTADO FINAL
// ====================================================================
echo "<div style='background: #c8e6c9; padding: 20px; margin: 10px; border-left: 5px solid #4CAF50;'>";
echo "<h2>🎯 RESULTADO FINAL</h2>";
echo "<p><strong>Se todos os testes passaram:</strong> ✅ Seu ambiente está configurado corretamente!</p>";
echo "<p><strong>Próximo passo:</strong> Usar o código corrigido na classe Dados.php</p>";
echo "</div>";

echo "</body></html>";
?>