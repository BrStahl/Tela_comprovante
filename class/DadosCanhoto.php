<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";


class DadosCanhoto extends ConexaoSCI{

	
	//BUSCA DADOS
	public function exibeGrupoCliente(){
		
		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];
		

		$query = "select GRUPO_CLIENTE_ID, grupo
					from cargosol..grupo_cliente gc with (nolock)
					where TAB_STATUS_ID = 1
					and GRUPO_CLIENTE_ID not in (84,70,37)
					order by grupo";
		//print $query;
		$result = $con->executar($query);			
		
		$retorno = '';
		
		$select = "<option value=''></option>";
		
		while(odbc_fetch_row($result)) 
		{ 
			$grupo_cliente_id	= odbc_result($result,1);
			$nome_grupo 		= odbc_result($result,2);

			$select .= "<option value='".$grupo_cliente_id."'>".$nome_grupo."</option>";
			
		}

		$retorno->relatorio = $select;

		return $retorno;	
	
	}


	public function pesquisaProtocolo($num_protocolo,$nota_fiscal,$cliente_grupo_id,$cliente_id,$grupo_cliente_id,$status_id,$data_inicial,$data_final){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$ip = $_SERVER['HTTP_HOST'];
	 	
		if ($num_protocolo != '')
			$condicao_protocolo = "and protocolo.id = $num_protocolo";
		else
			$condicao_protocolo = "";	
			
		if ($nota_fiscal != '')
		{	
			$condicao_nota_fiscal = "join canhoto_nfe canhoto_nfe with (nolock) on
										canhoto_nfe.protocolo_id = protocolo.id
										and canhoto_nfe.status_id = 'a'
										and canhoto_nfe.nota_fiscal = '$nota_fiscal'";
		}
		else
			$condicao_nota_fiscal = "";	
			
		if ($cliente_grupo_id != '')
		{
			if ($cliente_id != '')
				$condicao_cliente_grupo = "AND PESSOA.PESSOA_ID = $cliente_id";
			else
				$condicao_cliente_grupo = "AND grupo.grupo_cliente_id = $grupo_cliente_id";
		}
		else
			$condicao_cliente_grupo = "";
			
			
			
			
		if ($status_id != '')
			$condicao_status = "and protocolo.status_id = '$status_id'";
		else
			$condicao_status = "";	
			
			
		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = "and protocolo.data_hora_gravacao >= '$data_inicial' and protocolo.data_hora_gravacao < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";														
	 	
	 
		$query = "select protocolo.id num_protocolo, 
					convert(varchar(10), protocolo.data, 103) data,
					case when cliente_id is not null
							then '<b>CLIENTE: </b>'+PESSOA.Nome_Fantasia
							ELSE '<b>GRUPO: </b>'+GRUPO.GRUPO
					END CLIENTE_GRUPO,					
					usuario.nome usuario,
					case protocolo.status_id
						when 'a' then 'ATIVO'
						when 'f' then 'FINALIZADO'
						when 'i' then 'CANCELADO'
					end status,
					protocolo.status_id
					from protocolo_canhoto_nfe protocolo with (nolock)
					join usuario with (nolock) on
						usuario.id = protocolo.user_gravacao
					left join CARGOSOL..pessoa with (nolock) on	
					pessoa.pessoa_id = protocolo.cliente_id
					left join cargosol..grupo_cliente grupo with (nolock) on
						grupo.grupo_cliente_id = protocolo.grupo_cliente_id					
					
					$condicao_nota_fiscal
					
					where 1 = 1
					$condicao_protocolo
					$condicao_cliente_grupo
					$condicao_status
					$condicao_datas";
		//print "<pre>";
		//print $query;
		//print "</pre>";		
		$result = $con->executar($query);
		
		$retorno = '';
		$arq_xml = '';

			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'>Nº PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='100'>DATA EMISSÃO</th>	
						<th bgcolor='#d3d3d3' width='70'>CLIENTE/GRUPO</th>
						<th bgcolor='#d3d3d3' width='70'>USUÁRIO</th>
						<th bgcolor='#d3d3d3' width='70'>STATUS</th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$num_protocolo			= odbc_result($result,1);
			$data_emissao			= odbc_result($result,2);
			$cliente_grupo			= odbc_result($result,3);
			$usuario				= odbc_result($result,4);
			$status					= odbc_result($result,5);
			$status_id				= odbc_result($result,6);
	

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>
						<a href='http://$ip/comprovante_entrega/canhoto_nfe.php?id=$num_protocolo' target='_blank'>".$num_protocolo."</a></font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_emissao."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$cliente_grupo."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$usuario."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status."</font></center></td>									
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->relatorio = $table;

		  return $retorno;

	}


	public function buscaProtocoloCanhoto($protocolo_id){
		
		$con = new ConexaoSCI;

		$logado = $_SESSION['usuario_logado'];
		
		$ip = $_SERVER['HTTP_HOST'];
		
		$retorno = '';
		$arq_xml = '';
	 
		$query = "select id, convert(varchar(10), data, 103) data, cliente_grupo_id, cliente_id, pessoa.nome_fantasia, 
				  grupo_cliente_id, responsavel_cliente, depto_cliente, status_id
				  from protocolo_canhoto_nfe protocolo with (nolock)
				  left join cargosol..pessoa with (nolock) on
					pessoa.pessoa_id = protocolo.cliente_id
				  where id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);
	
		$id						= odbc_result($result,1);
		$data					= odbc_result($result,2);
		$cliente_grupo_id		= odbc_result($result,3);		
		$cliente_id				= odbc_result($result,4);
		$nome_cliente			= odbc_result($result,5);
		$grupo_cliente_id		= odbc_result($result,6);
		$responsavel_cliente	= odbc_result($result,7);			
		$depto_cliente			= odbc_result($result,8);
		$status_id				= odbc_result($result,9);

		$responsavel_cliente = utf8_encode($responsavel_cliente);
		$depto_cliente = utf8_encode($depto_cliente);

		$resultado = "1|$id|$data|$cliente_grupo_id|$cliente_id|$nome_cliente|$grupo_cliente_id|$responsavel_cliente|$depto_cliente|$status_id|";
		
	  	$retorno->protocolo = $resultado;

		return $retorno;

	}


	public function exibeCanhotoProtocolo($protocolo_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select canhoto.id, nota_fiSCal, protocolo.status_id
				  from canhoto_nfe canhoto with (nolock)
				  join protocolo_canhoto_nfe protocolo with (nolock) on
					protocolo.id = canhoto.protocolo_id
				  where protocolo_id = $protocolo_id
				  and canhoto.status_id = 'a'";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
		$arq_xml = '';
		$aux = '';
			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='100'><a href='javascript:void(0)'>NOTA_FISCAL</a></th>
						<th bgcolor='#d3d3d3' width='50'><a href='javascript:void(0)'>EXCLUIR</a></th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$id						= odbc_result($result,1);
			$nota_fiscal			= odbc_result($result,2);
			$status_protocolo		= odbc_result($result,3);
	
			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$nota_fiscal."</font></center></td>";	

			if ($status_protocolo == 'a')
			{
				$aux = 'S';
				$table .= "<td><center><font size='-1'><a href='javascript:exclui_canhoto($id,$protocolo_id);'><img src='../SCA/images/excluir1.jpg' width='30' heigth='30' style='border:none'></a></font></center>
					</td>";
									
			}
			else
				$table .= "<td><center><font size='-1'> - </font></center>
					</td>";			
			

			$table .= "</tr>"; 
				 
		 }//fim while 
		 
		  $table .= "</table>";
		  
		  $table .= "<input type='hidden' name='aux' id='aux' maxlength='5' size='1' value='$aux'/>";
		  
		  $retorno->canhoto = $table;

		  return $retorno;

	}


	public function buscaCliente($cliente){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
			  
		$fabricante = utf8_decode($fabricante);
			 
		$query = "select pessoa_id
				  from cargosol..pessoa with (nolock)
				  where tab_status_id <> 2
				  and nome_fantasia = '$cliente'";
		//print $query;
		$result = $con->executar($query);
		
		$retorno = '';
 
		$id		= odbc_result($result,1);

		$retorno->resultado = "1|".$id."|";

		return $retorno;	
	
	}


	public function imprimeProtocoloCanhoto($protocolo_id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
	 
		$query = "select distinct canhoto.nota_fiscal, protocolo.id num_protocolo, 
					convert(varchar(10), protocolo.data, 103) data,
					case when cliente_id is not null
							THEN PESSOA.Nome_Fantasia
							ELSE GRUPO.GRUPO+' (GRUPO)'
					END CLIENTE_GRUPO,					
					responsavel_cliente,
					depto_cliente
					from canhoto_nfe canhoto with (nolock)
					join protocolo_canhoto_nfe protocolo with (nolock) on
						protocolo.id = canhoto.protocolo_id
					left join CARGOSOL..pessoa with (nolock) on	
					pessoa.pessoa_id = protocolo.cliente_id
					left join cargosol..grupo_cliente grupo with (nolock) on
						grupo.grupo_cliente_id = protocolo.grupo_cliente_id					
					where canhoto.status_id = 'a'
					and protocolo.status_id in ('f')
					and protocolo.id = $protocolo_id";
		//print $query;
		$result = $con->executar($query);
		$result_prot = $con->executar($query);
	
	
		$num_protocolo			= odbc_result($result_prot,2);
		$data					= odbc_result($result_prot,3);
		$cliente_grupo			= odbc_result($result_prot,4);		
		$responsavel_cliente	= odbc_result($result_prot,5);	
		$depto_cliente			= odbc_result($result_prot,6);	
		
		$responsavel_cliente = utf8_encode($responsavel_cliente);
		$depto_cliente = utf8_encode($depto_cliente);		
		
		$retorno = '';
		$arq_xml = '';
		$conta_coluna = 1;
			   
		$table = "
				<table border='1'>
					<tr>
						<td bgcolor='#d3d3d3' colspan='7' height='25'><center><font size='-1'><b>NOTAS FISCAIS</font></td>
					</tr>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$nota_fiscal			= odbc_result($result,1);

			if ($conta_coluna == 1)
				$table .= "<tr>";
				
			$table .= "
					<td bgcolor='#FFFFFF' width='125'><center><font size='-1'>".$nota_fiscal."</font></center></td>";

			if ($conta_coluna == 7)
			{
				$table .= "</tr>"; 
				$conta_coluna = 1;
			}
			else
				$conta_coluna++;
		 } 
		 
		 while($conta_coluna <= 7)
		 {
			$table .= "
					<td bgcolor='#FFFFFF' width='125' ><center><font size='-1'>&nbsp;</font></center></td>";			 
			 
			$conta_coluna++;
		 }
		 
		 
		 
		  $table .= "</tbody></table>";

		  $retorno->num_protocolo = $num_protocolo;
		  $retorno->data = $data;
		  $retorno->cliente_grupo = $cliente_grupo;
		  $retorno->responsavel_cliente = $responsavel_cliente;
		  $retorno->depto_cliente = $depto_cliente;
		  $retorno->canhoto = $table;

		  return $retorno;

	}


	public function comparaDatas($data_inicial,$data_final){
		
		$con = new ConexaoSCI;
		
		$query = "select case when '$data_inicial' > '$data_final'
								then 'maior'
								else 'menor'
						 end";
		//print $query;
		$result = $con->executar($query);	

		$compara				= odbc_result($result,1);	
		
		$retorno = '';
		
		$retorno = $compara;

		//$retorno->compara = $compara;			
		
		return $retorno;	

	}	



