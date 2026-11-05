<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php";


class Dados extends ConexaoSCI
{


	//BUSCA DADOS
	public function exibeComprovanteEnvio($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end	PO,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id, 
					case when ressalva is not null
							then 'checked'
							else NULL
					end ressalva,
					protocolo.status_id,
					comprovante.qtde_ressalva,
					case when protocolo.ressalva_todos is not null
							then 'checked'
							else null
					end ressalva_todos
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					where comprovante.status_id = 'a'
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id, 
					case when ressalva is not null
							then 'checked'
							else NULL
					end ressalva,
					protocolo.status_id,
					comprovante.qtde_ressalva,
					case when protocolo.ressalva_todos is not null
							then 'checked'
							else null
					end ressalva_todos
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join corpore..TMOV WITH (NOLOCK) on
						TMOV.IDMOV = comprovante.idmov
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					where comprovante.status_id = 'a'
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);
		$result1 = $con->executar($query);

		$status_prot		= odbc_result($result1, 8);
		$checked_todos		= odbc_result($result1, 10);

		if ($status_prot == 'a')
			$status_t = '';
		else
			$status_t = 'disabled';

		$retorno = '';
		$arq_xml = '';
		$aux = '';

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DOCUMENTO</a></th>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DATA EMISSÃO</a></th>	
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>PONTO OPERAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='400'><a href='javascript:void(0)'>CLIENTE</a></th>
						<th bgcolor='#d3d3d3' width='70'><a href='javascript:void(0)'><input type='checkbox' name='ressalvatodos' id='ressalvatodos' class='txt-center ressalva_t' aria-describedby='basic-addon1' $checked_todos value='1' $status_t onclick='javascript:ressalva_todos($protocolo_id,1,1)'/>RESSALVA</a></th>
						<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$checked				= odbc_result($result, 7);
			$status_protocolo		= odbc_result($result, 8);
			$qtde_ressalva			= odbc_result($result, 9);


			if ($checked != '')
				$disabled_qtde = '';
			else
				$disabled_qtde = 'disabled';

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $documento . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $cliente . "</font></center></td>";

