<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ERROR);
/**
 * 
CREATE TABLE dbGerenciamento..LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG(
	id int identity(1,1) not null ,
	id_usuario int not null,
	id_protocolo_envio_comprovante_dig int not null,
	momento datetime not null default getdate(),
	operacao varchar(32) not null 
	);
 */

session_name("covre_ti");
session_start();

// POST, SESSION, GET E REQUEST

$id_protocolo = intval(trim($_POST['id_protocolo']));
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

$resposta = array(
    'status' => 'ok',
    'mensagem' => 'estornado com sucesso'
);

if ($id_protocolo <= 0) {
    $resposta['status'] = 'erro';
    $resposta['mensagem'] = 'protocolo invalido';
}

$SQL = "SELECT 
            ISNULL(DATEDIFF(DAY, data_hora_finalizacao, getdate()), 0) AS DIAS_POS_FINALIZACAO,
            CASE WHEN status_id = 'f' THEN 'true' else 'false' END FINALIZADO
        FROM 
            dbGerenciamento..protocolo_envio_comprovante_dig 
        WHERE 
            id = $id_protocolo";

$resultadoValidacao = $con->select($SQL);
if ($resultadoValidacao === false) {
    $resposta['status'] = 'erro';
    $resposta['mensagem'] = 'um erro ocorreu ao gerar o estorno, tente carregar novamente';

    echo json_encode($resposta);
    exit();
}

$finalizado = $resultadoValidacao[0]['FINALIZADO'] == 'true';
$dias_pos_finalizado = $resultadoValidacao[0]['DIAS_POS_FINALIZACAO'];

if ($finalizado && $dias_pos_finalizado > 2) {
    $resposta['status'] = 'erro';
    $resposta['mensagem'] = 'Não é permitido o estorno de protocolos finalizados há mais de 2 dias';

    echo json_encode($resposta);
    exit();
}


/**
 * Como o protocolo vai ser finalizado novamente após o estorno, 
 * deve-se registrar quem realizou a finalização anterior 
 * para manter a integridade de registro.
 */
$SQL = "
        DECLARE @STATUS  VARCHAR(10);
        SET @STATUS = 'OK';
        BEGIN TRY BEGIN TRANSACTION

			INSERT INTO dbGerenciamento..LOG_PROTOCOLO_ENVIO_COMPROVANTE_DIG
                (id_usuario, id_protocolo_envio_comprovante_dig, operacao, momento)
            VALUES 
			(
				(SELECT user_finalizacao FROM dbGerenciamento..protocolo_envio_comprovante_dig WHERE id = $id_protocolo AND status_id = 'f'),
				$id_protocolo,
				'finalizacao', 
				(SELECT data_hora_finalizacao FROM dbGerenciamento..protocolo_envio_comprovante_dig WHERE id = $id_protocolo AND status_id = 'f')
			),			(
                (SELECT id FROM dbGerenciamento..usuario WHERE usuario = '$logado' AND STATUS = 'a'),
                $id_protocolo,
                'estorno',
				getdate()
            );
			
            -- Caso tente estornar um protocolo ativo, ele vai dar erro no log então ativa o rollback
			UPDATE dbGerenciamento..protocolo_envio_comprovante_dig
						  SET 
							status_id = 'a'
						  WHERE 
							id = $id_protocolo
							AND status_id = 'f';

			COMMIT
            SET @STATUS = 'OK';
        END TRY BEGIN CATCH 
            ROLLBACK
            SET @STATUS = ERROR_MESSAGE();
			select 1/0; -- retorna um erro ao entrar no bloco catch
        END CATCH;
        
        SELECT @STATUS AS STATUS;";

$isOk = $con->executar($SQL) !== false;

$resposta['status'] = $isOk ? 'ok' : 'erro';
$resposta['mensagem'] = $isOk ? 'estornado com sucesso' : 'um erro ocorreu ao gerar o estorno, tente carregar novamente';

echo json_encode($resposta);
