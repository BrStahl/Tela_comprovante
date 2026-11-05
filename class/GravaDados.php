<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";

class GravaDados extends ConexaoSCI
{


	public function gravaProtocoloEnvio($id, $ponto_operacao_id, $tipo_documento_id, $status_id, $sistema)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			if ($id == '') {

				$query = "insert into protocolo_envio_comprovante (ponto_operacao_id, tipo_documento_id, user_gravacao, 
						  data_hora_gravacao, status_id,sistema) values ($ponto_operacao_id, '$tipo_documento_id', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 
						  '$status_id', $sistema)";
				//print $query;
				$con->executar($query);


				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$id = odbc_result($result, 1);

				$ret_gravacao = "1|ok|$id";
			} else {
				$query = "update protocolo_envio_comprovante
						  set ponto_operacao_id = $ponto_operacao_id,
						  tipo_documento_id = '$tipo_documento_id',
						  status_id = '$status_id',
						  sistema = $sistema
						  data_hora_inativacao = case when '$status_id' = 'a' then null else getdate() end,
						  user_inativacao = case when '$status_id' = 'a' then null else (select id 
																						 from usuario with (nolock)
																						 where usuario = '$logado' 
																						 and status = 'a') end
						  where id = $id";
				//print $query;
				$con->executar($query);


				$ret_gravacao = "2|ok|$id";
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function gravaProtocoloEnvioSts($id, $ponto_operacao_id, $tipo_documento_id, $status_id, $sistema)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			if ($id == '') {

				$query = "insert into protocolo_envio_comprovante_sts (ponto_operacao_id, tipo_documento_id, user_gravacao, 
						  data_hora_gravacao, status_id,sistema) values ($ponto_operacao_id, '$tipo_documento_id', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 
						  '$status_id', $sistema)";
				//print $query;
				$con->executar($query);


				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$id = odbc_result($result, 1);

				$ret_gravacao = "1|ok|$id";
			} else {
				$query = "update protocolo_envio_comprovante_sts
						  set ponto_operacao_id = $ponto_operacao_id,
						  tipo_documento_id = '$tipo_documento_id',
						  status_id = '$status_id',
						  sistema = $sistema
						  data_hora_inativacao = case when '$status_id' = 'a' then null else getdate() end,
						  user_inativacao = case when '$status_id' = 'a' then null else (select id 
																						 from usuario with (nolock)
																						 where usuario = '$logado' 
																						 and status = 'a') end
						  where id = $id";
				//print $query;
				$con->executar($query);


				$ret_gravacao = "2|ok|$id";
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


















	public function validaComprovanteDuplicado($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
		$query = "SELECT 
						ISNULL(PROTOCOLO_ENVIO_ID, 0) PROTOCOLO_ENVIO_ID
					FROM COMPROVANTE_ENVIO WITH(NOLOCK)
					WHERE CHAVE_ACESSO = '" . $chave . "'   ";

		$result 		   = $con->executar($query);
		$id_protocolo = odbc_result($result, 1);
		return $id_protocolo;
	}

	public function validaComprovanteDuplicadoSts($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
		$query = "SELECT 
						ISNULL(PROTOCOLO_ENVIO_ID, 0) PROTOCOLO_ENVIO_ID
					FROM COMPROVANTE_ENVIO_STS WITH(NOLOCK)
					WHERE CHAVE_ACESSO = '" . $chave . "' AND  STATUS_ID='A' ";

		$result 		   = $con->executar($query);
		$id_protocolo = odbc_result($result, 1);
		return $id_protocolo;
	}














	public function validaComprovanteDuplicadoDig($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
		$query = "SELECT 
		ISNULL(PROTOCOLO_ENVIO_ID, 0) PROTOCOLO_ENVIO_ID
			FROM COMPROVANTE_ENVIO_DIG WITH(NOLOCK)
			WHERE CHAVE_ACESSO = '" . $chave . "' ";
		$result 		   = $con->executar($query);
		$id_protocolo = odbc_result($result, 1);

		return $id_protocolo;
	}

	public function gravaComprovanteEnvioRodopar($chave, $protocolo_id, $tipo_documento)
{
    $con = new ConexaoSCI;
    $dados = new Dados;
    $logado = $_SESSION['usuario_logado'];

    if ($logado != '' && $protocolo_id != '') {

        $tp_doc = "";
        if ($tipo_documento == 'C') {
            $doc_rodopar = $dados->buscaDacumentosAPI($chave);
            $tp_doc = "CTE";
        } else {
            $filial = substr($chave, 0, 2);
            $serie = substr($chave, 2, 3);
            $doc = substr($chave, 5, 6);

            $doc_rodopar = $dados->buscaDacumentosAPI('', (int)$filial, $serie, (int)$doc);
            $tp_doc = "OST";
        }
        $documentoFinal = null;

        // =================================================================
        // INÍCIO DA CORREÇÃO 1 - LÓGICA DE BUSCA
        //
        // $doc_rodopar é SEMPRE uma lista [doc1, doc2, ...]
        
        if (is_array($doc_rodopar) && count($doc_rodopar) > 0) {
            foreach ($doc_rodopar as $doc) {
                // $doc é o documento, não tem mais ['Documento']
                if (is_array($doc) && isset($doc['Tipo']) && $doc['Tipo'] === $tp_doc) {
                    $documentoFinal = $doc; // $doc é o documento
                    break; // Achamos
                }
            }
        }
        // =================================================================


        // =================================================================
        // INÍCIO DA CORREÇÃO 2 - VERIFICAÇÃO DE SUCESSO
        //
        // Verificamos se $documentoFinal FOI encontrado (não é null) e é um array
        if (is_array($documentoFinal) && isset($documentoFinal['Numero']) && $documentoFinal['Numero'] > 0) {
        // =================================================================

            $retorno = '';
            $protocolo_ = $this->validaComprovanteDuplicado($documentoFinal['Chave'], $protocolo_id);

            if ($protocolo_ != '0' && $protocolo_ != $protocolo_id) {
                $ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
                $retorno->ret_gravacao = $ret_gravacao;

                return $retorno;
            }

            // =================================================================
            // INÍCIO DA CORREÇÃO 3 - QUERY (ASPAS E INJECTION)
            //
            // Removemos aspas de 'Numero' e 'Filial' (são int)
            // Adicionado str_replace para 'Cliente' (evitar erro com aspas no nome)
            $cliente_seguro = str_replace("'", "''", $documentoFinal['Cliente']);
            
            $query = "insert into comprovante_envio (protocolo_envio_id, chave_acesso, 
                            user_gravacao, data_hora_gravacao, status_id, r_doc, r_serie, r_fil, r_cliente, r_data_emissao) 
                            SELECT $protocolo_id, '$chave', 
                            (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
                            ," . $documentoFinal['Numero'] . ",'" . $documentoFinal['Serie'] . "'," . $documentoFinal['Filial'] . "
                            ,'" . $cliente_seguro . "', convert(datetime, '" . $documentoFinal['Emissao'] . "',103)
                            WHERE '$chave' NOT IN (select top 1 chave_acesso
                                                    from comprovante_envio
                                                    where protocolo_envio_id = $protocolo_id
                                                    and chave_acesso = '$chave'
                                                    and status_id = 'a')
                            ";
            // =================================================================

            $con->executar($query);

            $query = "SELECT @@IDENTITY AS Ident";
            $result = $con->executar($query);
            $chave_id = odbc_result($result, 1);

            $ret_gravacao = "1|ok|$chave_id";
        } else {
            $ret_gravacao = "Documento não encontrado na API"; // Mensagem mais clara
        }
    } else {
        $ret_gravacao = "Sessão expirada, favor logar novamente";
    }

    $retorno->ret_gravacao = $ret_gravacao;

    return $retorno;
}



	//public function gravaComprovanteEnvioRodoparSts($chave, $protocolo_id, $tipo_documento)
	public function gravaComprovanteEnvioRodoparSts($chave, $protocolo_id, $tipo_documento, $ponto_operacao_id = null)
	{
		$con = new ConexaoSCI;
		$dados = new Dados;
		$logado = $_SESSION['usuario_logado'];
	
		if ($logado != '' && $protocolo_id != '') {
	
			$tp_doc = "";
			if ($tipo_documento == 'C') {
				$doc_rodopar = $dados->buscaDacumentosAPI($chave);
				$tp_doc = "CTE";
			} else {
				$filial = substr($chave, 0, 2);
				$serie = substr($chave, 2, 3);
				$doc = substr($chave, 5, 6);
	
				$doc_rodopar = $dados->buscaDacumentosAPI('', (int)$filial, $serie, (int)$doc);
				$tp_doc = "OST";
			}
			$documentoFinal = null;
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 1 - LÓGICA DE BUSCA
			//
			// $doc_rodopar é SEMPRE uma lista [doc1, doc2, ...]
			// Iteramos para achar o $tp_doc correto.
			
			if (is_array($doc_rodopar) && count($doc_rodopar) > 0) {
				foreach ($doc_rodopar as $doc) {
					// $doc é o documento, não tem mais ['Documento']
					if (is_array($doc) && isset($doc['Tipo']) && $doc['Tipo'] === $tp_doc) {
						$documentoFinal = $doc; // $doc é o documento
						break; // Achamos
					}
				}
			}
			// =================================================================
	
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 2 - VERIFICAÇÃO DE SUCESSO
			//
			// Verificamos se $documentoFinal FOI encontrado (não é null) e é um array
			if (is_array($documentoFinal) && isset($documentoFinal['Numero']) && $documentoFinal['Numero'] > 0) {
			// =================================================================
	
				$retorno = '';
				$protocolo_ = $this->validaComprovanteDuplicadoSts($documentoFinal['Chave'], $protocolo_id);
	
				if ($protocolo_ != '0' && $protocolo_ != $protocolo_id) {
					$ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
					$retorno->ret_gravacao = $ret_gravacao;
	
					return $retorno;
				}
	
				// =================================================================
				// INÍCIO DA CORREÇÃO 3 - QUERY (ASPAS, INJECTION e LÓGICA)
				//
				// Removemos aspas de 'Numero' e 'Filial' (são int)
				// Adicionado str_replace para 'Cliente' (evitar erro com aspas no nome)
				$cliente_seguro = str_replace("'", "''", $documentoFinal['Cliente']);
				
				$query = "insert into comprovante_envio_sts (protocolo_envio_id, chave_acesso, 
								user_gravacao, data_hora_gravacao, status_id, r_doc, r_serie, r_fil, r_cliente, r_data_emissao, ponto_operacao_id) 
								SELECT $protocolo_id, '$chave', 
								(select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
								," . $documentoFinal['Numero'] . ",'" . $documentoFinal['Serie'] . "'," . $documentoFinal['Filial'] . "
								,'" . $cliente_seguro . "', convert(datetime, '" . $documentoFinal['Emissao'] . "',103)
								,$ponto_operacao_id
								WHERE '$chave' NOT IN (select top 1 chave_acesso
														from comprovante_envio_sts -- CORREÇÃO DO BUG LÓGICO (estava 'comprovante_envio')
														where protocolo_envio_id = $protocolo_id
														and chave_acesso = '$chave'
														and status_id = 'a')
								";
				// =================================================================
	
				$con->executar($query);
	
				$query = "SELECT @@IDENTITY AS Ident";
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);
	
				$ret_gravacao = "1|ok|$chave_id";
			} else {
				$ret_gravacao = "Documento não encontrado na API"; // Mensagem mais clara
			}
		} else {
			$ret_gravacao = "Sessão expirada, favor logar novamente";
		}
	
		$retorno->ret_gravacao = $ret_gravacao;
	
		return $retorno;
	}


































	public function gravaComprovanteEnvio($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		if ($logado != '') {

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
			$query = "select 
			tipo_documento_id,
			
			then case when tipo_documento_id = 'C'
					then 						
						(SELECT TOP 1 Docto_Transporte_Id
							from cargosol..cte with (nolock)
							where Chave_CTe = '$chave'
							and Tab_Status_Id <> 2) 						
					else (SELECT TOP 1 IDMOV
							FROM corpore..TMOV with (nolock)
							WHERE TMOV.CODFILIAL = SUBSTRING('$chave', 1,3) 
							AND TMOV.NUMEROMOV = SUBSTRING('$chave', 4,8)
							AND TMOV.IDMOV = SUBSTRING('$chave', 12,8)
							AND CODCOLIGADA = 1) 
			end,				
			id,
			protocolo.sistema
			from protocolo_envio_comprovante protocolo with (nolock)
			where status_id = 'a'
			and id = $protocolo_id";

			$result = $con->executar($query);
			$tipo_documento_id = odbc_result($result, 1);
			$id_documento	   = odbc_result($result, 2);
			$id_protocolo_	   = odbc_result($result, 3);



			if ($id_documento > 0) {

				$retorno = '';
				$protocolo_ = $this->validaComprovanteDuplicado($chave, $protocolo_id);

				$query = "insert into comprovante_envio (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  case when '$tipo_documento_id' = 'C' AND $sistema = 1 then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' AND $sistema = 1 then '$id_documento' else NULL end,					  			 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
						  WHERE '$chave' NOT IN (select top 1 chave_acesso
												  from comprovante_envio
												  where protocolo_envio_id = $protocolo_id
												  and chave_acesso = '$chave'
												  and status_id = 'a')
						  ";

				$con->executar($query);

				$query = "SELECT @@IDENTITY AS Ident";
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);


				if ($protocolo_ != '0' && ($id_protocolo_ != $protocolo_)) {
					$ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
				} else {
					$ret_gravacao = "1|ok|$chave_id";
				}
			} else
				$ret_gravacao = "Documento não encontrado";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function excluiComprovante($id, $tela)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {
			if ($tela == 1) {
				$query = "update comprovante_envio
						  set status_id = 'i', 
						  data_hora_inativacao = getdate(), 
						  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
						  where id = $id";
				//print $query;
				$con->executar($query);
			} else {
				$query = "update comprovante_envio_dig
						  set status_id = 'i', 
						  data_hora_inativacao = getdate(), 
						  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
						  where id = $id";
				//print $query;
				$con->executar($query);
			}


			$ret_gravacao =  "ok";
		} else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}




	public function excluiComprovanteSts($id, $tela)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {
			if ($tela == 1) {
				$query = "update comprovante_envio_sts
						  set status_id = 'i', 
						  data_hora_inativacao = getdate(), 
						  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
						  where id = $id";
				//print $query;
				$con->executar($query);
			} else {
				$query = "update comprovante_envio_dig
						  set status_id = 'i', 
						  data_hora_inativacao = getdate(), 
						  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
						  where id = $id";
				//print $query;
				$con->executar($query);
			}


			$ret_gravacao =  "ok";
		} else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}
























	public function excluiComprovanteConf($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {

			$query = "update comprovante_recebimento
					  set status_id = 'i', 
					  data_hora_inativacao = getdate(), 
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
					  where id = $id";
			//print $query;
			$con->executar($query);


			$ret_gravacao =  "ok";
		} else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}





	public function excluiComprovanteConfSts($id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {

			$query = "update comprovante_recebimento_sts
					  set status_id = 'i', 
					  data_hora_inativacao = getdate(), 
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
					  where id = $id";
			//print $query;
			$con->executar($query);


			$ret_gravacao =  "ok";
		} else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}





























	public function gravaRessalva($ressalva, $id, $tela)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {
			if ($tela == 1) {
				$query = "update comprovante_envio
						  set ressalva = case when '$ressalva' = 'n' then NULL else 's' end,
						  qtde_ressalva = case when '$ressalva' = 'n' then NULL else '1' end
						  where id = $id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			} else {
				$query = "update comprovante_envio_dig
						  set ressalva = case when '$ressalva' = 'n' then NULL else 's' end,
						  qtde_ressalva = case when '$ressalva' = 'n' then NULL else '1' end
						  where id = $id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaQtdeRessalva($qtde, $id, $tela)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {

			if ($tela == 1) {
				$query = "update comprovante_envio
						  set qtde_ressalva = case when '$qtde' = '' then NULL else '$qtde' end
						  where id = $id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			} else {
				$query = "update comprovante_envio_dig
						  set qtde_ressalva = case when '$qtde' = '' then NULL else '$qtde' end
						  where id = $id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaRessalvaTodos($ressalva, $protocolo_id, $tela, $aux)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {
			if ($tela == 1) {
				if ($aux == 1) {
					$query = "update comprovante_envio
							  set ressalva = case when '$ressalva' = 'n' then NULL else 's' end,
							  qtde_ressalva = case when '$ressalva' = 'n' then NULL else '1' end
							  where protocolo_envio_id = $protocolo_id
							  and status_id = 'a'";
					//print $query;
					$con->executar($query);
				}

				$query = "update protocolo_envio_comprovante
						  set ressalva_todos = case when '$ressalva' = 'n' then NULL else 's' end
						  where id = $protocolo_id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			} else {
				if ($aux == 1) {
					$query = "update comprovante_envio_dig
							  set ressalva = case when '$ressalva' = 'n' then NULL else 's' end,
							  qtde_ressalva = case when '$ressalva' = 'n' then NULL else '1' end
							  where protocolo_envio_id = $protocolo_id
							  and status_id = 'a'";
					//print $query;
					$con->executar($query);
				}

				$query = "update protocolo_envio_comprovante_dig
						  set ressalva_todos = case when '$ressalva' = 'n' then NULL else 's' end
						  where id = $protocolo_id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function geraProtocolo($id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$ip = $_SERVER['HTTP_HOST'];

		if ($logado != '') {

			//GERA UM NOVO MALOTE NO SISTEMA RASTREABILIDADE DE MALOTES
		
			$query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
			$result_user = $con->executar($query_user);

		   // !ATENÇÃO: A variável $result_user não foi definida no seu código original.
		   // Você precisa adicionar a query de usuário aqui, por exemplo:
		   // $query_user = "select nome from usuario with (nolock) where usuario = '$logado' and status = 'a'";
		   // $result_user = $con->executar($query_user);
		   // Sem isso, o if(odbc_fetch_row($result_user)) abaixo pode falhar.
		   // Assumindo que $result_user será corrigido:
		   if (isset($result_user) && odbc_fetch_row($result_user)) {
			   $rec_nom = odbc_result($result_user, 1);
		   }
		   $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário

		   // 2. Obter tipo de documento do protocolo
		   $query_proto_info = "select tipo_documento_id 
								from protocolo_envio_comprovante with (nolock) 
								where id = $id";
		   $result_proto_info = $con->executar($query_proto_info);
	   
		   $tipo_documento_id = 'C'; // default
		   if (odbc_fetch_row($result_proto_info)) {
 
				 // *** CORREÇÃO AQUI ***
				 // Primeiro, pegue o valor da coluna usando odbc_result()
				 $valor_do_banco = odbc_result($result_proto_info, 1);
 
				 // Segundo, aplique o trim/strtoupper nesse valor
				 $tipo_documento_id = strtoupper(trim($valor_do_banco));
				 // *** FIM DA CORREÇÃO ***
				 var_dump($tipo_documento_id); 

		   }
		   // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
		   $tip_doc_json = 'CTE';
			 // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
			   $query_docs = "select chave_acesso 
							  from comprovante_envio with (nolock)
							  where protocolo_envio_id = $id
							  and status_id = 'a'";
			   $result_docs = $con->executar($query_docs);
			   
							   // CORREÇÃO 1: Mudar [] para array()
			   $inp_value_json = array(); 
			   $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
			   
			   while (odbc_fetch_row($result_docs)) {
				   $chave_doc = odbc_result($result_docs, 1);
				   
									   // CORREÇÃO 2: Mudar [] para array()
				   $inp_value_json[] = array(
					   "TIPDOC" => $tip_doc_json,
					   "CHAVE" => $chave_doc,
					   "FILDOC" => "",
					   "SERDOC" => "",
					   "CODDOC" => "",
					   "CODOCO" => "135", // Código de ocorrência do exemplo
					   "CODOBS" => "",
					   "DATOCO" => $dat_oco,
					   "RECNOM" => $rec_nom,
					   "OBSERV" => "OCORRENCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
				   );
			   }
		   // 4. Montar e enviar a requisição cURL se houver documentos
		   if (!empty($inp_value_json)) {
			   
			   // AJUSTE PARA PHP 5: Trocado [] por array()
			   $post_data = array(
				   "PROC" => "VRAPI_POST_RODOCH",
				   "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
				   "INP_VALUE_JSON" => $inp_value_json
			   );

			   $json_payload = json_encode($post_data); // Requer PHP 5.2+

			   $curl = curl_init();

			   curl_setopt_array($curl, array(
				   CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => '',
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
				   CURLOPT_FOLLOWLOCATION => true,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_CUSTOMREQUEST => 'POST',
				   CURLOPT_POSTFIELDS => $json_payload,
				   CURLOPT_HTTPHEADER => array(
					   'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
					   'Content-Type: application/json'
				   ),
			   ));

			   // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
			   
			   $response = curl_exec($curl);
			   $err = curl_error($curl); // Captura o erro, se houver
			   curl_close($curl);

			   // Prepara o que será salvo no log de retorno
			   $log_retorno = '';
			   if ($response === false) {
				   // Se o cURL falhou, loga a mensagem de erro
				   $log_retorno = 'cURL Error: ' . $err;
			   } else {
				   // Se o cURL teve sucesso, loga a resposta da API
				   $log_retorno = $response;
			   }

			   // Prepara as strings para inserir no banco, escapando apóstrofos (')
			   // Isto é vital para evitar quebras na query SQL
			   $json_payload_sql = str_replace("'", "''", $json_payload);
			   $log_retorno_sql = str_replace("'", "''", $log_retorno);

			   // Monta e executa a query de log
			   $query_log = "INSERT INTO log_api_protocolo_finalizacao 
								 (protocolo_envio_id, json_envio, json_retorno)
							 VALUES 
								 ($id, '$json_payload_sql', '$log_retorno_sql')";
			   
			   // Usa a mesma conexão para executar o log
			   // Não tratamos o erro disto para não parar a finalização principal
			   $con->executar($query_log); 
			   
			   // --- FIM DO NOVO BLOCO DE LOG ---
			   
		   }
		   
		
			//BUSCA O ULTIMO MALOTE INSERIDO
			$query = "SELECT TOP 1 'MA-'+right('0000'+RTRIM(id+1),5) CODIGO, ID+1 
						  FROM rastreabilidade_malote
						  ORDER BY ID DESC";
			//print $query;
			$result = $con->executar($query);
			$codigo_malote   = odbc_result($result, 1);
			$malote_id   	 = odbc_result($result, 2);


			$query = "insert into rastreabilidade_malote (tipo_malote_id, data_postagem, recebedor_id, lacre, num_lacre, 
						  nome_expedidor, po_destino_id, observacao, status_id, codigo_malote)
						  VALUES (2, getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), 
						  'n', ' ', (select nome from usuario where usuario = '$logado' and status = 'a'), 1, 
						  'Comprovantes - Protocolo: $id', 'P', '$codigo_malote')";
			//print $query;
			$con->executar($query);

			$link_malote = "<a href='http://$ip/rastreabilidade_malote/rastreabilidade_malote.php?cd=$malote_id' target='_blank'>$codigo_malote</a>";

			//FIM GERACAO MALOTE

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
			$query = "update protocolo_envio_comprovante
					  set status_id = 't',
					  user_geracao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  data_hora_geracao = getdate(),
					  malote_id = case when '$malote_id' = '' then NULL else '$malote_id' end
					  where id = $id
					  and status_id = 'a'";
			//print $query;
			$con->executar($query);



			$ret_gravacao = "Ok|$codigo_malote|$link_malote";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}






	public function geraProtocoloSts($id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$ip = $_SERVER['HTTP_HOST'];

		if ($logado != '') {


			$query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
			$result_user = $con->executar($query_user);

		   if (isset($result_user) && odbc_fetch_row($result_user)) {
			   $rec_nom = odbc_result($result_user, 1);
		   }
		   $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário

		   // 2. Obter tipo de documento do protocolo
		   $query_proto_info = "select tipo_documento_id 
								from protocolo_envio_comprovante_sts with (nolock) 
								where id = $id";
		   $result_proto_info = $con->executar($query_proto_info);
	   
		   $tipo_documento_id = 'C'; // default
		   if (odbc_fetch_row($result_proto_info)) {
 
				 // *** CORREÇÃO AQUI ***
				 // Primeiro, pegue o valor da coluna usando odbc_result()
				 $valor_do_banco = odbc_result($result_proto_info, 1);
 
				 // Segundo, aplique o trim/strtoupper nesse valor
				 $tipo_documento_id = strtoupper(trim($valor_do_banco));
				 // *** FIM DA CORREÇÃO ***
				/// var_dump($tipo_documento_id); 

		   }
		   // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
		   $tip_doc_json = 'CTE';
			 // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
			   $query_docs = "select chave_acesso 
							  from comprovante_envio_sts with (nolock)
							  where protocolo_envio_id = $id
							  and status_id = 'a'";
			   $result_docs = $con->executar($query_docs);
			   
							   // CORREÇÃO 1: Mudar [] para array()
			   $inp_value_json = array(); 
			   $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
			   
			   while (odbc_fetch_row($result_docs)) {
				   $chave_doc = odbc_result($result_docs, 1);
				   
									   // CORREÇÃO 2: Mudar [] para array()
				   $inp_value_json[] = array(
					   "TIPDOC" => $tip_doc_json,
					   "CHAVE" => $chave_doc,
					   "FILDOC" => "",
					   "SERDOC" => "",
					   "CODDOC" => "",
					   "CODOCO" => "143", // Código de ocorrência do exemplo
					   "CODOBS" => "",
					   "DATOCO" => $dat_oco,
					   "RECNOM" => $rec_nom,
					   "OBSERV" => "OCORRENCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
				   );
			   }
		   // 4. Montar e enviar a requisição cURL se houver documentos
		   if (!empty($inp_value_json)) {
			   
			   // AJUSTE PARA PHP 5: Trocado [] por array()
			   $post_data = array(
				   "PROC" => "VRAPI_POST_RODOCH",
				   "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
				   "INP_VALUE_JSON" => $inp_value_json
			   );

			   $json_payload = json_encode($post_data); // Requer PHP 5.2+

			   $curl = curl_init();

			   curl_setopt_array($curl, array(
				   CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
				   CURLOPT_RETURNTRANSFER => true,
				   CURLOPT_ENCODING => '',
				   CURLOPT_MAXREDIRS => 10,
				   CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
				   CURLOPT_FOLLOWLOCATION => true,
				   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				   CURLOPT_CUSTOMREQUEST => 'POST',
				   CURLOPT_POSTFIELDS => $json_payload,
				   CURLOPT_HTTPHEADER => array(
					   'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
					   'Content-Type: application/json'
				   ),
			   ));

			   // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
			   
			   $response = curl_exec($curl);
			   $err = curl_error($curl); // Captura o erro, se houver
			   curl_close($curl);

			   // Prepara o que será salvo no log de retorno
			   $log_retorno = '';
			   if ($response === false) {
				   // Se o cURL falhou, loga a mensagem de erro
				   $log_retorno = 'cURL Error: ' . $err;
			   } else {
				   // Se o cURL teve sucesso, loga a resposta da API
				   $log_retorno = $response;
			   }

			   // Prepara as strings para inserir no banco, escapando apóstrofos (')
			   // Isto é vital para evitar quebras na query SQL
			   $json_payload_sql = str_replace("'", "''", $json_payload);
			   $log_retorno_sql = str_replace("'", "''", $log_retorno);

			   // Monta e executa a query de log
			   $query_log = "INSERT INTO log_api_protocolo_finalizacao 
								 (protocolo_envio_id, json_envio, json_retorno)
							 VALUES 
								 ($id, '$json_payload_sql', '$log_retorno_sql')";
			   
			   // Usa a mesma conexão para executar o log
			   // Não tratamos o erro disto para não parar a finalização principal
			   $con->executar($query_log); 
			   
			   // --- FIM DO NOVO BLOCO DE LOG ---
			   
		   }

































			//GERA UM NOVO MALOTE NO SISTEMA RASTREABILIDADE DE MALOTES

			//BUSCA O ULTIMO MALOTE INSERIDO
			$query = "SELECT TOP 1 'MA-'+right('0000'+RTRIM(id+1),5) CODIGO, ID+1 
						  FROM rastreabilidade_malote
						  ORDER BY ID DESC";
			//print $query;
			$result = $con->executar($query);
			$codigo_malote   = odbc_result($result, 1);
			$malote_id   	 = odbc_result($result, 2);


			$query = "insert into rastreabilidade_malote (tipo_malote_id, data_postagem, recebedor_id, lacre, num_lacre, 
						  nome_expedidor, po_destino_id, observacao, status_id, codigo_malote)
						  VALUES (2, getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), 
						  'n', ' ', (select nome from usuario where usuario = '$logado' and status = 'a'), 1, 
						  'Comprovantes - Protocolo: $id', 'P', '$codigo_malote')";
			//print $query;
			$con->executar($query);

			$link_malote = "<a href='http://$ip/rastreabilidade_malote/rastreabilidade_malote.php?cd=$malote_id' target='_blank'>$codigo_malote</a>";

			//FIM GERACAO MALOTE

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
			$query = "update protocolo_envio_comprovante_sts
					  set status_id = 't',
					  user_geracao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  data_hora_geracao = getdate(),
					  malote_id = case when '$malote_id' = '' then NULL else '$malote_id' end
					  where id = $id
					  and status_id = 'a'";
			//print $query;
			$con->executar($query);



			






























			$ret_gravacao = "Ok|$codigo_malote|$link_malote";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}

















	public function finalizaProtocolo($id, $tela)
    {

        $con = new ConexaoSCI;

        $dados = new Dados;

        $logado = $_SESSION['usuario_logado'];

        $retorno = '';

        if ($logado != '') {
            if ($tela == 1) {

				$query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
				$result_user = $con->executar($query_user);
	
			   // !ATENÇÃO: A variável $result_user não foi definida no seu código original.
			   // Você precisa adicionar a query de usuário aqui, por exemplo:
			   // $query_user = "select nome from usuario with (nolock) where usuario = '$logado' and status = 'a'";
			   // $result_user = $con->executar($query_user);
			   // Sem isso, o if(odbc_fetch_row($result_user)) abaixo pode falhar.
			   // Assumindo que $result_user será corrigido:
			   if (isset($result_user) && odbc_fetch_row($result_user)) {
				   $rec_nom = odbc_result($result_user, 1);
			   }
			   $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário
	
			   // 2. Obter tipo de documento do protocolo
			   $query_proto_info = "select tipo_documento_id 
									from protocolo_envio_comprovante with (nolock) 
									where id = $id";
			   $result_proto_info = $con->executar($query_proto_info);
		   
			   $tipo_documento_id = 'C'; // default
			   if (odbc_fetch_row($result_proto_info)) {
	 
					 // *** CORREÇÃO AQUI ***
					 // Primeiro, pegue o valor da coluna usando odbc_result()
					 $valor_do_banco = odbc_result($result_proto_info, 1);
	 
					 // Segundo, aplique o trim/strtoupper nesse valor
					 $tipo_documento_id = strtoupper(trim($valor_do_banco));
					 // *** FIM DA CORREÇÃO ***
					 var_dump($tipo_documento_id); 
	
			   }
			   // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
			   $tip_doc_json = 'CTE';
				 // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
				   $query_docs = "select chave_acesso 
								  from comprovante_recebimento with (nolock)
								  where protocolo_envio_id = $id
								  and status_id = 'a'";
				   $result_docs = $con->executar($query_docs);
				   
								   // CORREÇÃO 1: Mudar [] para array()
				   $inp_value_json = array(); 
				   $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
				   
				   while (odbc_fetch_row($result_docs)) {
					   $chave_doc = odbc_result($result_docs, 1);
					   
										   // CORREÇÃO 2: Mudar [] para array()
					   $inp_value_json[] = array(
						   "TIPDOC" => $tip_doc_json,
						   "CHAVE" => $chave_doc,
						   "FILDOC" => "",
						   "SERDOC" => "",
						   "CODDOC" => "",
						   "CODOCO" => "145", // Código de ocorrência do exemplo
						   "CODOBS" => "",
						   "DATOCO" => $dat_oco,
						   "RECNOM" => $rec_nom,
						   "OBSERV" => "OCORRENCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
					   );
				   }
			   // 4. Montar e enviar a requisição cURL se houver documentos
			   if (!empty($inp_value_json)) {
				   
				   // AJUSTE PARA PHP 5: Trocado [] por array()
				   $post_data = array(
					   "PROC" => "VRAPI_POST_RODOCH",
					   "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
					   "INP_VALUE_JSON" => $inp_value_json
				   );
	
				   $json_payload = json_encode($post_data); // Requer PHP 5.2+
	
				   $curl = curl_init();
	
				   curl_setopt_array($curl, array(
					   CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
					   CURLOPT_RETURNTRANSFER => true,
					   CURLOPT_ENCODING => '',
					   CURLOPT_MAXREDIRS => 10,
					   CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
					   CURLOPT_FOLLOWLOCATION => true,
					   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					   CURLOPT_CUSTOMREQUEST => 'POST',
					   CURLOPT_POSTFIELDS => $json_payload,
					   CURLOPT_HTTPHEADER => array(
						   'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
						   'Content-Type: application/json'
					   ),
				   ));
	
				   // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
				   
				   $response = curl_exec($curl);
				   $err = curl_error($curl); // Captura o erro, se houver
				   curl_close($curl);
	
				   // Prepara o que será salvo no log de retorno
				   $log_retorno = '';
				   if ($response === false) {
					   // Se o cURL falhou, loga a mensagem de erro
					   $log_retorno = 'cURL Error: ' . $err;
				   } else {
					   // Se o cURL teve sucesso, loga a resposta da API
					   $log_retorno = $response;
				   }
	
				   // Prepara as strings para inserir no banco, escapando apóstrofos (')
				   // Isto é vital para evitar quebras na query SQL
				   $json_payload_sql = str_replace("'", "''", $json_payload);
				   $log_retorno_sql = str_replace("'", "''", $log_retorno);
	
				   // Monta e executa a query de log
				   $query_log = "INSERT INTO log_api_protocolo_finalizacao 
									 (protocolo_envio_id, json_envio, json_retorno)
								 VALUES 
									 ($id, '$json_payload_sql', '$log_retorno_sql')";
				   
				   // Usa a mesma conexão para executar o log
				   // Não tratamos o erro disto para não parar a finalização principal
				   $con->executar($query_log); 
				   
				   // --- FIM DO NOVO BLOCO DE LOG ---
				   
			   }
			   
			   //ALTERA O STATUS DO COMPROVANTE QUE ESTÁ FALTANDO
			   $query = "UPDATE comprovante_envio
						   SET status_id = 'n'
						   from comprovante_envio envio with (nolock)
						   where envio.protocolo_envio_id = $id
						   and envio.status_id = 'a'
						   and envio.chave_acesso not in (select recebimento.chave_acesso
														  from comprovante_recebimento recebimento with (nolock)
														  where recebimento.status_id = 'a'
														  and recebimento.protocolo_envio_id = $id
														  and recebimento.chave_acesso = envio.chave_acesso)";
			   //print $query;
			   $con->executar($query);
	
	
	
			   //FINALIZA O PROTOCOLO 
			   $query = "update protocolo_envio_comprovante
						 set status_id = 'f',
						 user_finalizacao = (select id from usuario where usuario = '$logado' and status = 'a'),
						 data_hora_finalizacao = getdate()
						 where id = $id
						 and status_id = 't'";
			   //print $query;
			   $con->executar($query);
	
	
			   //FINALIZA O MALOTE
			   $query = "update rastreabilidade_malote
						 set data_recebimento = getdate(),
						 recebedor_destino_id = (select id from usuario where usuario = '$logado' and status = 'a'),
						 status_id = 'f'
						 where id = (select malote_id from protocolo_envio_comprovante where id = $id)
						 and status_id <> 'f'";
			   //print $query;
			   $con->executar($query);
	
			   
	
				//BUSCA O ULTIMO MALOTE INSERIDO
				$query = "SELECT TOP 1 'MA-'+right('0000'+RTRIM(id+1),5) CODIGO, ID+1 
							  FROM rastreabilidade_malote
							  ORDER BY ID DESC";
				//print $query;
				$result = $con->executar($query);
				$codigo_malote   = odbc_result($result, 1);
				$malote_id   	 = odbc_result($result, 2);
	
	
				$query = "insert into rastreabilidade_malote (tipo_malote_id, data_postagem, recebedor_id, lacre, num_lacre, 
							  nome_expedidor, po_destino_id, observacao, status_id, codigo_malote)
							  VALUES (2, getdate(), (select id from usuario where usuario = '$logado' and status = 'a'), 
							  'n', ' ', (select nome from usuario where usuario = '$logado' and status = 'a'), 1, 
							  'Comprovantes - Protocolo: $id', 'P', '$codigo_malote')";
				//print $query;
				$con->executar($query);
	
				$link_malote = "<a href='http://$ip/rastreabilidade_malote/rastreabilidade_malote.php?cd=$malote_id' target='_blank'>$codigo_malote</a>";
	
				//FIM GERACAO MALOTE
	
				//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
				$query = "update protocolo_envio_comprovante
						  set status_id = 't',
						  user_geracao = (select id from usuario where usuario = '$logado' and status = 'a'),
						  data_hora_geracao = getdate(),
						  malote_id = case when '$malote_id' = '' then NULL else '$malote_id' end
						  where id = $id
						  and status_id = 'a'";
				//print $query;
				$con->executar($query);
	

            } else {


				$query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
				$result_user = $con->executar($query_user);

                // !ATENÇÃO: A variável $result_user não foi definida no seu código original.
                // Você precisa adicionar a query de usuário aqui, por exemplo:
                // $query_user = "select nome from usuario with (nolock) where usuario = '$logado' and status = 'a'";
                // $result_user = $con->executar($query_user);
                // Sem isso, o if(odbc_fetch_row($result_user)) abaixo pode falhar.
                // Assumindo que $result_user será corrigido:
                if (isset($result_user) && odbc_fetch_row($result_user)) {
                    $rec_nom = odbc_result($result_user, 1);
                }
                $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário

                // 2. Obter tipo de documento do protocolo
                $query_proto_info = "select tipo_documento_id 
                                     from protocolo_envio_comprovante with (nolock) 
                                     where id = $id";
                $result_proto_info = $con->executar($query_proto_info);
				$tipo_documento_id = 'C'; // default
				if (odbc_fetch_row($result_proto_info)) {
	  
					  // *** CORREÇÃO AQUI ***
					  // Primeiro, pegue o valor da coluna usando odbc_result()
					  $valor_do_banco = odbc_result($result_proto_info, 1);
	  
					  // Segundo, aplique o trim/strtoupper nesse valor
					  $tipo_documento_id = strtoupper(trim($valor_do_banco));
					  // *** FIM DA CORREÇÃO ***

					  var_dump($tipo_documento_id); 

	
				}
                // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
                $tip_doc_json = 'CTE';

                  // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
                    $query_docs = "select chave_acesso 
					from comprovante_envio_dig with (nolock)
					where protocolo_envio_id= $id
					and status_id='a'";
                    $result_docs = $con->executar($query_docs);
                    
                                    // CORREÇÃO 1: Mudar [] para array()
                    $inp_value_json = array(); 
                    $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
                    
                    while (odbc_fetch_row($result_docs)) {
                        $chave_doc = odbc_result($result_docs, 1);
                        
                                            // CORREÇÃO 2: Mudar [] para array()
                        $inp_value_json[] = array(
                            "TIPDOC" => $tip_doc_json,
                            "CHAVE" => $chave_doc,
                            "FILDOC" => "",
                            "SERDOC" => "",
                            "CODDOC" => "",
                            "CODOCO" => "100", // Código de ocorrência do exemplo
                            "CODOBS" => "",
                            "DATOCO" => $dat_oco,
							"RECNOM" => $rec_nom,
                            "OBSERV" => "OCORRÊNCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
                        );
                    }
                // 4. Montar e enviar a requisição cURL se houver documentos
                if (!empty($inp_value_json)) {
                    
                    // AJUSTE PARA PHP 5: Trocado [] por array()
                    $post_data = array(
                        "PROC" => "VRAPI_POST_RODOCH",
                        "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
                        "INP_VALUE_JSON" => $inp_value_json
                    );

                    $json_payload = json_encode($post_data); // Requer PHP 5.2+

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json_payload,
                        CURLOPT_HTTPHEADER => array(
							'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
							'Content-Type: application/json'
                        ),
                    ));

                    // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
                    
                    $response = curl_exec($curl);
                    $err = curl_error($curl); // Captura o erro, se houver
                    curl_close($curl);

                    // Prepara o que será salvo no log de retorno
                    $log_retorno = '';
                    if ($response === false) {
                        // Se o cURL falhou, loga a mensagem de erro
                        $log_retorno = 'cURL Error: ' . $err;
                    } else {
                        // Se o cURL teve sucesso, loga a resposta da API
                        $log_retorno = $response;
                    }

                    // Prepara as strings para inserir no banco, escapando apóstrofos (')
                    // Isto é vital para evitar quebras na query SQL
                    $json_payload_sql = str_replace("'", "''", $json_payload);
                    $log_retorno_sql = str_replace("'", "''", $log_retorno);

                    // Monta e executa a query de log
                    $query_log = "INSERT INTO log_api_protocolo_finalizacao 
                                      (protocolo_envio_id, json_envio, json_retorno)
                                  VALUES 
                                      ($id, '$json_payload_sql', '$log_retorno_sql')";
                    
                    // Usa a mesma conexão para executar o log
                    // Não tratamos o erro disto para não parar a finalização principal
                    $con->executar($query_log); 
                    
                    // --- FIM DO NOVO BLOCO DE LOG ---
                    
                }






                //FINALIZA O PROTOCOLO DIGITALIZADO
                $query = "update protocolo_envio_comprovante_dig
                          set status_id = 'f',
                          user_finalizacao = (select id from usuario where usuario = '$logado' and status = 'a'),
                          data_hora_finalizacao = getdate()
                          where id = $id
                          and status_id = 'a'";
                //print $query;
                $con->executar($query);
            }
        } else
            $ret_gravacao = "Sessão expirada, favor logar novamente";

        $retorno->ret_gravacao = $ret_gravacao;

        return $retorno;
    }








	public function finalizaProtocoloSts($id, $tela)
    {

        $con = new ConexaoSCI;

        $dados = new Dados;

        $logado = $_SESSION['usuario_logado'];

        $retorno = '';

        if ($logado != '') {
            if ($tela == 1) {


           $query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
                 $result_user = $con->executar($query_user);

                if (isset($result_user) && odbc_fetch_row($result_user)) {
                    $rec_nom = odbc_result($result_user, 1);
                }
                $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário

                // 2. Obter tipo de documento do protocolo
                $query_proto_info = "select tipo_documento_id 
                                     from protocolo_envio_comprovante_sts with (nolock) 
                                     where id = $id";
                $result_proto_info = $con->executar($query_proto_info);
            
				$tipo_documento_id = 'C'; // default
				if (odbc_fetch_row($result_proto_info)) {
	  
					  // *** CORREÇÃO AQUI ***
					  // Primeiro, pegue o valor da coluna usando odbc_result()
					  $valor_do_banco = odbc_result($result_proto_info, 1);
	  
					  // Segundo, aplique o trim/strtoupper nesse valor
					  $tipo_documento_id = strtoupper(trim($valor_do_banco));
					  // *** FIM DA CORREÇÃO ***
					  var_dump($tipo_documento_id); 
	
				}
                // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
                $tip_doc_json = 'CTE';
                  // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
                    $query_docs = "select chave_acesso 
                                   from comprovante_recebimento_sts with (nolock)
                                   where protocolo_envio_id = $id
                                   and status_id = 'a'";
                    $result_docs = $con->executar($query_docs);
                    
                                    // CORREÇÃO 1: Mudar [] para array()
                    $inp_value_json = array(); 
                    $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
                    
                    while (odbc_fetch_row($result_docs)) {
                        $chave_doc = odbc_result($result_docs, 1);
                        
                                            // CORREÇÃO 2: Mudar [] para array()
                        $inp_value_json[] = array(
                            "TIPDOC" => $tip_doc_json,
                            "CHAVE" => $chave_doc,
                            "FILDOC" => "",
                            "SERDOC" => "",
                            "CODDOC" => "",
                            "CODOCO" => "144", // Código de ocorrência do exemplo
                            "CODOBS" => "",
                            "DATOCO" => $dat_oco,
                            "RECNOM" => $rec_nom,
                            "OBSERV" => "OCORRENCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
                        );
                    }
                // 4. Montar e enviar a requisição cURL se houver documentos
                if (!empty($inp_value_json)) {
                    
                    // AJUSTE PARA PHP 5: Trocado [] por array()
                    $post_data = array(
                        "PROC" => "VRAPI_POST_RODOCH",
                        "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
                        "INP_VALUE_JSON" => $inp_value_json
                    );

                    $json_payload = json_encode($post_data); // Requer PHP 5.2+

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json_payload,
                        CURLOPT_HTTPHEADER => array(
							'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
							'Content-Type: application/json'
                        ),
                    ));

                    // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
                    
                    $response = curl_exec($curl);
                    $err = curl_error($curl); // Captura o erro, se houver
                    curl_close($curl);

                    // Prepara o que será salvo no log de retorno
                    $log_retorno = '';
                    if ($response === false) {
                        // Se o cURL falhou, loga a mensagem de erro
                        $log_retorno = 'cURL Error: ' . $err;
                    } else {
                        // Se o cURL teve sucesso, loga a resposta da API
                        $log_retorno = $response;
                    }

                    // Prepara as strings para inserir no banco, escapando apóstrofos (')
                    // Isto é vital para evitar quebras na query SQL
                    $json_payload_sql = str_replace("'", "''", $json_payload);
                    $log_retorno_sql = str_replace("'", "''", $log_retorno);

                    // Monta e executa a query de log
                    $query_log = "INSERT INTO log_api_protocolo_finalizacao 
                                      (protocolo_envio_id, json_envio, json_retorno)
                                  VALUES 
                                      ($id, '$json_payload_sql', '$log_retorno_sql')";
                    
                    // Usa a mesma conexão para executar o log
                    // Não tratamos o erro disto para não parar a finalização principal
                    $con->executar($query_log); 
                    
                    // --- FIM DO NOVO BLOCO DE LOG ---
                    
                }
                
                //ALTERA O STATUS DO COMPROVANTE QUE ESTÁ FALTANDO
                $query = "UPDATE comprovante_envio_sts
                            SET status_id = 'n'
                            from comprovante_envio_sts envio with (nolock)
                            where envio.protocolo_envio_id = $id
                            and envio.status_id = 'a'
                            and envio.chave_acesso not in (select recebimento.chave_acesso
                                                           from comprovante_recebimento_sts recebimento with (nolock)
                                                           where recebimento.status_id = 'a'
                                                           and recebimento.protocolo_envio_id = $id
                                                           and recebimento.chave_acesso = envio.chave_acesso)";
                //print $query;
                $con->executar($query);



                //FINALIZA O PROTOCOLO 
                $query = "update protocolo_envio_comprovante_sts
                          set status_id = 'f',
                          user_finalizacao = (select id from usuario where usuario = '$logado' and status = 'a'),
                          data_hora_finalizacao = getdate()
                          where id = $id
                          and status_id = 't'";
                //print $query;
                $con->executar($query);


                //FINALIZA O MALOTE
                $query = "update rastreabilidade_malote
                          set data_recebimento = getdate(),
                          recebedor_destino_id = (select id from usuario where usuario = '$logado' and status = 'a'),
                          status_id = 'f'
                          where id = (select malote_id from protocolo_envio_comprovante where id = $id)
                          and status_id <> 'f'";
                //print $query;
                $con->executar($query);
            }
			/*
			
			else {


				$query_user = "select usuario from usuario with (nolock) where usuario = '$logado' and status = 'a'";
				$result_user = $con->executar($query_user);

                // !ATENÇÃO: A variável $result_user não foi definida no seu código original.
                // Você precisa adicionar a query de usuário aqui, por exemplo:
                // $query_user = "select nome from usuario with (nolock) where usuario = '$logado' and status = 'a'";
                // $result_user = $con->executar($query_user);
                // Sem isso, o if(odbc_fetch_row($result_user)) abaixo pode falhar.
                // Assumindo que $result_user será corrigido:
                if (isset($result_user) && odbc_fetch_row($result_user)) {
                    $rec_nom = odbc_result($result_user, 1);
                }
                $rec_doc = "DOCTO RECEBEDOR"; // Valor padrão do exemplo do usuário

                // 2. Obter tipo de documento do protocolo
                $query_proto_info = "select tipo_documento_id 
                                     from protocolo_envio_comprovante_sts with (nolock) 
                                     where id = $id";
                $result_proto_info = $con->executar($query_proto_info);
				$tipo_documento_id = 'C'; // default
				if (odbc_fetch_row($result_proto_info)) {
	  
					  // *** CORREÇÃO AQUI ***
					  // Primeiro, pegue o valor da coluna usando odbc_result()
					  $valor_do_banco = odbc_result($result_proto_info, 1);
	  
					  // Segundo, aplique o trim/strtoupper nesse valor
					  $tipo_documento_id = strtoupper(trim($valor_do_banco));
					  // *** FIM DA CORREÇÃO ***

					  var_dump($tipo_documento_id); 

	
				}
                // Mapeia o tipo de documento do sistema para o esperado pela API (CTE ou OST)
                $tip_doc_json = 'CTE';

                  // 3. Obter todos os comprovantes recebidos (conferidos) com status 'a'
                    $query_docs = "select chave_acesso 
					from comprovante_envio_dig with (nolock)
					where protocolo_envio_id= $id
					and status_id='a'";
                    $result_docs = $con->executar($query_docs);
                    
                                    // CORREÇÃO 1: Mudar [] para array()
                    $inp_value_json = array(); 
                    $dat_oco = date('Y-m-d H:i'); // Timestamp da ocorrência (agora)
                    
                    while (odbc_fetch_row($result_docs)) {
                        $chave_doc = odbc_result($result_docs, 1);
                        
                                            // CORREÇÃO 2: Mudar [] para array()
                        $inp_value_json[] = array(
                            "TIPDOC" => $tip_doc_json,
                            "CHAVE" => $chave_doc,
                            "FILDOC" => "",
                            "SERDOC" => "",
                            "CODDOC" => "",
                            "CODOCO" => "100", // Código de ocorrência do exemplo
                            "CODOBS" => "",
                            "DATOCO" => $dat_oco,
							"RECNOM" => $rec_nom,
                            "OBSERV" => "OCORRÊNCIA LANÇADA VIA COMPROVANTE ENTREGA - SCI"
                        );
                    }
                // 4. Montar e enviar a requisição cURL se houver documentos
                if (!empty($inp_value_json)) {
                    
                    // AJUSTE PARA PHP 5: Trocado [] por array()
                    $post_data = array(
                        "PROC" => "VRAPI_POST_RODOCH",
                        "USUARIO" => "integracao_covre", // Do exemplo cURL do usuário
                        "INP_VALUE_JSON" => $inp_value_json
                    );

                    $json_payload = json_encode($post_data); // Requer PHP 5.2+

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'http://54.232.121.164:9505/api/consulta_wr',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30, // Definindo um timeout de 30s
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => $json_payload,
                        CURLOPT_HTTPHEADER => array(
							'token: aW50ZWdyYWNhb19jb3ZyZXxpbnRlZ3JhY2FvX2NvdnJlMTAx',
							'Content-Type: application/json'
                        ),
                    ));

                    // --- INÍCIO DO NOVO BLOCO DE LOG (Banco de Dados) ---
                    
                    $response = curl_exec($curl);
                    $err = curl_error($curl); // Captura o erro, se houver
                    curl_close($curl);

                    // Prepara o que será salvo no log de retorno
                    $log_retorno = '';
                    if ($response === false) {
                        // Se o cURL falhou, loga a mensagem de erro
                        $log_retorno = 'cURL Error: ' . $err;
                    } else {
                        // Se o cURL teve sucesso, loga a resposta da API
                        $log_retorno = $response;
                    }

                    // Prepara as strings para inserir no banco, escapando apóstrofos (')
                    // Isto é vital para evitar quebras na query SQL
                    $json_payload_sql = str_replace("'", "''", $json_payload);
                    $log_retorno_sql = str_replace("'", "''", $log_retorno);

                    // Monta e executa a query de log
                    $query_log = "INSERT INTO log_api_protocolo_finalizacao 
                                      (protocolo_envio_id, json_envio, json_retorno)
                                  VALUES 
                                      ($id, '$json_payload_sql', '$log_retorno_sql')";
                    
                    // Usa a mesma conexão para executar o log
                    // Não tratamos o erro disto para não parar a finalização principal
                    $con->executar($query_log); 
                    
                    // --- FIM DO NOVO BLOCO DE LOG ---
                    
                }






                //FINALIZA O PROTOCOLO DIGITALIZADO
                $query = "update protocolo_envio_comprovante_dig
                          set status_id = 'f',
                          user_finalizacao = (select id from usuario where usuario = '$logado' and status = 'a'),
                          data_hora_finalizacao = getdate()
                          where id = $id
                          and status_id = 'a'";
                //print $query;
                $con->executar($query);
            }*/
        } else
            $ret_gravacao = "Sessão expirada, favor logar novamente";

        $retorno->ret_gravacao = $ret_gravacao;

        return $retorno;
    }












































	public function gravaComprovanteRecebimento($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';



		if ($logado != '') {

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO DE RECEBIMENTO
			$query1 = "select TOP 1 
			tipo_documento_id,
			case when tipo_documento_id = 'C'
					then (select Docto_Transporte_Id
							from cargosol..cte with (nolock)
							where Chave_CTe = '$chave'
							and Tab_Status_Id <> 2)
					else (SELECT TOP 1 IDMOV
							FROM corpore..TMOV with (nolock)
							WHERE TMOV.CODFILIAL = SUBSTRING('$chave', 1,3) 
							AND TMOV.NUMEROMOV = SUBSTRING('$chave', 4,8)
							AND TMOV.IDMOV = SUBSTRING('$chave', 12,8)
							AND CODCOLIGADA = 1)
			end
			from protocolo_envio_comprovante protocolo with (nolock)
			where status_id = 't'
			AND CONVERT(DATE,protocolo.data_hora_gravacao) < '2024-08-12'
			and id = $protocolo_id
";
			//print $query1;
			$result1 = $con->executar($query1);
			$tipo_documento_id = odbc_result($result1, 1);
			$id_documento	   = odbc_result($result1, 2);

			if ($id_documento > 0) {
				$query = "insert into comprovante_recebimento (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  case when '$tipo_documento_id' = 'C' AND '$id_documento' <> '' then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' AND '$id_documento' <> '' then '$id_documento' else NULL end,					  			 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
						  WHERE '$chave' NOT IN (select top 1 chave_acesso
												  from comprovante_recebimento
												  where protocolo_envio_id = $protocolo_id
												  and chave_acesso = '$chave'
												  and status_id = 'a')
						  ";
				//print $query;
				$con->executar($query);

				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);

				$ret_gravacao = "1|ok|$chave_id";
			} else
				$ret_gravacao = "Documento não encontrado";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaComprovanteRecebimentoSts($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';



		if ($logado != '') {

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO DE RECEBIMENTO
			$query1 = "select TOP 1 
			tipo_documento_id,
			case when tipo_documento_id = 'C'
					then (select Docto_Transporte_Id
							from cargosol..cte with (nolock)
							where Chave_CTe = '$chave'
							and Tab_Status_Id <> 2)
					else (SELECT TOP 1 IDMOV
							FROM corpore..TMOV with (nolock)
							WHERE TMOV.CODFILIAL = SUBSTRING('$chave', 1,3) 
							AND TMOV.NUMEROMOV = SUBSTRING('$chave', 4,8)
							AND TMOV.IDMOV = SUBSTRING('$chave', 12,8)
							AND CODCOLIGADA = 1)
			end
			from protocolo_envio_comprovante_sts protocolo with (nolock)
			where status_id = 't'
			AND CONVERT(DATE,protocolo.data_hora_gravacao) < '2024-08-12'
			and id = $protocolo_id
";
			//print $query1;
			$result1 = $con->executar($query1);
			$tipo_documento_id = odbc_result($result1, 1);
			$id_documento	   = odbc_result($result1, 2);

			if ($id_documento > 0) {
				$query = "insert into comprovante_recebimento_sts (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  case when '$tipo_documento_id' = 'C' AND '$id_documento' <> '' then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' AND '$id_documento' <> '' then '$id_documento' else NULL end,					  			 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
						  WHERE '$chave' NOT IN (select top 1 chave_acesso
												  from comprovante_recebimento_sts
												  where protocolo_envio_id = $protocolo_id
												  and chave_acesso = '$chave'
												  and status_id = 'a')
						  ";
				//print $query;
				$con->executar($query);

				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);

				$ret_gravacao = "1|ok|$chave_id";
			} else
				$ret_gravacao = "Documento não encontrado";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}
















	public function gravaComprovanteRecebimentoRodopar($chave, $protocolo_id, $tipo_documento)
	{
		$con = new ConexaoSCI;
		$dados = new Dados;
		$logado = $_SESSION['usuario_logado'];
	
		if ($logado != '' && $protocolo_id != '') {
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 1 - LÓGICA DE BUSCA
			//
			// $doc_rodopar_lista é SEMPRE uma lista [doc1, doc2, ...]
			$doc_rodopar_lista = array(); // Inicializa como array vazio
	
			if ($tipo_documento == 'C') {
				$doc_rodopar_lista = $dados->buscaDacumentosAPI($chave);
			} else {
				$filial = substr($chave, 0, 2);
				$serie = substr($chave, 2, 3);
				$doc = substr($chave, 5, 6);
				$doc_rodopar_lista = $dados->buscaDacumentosAPI('', (int)$filial, $serie, (int)$doc);
			}
	
			// Pega o PRIMEIRO documento da lista.
			// Se a lista estiver vazia, $documentoFinal será null.
			$documentoFinal = null;
			if (is_array($doc_rodopar_lista) && count($doc_rodopar_lista) > 0) {
				$documentoFinal = $doc_rodopar_lista[0]; // Pega o primeiro item
			}
			// =================================================================
	
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 2 - VERIFICAÇÃO DE SUCESSO
			//
			// Agora verificamos a variável $documentoFinal
			if (is_array($documentoFinal) && isset($documentoFinal['Numero']) && $documentoFinal['Numero'] > 0) {
			// =================================================================
	
				$retorno = '';
				// Usa $documentoFinal aqui
				$protocolo_ = $this->validaComprovanteDuplicado($documentoFinal['Chave'], $protocolo_id);
	
				// =================================================================
				// INÍCIO DA CORREÇÃO 3 - QUERY (ASPAS E INJECTION)
				//
				// Removemos aspas de 'Numero' e 'Filial' (são int)
				// Adicionado str_replace para 'Cliente' (evitar erro com aspas no nome)
				$cliente_seguro = str_replace("'", "''", $documentoFinal['Cliente']);
				
				$query = "insert into comprovante_recebimento (protocolo_envio_id, chave_acesso, 
								user_gravacao, data_hora_gravacao, status_id, r_doc, r_serie, r_fil, r_cliente, r_data_emissao) 
								SELECT $protocolo_id, '$chave', 
								(select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
								," . $documentoFinal['Numero'] . ",'" . $documentoFinal['Serie'] . "'," . $documentoFinal['Filial'] . "
								,'" . $cliente_seguro . "', convert(datetime, '" . $documentoFinal['Emissao'] . "',103)
								WHERE '$chave' NOT IN (select top 1 chave_acesso
														from comprovante_recebimento
														where protocolo_envio_id = $protocolo_id
														and chave_acesso = '$chave'
														and status_id = 'a')
								";
				// =================================================================
	
				$con->executar($query);
	
				$query = "SELECT @@IDENTITY AS Ident";
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);
	
				if ($protocolo_ != '0' && $protocolo_ != $protocolo_id) {
					$ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
				} else {
					$ret_gravacao = "1|ok|$chave_id";
				}
			} else {
				$ret_gravacao = "Documento não encontrado na API"; // Mensagem mais clara
			}
		} else {
			$ret_gravacao = "Sessão expirada, favor logar novamente";
		}
	
		$retorno->ret_gravacao = $ret_gravacao;
	
		return $retorno;
	}



	public function gravaComprovanteRecebimentoRodoparSts($chave, $protocolo_id, $tipo_documento)
	{
		$con = new ConexaoSCI;
		$dados = new Dados;
		$logado = $_SESSION['usuario_logado'];
	
		if ($logado != '' && $protocolo_id != '') {
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 1 - LÓGICA DE BUSCA
			//
			// $doc_rodopar_lista é SEMPRE uma lista [doc1, doc2, ...]
			$doc_rodopar_lista = array(); // Inicializa como array vazio
	
			if ($tipo_documento == 'C') {
				$doc_rodopar_lista = $dados->buscaDacumentosAPI($chave);
			} else {
				$filial = substr($chave, 0, 2);
				$serie = substr($chave, 2, 3);
				$doc = substr($chave, 5, 6);
				$doc_rodopar_lista = $dados->buscaDacumentosAPI('', (int)$filial, $serie, (int)$doc);
			}
	
			// Pega o PRIMEIRO documento da lista.
			// Se a lista estiver vazia, $documentoFinal será null.
			$documentoFinal = null;
			if (is_array($doc_rodopar_lista) && count($doc_rodopar_lista) > 0) {
				$documentoFinal = $doc_rodopar_lista[0]; // Pega o primeiro item
			}
			// =================================================================
	
	
			// =================================================================
			// INÍCIO DA CORREÇÃO 2 - VERIFICAÇÃO DE SUCESSO
			//
			// Agora verificamos a variável $documentoFinal
			if (is_array($documentoFinal) && isset($documentoFinal['Numero']) && $documentoFinal['Numero'] > 0) {
			// =================================================================
	
				$retorno = '';
				// Usa $documentoFinal aqui
				$protocolo_ = $this->validaComprovanteDuplicadoSts($documentoFinal['Chave'], $protocolo_id);
	
				// =================================================================
				// INÍCIO DA CORREÇÃO 3 - QUERY (ASPAS E INJECTION)
				//
				// Removemos aspas de 'Numero' e 'Filial' (são int)
				// Adicionado str_replace para 'Cliente' (evitar erro com aspas no nome)
				$cliente_seguro = str_replace("'", "''", $documentoFinal['Cliente']);
				
				$query = "insert into comprovante_recebimento_sts (protocolo_envio_id, chave_acesso, 
								user_gravacao, data_hora_gravacao, status_id, r_doc, r_serie, r_fil, r_cliente, r_data_emissao) 
								SELECT $protocolo_id, '$chave', 
								(select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
								," . $documentoFinal['Numero'] . ",'" . $documentoFinal['Serie'] . "'," . $documentoFinal['Filial'] . "
								,'" . $cliente_seguro . "', convert(datetime, '" . $documentoFinal['Emissao'] . "',103)
								WHERE '$chave' NOT IN (select top 1 chave_acesso
														from comprovante_recebimento_sts
														where protocolo_envio_id = $protocolo_id
														and chave_acesso = '$chave'
														and status_id = 'a')
								";
				// =================================================================
	
				$con->executar($query);
	
				$query = "SELECT @@IDENTITY AS Ident";
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);
	
				if ($protocolo_ != '0' && $protocolo_ != $protocolo_id) {
					$ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
				} else {
					$ret_gravacao = "1|ok|$chave_id";
				}
			} else {
				$ret_gravacao = "Documento não encontrado na API"; // Mensagem mais clara
			}
		} else {
			$ret_gravacao = "Sessão expirada, favor logar novamente";
		}
	
		$retorno->ret_gravacao = $ret_gravacao;
	
		return $retorno;
	}























	public function geraProtocoloMais($protocolo_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {
			//INSERE NOVO PROTOCOLO
			$query = "insert into protocolo_envio_comprovante (ponto_operacao_id, tipo_documento_id, user_gravacao, 
					  data_hora_gravacao, user_finalizacao, data_hora_finalizacao, status_id)
					  select colaborador.Ponto_Operacao_Id, tipo_documento_id, usuario.id, getdate(), usuario.id, getdate(), 'f'
						from protocolo_envio_comprovante protocolo with (nolock)
						join usuario with (nolock) on
							usuario.id = (select id from usuario where usuario = '$logado' and status = 'a')
						join CARGOSOL..colaborador with (nolock) on
							colaborador.Pessoa_Id = usuario.pessoa_id
						where protocolo.id = $protocolo_id";
			//print $query;
			$con->executar($query);


			$query = "SELECT @@IDENTITY AS Ident";
			//print $query;
			$result = $con->executar($query);
			$id = odbc_result($result, 1);

			//INSERE NOVOS COMPROVANTES ENVIO
			$query = "insert into comprovante_envio (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, user_gravacao, 
					  data_hora_gravacao, status_id) 
					  SELECT $id, chave_acesso, docto_transporte_id, idmov, 
					  (select id from usuario where usuario = '$logado' and status = 'a'), getdate(), 'a'
					  from comprovante_recebimento comprovante with (nolock)
					  where comprovante.status_id = 'a'
					  and comprovante.protocolo_envio_id = $protocolo_id
					  and comprovante.chave_acesso not in (select comprovante_envio.chave_acesso
														   from comprovante_envio with (nolock)
														   where comprovante_envio.chave_acesso = comprovante.chave_acesso
														   and comprovante_envio.status_id = 'a'
														   and comprovante_envio.protocolo_envio_id = 
														   comprovante.protocolo_envio_id)";
			//print $query;
			$con->executar($query);


			//INSERE NOVOS COMPROVANTES RECEBIMENTO
			$query = "insert into comprovante_recebimento (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
					  user_gravacao, data_hora_gravacao, status_id) 
					  SELECT $id, chave_acesso, docto_transporte_id, idmov, 
					  (select id from usuario where usuario = '$logado' and status = 'a'), getdate(), 'a'
					  from comprovante_recebimento comprovante with (nolock)
					  where comprovante.status_id = 'a'
					  and comprovante.protocolo_envio_id = $protocolo_id
					  and comprovante.chave_acesso not in (select comprovante_envio.chave_acesso
														   from comprovante_envio with (nolock)
														   where comprovante_envio.chave_acesso = comprovante.chave_acesso
														   and comprovante_envio.status_id = 'a'
														   and comprovante_envio.protocolo_envio_id = 
														   comprovante.protocolo_envio_id)";
			//print $query;
			$con->executar($query);


			//ALTERA STATUS COMPROVANTES RECEBIDOS A MAIS NO LOTE ANTERIOR
			$query = "update comprovante_recebimento
						set status_id = 'm'
						from comprovante_recebimento comprovante_anterior with (nolock)
						where comprovante_anterior.status_id = 'a'
						and comprovante_anterior.protocolo_envio_id = $protocolo_id
						and chave_acesso in (select chave_acesso
											 from comprovante_recebimento with (nolock)
											 where comprovante_recebimento.protocolo_envio_id = $id
											 and comprovante_recebimento.status_id = 'a')";
			//print $query;
			$con->executar($query);

			$ret_gravacao = "1|Ok|$id";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function gravaProtocoloEnvioDig($id, $tipo_documento_id, $status_id, $sistema)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			if ($id == '') {

				$query = "insert into protocolo_envio_comprovante_dig (tipo_documento_id, user_gravacao, data_hora_gravacao, 
						  status_id,sistema) values ('$tipo_documento_id', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 
						  '$status_id',$sistema)";
				//print $query;
				$con->executar($query);


				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$id = odbc_result($result, 1);

				$ret_gravacao = "1|ok|$id";
			} else {
				$query = "update protocolo_envio_comprovante_dig
						  set tipo_documento_id = '$tipo_documento_id',
						  status_id = '$status_id',
						  data_hora_inativacao = case when '$status_id' = 'a' then null else getdate() end,
						  user_inativacao = case when '$status_id' = 'a' then null else (select id 
																						 from usuario with (nolock)
																						 where usuario = '$logado' 
																						 and status = 'a') end,
							sistema = $sistema
						  where id = $id";
				//print $query;
				$con->executar($query);


				$ret_gravacao = "2|ok|$id";
			}
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}


	public function verificaDigitalizadoDuplicidade($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$query = "SELECT 
						ISNULL(PROTOCOLO_ENVIO_ID, '0') PROTOCOLO_ENVIO_ID
					FROM COMPROVANTE_ENVIO_DIG 
					WHERE CHAVE_ACESSO = '" . $chave . "'
					AND COMPROVANTE_ENVIO_DIG.PROTOCOLO_ENVIO_ID = $protocolo_id
					AND STATUS_ID = 'A'";

		$result 			= $con->executar($query);
		$protocolo_envio_id = odbc_result($result, 1);

		return $protocolo_envio_id;
	}

	public function verificaDigitalizadoDuplicidadeRodopar($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$query = "SELECT 
						ISNULL(PROTOCOLO_ENVIO_ID, '0') PROTOCOLO_ENVIO_ID
					FROM COMPROVANTE_ENVIO_DIG 
					WHERE CHAVE_ACESSO = '" . $chave . "'
					AND COMPROVANTE_ENVIO_DIG.PROTOCOLO_ENVIO_ID = $protocolo_id
					AND STATUS_ID = 'A'";
		//return $query;
		$result 			= $con->executar($query);
		$protocolo_envio_id = odbc_result($result, 1);

		return $protocolo_envio_id;
	}

	public function gravaComprovanteEnvioDig($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		$chave_id = '';

		// if($protocolo_ != '0' && ($id_protocolo_ != $protocolo_)){
		// 	$retorno->ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: ".$protocolo_;
		// 	return $retorno;
		// } else if($id_protocolo_ == $protocolo_){
		// 	$retorno->ret_gravacao = 'ok';
		// 	return $retorno;
		// } 


		if (substr($chave, 0, 3) == '000')
			$chave = substr($chave, 1);


		if ($logado != '') {

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
			$query = "select distinct top 1 
						tipo_documento_id,
						case when tipo_documento_id = 'C'
								then (select Docto_Transporte_Id
										from cargosol..cte with (nolock)
										where Chave_CTe = '$chave'
										and Tab_Status_Id <> 2)
								else (SELECT TOP 1 IDMOV
										FROM corpore..TMOV with (nolock)
										WHERE TMOV.CODFILIAL = SUBSTRING('$chave', 1,3) 
										AND TMOV.NUMEROMOV = SUBSTRING('$chave', 4,8)
										AND TMOV.IDMOV = SUBSTRING('$chave', 12,8)
										AND CODCOLIGADA = 1)
						end,
						protocolo.id,
						chave_acesso
						from protocolo_envio_comprovante_dig protocolo with (nolock)
						left join comprovante_envio_dig comprovante with (nolock) on
							comprovante.protocolo_envio_id = protocolo.id
							and comprovante.chave_acesso = '$chave'
							and comprovante.status_id = 'a'
						where protocolo.status_id = 'a'
						and protocolo.id =  $protocolo_id";
			//print $query;
			$result = $con->executar($query);
			$tipo_documento_id = odbc_result($result, 1);
			$id_documento	   = odbc_result($result, 2);
			$id_protocolo_	   = odbc_result($result, 3);
			$chave_encontrada  = odbc_result($result, 4);

			if ($id_documento > 0) {

				$protocolo = $this->verificaDigitalizadoDuplicidade($chave, $protocolo_id);

				$query = "insert into comprovante_envio_dig (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  case when '$tipo_documento_id' = 'C' then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' then '$id_documento' else NULL end,					  			 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
						  WHERE '$chave' NOT IN (select top 1 chave_acesso
												  from comprovante_envio_dig with (nolock)
												  where protocolo_envio_id = $protocolo_id
												  and chave_acesso = '$chave'
												  and status_id = 'a')
						  ";
				//print $query;
				$con->executar($query);

				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);
				$chave_id = odbc_result($result, 1);

				if (($protocolo != '0') && ($id_protocolo_ == $protocolo)) {
					$ret_gravacao = 'Atenção, a chave inserida ja foi enviada para digitalização no protocolo:' . $protocolo;
				} else {
					$ret_gravacao = "1|ok|$chave_id";
				}
				//else
				//$ret_gravacao = "Chave já inserida no protocolo";


			} else
				$ret_gravacao = "Documento não encontrado";
		} else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}



	public function gravaComprovanteEnvioDigRodopar($chave, $protocolo_id, $tipo_documento)
{
    $con = new ConexaoSCI;
    $dados = new Dados;
    $logado = $_SESSION['usuario_logado'];
    $ret_gravacao = "";
    $chave_id = '';

    if ($logado != '' && $protocolo_id != '') {

        // ... (lógica de validação duplicada está comentada) ...

        $tp_doc = "";
        if ($tipo_documento == 'C') {
            $doc_rodopar = $dados->buscaDacumentosAPI($chave);
            $tp_doc = "CTE";
        } else {
            $filial = substr($chave, 0, 2);
            $serie = substr($chave, 2, 3);
            $doc = substr($chave, 5, 6);

            $doc_rodopar = $dados->buscaDacumentosAPI('', (int)$filial, $serie, (int)$doc);
            $tp_doc = "OST";
        }

        $documentoFinal = null;

        // =================================================================
        // INÍCIO DA CORREÇÃO 1 - LÓGICA DE BUSCA
        //
        // $doc_rodopar é SEMPRE uma lista [doc1, doc2, ...]
        // Iteramos para achar o $tp_doc correto.
        
        if (is_array($doc_rodopar) && count($doc_rodopar) > 0) {
            foreach ($doc_rodopar as $doc) {
                // $doc é o documento, não tem mais ['Documento']
                // Verificamos se $doc é um array e se o 'Tipo' bate
                if (is_array($doc) && isset($doc['Tipo']) && $doc['Tipo'] === $tp_doc) { 
                    $documentoFinal = $doc; // $doc é o documento
                    break; // Achamos o que queríamos
                }
            }
        }
        // Se a lista estava vazia ou o tipo não bateu, $documentoFinal continua null
        // =================================================================


        // =================================================================
        // INÍCIO DA CORREÇÃO 2 - VERIFICAÇÃO DE SUCESSO
        //
        // Verificamos se $documentoFinal FOI encontrado (não é null)
        if (is_array($documentoFinal) && $documentoFinal['Numero'] > 0) {
        // =================================================================
            
            $retorno = '';
            $protocolo_ = $this->validaComprovanteDuplicadoDig($documentoFinal['Chave'], $protocolo_id);

            if ($protocolo_ != '0' && $protocolo_ != $protocolo_id) {
                $ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
            }

            // =================================================================
            // INÍCIO DA CORREÇÃO 3 - QUERY (ASPAS E SQL INJECTION)
            //
            // Removemos aspas de 'Numero' e 'Filial' (são int)
            // Adicionado str_replace para 'Cliente' (evitar erro com aspas no nome)
            $cliente_seguro = str_replace("'", "''", $documentoFinal['Cliente']);
            
            $query = "insert into comprovante_envio_dig (protocolo_envio_id, chave_acesso, 
                        user_gravacao, data_hora_gravacao, status_id, r_doc, r_serie, r_fil, r_cliente, r_data_emissao) 
                        SELECT $protocolo_id, '$chave', 
                        (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
                        ," . $documentoFinal['Numero'] . ",'" . $documentoFinal['Serie'] . "'," . $documentoFinal['Filial'] . "
                        ,'" . $cliente_seguro . "', convert(datetime, '" . $documentoFinal['Emissao'] . "',103)
                        WHERE '$chave' NOT IN (select top 1 chave_acesso
                                                from comprovante_envio_dig
                                                where protocolo_envio_id = $protocolo_id
                                                and chave_acesso = '$chave'
                                                and status_id = 'a')
                        ";
            // =================================================================
            
            $con->executar($query);

            $query = "SELECT @@IDENTITY AS Ident";
            $result = $con->executar($query);
            $chave_id = odbc_result($result, 1);

            if ($ret_gravacao == '') {
                $ret_gravacao = "1|ok|$chave_id";
            }
        } else {
            $ret_gravacao = "Documento não encontrado na API"; // Mensagem mais clara
        }
    } else {
        $ret_gravacao = "Sessão expirada, favor logar novamente";
    }

    $retorno->ret_gravacao = $ret_gravacao;

    return $retorno;
}
}