			if ($status_protocolo == 'a') {
				$aux = 'S';

				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>
						<input type='checkbox' name='ressalva$docto_transporte_id' id='ressalva$docto_transporte_id' class='txt-center classe_checkbox' aria-describedby='basic-addon1' $checked value='1' onclick='javascript:grava_ressalva(this,$id,1,$protocolo_id)'/>
						
						<input type='text' name='qtde_ressalva$id' id='qtde_ressalva$id' class='txt-center classe_campo'  aria-describedby='basic-addon1' $checked value='$qtde_ressalva' onblur='javascript:grava_qtde(this,$id,1)' maxlength='5' size='1' $disabled_qtde/>
						
					</td>
				<td><center><font size='-1'><a href='javascript:exclui_comprovante($id,$protocolo_id,1);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center>
				
				</td>";
			} else {
				if ($status_protocolo == 'i')
					$aux = 'C';

				if ($checked != '')
					$table .= "<td><center><font size='-1'>$qtde_ressalva</font></center></td>";
				else
					$table .= "<td><center><font size='-1'>&nbsp;</font></center></td>";

				$table .= "<td><center><font size='-1'> - </font></center></td>";
			}
			$table .= "</tr>";
		} //fim while 

		$table .= "</table>";

		$table .= "<input type='hidden' name='aux' id='aux' maxlength='5' size='1' value='$aux'/>";

		$retorno->comprovante = $table;

		return $retorno;
	}



	public function exibePo($tipo_documento_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		if (($tipo_documento_id == 'C') || ($tipo_documento_id == 'CTE')) //E-Cargo
		{
			$query = "select po.ponto_operacao_id, pessoa.nome_fantasia
						from CARGOSOL..ponto_operacao po (nolock)
						join cargosol..pessoa (nolock) on
							pessoa.pessoa_id = po.pessoa_id
							and pessoa.tab_status_id = 1
						where po.tab_status_id = 1
						and po.ponto_operacao_id <> 32
						order by pessoa.nome_fantasia";
			//print $query;
			$result = $con->executar($query);
		} else {
			$query = "select CODFILIAL, NOMEFANTASIA collate sql_latin1_general_cp1251_ci_as
					  from corpore..GFILIAL with (nolock)
					  where codcoligada = 1
					  AND CODFILIAL IN (1,2,3,5,11,15)
					  ORDER BY NOMEFANTASIA";
			//print $query;
			$result = $con->executar($query);
		}

		$retorno = '';

		$select = "<option value=''></option>";

		while (odbc_fetch_row($result)) {
			$tabela_id		= odbc_result($result, 1);
			$nome_tabela	= odbc_result($result, 2);

			$select .= "<option value='" . $tabela_id . "'>" . $nome_tabela . "</option>";
		}

		$retorno->relatorio = $select;

		return $retorno;
	}


	public function pesquisaProtocolo($num_protocolo, $tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];

		if ($num_protocolo != '')
			$condicao_protocolo = "and protocolo.id = $num_protocolo";
		else
			$condicao_protocolo = "";

		if ($tipo_documento_id != '') {
			if ($tipo_documento_id == 'C')
				$condicao_tipo = "join comprovante_envio comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										and comprovante.status_id = 'a'	
								  join cargosol..docto_transporte dt with (nolock) on
										dt.docto_transporte_id = comprovante.docto_transporte_id
										and dt.tab_status_id <> 2
								  join cargosol..cte with (nolock) on
										cte.docto_transporte_id = dt.docto_transporte_id
										and cte.numero = '$num_documento'";
			else
				$condicao_tipo = "join comprovante_envio comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										and comprovante.status_id = 'a'	
								  join corpore..TMOV WITH (NOLOCK) on
										TMOV.IDMOV = comprovante.idmov
										AND TMOV.NUMEROMOV = '$num_documento'";
		} else
			$condicao_tipo = "";


		if ($status_id != '')
			$condicao_status = "and protocolo.status_id = '$status_id'";
		else
			$condicao_status = "";


		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = "and protocolo.data_hora_gravacao >= '$data_inicial' and protocolo.data_hora_gravacao < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";


		$query = "select protocolo.id num_protocolo, 
					convert(varchar(10), protocolo.data_hora_gravacao, 103)+' '+convert(varchar(5), protocolo.data_hora_gravacao, 108) data_gravacao,
					case when tipo_documento_id = 'C'
							then pessoa.nome_fantasia
							else case protocolo.ponto_operacao_id
									when 1 then 'MATRIZ'
									when 2 then 'FILIAL - OSASCO'
									when 3 THEN 'FILIAL - SANTOS'
									when 5 THEN 'FILIAL - GUAXUPE'									
									when 11 then 'FILIAL - CAMPINAS'
									when 15 THEN 'FILIAL - APARECIDA DE GOIANIA'
								 END
					end ponto_operacao,
					case when tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_docto,
					usuario.nome usuario,
					case protocolo.status_id
						when 'a' then 'ATIVO'
						when 'f' then 'FINALIZADO'
						when 'i' then 'CANCELADO'
						when 't' then 'EM TRANSITO'
					end status,
					protocolo.status_id
					from protocolo_envio_comprovante protocolo with (nolock)
					join cargosol..ponto_operacao po with (nolock) on
						po.ponto_operacao_id = protocolo.ponto_operacao_id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = po.pessoa_id
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao
					
					$condicao_tipo
					
					where 1 = 1
					$condicao_protocolo
					$condicao_status
					$condicao_datas";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='150'>PONTO OPERAÇÃO</th>
						<th bgcolor='#d3d3d3' width='400'>TIPO DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='70'>USUÁRIO</th>
						<th bgcolor='#d3d3d3' width='70'>STATUS</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$num_protocolo			= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$tipo_docto				= odbc_result($result, 4);
			$usuario				= odbc_result($result, 5);
			$status					= odbc_result($result, 6);
			$status_id				= odbc_result($result, 7);

			if ($status_id == 'f')
				$pagina = 'recebimento_comprovante.php';
			else
				$pagina = 'envio_comprovante.php';

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>
						<a href='http://$ip/comprovante_entrega/$pagina?id=$num_protocolo' target='_blank'>" . $num_protocolo . "</a></font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $usuario . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->relatorio = $table;

		return $retorno;
	}



	public function imprimeComprovanteEnvio($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end PO,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id, 
					case when ressalva is not null
							then qtde_ressalva
							else NULL
					end ressalva,
					protocolo.id num_protocolo,
					case when protocolo.tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_documento,
					pessoa_protocolo.Nome_Fantasia P_OPERACAO,
					protocolo.status_id,
					protocolo.tipo_documento_id
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					join cargosol..ponto_operacao po_protocolo with (nolock) on
						po_protocolo.ponto_operacao_id = protocolo.Ponto_Operacao_Id					
					join cargosol..pessoa pessoa_protocolo with (nolock) on
						pessoa_protocolo.pessoa_id = po_protocolo.PESSOA_ID
					where comprovante.status_id = 'a'
					and protocolo.status_id in ('t','f')
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'						
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id, 
					case when ressalva is not null
							then qtde_ressalva
							else NULL
					end ressalva,
					protocolo.id,
					case when protocolo.tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_documento,
					GFILIAL.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS,
					protocolo.status_id,
					protocolo.tipo_documento_id
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join corpore..TMOV WITH (NOLOCK) on
						TMOV.IDMOV = comprovante.idmov
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					JOIN corpore..GFILIAL with (nolock) ON
						GFILIAL.CODFILIAL = PROTOCOLO.ponto_operacao_id
						AND GFILIAL.CODCOLIGADA = 1
					where comprovante.status_id = 'a'
					and protocolo.status_id in ('t','f')
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);
		$result_prot = $con->executar($query);


		$num_protocolo			= odbc_result($result_prot, 8);
		$desc_tipo_documento	= odbc_result($result_prot, 9);
		$desc_ponto_operacao	= odbc_result($result_prot, 10);
		$status_protocolo		= odbc_result($result_prot, 11);
		$tipo_documento_id		= odbc_result($result_prot, 12);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table border='1'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><center><font size='-1'><b>DOCUMENTO</font></td>
						<td bgcolor='#d3d3d3' width='120'><center><font size='-1'><b>DATA EMISSÃO</font></td>	
						<td bgcolor='#d3d3d3' width='150'><center><font size='-1'><b>PONTO OPERAÇÃO</font></td>
						<td bgcolor='#d3d3d3' width='400'><center><font size='-1'><b>CLIENTE</font></td>
						<td bgcolor='#d3d3d3' width='70'><center><font size='-1'><b>&nbsp;RESSALVA&nbsp;</font></td>
					</tr>
				  ";


		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$ressalva				= odbc_result($result, 7);

			$table .= "
				  <tr>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $documento . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $cliente . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $ressalva . "</font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->num_protocolo = $num_protocolo;
		$retorno->tipo_documento = $desc_tipo_documento;
		$retorno->ponto_operacao = $desc_ponto_operacao;
		$retorno->status_protocolo = $status_protocolo;
		$retorno->comprovante = $table;

		return $retorno;
	}


	public function buscaProtocoloEnvio($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];

		$retorno = '';
		$arq_xml = '';

		$query = "select id, tipo_documento_id, ponto_operacao_id, status_id, malote_id
				  from protocolo_envio_comprovante protocolo with (nolock)
				  where id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);

		$id						= odbc_result($result, 1);
		$tipo_documento_id		= odbc_result($result, 2);
		$ponto_operacao_id		= odbc_result($result, 3);
		$status_id				= odbc_result($result, 4);
		$malote_id				= odbc_result($result, 5);

		$codigo_malote = 'MA-' . $malote_id;

		$link_malote = "<a href='http://$ip/rastreabilidade_malote/rastreabilidade_malote.php?cd=$malote_id' target='_blank'>$codigo_malote</a>";

		$resultado = "1|$id|$tipo_documento_id|$ponto_operacao_id|$status_id|$link_malote|";

		$retorno->protocolo = $resultado;

		return $retorno;
	}


	/**
	 * Revisado em: 02/08/2024
	 */
	public function buscaPendentes()
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];


		$query = "select DISTINCT  protocolo.id num_protocolo, 
					(select count(*)
					 from comprovante_envio comprovante with (nolock)
					 where comprovante.protocolo_envio_id = protocolo.id
					 and comprovante.status_id not in ('i')),
					convert(varchar(10), protocolo.data_hora_gravacao, 103)+' '+convert(varchar(5), protocolo.data_hora_gravacao, 108) data_gravacao,
					pessoa.nome_fantasia ponto_operacao,
					 'CTE' tipo_docto,
					usuario.nome usuario,
					protocolo.id
					from protocolo_envio_comprovante protocolo with (nolock)
					join cargosol..ponto_operacao po with (nolock) on
						po.ponto_operacao_id = protocolo.ponto_operacao_id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = po.pessoa_id
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao
					where 1 = 1
					and protocolo.status_id = 't'
					and protocolo.tipo_documento_id = 'C'

					UNION
					
					select DISTINCT  protocolo.id num_protocolo, 
					(select count(*)
					 from comprovante_envio comprovante with (nolock)
					 where comprovante.protocolo_envio_id = protocolo.id
					 and comprovante.status_id not in ('i')),
					convert(varchar(10), protocolo.data_hora_gravacao, 103)+' '+convert(varchar(5), protocolo.data_hora_gravacao, 108) data_gravacao,
					GFILIAL.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS ponto_operacao,
					'FICHA' tipo_docto,
					usuario.nome usuario,
					protocolo.id
					from protocolo_envio_comprovante protocolo with (nolock)
					join corpore..GFILIAL with (nolock) on
						GFILIAL.CODFILIAL = protocolo.ponto_operacao_id
						and GFILIAL.CODCOLIGADA = 1
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao
					where 1 = 1
					and protocolo.status_id = 't'
					and protocolo.tipo_documento_id = 'F'";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'>QTDE DOCTOS</th>	
						<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='150'>PONTO OPERAÇÃO</th>
						<th bgcolor='#d3d3d3' width='400'>TIPO DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='70'>USUÁRIO</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$num_protocolo			= odbc_result($result, 1);
			$qtde_docto				= odbc_result($result, 2);
			$data_emissao			= odbc_result($result, 3);
			$ponto_operacao			= odbc_result($result, 4);
			$tipo_docto				= odbc_result($result, 5);
			$usuario				= odbc_result($result, 6);

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>
						<a href='http://$ip/comprovante_entrega/recebimento_comprovante.php?id=$num_protocolo' target='_blank'>" . $num_protocolo . "</a></font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $qtde_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>					
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $usuario . "</font></center></td>	
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->relatorio = $table;

		return $retorno;
	}



	public function exibeComprovanteConferido($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		//COMPROVANTES CONFERIDOS
		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end PO,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id,
					case when comprovante.chave_acesso not in (select comprovante_envio.chave_acesso
															   from comprovante_envio with (nolock)
															   where comprovante_envio.chave_acesso = comprovante.chave_acesso
															   and comprovante_envio.status_id = 'a'
															   and comprovante_envio.protocolo_envio_id = 
															   comprovante.protocolo_envio_id)
							then 'A MAIS'
							else NULL
					end comparacao,
					comprovante.status_id
					from comprovante_recebimento comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					where comprovante.status_id in ('a','m')
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'						
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id,
					case when comprovante.chave_acesso not in (select comprovante_envio.chave_acesso
															   from comprovante_envio with (nolock)
															   where comprovante_envio.chave_acesso = comprovante.chave_acesso
															   and comprovante_envio.status_id = 'a'
															   and comprovante_envio.protocolo_envio_id = 
															   comprovante.protocolo_envio_id)
							then 'A MAIS'
							else NULL
					end comparacao,
					comprovante.status_id
					from comprovante_recebimento comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join corpore..TMOV WITH (NOLOCK) on
						RIGHT('000'+RTRIM(TMOV.CODFILIAL),3)+CAST(TMOV.NUMEROMOV AS VARCHAR)+CAST(TMOV.IDMOV AS VARCHAR) = comprovante.chave_acesso collate SQL_Latin1_General_CP1_CI_AI
						/*TMOV.IDMOV = comprovante.idmov*/
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					where comprovante.status_id in ('a','m')
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DOCUMENTO</a></th>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DATA EMISSÃO</a></th>	
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>PONTO OPERAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='400'><a href='javascript:void(0)'>CLIENTE</a></th>
						<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></th>
					</thead><tbody>
				  ";

		$disabled_extra = '';

		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$comparacao				= odbc_result($result, 7);
			$status_comprovante		= odbc_result($result, 8);

			if ($comparacao == 'A MAIS') {
				$mais = 1;
				$cor = '#FF6A6A';
			} else
				$cor = '#FFFFFF';


			if ($status_comprovante == 'm')
				$disabled_extra = 'disabled';



			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='$cor'><center><font size='-1'>" . $documento . "</font></center></td>	
					<td bgcolor='$cor'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='$cor'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='$cor'><center><font size='-1'>" . $cliente . "</font></center></td>";

			if ($cor == '#FF6A6A')
				$table .= "<td bgcolor='$cor'><center><font size='-1'><a href='javascript:exclui_comprovante_conf($id,$protocolo_id);'><img src='../SCA/images/excluir_.gif' width='15' heigth='15' style='border:none'></a></font></center></td>";
			else
				$table .= "<td><center><font size='-1'> - </font></center></td>";


			$table .= "</tr>";
		}
		$table .= "</table>";

		if ($mais == 1)
			$table .= "<rigth><input type='button' class='btn-sm btn-primary' value='Gerar Protocolo' id='gera_protocolo_extra' name='gera_protocolo_extra' onClick='javascript:gera_protocolo_mais($protocolo_id)' $disabled_extra>";

		$retorno->comprovante_conferido = $table;

		return $retorno;
	}



	public function exibeComprovanteNaoConferido($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end PO,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id, comprovante.status_id
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					where comprovante.status_id in ('a','n')
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id
					and chave_acesso not in (select recebimento.chave_acesso
											 from comprovante_recebimento recebimento with (nolock)
											 where recebimento.protocolo_envio_id = protocolo.id
											 and recebimento.chave_acesso = comprovante.chave_acesso
											 and recebimento.status_id = 'a')

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'						
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id, comprovante.status_id
					from comprovante_envio comprovante with (nolock)
					join protocolo_envio_comprovante protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join corpore..TMOV WITH (NOLOCK) on
						TMOV.IDMOV = comprovante.idmov
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					where comprovante.status_id in ('a','n')
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id
					and chave_acesso not in (select recebimento.chave_acesso
											 from comprovante_recebimento recebimento with (nolock)
											 where recebimento.protocolo_envio_id = protocolo.id
											 and recebimento.chave_acesso = comprovante.chave_acesso
											 and recebimento.status_id = 'a')";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DOCUMENTO</a></th>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>DATA EMISSÃO</a></th>	
						<th bgcolor='#d3d3d3' width='150'><a href='javascript:void(0)'>PONTO OPERAÇÃO</a></th>
						<th bgcolor='#d3d3d3' width='400'><a href='javascript:void(0)'>CLIENTE</a></th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$status_comprovante		= odbc_result($result, 7);

			if ($status_comprovante == 'n')
				$cor = '#FF6A6A';
			else
				$cor = '#FFFFFF';


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='$cor'><center><font size='-1'>" . $documento . "</font></center></td>	
					<td bgcolor='$cor'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='$cor'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='$cor'><center><font size='-1'>" . $cliente . "</font></center></td>";

			$table .= "</tr>";
		}

		$table .= "</table>";

		$retorno->comprovante_nao_conferido = $table;

		return $retorno;
	}


	public function exibeComprovanteEnvioDig($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end FILIAL,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id, 
					case when ressalva is not null
							then 'checked'
							else NULL
					end ressalva,
					protocolo.status_id,
					comprovante.qtde_ressalva,
					'CTE' tipo,
					dt.SERIE SERIE,
					case when protocolo.ressalva_todos is not null
							then 'checked'
							else null
					end ressalva_todos
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					where comprovante.status_id = 'a'
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1-MTZ'
						when 2 THEN '2-SPO'
						when 3 THEN '3-STS'
						when 5 THEN '5-GUA'						
						when 11 THEN '11-CAM'
						when 15 THEN '15-ADG'
						
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id, 
					case when ressalva is not null
							then 'checked'
							else NULL
					end ressalva,
					protocolo.status_id,
					comprovante.qtde_ressalva,
					'FIC' tipo,
					TMOV.SERIE COLLATE SQL_Latin1_General_CP1_CI_AS SERIE,
					case when protocolo.ressalva_todos is not null
							then 'checked'
							else null
					end ressalva_todos
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
						/*and protocolo.status_id = 'a'*/
					join corpore..TMOV WITH (NOLOCK) on
						TMOV.IDMOV = comprovante.idmov
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					where comprovante.status_id = 'a'
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id
					ORDER BY comprovante.id";
		//print $query;
		$result = $con->executar($query);
		$result_status = $con->executar($query);
		$status_protocolo	= odbc_result($result_status, 8);
		$checked_todos		= odbc_result($result_status, 12);

		if ($status_protocolo == 'a')
			$status_t = '';
		else
			$status_t = 'disabled';

		$retorno = '';
		$arq_xml = '';
		$contador = 1;

		$table1 = "<div id='escondido' style='display:none'>
				<table class='table table-bordered table-hover' id='myTable4'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>ITEM</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>TIPO</td>
						<td bgcolor='#d3d3d3' width='150'><font color='#000000'><b>FILIAL</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>SERIE</td>
						<td bgcolor='#d3d3d3' width='300'><font color='#000000'><b>DOC</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>EMISSÃO</td>	
						<td bgcolor='#d3d3d3' width='400'><font color='#000000'><b>CLIENTE</td>
						<td bgcolor='#d3d3d3' width='70'><font color='#000000'><b>RESSALVA</td>
					</tr><tbody id='tabela1'></tbody>";


		$table .= "
				<table class='table table-bordered table-hover' id='myTable3'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>ITEM</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>TIPO</td>
						<td bgcolor='#d3d3d3' width='150'><font color='#000000'><b>FILIAL</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>SERIE</td>
						<td bgcolor='#d3d3d3' width='300'><font color='#000000'><b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp&nbsp;DOC&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>EMISSÃO</td>	
						<td bgcolor='#d3d3d3' width='400'><font color='#000000'><b>CLIENTE</td>
						<td bgcolor='#d3d3d3' width='140'><font color='#000000'><input type='checkbox' name='ressalvatodos' id='ressalvatodos' class='txt-center ressalva_t' aria-describedby='basic-addon1' $checked_todos value='1' $status_t onclick='javascript:ressalva_todos($protocolo_id,2,1)'/><b>RESSALVA</b></td>";

		if ($status_protocolo != 'f')
			$table .= "<td bgcolor='#d3d3d3' width='50'><font color='#000000'><b>EXCLUIR</td>";

		$table .=	"</tr>
					 <tbody id='tabela'>";

		$aux = '';

		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$checked				= odbc_result($result, 7);
			//$status_protocolo		= odbc_result($result,8);
			$qtde_ressalva			= odbc_result($result, 9);
			$tipo					= odbc_result($result, 10);
			$serie					= odbc_result($result, 11);

			if ($checked != '')
				$disabled_qtde = '';
			else
				$disabled_qtde = 'disabled';


			$table .= "<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							<td ><center><font size='-1'>" . $contador . "</font></center></td>
							<td ><center><font size='-1'>" . $tipo . "</font></center></td>	
							<td ><center><font size='-1'>" . $ponto_operacao . "</font></center></td>						
							<td ><center><font size='-1'>" . $serie . "</font></center></td>
							<td ><center><font size='-1'>" . $documento . "</td>	
							<td ><center><font size='-1'>" . $data_emissao . "</font></center></td>
							<td ><center><font size='-1'>" . $cliente . "</font></center></td>";

			$table1 .= "<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							<td ><center><font size='-1'>" . $contador . "</font></center></td>
							<td ><center><font size='-1'>" . $tipo . "</font></center></td>	
							<td ><center><font size='-1'>" . $ponto_operacao . "</font></center></td>						
							<td ><center><font size='-1'>" . $serie . "</font></center></td>";

			if ($tipo == 'FIC')
				$table1 .= "<td ><center><font size='-1'>=REPT(0;8-NÚM.CARACT($documento))&$documento</font></center></td>";
			else
				$table1 .= "<td ><center><font size='-1'>" . $documento . "</font></center></td>";

			$table1 .= "<td ><center><font size='-1'>" . $data_emissao . "</font></center></td>
						<td ><center><font size='-1'>" . $cliente . "</font></center></td>
						<td><center><font size='-1'>" . $qtde_ressalva . "</font></center></td>";

			if ($status_protocolo == 'a') {
				$aux = 'S';

				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>
						<input type='checkbox' name='ressalva$docto_transporte_id' id='ressalva$docto_transporte_id' class='txt-center classe_checkbox' aria-describedby='basic-addon1' $checked value='1' onclick='javascript:grava_ressalva(this,$id,2,$protocolo_id)'/>
						
						<input type='text' name='qtde_ressalva$id' id='qtde_ressalva$id' class='txt-center classe_campo'  aria-describedby='basic-addon1' $checked value='$qtde_ressalva' onblur='javascript:grava_qtde(this,$id,2)' maxlength='5' size='1' $disabled_qtde/>
						
					</td>
				<td><center><font size='-1'><a href='javascript:exclui_comprovante($id,$protocolo_id,2);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>";
			} else {
				if ($status_protocolo == 'i')
					$aux = 'C';

				if ($checked != '')
					$table .= "<td><center><font size='-1'>$qtde_ressalva</font></center></td>";
				else
					$table .= "<td><center><font size='-1'>&nbsp;</font></center></td>";
			}
			$table .= "</tr>";

			$contador++;
		}

		$table .= "</tbody></table>";
		$table1 .= "</tbody></table></div>";

		//$table1 = str_replace("=REPT", "", $table);

		$table .= "<input type='hidden' name='aux' id='aux' maxlength='5' size='1' value='$aux'/>";

		$retorno->comprovante = $table1 . $table;

		return $retorno;
	}


	public function pesquisaProtocoloDig($num_protocolo, $tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];

		if ($num_protocolo != '')
			$condicao_protocolo = "&& protocolo.id = $num_protocolo";
		else
			$condicao_protocolo = "";

		if ($tipo_documento_id != '') {
			if ($tipo_documento_id == 'C')
				$condicao_tipo = "join comprovante_envio_dig comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										&& comprovante.status_id = 'a'	
								  join cargosol..docto_transporte dt with (nolock) on
										dt.docto_transporte_id = comprovante.docto_transporte_id
										&& dt.tab_status_id <> 2
								  join cargosol..cte with (nolock) on
										cte.docto_transporte_id = dt.docto_transporte_id
										&& cte.numero = '$num_documento'";
			else
				$condicao_tipo = "join comprovante_envio_dig comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										&& comprovante.status_id = 'a'	
								  join corpore..TMOV WITH (NOLOCK) on
										TMOV.IDMOV = comprovante.idmov
										AND TMOV.NUMEROMOV = '$num_documento'";
		} else
			$condicao_tipo = "";


		if ($status_id != '')
			$condicao_status = "AND protocolo.status_id = '$status_id'";
		else
			$condicao_status = "";


		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = " AND protocolo.data_hora_gravacao >= '$data_inicial' AND protocolo.data_hora_gravacao < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";


		$query = "select protocolo.id num_protocolo, 
					convert(varchar(10), protocolo.data_hora_gravacao, 103)+' '+convert(varchar(5), protocolo.data_hora_gravacao, 108) data_gravacao,
					case when tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_docto,
					usuario.nome usuario,
					case protocolo.status_id
						when 'a' then 'ATIVO'
						when 'f' then 'FINALIZADO'
						when 'i' then 'CANCELADO'
						when 't' then 'EM TRANSITO'
					end status,
					protocolo.status_id,
					CASE WHEN CONVERT(DATE,protocolo.data_hora_gravacao) >= '2024-08-12' THEN 'RODO' ELSE 'ECA' END TP 
					from protocolo_envio_comprovante_dig protocolo with (nolock)
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao 
					
					$condicao_tipo
					
					where 1 = 1
					$condicao_protocolo
					$condicao_status
					$condicao_datas";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='400'>TIPO DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='70'>USUÁRIO</th>
						<th bgcolor='#d3d3d3' width='70'>STATUS</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$num_protocolo			= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$tipo_docto				= odbc_result($result, 3);
			$usuario				= odbc_result($result, 4);
			$status					= odbc_result($result, 5);
			$status_id				= odbc_result($result, 6);
			$tp				= odbc_result($result, 7);

			$dest = 'rodopar';

			if ($tp == 'ECA') {
				$dest = 'dig';
			}

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>
						<a href='http://$ip/comprovante_entrega/envio_comprovante_$dest.php?id=$num_protocolo' target='_blank'>" . $num_protocolo . "</a></font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $usuario . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		//$table .= "<script>order()</script>";

		$retorno->relatorio = $table;

		return $retorno;
	}


	public function buscaProtocoloEnvioDig($protocolo_id)
	{

		$con = new ConexaoSCI;


		$logado = $_SESSION['usuario_logado'];


		$retorno = '';
		$arq_xml = '';

		$query = "select id, tipo_documento_id, status_id
				  from protocolo_envio_comprovante_dig protocolo with (nolock)
				  where id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);


		$id						= odbc_result($result, 1);
		$tipo_documento_id		= odbc_result($result, 2);
		$status_id				= odbc_result($result, 3);


		$resultado = "1|$id|$tipo_documento_id|$status_id|";

		$retorno->protocolo = $resultado;

		return $retorno;
	}


	public function imprimeComprovanteEnvioDig($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "SELECT CAST(cte.numero AS VARCHAR) DOCUMENTO, convert(varchar(10), cte.data_emissao, 103) data_emissao, 
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end PO,
					pessoa.Nome_Fantasia cliente, dt.Docto_Transporte_Id, comprovante.id, 
					case when ressalva is not null
							then qtde_ressalva
							else NULL
					end ressalva,
					protocolo.id num_protocolo,
					case when protocolo.tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_documento,
					'TRANSPORTES COVRE' P_OPERACAO,
					protocolo.status_id,
					protocolo.tipo_documento_id,
					comprovante.id id_comprovante
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join cargosol..cte with (nolock) on
						cte.Docto_Transporte_Id = comprovante.docto_transporte_id
					join CARGOSOL..docto_transporte dt with (nolock) on
						dt.Docto_Transporte_Id = cte.Docto_Transporte_Id
					join CARGOSOL..pessoa with (nolock) on
						pessoa.Pessoa_Id = dt.Cliente_Id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.Ponto_Operacao_Id
					where comprovante.status_id = 'a'
					and protocolo.status_id in ('t','f')
					and protocolo.tipo_documento_id = 'C'
					and protocolo.id = $protocolo_id

					UNION

					SELECT NUMEROMOV, convert(varchar(10), TMOV.DATAEMISSAO, 103) data_emissao, 
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'						
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial,
					FCFO.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS, TMOV.IDMOV, comprovante.id, 
					case when ressalva is not null
							then qtde_ressalva
							else NULL
					end ressalva,
					protocolo.id,
					case when protocolo.tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_documento,
					'MATRIZ' COLLATE SQL_Latin1_General_CP1_CI_AS,
					protocolo.status_id,
					protocolo.tipo_documento_id,
					comprovante.id id_comprovante					
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id
					join corpore..TMOV WITH (NOLOCK) on
						TMOV.IDMOV = comprovante.idmov
					JOIN corpore..FCFO WITH (NOLOCK) ON
						FCFO.CODCFO = TMOV.CODCFO
						AND FCFO.CODCOLIGADA = 1
					where comprovante.status_id = 'a'
					and protocolo.status_id in ('t','f')
					AND TMOV.CODCOLIGADA = 1
					and protocolo.tipo_documento_id = 'F'
					and protocolo.id = $protocolo_id
					order by id_comprovante";
		//print $query;
		$result = $con->executar($query);
		$result_prot = $con->executar($query);


		$num_protocolo			= odbc_result($result_prot, 8);
		$desc_tipo_documento	= odbc_result($result_prot, 9);
		$desc_ponto_operacao	= odbc_result($result_prot, 10);
		$status_protocolo		= odbc_result($result_prot, 11);
		$tipo_documento_id		= odbc_result($result_prot, 12);



		$retorno = '';
		$arq_xml = '';


		$table = "
				<table border='1'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><center><font size='-1'><b>DOCUMENTO</font></td>
						<td bgcolor='#d3d3d3' width='120'><center><font size='-1'><b>DATA EMISSÃO</font></td>	
						<td bgcolor='#d3d3d3' width='150'><center><font size='-1'><b>PONTO OPERAÇÃO</font></td>
						<td bgcolor='#d3d3d3' width='400'><center><font size='-1'><b>CLIENTE</font></td>
						<td bgcolor='#d3d3d3' width='70'><center><font size='-1'><b>&nbsp;RESSALVA&nbsp;</font></td>
					</tr>
				  ";


		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$ponto_operacao			= odbc_result($result, 3);
			$cliente				= odbc_result($result, 4);
			$docto_transporte_id	= odbc_result($result, 5);
			$id						= odbc_result($result, 6);
			$ressalva				= odbc_result($result, 7);

			$table .= "
				  <tr>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $documento . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $cliente . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $ressalva . "</font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->num_protocolo = $num_protocolo;
		$retorno->tipo_documento = $desc_tipo_documento;
		$retorno->ponto_operacao = $desc_ponto_operacao;
		$retorno->status_protocolo = $status_protocolo;
		$retorno->comprovante = $table;

		return $retorno;
	}


	public function imprimeComprovanteEnvioRodopar($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select chave_acesso, comprovante.status_id, protocolo.status_id,protocolo.tipo_documento_id, comprovante.id
		,protocolo.id,
		case when protocolo.tipo_documento_id = 'C'
				then 'CTE'
				else 'FICHA'
		end tipo_documento,
		'MATRIZ' COLLATE SQL_Latin1_General_CP1_CI_AS,
		protocolo.status_id,
		protocolo.tipo_documento_id
		from comprovante_envio_dig comprovante with (nolock)
		join protocolo_envio_comprovante_dig protocolo with (nolock) on
			protocolo.id = comprovante.protocolo_envio_id
		where protocolo.id = $protocolo_id
		and comprovante.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		$result_prot = $con->executar($query);


		$num_protocolo			= odbc_result($result_prot, 5);
		$desc_tipo_documento	= odbc_result($result_prot, 6);
		$desc_ponto_operacao	= odbc_result($result_prot, 7);
		$status_protocolo		= odbc_result($result_prot, 8);
		$tipo_documento_id		= odbc_result($result_prot, 9);


		$retorno = '';
		$arq_xml = '';


		$table = "
				<table border='1'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><center><font size='-1'><b>TIPO DOCUMENTO</font></td>
						<td bgcolor='#d3d3d3' width='100'><center><font size='-1'><b>DOCUMENTO</font></td>
						<td bgcolor='#d3d3d3' width='120'><center><font size='-1'><b>DATA EMISSÃO</font></td>	
						<td bgcolor='#d3d3d3' width='150'><center><font size='-1'><b>PONTO OPERAÇÃO</font></td>
						<td bgcolor='#d3d3d3' width='70'><center><font size='-1'><b>&nbsp;RESSALVA&nbsp;</font></td>
					</tr>
				  ";
		//<td bgcolor='#d3d3d3' width='70'><center><font size='-1'><b>&nbsp;RESSALVA&nbsp;</font></td>

		while (odbc_fetch_row($result)) {

			$key				= odbc_result($result, 1);
			$tipoDoc				= odbc_result($result, 4);
			$id				= odbc_result($result, 5);
			$doc = $tipoDoc == 'F' ? 'Fatura' : 'CTE';
			$filial = '';
			if ($tipoDoc == 'C') {
				$cUF = substr($key, 0, 2);        // Código da UF do emitente do Documento Fiscal
				$AAMM = substr($key, 2, 4);       // Ano e Mês de emissão do CT-e
				$CNPJ = substr($key, 6, 14);      // CNPJ do emitente
				$mod = substr($key, 20, 2);       // Modelo do Documento Fiscal
				$serie = substr($key, 22, 3);     // Série do Documento Fiscal
				$nCT = substr($key, 25, 9);       // Número do Documento Fiscal
				$tpEmis = substr($key, 34, 1);    // Forma de emissão do CT-e
				$cCT = substr($key, 35, 8);       // Código Numérico que compõe a Chave de Acesso
				$cDV = substr($key, 43, 1);

				if ($CNPJ == '51485274000159' && $serie == '1') {
					$filial = 'MTZ';
				} else if ($CNPJ == '51485274000159' && ($serie == '2' or $serie == '3')) {
					$filial = 'DEL';
				} else if ($CNPJ == '51485274000159' && ($serie == '4' || $serie == '5')) {
					$filial = 'SYN';
				} else if ($CNPJ == '51485274000230' && $serie == '1') {
					$filial = 'SPO';
				} else if ($CNPJ == '51485274000230' && ($serie == '2' or $serie == '3')) {
					$filial = 'GRU';
				} else if ($CNPJ == '51485274000400' && $serie == '1') {
					$filial = 'STS';
				} else if ($CNPJ == '51485274000663' && $serie == '1') {
					$filial = 'GUA';
				} else if ($CNPJ == '51485274001040' && $serie == '1') {
					$filial = 'CAM';
				} else if ($CNPJ == '51485274001040' && ($serie == '2' or $serie == '3')) {
					$filial = 'BSC';
				} else if ($CNPJ == '51485274001040' && ($serie == '4' or $serie == '5')) {
					$filial = 'ITV';
				} else if ($CNPJ == '51485274001040' && ($serie == '6' or $serie == '7')) {
					$filial = 'PSO';
				} else if ($CNPJ == '51485274001392' && $serie == '1') {
					$filial = 'MTG';
				} else if ($CNPJ == '51485274001473' && $serie == '1') {
					$filial = 'ADG';
				}
			} else {


				$filial = substr($key, 0, 2);
				$serie = substr($key, 2, 3);
				$nCT = substr($key, 5, 6);
				$AAMM = substr($key, 11, 4);
			}

			$data_edit = substr($AAMM, 2, 4) . '/20' . substr($AAMM, 0, 2);

			$table .= "
				  <tr>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $doc . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $nCT . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $data_edit . "</font></center></td>
					<td bgcolor='#FFFFFF' ><center><font size='-1'>" . $filial . "</font></center></td>	
					<td bgcolor='#FFFFFF' ><center><font size='-1'></font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->num_protocolo = $num_protocolo;
		$retorno->tipo_documento = $desc_tipo_documento;
		$retorno->ponto_operacao = $desc_ponto_operacao;
		$retorno->status_protocolo = $status_protocolo;
		$retorno->comprovante = $table;

		return $retorno;
	}


	public function exibeRelatorio($ponto_operacao_id, $tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final, $sistema, $ponto_operacao_rodo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		if ($tipo_documento_id != '')
			$condicao_tipo = "AND TIPO_DOCTO = '$tipo_documento_id'";
		else
			$condicao_tipo = "";


		if ($num_documento != '') {
			$condicao_documento = "AND DADOS.DOCTO = '$num_documento'";
			$condicao_documento_rodopar = "AND DADOS.DOCTO like '%$num_documento%'";
		} else {
			$condicao_documento = "";
			$condicao_documento_rodopar = "";
		}


		if ($ponto_operacao_id != '') {
			$condicao_po = "AND DADOS.PO_ID = '$ponto_operacao_id'";
		} else {
			$condicao_po = "";
		}

		if ($ponto_operacao_rodo_id != '') {
			$condicao_po_rodopar = "AND DADOS.filial = '$ponto_operacao_rodo_id'";
		} else {
			$condicao_po_rodopar = "";
		}

		if ($status_id != '')
			$condicao_status = "AND DADOS.STATUS_ID = '$status_id'";
		else
			$condicao_status = "";


		if (($data_inicial != '') && ($data_final != '')) {
			$condicao_datas = "AND DATAEMISSAO >= '$data_inicial' AND DATAEMISSAO < DATEADD(DD,1,'$data_final')";
			$condicao_datas_rodopar = "AND DADOS.data_hora_gravacao >= '$data_inicial' AND DADOS.data_hora_gravacao < DATEADD(DD,1,'$data_final')";
		} else
			$condicao_datas = "";


		if ($sistema == 1) {



			$query = "SELECT TIPO_DOCTO,
			DOCTO,
			DATA_EMISSAO DATA_EMISSAO,
			filial,
			PROTOCOLO,
			CASE DADOS.STATUS_ID
				WHEN 'D' THEN 'DIGITALIZADO'
				WHEN 'R' THEN 'RECEBIDO'
				WHEN 'T' THEN 'EM TRANSITO'
				WHEN 'S' THEN 'SEM COMPROVANTE'
			END DESC_STATUS,
			null PO_ID,
			DADOS.STATUS_ID,
			'CONSULTAR NO RODOPAR' STATUS_DOCTO,
			dados.data_hora_gravacao dt
			FROM (
SELECT DISTINCT 
'CTE' TIPO_DOCTO,
SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 26, 9) DOCTO,

SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 5, 2) + '/' + 
CASE 
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 3, 2) >= '00' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 3, 2) <= '50'
THEN '20' + SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 3, 2)
ELSE '19' + SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 3, 2)
END AS DATA_EMISSAO,

