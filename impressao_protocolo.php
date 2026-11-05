<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
//require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";


$autenticacao_sci = new Autentica();

$con = new ConexaoSCI();

$dados = new Dados;


$localItem = "../comprovante_entrega/envio_comprovante.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;
$nome_usuario  = $dados_autenticacao->nome_usuario;

$acesso = "permitido";


if ($acesso <> "permitido") {
	print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";

	$dados_gravacao = $autenticacao_sci->redirecionaLogin();
	$local  = $dados_gravacao->local;

	print "<script language = 'JavaScript'>window.location='$local';</script/>";
} else {
	$dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item, $usuario_id);

	/** Buttons */
	if ($fechar_p != "") {
		print "<script language='javascript'>open(location, '_self').close();</script>";
	}
	if ($buscar) {
	}


	$id			= $_GET['id'];
	$tela		= $_GET['tl'];


	$data_impressao = date("d/m/Y");
?>
	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Protocolo de Entrega</title>

		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
		<script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>
		<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

		<link rel="stylesheet" href="../SCI/css/bootstrap.css">

		<link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

		<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css" />

		<script src="../SCI/js/bootstrap.js"></script>
		<script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

		<style type='text/css'>
			.botao_site_1 {
				font-family: Verdana;
				font-size: 15px;
				font-style: normal;
				font-weight: normal;
				font-variant: normal;
				color: #FFFFFF;
				margin: 2px;
				padding: 0px;
				height: 30px;
				width: auto;
				border: 1px solid #FFFFFF;
				background-color: #095D92;
				text-align: center;
				vertical-align: middle;
				cursor: default;
			}

			.cor-table {
				border: solid #000 2px !important;

			}

			@media print {
				tr {
					page-break-inside: avoid;
				}

				.no-print {
					display: none;
				}
			}
		</style>


		<script language="javascript">
			function imprimi_some() {
				$("#botao_imprimir").hide();
				self.print();
				//$("#botao_imprimir").show(); 
				//open(location, '_self').close();
			}
		</script>


	</head>

	<body>
		<form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
			<?php
			if ($tela == 1) {
				$relatorio_comprovante = $dados->imprimeComprovanteEnvio($id);
				$titulo = "PROTOCOLO ENVIO DE COMPROVANTES PARA MATRIZ";
			} else {
				$relatorio_comprovante = $dados->imprimeComprovanteEnvioDig($id);
				$titulo = "PROTOCOLO ENVIO DE COMPROVANTES PARA DIGITALIZAÇÃO";
			}

			$num_protocolo = $relatorio_comprovante->num_protocolo;
			$tipo_documento = $relatorio_comprovante->tipo_documento;
			$ponto_operacao = $relatorio_comprovante->ponto_operacao;
			$rel_comprovante = $relatorio_comprovante->comprovante;


			?>
			<div>
				<table width="877" border="0" cellspacing=0 cellpadding=2 bordercolor="666633">
					<tr>
						<td>&nbsp;&nbsp;&nbsp;</td>
						<td>
							<table width="877" border="0" cellspacing=0 cellpadding=2 bordercolor="666633">
								<tr>
									<td width="156"><img src="../SCI/images/logo_covre.jpg" width="156" height="50"></td>
									<td width="601">
										<div align="center">
											<font size="+2"><b><?php print $titulo ?></b></font>
										</div>
									</td>
									<td width="108">&nbsp;</td>
								</tr>
							</table>
							<p></p>


							<p></p>
							<table width="876" border="1" cellspacing=0 cellpadding=2 bordercolor="666633" frame="box" rules="none">
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>
								<tr>
									<td width="10">&nbsp;</td>
									<td width="194">
										<div align="left">
											<font size="+1"><b>Nº PROTOCOLO: </b></font>
										</div>
									</td>
									<td width="254">
										<div align="left">
											<font size="+1"><b>TIPO DOCUMENTO: </b></font>
										</div>
									</td>
									<td width="392">
										<div align="left">
											<font size="+1"><b>PONTO DE OPERAÇÃO: </b></font>
										</div>
									</td>
								</tr>
								<tr>
									<td width="10">&nbsp;</td>
									<td width="194"><?php print $num_protocolo ?></td>
									<td width="254"><?php print $tipo_documento ?></td>
									<td width="392"><?php print $ponto_operacao ?></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
								</tr>

							</table>


						</td>
					</tr>
				</table>
				<div style="margin: 20px;">
					<?php
					print $rel_comprovante;
					?>
				</div>





				<table width="876" border="0">
					<tr>
						<td width="225" height="30"></td>
					</tr>
				</table>
				<table width="876" border="0" cellspacing=0 cellpadding=2 bordercolor="666633" frame="box" rules="none">
					<tr>
						<td width="493" height="26">
							<div align="left">
								<font size="+1"><b>USUÁRIO IMPRESSÃO: </b></font>
							</div>
						</td>
						<td width="369">
							<div align="left">
								<font size="+1"><b>DATA IMPRESSÃO: </b></font>
							</div>
							<div align="left"></div>
						</td>
					</tr>
					<tr>
						<td width="493"><?php print $nome_usuario ?></td>
						<td><?php print $data_impressao ?></td>
					</tr>
					<tr>
						<td width="493">&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>

				<div id="botao_imprimir">
					<table width="100%" border='0'>
						<tr>
							<td class="no-border left altura">
								<div align="center" style="width: 880px;
    padding: 10px;">
									<button type='button' class='btn btn-theme altura-campo' id='imprimir' name='imprimir' style=' color:#FFF; background-color:#095D92' onClick="imprimi_some()"> Imprimir </button>
									<button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>
								</div>
							</td>
							<td class="no-border left altura">
								<div align="center"></div>
							</td>
						</tr>
					</table>
				</div>

		</form>





		</div>
		<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
		<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
		<script type="text/javascript" src="js/main.js"></script>





	</body>

	</html>
<?


}
?>