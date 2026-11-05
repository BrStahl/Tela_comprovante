<?php
session_name("covre_ti");
session_start();

require_once $_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php";
require_once "Dados.php";

class GravaDadosCanhoto extends ConexaoSCI{


	public function gravaProtocoloCanhoto($id, $data_prot, $cliente_grupo_id, $cliente_id, $grupo_cliente_id, $responsavel_cliente, $depto_cliente, $status_id){
		
		$con = new ConexaoSCI;
		
		$dados = new Dados;	
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		
		if ($logado != '')
		{
	
			$responsavel_cliente = utf8_decode($responsavel_cliente);
			$depto_cliente		 = utf8_decode($depto_cliente);
	
			if ($id == '')
			{
			
				$query = "insert into protocolo_canhoto_nfe (data, cliente_grupo_id, cliente_id, grupo_cliente_id, 
						  responsavel_cliente, depto_cliente, user_gravacao, data_hora_gravacao, status_id) 
						  values ('$data_prot', '$cliente_grupo_id', 
						  case when '$cliente_id' = '' then NULL else '$cliente_id' end,
						  case when '$grupo_cliente_id' = '' then NULL else '$grupo_cliente_id' end, 
						  case when '$responsavel_cliente' = '' then NULL else '$responsavel_cliente' end, 
						  case when '$depto_cliente' = '' then NULL else '$depto_cliente' end, 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), 
						  getdate(), '$status_id')";
				//print $query;
				$con->executar($query);
				
				$query = "SELECT @@IDENTITY AS Ident";
				//print $query;
				$result = $con->executar($query);				
				$id = odbc_result($result, 1);
				
				$ret_gravacao = "1|ok|$id";
			}
			else
			{
				$query = "update protocolo_canhoto_nfe
						  set data = '$data_prot',
						  cliente_grupo_id = '$cliente_grupo_id',
						  cliente_id = case when '$cliente_id' = '' then NULL else '$cliente_id' end,
						  grupo_cliente_id = case when '$grupo_cliente_id' = '' then NULL else '$grupo_cliente_id' end,
						  responsavel_cliente = case when '$responsavel_cliente' = '' then NULL else '$responsavel_cliente' end,
						  depto_cliente = case when '$depto_cliente' = '' then NULL else '$depto_cliente' end,
						  data_hora_inativacao = case when '$status_id' = 'a' then null else getdate() end,
						  user_inativacao = case when '$status_id' = 'a' then null else (select id 
																						 from usuario with (nolock)
																						 where usuario = '$logado' 
																						 and status = 'a') end,
						  status_id = '$status_id'
						  where id = $id";
				//print $query;
				$con->executar($query);
				
				
				$ret_gravacao = "2|ok|$id";			
				
			}
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}	
	
	
	public function gravaNotaFiscal($protocolo_id, $nota_fiscal){
		
		$con = new ConexaoSCI;
		
		$dados = new Dados;	
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		
		if ($logado != '')
		{
			//VERIFICA SE A NOTA FISCAL JÁ FOI DIGITADA NO LOTE
			$query = "SELECT id
					  FROM canhoto_nfe with (nolock)
					  where status_id = 'a'
					  and protocolo_id = $protocolo_id
					  and nota_fiscal = '$nota_fiscal'";
			//print $query;
			$result = $con->executar($query);			
			$nf_inserida	= odbc_result($result,1);			
			
			if ($nf_inserida != '')
			{
				$ret_gravacao = "Nota Fiscal Já inserida neste protocolo";			
			}
			else
			{
				$query = "insert into canhoto_nfe (protocolo_id, nota_fiscal, user_gravacao, data_hora_gravacao, status_id) 
						  values ($protocolo_id, '$nota_fiscal', 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), 
						  getdate(), 'a')";
				//print $query;
				$con->executar($query);
				
				$ret_gravacao = "1|ok|$id";
			}
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}		
	
	
	public function excluiCanhoto($id){
		
		$con = new ConexaoSCI;
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		if ($logado != '')
		{

			$query = "update canhoto_nfe
					  set status_id = 'i', 
					  data_hora_inativacao = getdate(), 
					  user_inativacao = (select id from usuario where usuario = '$logado' and status = 'a')
					  where id = $id";
			//print $query;
			$con->executar($query);
			
			$ret_gravacao =  "ok";
		}
		else
			$ret_gravacao = "sessao_expirada";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}
	
	
	public function finalizaProtocolo($id){
		
		$con = new ConexaoSCI;
		
		$dados = new Dados;	
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		if ($logado != '')
		{

			//VERIFICA SE O COMPROVANTE JÁ ESTÁ VINCULADO AO PROTOCOLO
			$query = "update protocolo_canhoto_nfe
					  set status_id = 'f',
					  user_finalizacao = (select id from usuario where usuario = '$logado' and status = 'a'),
					  data_hora_finalizacao = getdate()
					  where id = $id
					  and status_id = 'a'";
			//print $query;
			$con->executar($query);

		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}		
	
	
	
public function gravaProtocoloCanhotoPendente($values){
		
		$con = new ConexaoSCI;
		
		$dados = new Dados;	
		
		$logado = $_SESSION['usuario_logado'];
		
		$retorno = '';
		
		
		if ($logado != '')
		{
			$count = substr_count($values, ',');
	
			$cliente_grupo_id = 'G';
			$grupo_cliente_id = 217;
	
			$query = "insert into protocolo_canhoto_nfe (data, cliente_grupo_id, cliente_id, grupo_cliente_id, 
					  user_gravacao, data_hora_gravacao, status_id) 
					  values (CONVERT(VARCHAR(10), GetDate(), 120), '$cliente_grupo_id', 
					  case when '$cliente_id' = '' then NULL else '$cliente_id' end,
					  case when '$grupo_cliente_id' = '' then NULL else '$grupo_cliente_id' end, 
					  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), 
					  getdate(), 'a')";
			//print $query;
			$con->executar($query);	

			$query = "SELECT @@IDENTITY AS Ident";
			//print $query;
			$result = $con->executar($query);				
			$protocolo_id = odbc_result($result, 1);			
	
	
			for($i=0;$i<=$count;$i++)
			{
				if ($i < $count)
				{					
					$pos_virgula = strpos($values,',');	
					$nota_fiscal_id = substr($values, 0, $pos_virgula); 
				
					$values = str_replace($nota_fiscal_id.",","",$values);
				}
				else
					$nota_fiscal_id = $values;					
			
			
				$query = "insert into canhoto_nfe (protocolo_id, nota_fiscal, user_gravacao, data_hora_gravacao, status_id, 
						  nota_fiscal_id) 
						  SELECT $protocolo_id,NOTA_FISCAL, 
						  (select id from usuario with (nolock) where usuario = '$logado' and status = 'a'), getdate(), 'a', 
						  NOTA_FISCAL_ID
						  FROM CARGOSOL..NOTA_FISCAL WITH (NOLOCK)
						  WHERE NOTA_FISCAL_ID = $nota_fiscal_id";
				//print $query;
				$con->executar($query);
			
			}

			$ret_gravacao = "1|ok|$protocolo_id";
			
		}
		else
			$ret_gravacao = "Sessão expirada, favor logar novamente";

		$retorno->ret_gravacao = $ret_gravacao;

		return $retorno;
	}		
	
	
}
	


?>