CASE 
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000159' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'MTZ'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000159' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('002', '003') THEN 'DEL'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000159' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('004', '005') THEN 'SYN'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000230' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'SPO'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000230' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('002', '003') THEN 'GRU'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000400' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'STS'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274000663' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'GUA'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001040' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'CAM'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001040' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('002', '003') THEN 'BSC'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001040' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('004', '005') THEN 'ITV'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001040' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) IN ('006', '007') THEN 'PSO'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001392' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'MTG'
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 7, 14) = '51485274001473' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 23, 3) = '001' THEN 'ADG'
ELSE NULL
END AS filial,
ISNULL(ISNULL(CED.PROTOCOLO_ENVIO_ID, COMPROVANTE_RECEBIMENTO.PROTOCOLO_ENVIO_ID),COMPROVANTE_ENVIO.PROTOCOLO_ENVIO_ID) PROTOCOLO,
					CASE WHEN CED.DOCTO_TRANSPORTE_ID IS NOT NULL
							THEN 'D'
							ELSE CASE WHEN COMPROVANTE_RECEBIMENTO.DOCTO_TRANSPORTE_ID IS NOT NULL
										THEN 'R'
										ELSE CASE WHEN COMPROVANTE_ENVIO.DOCTO_TRANSPORTE_ID IS NOT NULL
													THEN 'T'
													ELSE 'S'
											END
								END
					END STATUS_ID,
					CED.data_hora_gravacao
