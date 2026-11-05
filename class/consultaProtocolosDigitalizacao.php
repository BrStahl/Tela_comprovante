<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";

date_default_timezone_set("America/Sao_Paulo");

/**
 * Ao invés de estornar, simplesmente alterar e gerar registro de quem alterou.
 * 
 * Talvez adicionar uma flag q
 */


/**
 * 
 * Passos para conclusão.
 * 
 * - Criar consulta comprovante por nº e período de registro
 * - Permitir carregar protocolos anteriores para edição.
 * - Registros alterados devem receber flag de alterado e ser gravados na tabela de log especifica
 *   contendo usuario, momento, documento, protocolo.
 * 
 * - permitir salvar alterações
 * - enviar para testes
 * 
 * 
 */
function listarProtocolosFinalizados($conexaoSCI = null, $nProtocolo = null, $start = null, $end = null)
{
    if (is_null($conexaoSCI))
        $conexaoSCI = new ConexaoSCI();

    if (is_null($nProtocolo))
        $nProtocolo = $_POST['n_protocolo'];

    if (is_null($start))
        $start =  $_POST['dt_start'];

    if (is_null($end))
        $end =  $_POST['dt_end'];


    $resposta = array();
    $resposta['ok'] = false;
    $resposta['result_set'] = array();
    $resposta['result_msg'] = "Busca concluída.";

    // verifica nº protocolo
    $nProtocolo = isset($nProtocolo) && !empty($nProtocolo) && intval($nProtocolo) > 0 ? intval($nProtocolo) : null;

    // verifica período de datas

    $start = isset($start) && !empty($start) ? strtotime($start) : null;
    $end = isset($end) && !empty($end) ? strtotime($end) : null;

    if ($start != null && $end != null) {
        $dias = strtotime($end) - strtotime($start) / 86400;

        if ($dias < 0 || $dias > 31) {
            $resposta['result_msg'] = "Intervalo de data inválido.";
            return $resposta;
        }

        $start = date("Y-m-d", $start);
        $end = date("Y-m-d", $end);
    }

    // verifica filtros

    if ($nProtocolo == null && ($start == null || $end == null)) {
        $resposta['result_msg'] = "Necessário nº protocolo ou intervalo de datas para busca";
        return $resposta;
    }

    // executa a consulta

    $SQL = "SELECT 
                id,
                data_hora_gravacao, 
                data_hora_finalizacao, 
                status_id ,
                ressalva_todos,
                tipo_documento_id 
            FROM dbGerenciamento.dbo.protocolo_envio_comprovante_dig pecd 
            WHERE 1 = 1 ";

    if (!is_null($nProtocolo))
        $SQL .= "AND pecd.id = $nProtocolo ";

    if (!is_null($start) && !is_null($end))
        $SQL .= "AND pecd.data_hora_gravacao BETWEEN '$start' AND '$end' ";

    $SQL .= "ORDER BY pecd.id DESC";

    $resposta['result_set'] = $conexaoSCI->select($SQL);
    $resposta['ok'] = $resposta['result_set'] != false;

    if(!$resposta['ok']){
        return $resposta;
    }

    foreach($resposta['result_set'] as &$row){
        break;
        $id = $row['id'];
        $row['link_btn'] = "<button class='btn-sm btn-primary' onclick='carregarProtocolo($id)>Carregar</button>'";
    }
    return $resposta;
}

function geraLogAlteracao($nProtocolo, $nComprovante)
{

    $usuario = $_SESSION['usuario_logado'];
    $momento = "getdate()";

    $INSERT = "";
}
