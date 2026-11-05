<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";

class GravaDados extends ConexaoSCI
{


	public function gravaProtocoloEnvio($id, $ponto_operacao_id, $tipo_documento_id, $status_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			if ($id == '') {

				$query = "insert into protocolo_envio_comprovante (ponto_operacao_id, tipo_documento_id, user_gravacao, 
						  data_hora_gravacao, status_id) values ($ponto_operacao_id, '$tipo_documento_id', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 
						  '$status_id')";
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
					WHERE CHAVE_ACESSO = '" . $chave . "'";

		$result 		   = $con->executar($query);

		$id_protocolo = odbc_result($result, 1);
		return $id_protocolo;
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
						id
						from protocolo_envio_comprovante protocolo with (nolock)
						where status_id = 'a'
						and id = $protocolo_id";
			//print $query;
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
						  case when '$tipo_documento_id' = 'C' then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' then '$id_documento' else NULL end,					  			 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a'
						  WHERE '$chave' NOT IN (select top 1 chave_acesso
												  from comprovante_envio
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

				//var_dump($protocolo_.' - '.$id_protocolo_.$protocolo_);


				if ($protocolo_ != '0' && ($id_protocolo_ != $protocolo_)) {
					$ret_gravacao = "Atenção, esse documento ja foi vinculado ao protocolo: " . $protocolo_;
				} else {
					//					$ret_gravacao = "1|ok|$chave_id";
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


	public function finalizaProtocolo($id, $tela)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';

		if ($logado != '') {
			if ($tela == 1) {

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
			} else {
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
						and id = $protocolo_id";
			//print $query1;
			$result1 = $con->executar($query1);
			$tipo_documento_id = odbc_result($result1, 1);
			$id_documento	   = odbc_result($result1, 2);

			if ($id_documento > 0) {
				$query = "insert into comprovante_recebimento (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  case when '$tipo_documento_id' = 'C' then '$id_documento' else NULL end,
						  case when '$tipo_documento_id' = 'F' then '$id_documento' else NULL end,					  			 
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


	public function gravaProtocoloEnvioDig($id, $tipo_documento_id, $status_id)
	{

		$con = new ConexaoSCI;

		$dados = new Dados;

		$logado = $_SESSION['usuario_logado'];

		$retorno = '';


		if ($logado != '') {

			if ($id == '') {

				$query = "insert into protocolo_envio_comprovante_dig (tipo_documento_id, user_gravacao, data_hora_gravacao, 
						  status_id) values ('$tipo_documento_id', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 
						  '$status_id')";
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


	public function verificaDigitalizadoDuplicidade($chave, $protocolo_id)
	{

		$con = new ConexaoSCI;

		$query = "SELECT 
						ISNULL(PROTOCOLO_ENVIO_ID, '0') PROTOCOLO_ENVIO_ID
					FROM COMPROVANTE_ENVIO_DIG 
					WHERE CHAVE_ACESSO = '" . $chave . "'
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
			$query = "select distinct
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

				//var_dump($protocolo.' - '.$id_protocolo_); exit;

				if (($protocolo != '0') && ($id_protocolo_ != $protocolo)) {
					$ret_gravacao = 'Atenção, a chave inserida ja foi enviada para digitalização no protocolo:' . $protocolo;
				} else
				//if (($chave_encontrada == '') || ($chave_id != ''))
				{ //($id_protocolo_ == $protocolo_){
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

	public function gravaComprovanteEnvioDigRodopar($chave, $protocolo_id)
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


			$protocolo = $this->verificaDigitalizadoDuplicidadeRodopar($chave, $protocolo_id);
			//return $protocolo;
			if (($protocolo != '0' and $protocolo != '')) {
				return  'Atenção, a chave inserida ja foi enviada para digitalização no protocolo:' . $protocolo;
			}


			$query = "insert into comprovante_envio_dig (protocolo_envio_id, chave_acesso, docto_transporte_id, idmov, 
						  user_gravacao, data_hora_gravacao, status_id) 
						  SELECT $protocolo_id, '$chave',
						  null,null,				  			 
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

			//var_dump($protocolo.' - '.$id_protocolo_); exit;

			return  "1|ok|$chave_id";

			return $retorno;
		} else
			return  "Sessão expirada, favor logar novamente";



		return $retorno;
	}
}