FROM protocolo_envio_comprovante_dig PEC 
JOIN comprovante_envio_dig CED
ON CED.PROTOCOLO_ENVIO_ID = PEC.ID
LEFT JOIN COMPROVANTE_ENVIO WITH (NOLOCK) ON
COMPROVANTE_ENVIO.protocolo_envio_id = PEC.ID
AND COMPROVANTE_ENVIO.STATUS_ID <> 'i'

LEFT JOIN COMPROVANTE_RECEBIMENTO WITH (NOLOCK) ON
COMPROVANTE_RECEBIMENTO.protocolo_envio_id = PEC.ID
AND COMPROVANTE_RECEBIMENTO.STATUS_ID NOT IN ('i','m')
WHERE PEC.data_hora_gravacao >= '2024-08-12' AND (CED.docto_transporte_id IS NULL AND CED.idmov IS NULL)
AND PEC.tipo_documento_id = 'C'

UNION 
SELECT DISTINCT 
 'FICHA' TIPO_DOCTO,
SUBSTRING(CED.CHAVE_ACESSO, 6, 6) DOCTO,


SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 14, 2) + '/' + 
CASE 
WHEN SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 12, 2) >= '00' AND SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 12, 2) <= '50'
THEN '20' + SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 12, 2)
ELSE '19' + SUBSTRING(LTRIM(RTRIM(CED.CHAVE_ACESSO)), 12, 2)
END AS DATA_EMISSAO,