public function pesquisaCanhotoPendente($nota_fiscal,$cliente_grupo_id,$cliente_id,$grupo_cliente_id,$status_id,$data_inicial,$data_final){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$ip = $_SERVER['HTTP_HOST'];

			
		if ($nota_fiscal != '')
		{	
			$condicao_nota_fiscal = "AND NF.NOTA_FISCAL = '$nota_fiscal'";
		}
		else
			$condicao_nota_fiscal = "";	
			
		if ($cliente_grupo_id != '')
		{
			if ($cliente_id != '')
				$condicao_cliente_grupo = "AND CLIENTE.PESSOA_ID = $cliente_id";
			else
				$condicao_cliente_grupo = "AND CLIENTE.GRUPO_CLIENTE_ID = $grupo_cliente_id";
		}
		else
			$condicao_cliente_grupo = "";
			
		
			
		if ($status_id == 'p')
			$condicao_status = "AND ((PROTOCOLO.STATUS_ID = 'a') OR (PROTOCOLO.STATUS_ID IS NULL))";
		else
			if ($status_id == 'f')
				$condicao_status = "AND PROTOCOLO.STATUS_ID = 'f'";
			else
				if ($status_id == 'a')
					$condicao_status = "";					
	
			
			
		if (($data_inicial != '') && ($data_final != ''))
			$condicao_datas = "AND NF.DATA_EMISSAO >= '$data_inicial' AND NF.DATA_EMISSAO < DATEADD(DD,1,'$data_final')";
		else
			$condicao_datas = "";														
	 	
	 
		$query = "SELECT 
					DADOS.NOTA_FISCAL,
					DADOS.SERIE,
					DADOS.EMISSAO,
					DADOS.NOTA_FISCAL_ID,
					CANHOTO.PROTOCOLO_ID PROTOCOLO,
					CASE PROTOCOLO.STATUS_ID
						WHEN 'a' THEN 'NAO FINALIZADO'
						WHEN 'f' THEN 'FINALIZADO'
					END STATUS_PROTOCOLO,
					DADOS.CLIENTE
					FROM (
							SELECT DISTINCT NF.NOTA_FISCAL, NF.SERIE, CONVERT(VARCHAR(10),NF.DATA_EMISSAO,103) EMISSAO, 
							MIN(NF.NOTA_FISCAL_ID) NOTA_FISCAL_ID, PESSOA.NOME_FANTASIA CLIENTE
							FROM CARGOSOL..DOCTO_TRANSPORTE DT WITH (NOLOCK)
							JOIN CARGOSOL..NOTA_FISCAL NF WITH (NOLOCK) ON
								NF.DOCTO_TRANSPORTE_ID = DT.DOCTO_TRANSPORTE_ID
							JOIN CARGOSOL..CLIENTE WITH (NOLOCK) ON
								CLIENTE.PESSOA_ID = DT.CLIENTE_ID
							JOIN CARGOSOL..PESSOA WITH (NOLOCK) ON
								PESSOA.PESSOA_ID = CLIENTE.PESSOA_ID
							WHERE DT.TAB_STATUS_ID <> 2
							AND NF.TAB_STATUS_ID = 1
							$condicao_nota_fiscal
							$condicao_datas
							$condicao_cliente_grupo
							GROUP BY NF.NOTA_FISCAL, NF.SERIE, NF.DATA_EMISSAO, PESSOA.NOME_FANTASIA
						)DADOS
					LEFT JOIN CANHOTO_NFE CANHOTO WITH (NOLOCK) ON
						CANHOTO.NOTA_FISCAL_ID = DADOS.NOTA_FISCAL_ID
						AND CANHOTO.STATUS_ID = 'a'
					LEFT JOIN PROTOCOLO_CANHOTO_NFE PROTOCOLO WITH (NOLOCK) ON
						PROTOCOLO.ID = CANHOTO.PROTOCOLO_ID
						AND PROTOCOLO.STATUS_ID IN ('a','f')
					WHERE 1 = 1
					$condicao_status
					ORDER BY DADOS.NOTA_FISCAL					
					";
		//print "<pre>".$query."</pre>";		
		$result = $con->executar($query);
		
		$retorno = '';
		$arq_xml = '';

			   
		$table = "
				<table class='table table-bordered table-hover' id='myTable3'>
					<thead>
						<th bgcolor='#d3d3d3' width='10'>&nbsp;</th>
						<th bgcolor='#d3d3d3' width='100'>NOTA FISCAL</th>
						<th bgcolor='#d3d3d3' width='100'>SÉRIE</th>	
						<th bgcolor='#d3d3d3' width='70'>DATA EMISSÃO</th>
						<th bgcolor='#d3d3d3' width='400'>CLIENTE</th>
						<th bgcolor='#d3d3d3' width='70'>PROTOCOLO</th>
						<th bgcolor='#d3d3d3' width='150'>STATUS PROTOCOLO</th>
					</thead><tbody>
				  ";				   
			   
		
		while(odbc_fetch_row($result)) 
		{ 
			$nota_fiscal			= odbc_result($result,1);
			$serie					= odbc_result($result,2);
			$data_emissao			= odbc_result($result,3);
			$nf_id					= odbc_result($result,4);
			$protocolo_id			= odbc_result($result,5);
			$status_protocolo		= odbc_result($result,6);
			$cliente				= odbc_result($result,7);
	
			if ($status_protocolo != '')
				$disabled = 'disabled';
			else
				$disabled = '';			
				

			$table .= "
				  <tr onmouseover=this.bgColor='#89BFF0' onmouseout=this.bgColor=''>
				  	<td><font size='-1'>
						  <center>
							<input type='checkbox' name='Pacote' id='sistema$nf_id' class='classe_checkbox' $disabled 
							value='$nf_id'/>
						  </center></font>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$nota_fiscal."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$serie."</font></center></td>	
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$data_emissao."</font></center></td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$cliente."</font></center></td>
					<td bgcolor='#FFFFFF'><center>
						<a href='http://$ip/comprovante_entrega/canhoto_nfe.php?id=$protocolo_id' target='_blank'>
							<font size='-1'>".$protocolo_id."</a></font></center>
					</td>
					<td bgcolor='#FFFFFF'><center><font size='-1'>".$status_protocolo."</font></center></td>	
				  </tr>"; 
				 
		 } 
		 
		  $table .= "</tbody></table><button type='button' class='btn btn-theme altura-campo' id='gerar' name='gerar' style=' color:#FFF; background-color:#095D92' onclick='javascript:gera_protocolo()'>Gerar Protocolo</button>";
		  
		  $table .= "<script>order()</script>";
		  
		  $retorno->relatorio = $table;

		  return $retorno;

	}

}


?>