CASE 
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '01' THEN 'MTZ'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '02' THEN 'SPO'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '04' THEN 'GUA'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '05' THEN 'CAM'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '06' THEN 'STS'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '07' THEN 'CNO'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '08' THEN 'MTG'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '10' THEN 'GRJ'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '11' THEN 'ARM2'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '12' THEN 'DEL'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '13' THEN 'GRU'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '14' THEN 'STA'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '15' THEN 'SYN'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '16' THEN 'BSC'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '17' THEN 'ITV'
WHEN SUBSTRING(CED.CHAVE_ACESSO, 1, 2) = '18' THEN 'PSO'       
ELSE NULL
END AS filial,
ISNULL(ISNULL(CED.PROTOCOLO_ENVIO_ID, COMPROVANTE_RECEBIMENTO.PROTOCOLO_ENVIO_ID),COMPROVANTE_ENVIO.PROTOCOLO_ENVIO_ID) PROTOCOLO,
					CASE WHEN CED.DOCTO_TRANSPORTE_ID IS NOT NULL
							THEN 'D'
							ELSE CASE WHEN COMPROVANTE_RECEBIMENTO.DOCTO_TRANSPORTE_ID IS NOT NULL
										THEN 'R'
										ELSE CASE WHEN COMPROVANTE_ENVIO.DOCTO_TRANSPORTE_ID IS NOT NULL
													THEN 'T'
													ELSE 'S'
											END
								END
					END STATUS_ID,
					CED.data_hora_gravacao
FROM protocolo_envio_comprovante_dig PEC 
JOIN comprovante_envio_dig CED
ON CED.PROTOCOLO_ENVIO_ID = PEC.ID
LEFT JOIN COMPROVANTE_ENVIO WITH (NOLOCK) ON
COMPROVANTE_ENVIO.protocolo_envio_id = PEC.ID
AND COMPROVANTE_ENVIO.STATUS_ID <> 'i'

LEFT JOIN COMPROVANTE_RECEBIMENTO WITH (NOLOCK) ON
COMPROVANTE_RECEBIMENTO.protocolo_envio_id = PEC.ID
AND COMPROVANTE_RECEBIMENTO.STATUS_ID NOT IN ('i','m')
WHERE PEC.data_hora_gravacao >= '2024-08-12' AND (CED.docto_transporte_id IS NULL AND CED.idmov IS NULL)
AND PEC.tipo_documento_id = 'F'

) as dados
where 1=1
$condicao_datas_rodopar
$condicao_tipo
$condicao_documento_rodopar
				$condicao_po_rodopar
				$condicao_status
			";
		} else {
			$query = "SELECT TIPO_DOCTO,
							DOCTO,
							CONVERT(VARCHAR(10), DATAEMISSAO, 103)+' '+CONVERT(VARCHAR(5), DATAEMISSAO, 108) DATA_EMISSAO,
							P_OPERACAO,
							PROTOCOLO,
							CASE DADOS.STATUS_ID
								WHEN 'D' THEN 'DIGITALIZADO'
								WHEN 'R' THEN 'RECEBIDO'
								WHEN 'T' THEN 'EM TRANSITO'
								WHEN 'S' THEN 'SEM COMPROVANTE'
							END DESC_STATUS,
							PO_ID,
							DADOS.STATUS_ID,
							DADOS.STATUS_DOCTO
							FROM (
									SELECT 'CTE' TIPO_DOCTO,			
									CAST(CTE.NUMERO AS VARCHAR) DOCTO, 
									CTE.DATA_EMISSAO DATAEMISSAO,
									PO_PESSOA.NOME_FANTASIA P_OPERACAO,
									ISNULL(ISNULL(COMPROVANTE_ENVIO_DIG.PROTOCOLO_ENVIO_ID, 
									COMPROVANTE_RECEBIMENTO.PROTOCOLO_ENVIO_ID),COMPROVANTE_ENVIO.PROTOCOLO_ENVIO_ID) PROTOCOLO,
									CASE WHEN COMPROVANTE_ENVIO_DIG.DOCTO_TRANSPORTE_ID IS NOT NULL
											THEN 'D'
											ELSE CASE WHEN COMPROVANTE_RECEBIMENTO.DOCTO_TRANSPORTE_ID IS NOT NULL
														THEN 'R'
														ELSE CASE WHEN COMPROVANTE_ENVIO.DOCTO_TRANSPORTE_ID IS NOT NULL
																	THEN 'T'
																	ELSE 'S'
															END
												END
									END STATUS_ID,
									PO.PONTO_OPERACAO_ID PO_ID,
									CASE WHEN CTE.TAB_STATUS_ID = 2
											THEN 'CANCELADO'
											ELSE 'ATIVO'
									END STATUS_DOCTO
									FROM CARGOSOL..DOCTO_TRANSPORTE DT WITH (NOLOCK)
									JOIN CARGOSOL..CTE WITH (NOLOCK) ON
										CTE.DOCTO_TRANSPORTE_ID = DT.DOCTO_TRANSPORTE_ID
										AND CTE.DATA_AUTORIZACAO IS NOT NULL
										/*AND CTE.TAB_STATUS_ID <> 2*/
									LEFT JOIN COMPROVANTE_ENVIO WITH (NOLOCK) ON
										COMPROVANTE_ENVIO.DOCTO_TRANSPORTE_ID = DT.DOCTO_TRANSPORTE_ID
										AND COMPROVANTE_ENVIO.STATUS_ID <> 'i'
									LEFT JOIN COMPROVANTE_RECEBIMENTO WITH (NOLOCK) ON
										COMPROVANTE_RECEBIMENTO.DOCTO_TRANSPORTE_ID = DT.DOCTO_TRANSPORTE_ID
										AND COMPROVANTE_RECEBIMENTO.STATUS_ID NOT IN ('i','m')
									LEFT JOIN COMPROVANTE_ENVIO_DIG WITH (NOLOCK) ON
										COMPROVANTE_ENVIO_DIG.DOCTO_TRANSPORTE_ID = DT.DOCTO_TRANSPORTE_ID
										AND COMPROVANTE_ENVIO_DIG.STATUS_ID <> 'i'
									JOIN CARGOSOL..PONTO_OPERACAO PO WITH (NOLOCK) ON
										PO.PONTO_OPERACAO_ID = DT.PONTO_OPERACAO_ID
									JOIN CARGOSOL..PESSOA PO_PESSOA WITH (NOLOCK) ON
										PO_PESSOA.PESSOA_ID = PO.PESSOA_ID
									JOIN CARGOSOL..ENDERECO_PESSOA EP_DESTINATARIO WITH (NOLOCK) ON
										EP_DESTINATARIO.ENDERECO_PESSOA_ID = DT.END_DESTINATARIO_ID
									JOIN CARGOSOL..MUNICIPIO MUNICIPIO_DESTINATARIO WITH (NOLOCK) ON	
										MUNICIPIO_DESTINATARIO.MUNICIPIO_ID = EP_DESTINATARIO.MUNICIPIO_ID
										/*AND MUNICIPIO_DESTINATARIO.UF NOT IN ('EX')*/								
									WHERE 1 = 1
									/*
									AND DT.DOCTO_TRANSPORTE_ID NOT IN (SELECT CANCELAMENTO.DOCTO_TRANSPORTE_ID
																		FROM CANCELAMENTO_CTE CANCELAMENTO WITH (NOLOCK)
																		WHERE STATUS_SOLICIT_ID IN (2,3,6,9))
									AND DT.TAB_TIPO_DOCUMENTO_ID IN (1,38)	
									*/
									UNION

									SELECT 'FICHA' TIPO_DOCTO,
									TMOV.NUMEROMOV DOCTO, 
									TMOV.DATAEMISSAO DATAEMISSAO,
									GFILIAL.NOMEFANTASIA COLLATE SQL_Latin1_General_CP1_CI_AS,
									ISNULL(ISNULL(COMPROVANTE_ENVIO_DIG.PROTOCOLO_ENVIO_ID, 
									COMPROVANTE_RECEBIMENTO.PROTOCOLO_ENVIO_ID),COMPROVANTE_ENVIO.PROTOCOLO_ENVIO_ID) PROTOCOLO,
									CASE WHEN COMPROVANTE_ENVIO_DIG.IDMOV IS NOT NULL
											THEN 'D'
											ELSE CASE WHEN COMPROVANTE_RECEBIMENTO.IDMOV IS NOT NULL
														THEN 'R'
														ELSE CASE WHEN COMPROVANTE_ENVIO.IDMOV IS NOT NULL
																	THEN 'T'
																	ELSE 'S'
															END
												END
									END STATUS,
									GFILIAL.CODFILIAL,
									CASE WHEN TMOV.STATUS = 'C'
											THEN 'CANCELADO'
											ELSE 'ATIVO'
									END STATUS_DOCTO
									FROM CORPORE..TMOV TMOV WITH (NOLOCK)
									JOIN CORPORE..GFILIAL WITH (NOLOCK) ON
										GFILIAL.CODFILIAL = TMOV.CODFILIAL
										AND GFILIAL.CODCOLIGADA = 1
									LEFT JOIN COMPROVANTE_ENVIO WITH (NOLOCK) ON
										COMPROVANTE_ENVIO.IDMOV = TMOV.IDMOV
										AND COMPROVANTE_ENVIO.STATUS_ID <> 'i'
									LEFT JOIN COMPROVANTE_RECEBIMENTO WITH (NOLOCK) ON
										COMPROVANTE_RECEBIMENTO.IDMOV = TMOV.IDMOV
										AND COMPROVANTE_RECEBIMENTO.STATUS_ID NOT IN ('i','m')
									LEFT JOIN COMPROVANTE_ENVIO_DIG WITH (NOLOCK) ON
										COMPROVANTE_ENVIO_DIG.IDMOV = TMOV.IDMOV
										AND COMPROVANTE_ENVIO_DIG.STATUS_ID <> 'i'
									WHERE 1 = 1
									AND TMOV.CODCOLIGADA = 1
					)DADOS
				WHERE 1 = 1
				$condicao_tipo
				$condicao_documento_rodopar
				$condicao_po
				$condicao_status
				$condicao_datas";
		}

		//print '<pre>' . $query . '</pre>';
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';
		$aux = '';
		$contador = 0;

		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>TIPO</th>
						<th bgcolor='#d3d3d3' width='100'>DOCUMENTO</th>";
		if ($sistema != 1) {
			$table .= "<th bgcolor='#d3d3d3' width='100'>STATUS DOCTO</th>";
		}
		$table .= "<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='150'>PONTO OPERAÇÃO</th>
						<th bgcolor='#d3d3d3' width='150'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='400'>STATUS</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$tipo_docto				= odbc_result($result, 1);
			$documento				= odbc_result($result, 2);
			$data_emissao			= odbc_result($result, 3);
			$ponto_operacao			= odbc_result($result, 4);
			$protocolo				= odbc_result($result, 5);
			$status					= odbc_result($result, 6);
			$status_docto			= odbc_result($result, 9);


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $documento . "</font></center></td>	
					";
			if ($sistema != 1) {
				$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status_docto . "</font></center></td>";
			}
			$table .= "<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $ponto_operacao . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $protocolo . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>					
				  </tr>";

			$contador++;
		} //fim while 

		$table .= "</table>";

		if ($contador > 0)
			$table .= "<input type='button' class='btn-sm btn-primary' value='XLS' id='xls' name='xls' onClick='javascript:gerar_excel(" . chr(34) . "relatorio" . chr(34) . "," . chr(34) . "myTable3" . chr(34) . ")' > ";

		$retorno->relatorio = $table;

		return $retorno;
	}


	public function comparaDatas($data_inicial, $data_final)
	{

		$con = new ConexaoSCI;

		$query = "select case when '$data_inicial' > '$data_final'
								then 'maior'
								else 'menor'
						 end";
		//print $query;
		$result = $con->executar($query);

		$compara				= odbc_result($result, 1);

		$retorno = '';

		$retorno = $compara;

		//$retorno->compara = $compara;			

		return $retorno;
	}


	public function pesquisaDoctoDig($tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];


		if ($num_documento != '')
			$condicao_documento = "and dados.documento = '$num_documento'";
		else
			$condicao_documento = "";

		if ($tipo_documento_id != '')
			$condicao_tipo = "and dados.tipo_docto = '$tipo_documento_id'";
		else
			$condicao_tipo = "";


		if ($status_id != '')
			$condicao_status = "and dados.status_id = '$status_id'";
		else
			$condicao_status = "";


		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = "and dados.dt_emissao >= '$data_inicial' and dados.dt_emissao < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";


		$query = "
			SELECT *
			FROM (
					select CAST(dt.num_docto_transporte AS VARCHAR) documento, 
					convert(varchar(10),dt.data_emissao, 103) data_emissao, 
					'CTE' tipo_docto,
					case when ponto_operacao.ponto_operacao_id < 10
							then '0'+cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
							else cast(ponto_operacao.ponto_operacao_id as varchar)+'-'+ponto_operacao.sigla
					end filial, 
					comprovante.protocolo_envio_id protocolo,
					qtde_ressalva ressalva,
					case protocolo.status_id
						when 'a' then 'Ativo'
						when 'f' then 'Finalizado'
					end status_protocolo,					
					dt.data_emissao dt_emissao,
					protocolo.status_id
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id					
					join cargosol..docto_transporte dt with (nolock) on
						dt.docto_transporte_id = comprovante.docto_transporte_id
					join cargosol..ponto_operacao with (nolock) on
						ponto_operacao.ponto_operacao_id = dt.ponto_operacao_id
					where comprovante.status_id not in ('i')
					and protocolo.status_id not in ('i')
					
					UNION
					
					select tmov.numeromov+'&nbsp;', convert(varchar(10),tmov.dataemissao, 103) data_emissao, 
					'FICHA' tipo_docto,
					CASE TMOV.CODFILIAL
						when 1 THEN '1 - MTZ'
						when 2 THEN '2 - SPO'
						when 3 THEN '3 - STS'
						when 5 THEN '5 - GUA'						
						when 11 THEN '11 - CAM'
						when 15 THEN '15 - ADG'
					END filial, 
					comprovante.protocolo_envio_id protocolo,
					qtde_ressalva ressalva,
					case protocolo.status_id
						when 'a' then 'Ativo'
						when 'f' then 'Finalizado'
					end status_protocolo,					
					tmov.dataemissao dt_emissao,
					protocolo.status_id
					from comprovante_envio_dig comprovante with (nolock)
					join protocolo_envio_comprovante_dig protocolo with (nolock) on
						protocolo.id = comprovante.protocolo_envio_id					
					join corpore..tmov tmov with (nolock) on
						tmov.idmov = comprovante.idmov
					where comprovante.status_id not in ('i')
					and protocolo.status_id not in ('i')
				)dados
			where 1 = 1
			$condicao_documento
			$condicao_tipo
			$condicao_datas
			$condicao_status
			
			order by dt_emissao, protocolo";
		//print "<pre>".$query."</pre>";
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'><b>DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='100'><b>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='100'><b>TIPO DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='100'><b>FILIAL</th>
						<th bgcolor='#d3d3d3' width='70'><b>RESSALVA</th>
						<th bgcolor='#d3d3d3' width='70'><b>PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'><b>STATUS PROTOCOLO</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$documento				= odbc_result($result, 1);
			$tipo_docto				= odbc_result($result, 2);
			$data_emissao			= odbc_result($result, 3);
			$filial					= odbc_result($result, 4);
			$protocolo				= odbc_result($result, 5);
			$ressalva				= odbc_result($result, 6);
			$status_protocolo		= odbc_result($result, 7);


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td><center><font size='-1'>" . $documento . "</font></center></td>
					<td><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td><center><font size='-1'>" . $filial . "</font></center></td>										
					<td><center><font size='-1'>" . $ressalva . "</font></center></td>	
					<td><center><font size='-1'>" . $protocolo . "</font></center></td>	
					<td><center><font size='-1'>" . $status_protocolo . "</font></center></td>								
				  </tr>";
		}

		$table .= "</tbody></table>";


		$table .= "<script>order()</script>";

		$retorno->relatorio = $table;

		return $retorno;
	}

	public function exibeComprovanteEnvioRodopar($protocolo_id)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];


		$query = "select chave_acesso, comprovante.status_id, protocolo.status_id,protocolo.tipo_documento_id, comprovante.id
		from comprovante_envio_dig comprovante with (nolock)
		join protocolo_envio_comprovante_dig protocolo with (nolock) on
			protocolo.id = comprovante.protocolo_envio_id
		where protocolo.id = $protocolo_id
		and comprovante.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';
		$contador = 1;

		$table1 = "<div id='escondido' style=''>
				<table class='table table-bordered table-hover' id='myTable4'>
					<tr>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>ITEM</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>TIPO</td>
						<td bgcolor='#d3d3d3' width='150'><font color='#000000'><b>FILIAL</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>SERIE</td>
						<td bgcolor='#d3d3d3' width='300'><font color='#000000'><b>DOC</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>EMISSÃO</td>
						<td bgcolor='#d3d3d3' width='100'><font color='#000000'><b>CHAVE</td>
						<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></th>
					</tr><tbody id='tabela1'>";



		$aux = '';

		while (odbc_fetch_row($result)) {
			$key				= odbc_result($result, 1);
			$tipoDoc				= odbc_result($result, 4);
			$id				= odbc_result($result, 5);
			$doc = $tipoDoc == 'F' ? 'Fatura' : 'CTE';
			$filial = '';
			if ($tipoDoc == 'C') {
				$cUF = substr($key, 0, 2);        // Código da UF do emitente do Documento Fiscal
				$AAMM = substr($key, 2, 4);       // Ano e Mês de emissão do CT-e
				$CNPJ = substr($key, 6, 14);      // CNPJ do emitente
				$mod = substr($key, 20, 2);       // Modelo do Documento Fiscal
				$serie = substr($key, 22, 3);     // Série do Documento Fiscal
				$nCT = substr($key, 25, 9);       // Número do Documento Fiscal
				$tpEmis = substr($key, 34, 1);    // Forma de emissão do CT-e
				$cCT = substr($key, 35, 8);       // Código Numérico que compõe a Chave de Acesso
				$cDV = substr($key, 43, 1);

				if ($CNPJ == '51485274000159' && $serie == '1') {
					$filial = 'MTZ';
				} else if ($CNPJ == '51485274000159' && ($serie == '2' or $serie == '3')) {
					$filial = 'DEL';
				} else if ($CNPJ == '51485274000159' && ($serie == '4' || $serie == '5')) {
					$filial = 'SYN';
				} else if ($CNPJ == '51485274000230' && $serie == '1') {
					$filial = 'SPO';
				} else if ($CNPJ == '51485274000230' && ($serie == '2' or $serie == '3')) {
					$filial = 'GRU';
				} else if ($CNPJ == '51485274000400' && $serie == '1') {
					$filial = 'STS';
				} else if ($CNPJ == '51485274000663' && $serie == '1') {
					$filial = 'GUA';
				} else if ($CNPJ == '51485274001040' && $serie == '1') {
					$filial = 'CAM';
				} else if ($CNPJ == '51485274001040' && ($serie == '2' or $serie == '3')) {
					$filial = 'BSC';
				} else if ($CNPJ == '51485274001040' && ($serie == '4' or $serie == '5')) {
					$filial = 'ITV';
				} else if ($CNPJ == '51485274001040' && ($serie == '6' or $serie == '7')) {
					$filial = 'PSO';
				} else if ($CNPJ == '51485274001392' && $serie == '1') {
					$filial = 'MTG';
				} else if ($CNPJ == '51485274001473' && $serie == '1') {
					$filial = 'ADG';
				}
			} else {


				$filial = substr($key, 0, 2);
				$serie = substr($key, 2, 3);
				$nCT = substr($key, 5, 6);
				$AAMM = substr($key, 11, 4);
			}


			$data_edit = substr($AAMM, 2, 4) . '/20' . substr($AAMM, 0, 2);

			$table1 .= "<tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
							<td ><center><font size='-1'>" . $contador . "</font></center></td>
							<td ><center><font size='-1'>" . $doc . "</font></center></td>	
							<td ><center><font size='-1'>" . $filial . "</font></center></td>						
							<td ><center><font size='-1'>" . $serie . "</font></center></td>";


			$table1 .= "<td ><center><font size='-1'>" . $nCT . "</font></center></td>";

			$table1 .= "<td ><center><font size='-1'>" . $data_edit . "</font></center></td>";
			$table1 .= "<td style='white-space: nowrap;'><center><font size='-1'>$key&nbsp;</font></center></td>";
			$table1 .= "<td><center><font size='-1'><a href='javascript:exclui_comprovante($id,$protocolo_id,3);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center></td>";
			$table1 .= "</tr>";


			$contador++;
		}

		//$table .= "</tbody></table>";
		$table1 .= "</tbody></table></div>";

		//$table1 = str_replace("=REPT", "", $table);

		//$table .= "<input type='hidden' name='aux' id='aux' maxlength='5' size='1' value='$aux'/>";

		$retorno->comprovante = $table1 . $table;

		return $retorno;
	}


	public function pesquisaProtocoloRodopar($num_protocolo, $tipo_documento_id, $num_documento, $status_id, $data_inicial, $data_final)
	{

		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];

		$ip = $_SERVER['HTTP_HOST'];

		if ($num_protocolo != '')
			$condicao_protocolo = "&& protocolo.id = $num_protocolo";
		else
			$condicao_protocolo = "";

		if ($tipo_documento_id != '') {
			if ($tipo_documento_id == 'C')
				$condicao_tipo = "join comprovante_envio_dig comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										and comprovante.status_id = 'a'	
								  join cargosol..docto_transporte dt with (nolock) on
										dt.docto_transporte_id = comprovante.docto_transporte_id
										and dt.tab_status_id <> 2
								  join cargosol..cte with (nolock) on
										cte.docto_transporte_id = dt.docto_transporte_id
										and cte.numero = '$num_documento'";
			else
				$condicao_tipo = "join comprovante_envio_dig comprovante with (nolock) on
										comprovante.protocolo_envio_id = protocolo.id
										and comprovante.status_id = 'a'	
								  join corpore..TMOV WITH (NOLOCK) on
										TMOV.IDMOV = comprovante.idmov
										and TMOV.NUMEROMOV = '$num_documento'";
		} else
			$condicao_tipo = "";


		if ($status_id != '')
			$condicao_status = "&& protocolo.status_id = '$status_id'";
		else
			$condicao_status = "";


		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = "&& protocolo.data_hora_gravacao >= '$data_inicial' && protocolo.data_hora_gravacao < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";


		$query = "select protocolo.id num_protocolo, 
					convert(varchar(10), protocolo.data_hora_gravacao, 103)+' '+convert(varchar(5), protocolo.data_hora_gravacao, 108) data_gravacao,
					case when tipo_documento_id = 'C'
							then 'CTE'
							else 'FICHA'
					end tipo_docto,
					usuario.nome usuario,
					case protocolo.status_id
						when 'a' then 'ATIVO'
						when 'f' then 'FINALIZADO'
						when 'i' then 'CANCELADO'
						when 't' then 'EM TRANSITO'
					end status,
					protocolo.status_id
					from protocolo_envio_comprovante_dig protocolo with (nolock)
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao
					
					$condicao_tipo
					
					where 1 = 1
					$condicao_protocolo
					$condicao_status
					$condicao_datas";
		//print $query;
		$result = $con->executar($query);

		$retorno = '';
		$arq_xml = '';


		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='400'>TIPO DOCUMENTO</th>
						<th bgcolor='#d3d3d3' width='70'>USUÁRIO</th>
						<th bgcolor='#d3d3d3' width='70'>STATUS</th>
					</thead><tbody>
				  ";


		while (odbc_fetch_row($result)) {
			$num_protocolo			= odbc_result($result, 1);
			$data_emissao			= odbc_result($result, 2);
			$tipo_docto				= odbc_result($result, 3);
			$usuario				= odbc_result($result, 4);
			$status					= odbc_result($result, 5);
			$status_id				= odbc_result($result, 6);


			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>
						<a href='http://$ip/comprovante_entrega/envio_comprovante_dig.php?id=$num_protocolo' target='_blank'>" . $num_protocolo . "</a></font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $data_emissao . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $tipo_docto . "</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $usuario . "</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>" . $status . "</font></center></td>									
				  </tr>";
		}

		$table .= "</tbody></table>";

		$table .= "<script>order()</script>";

		$retorno->relatorio = $table;

		return $retorno;
	}
}
