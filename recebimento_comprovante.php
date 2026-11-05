<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT'] . "/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/Dados.php";


$autenticacao_sci = new Autentica();

$con = new ConexaoSCI();

$dados = new Dados;


$localItem = "../comprovante_entrega/protocolo_pendente.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem, $logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;

//$acesso = "permitido";


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


	$id		= $_GET['id'];


	if ($novo != "") {
		print "
		<script language='javascript'>
		window.location.href='envio_comprovante.php';
		</script>
		";
	}



?>

	<!DOCTYPE html>
	<html lang="en">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="X-UA-Compatible" content="ie=edge">
		<title>Protocolo - Recebimento de Comprovante</title>
		<link rel="stylesheet" href="../SCI/css/bootstrap.css">
		<link rel="stylesheet" href="../SCI/css/estilo_bts.css">
		<link rel="stylesheet" href="css/relsm.css">
		<link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
		<link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css" />
		<link rel="stylesheet" type="text/css" href="https://nightly.datatables.net/buttons/css/buttons.dataTables.min.css" />
		<script src="../SCI/js/jquery.js"></script>
		<script src="../SCI/js/bootstrap.js"></script>
		<script src="../SCI/js/jquery.ajaxQueue.js"></script>
		<script src="../SCI/js/jquery.mask.js"></script>
		<script type="text/javascript" src="../SCI/js/datatables/datatables.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/plug-ins/1.10.10/sorting/datetime-moment.js"></script>

		<script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/dataTables.buttons.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.flash.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
		<script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.html5.min.js"></script>
		<script type="text/javascript" language="javascript" src="https://nightly.datatables.net/buttons/js/buttons.print.min.js"></script>

		</script>

		<style>
			.sem-borda {

				border-color: transparent !important;

			}

			#table1 table tr {
				border-color: #F00 !important;
			}
		</style>

		<script language="javascript">
			//var tempo1="";
			function confere_chave(elmnt, protocolo_id) {

				var tipo_documento_id = document.getElementById('tipo_documento_id').value;

				var sistema = document.getElementById('sistema').value;

				if (elmnt.value != '') {
					var numero_chave = elmnt.value;

					if (((tipo_documento_id == 'C') && (elmnt.value.length == 44)) ||
						((tipo_documento_id == 'F') && (elmnt.value.length == 15))) {
						$.ajax({
							type: "POST", //define o metódo de passagem de parametros
							url: "includes/confere_chave.php", //chama uma pagina
							data: "chave=" + elmnt.value + "&protocolo_id=" + protocolo_id + "&sistema=" + sistema + "&tp_doc=" + tipo_documento_id,
							success: function(msg) { //pega o retorno da pagina chamada
								console.log(msg)
								if (msg.indexOf("ok") == -1) {
									$('#msg').html(msg);
									$('#myModal_alert').modal('show');
								}

								exibe_comprovante_nao_conferido(protocolo_id);
								exibe_comprovante_conferido(protocolo_id);

								document.form1.chave_acesso.value = '';
								document.form1.chave_acesso.focus();

								//$("#exibe_resultado").html(msg);

							}
						});

					}

				}

			}
		</script>

	</head>


	<body style="background-color: #3CB371">
		<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
			<div class="panel-heading panel-legend" id="painel-heading"><b>Protocolo - Recebimento de Comprovante</b></div>
		</div>
		<br>
		<div class="col-xs-11">
			<?php

			$relatorio_comprovante = $dados->imprimeComprovanteEnvio($id);

			$num_protocolo 		= $relatorio_comprovante->num_protocolo;
			$tipo_documento 	= $relatorio_comprovante->tipo_documento;
			$tipo_documento_id 	= $relatorio_comprovante->tipo_documento_id;
			$ponto_operacao 	= $relatorio_comprovante->ponto_operacao;
			$status_protocolo 	= $relatorio_comprovante->status_protocolo;
			$rel_comprovante 	= $relatorio_comprovante->comprovante;
			$sistema 	= $relatorio_comprovante->sistema;

			if ($status_protocolo == 't')
				$desc_status = 'EM TRANSITO';
			else {
				$desc_status = 'FINALIZADO';
				$disabled_campo = 'disabled';
			}

			?>

			<form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
				<div class="panel panel-default col-lg-12 panel-padrao">
					<div class="panel-body col-xs-12">
						<font color="#0000FF"><b>Dados do Protocolo</b></font>
						<div class="table-responsive covre">
							<table width="90%" class="table table-bordered tb-padrao">
								<tbody>
									<tr>
										<td width="35" class="no-border left altura">
											<font size="-1"><b><span class="col-xs-2 left-inline">
														<input name='id' type='hidden' class='form-control txt-center altura-campo' id='id' value="<?php print $id ?>" size="4" maxlength='44' aria-describedby='basic-addon1'>
													</span></b></font>
										</td>
										<td width="108" class="no-border left altura">
											<font size="-1"><b>Protocolo:</b></font>
										</td>
										<td width="154" class="no-border left altura">
											<font size="-1"><b>Malote:</b></font>
										</td>
										<td align="left" class="no-border left altura">
											<font size="-1"><b>Tipo Documento:</b></font>
										</td>
										<td width="358" align="left" class="no-border left altura">
											<font size="-1"><b>Ponto de Operação:</b></font>
										</td>
										<td width="214" align="left" class="no-border left altura">
											<font size="-1"><b>Status:</b></font>
										</td>
										<td width="25" colspan="2" rowspan="2" align="left" class="no-border left altura">&nbsp;</td>
									</tr>

									<tr>
										<td class="no-border left altura">
											<font size="-1"><b><span class="col-xs-2 left-inline">
														<input name='tipo_documento_id' type='hidden' class='form-control txt-center altura-campo' id='tipo_documento_id' value="<?php print $tipo_documento_id ?>" size="4" maxlength='44' aria-describedby='basic-addon1'>
													</span></b></font>
										</td>
										<td class="no-border left altura">
											<font color="#0000FF" size="+1"><b>
													<div id="num_protocolo"></div>
												</b></font>
										</td>
										<td class="no-border left altura">
											<font size="+1">
												<div id="codigo_malote"></div>
											</font>
										</td>
										<td width="199" class="no-border left altura">
											<div class="col-xs-9 left-inline">
												<font size="+1"><?php print $tipo_documento ?></font>
											</div>
										</td>
										<td class="no-border left altura">
											<div class="col-xs-11 left-inline">
												<font size="+1"><?php print $ponto_operacao ?></font>
											</div>
										</td>
										<td class="no-border left altura">
											<div class="col-xs-9 left-inline">
												<div id="status_receb">
													<font size="+1"> <?php print $desc_status ?></font>
												</div>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p></p>
						<p></p>

						<table width="109%" class="table table-bordered tb-padrao">
							<tbody>
								<tr>
									<td colspan="2" class="no-border left altura">
										<font size="-1"><b>Chave de Acesso:</b></font>
									</td>
									<td width="86%" class="no-border left altura">
										<div class="col-xs-7 left-inline">
											<input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='chave_acesso' id='chave_acesso' maxlength='44' onkeyup="javascript:confere_chave(this, document.form1.id.value)" onkeydown="javascript: if (event.keyCode==13) {return false; } " <?php print $disabled_campo ?>>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
						<table width="109%" class="table table-bordered tb-padrao">
							<tbody>
								<tr>
									<td width="487" class="no-border left altura">
										<div align="center">
											<font size="-1"><b>Comprovantes Enviados</b></font>
										</div>
									</td>
									<td width="8" class="no-border left altura">
										<div align="center"></div>
									</td>
									<td width="603" class="no-border left altura">
										<div align="center">
											<font size="-1"><b>Comprovantes Recebidos</b></font>
										</div>
									</td>
								</tr>
								<tr>
									<td class="no-border left altura">
										<div id="exibe_resultado1">
											<?php

											$relatorio1 = $dados->exibeComprovanteNaoConferido($id);

											$rel_protocolo1 = $relatorio1->comprovante_nao_conferido;

											print $rel_protocolo1;

											?>
										</div>
									</td>
									<td class="no-border left altura">&nbsp;</td>
									<td class="no-border left altura">
										<div id="exibe_resultado2">
											<?php

											$relatorio2 = $dados->exibeComprovanteConferido($id);

											$rel_protocolo2 = $relatorio2->comprovante_conferido;

											print $rel_protocolo2;

											?>
										</div>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="panel-body col-lg-12">
						<div class="row">
						</div>
						<input type='button' class='btn-sm btn-primary' value='Finalizar' id='finalizar' name='finalizar' onClick="javascript:finaliza_protocolo(1)" <?php print $disabled_campo ?>>
						<button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>


					</div>
				</div>
		</div>
		<input type="hidden" value="<?= $sistema ?>" id='sistema'>
		</form>





		</div>
		<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
		<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript" src="../SCI/js/funcoes.js"></script>




	</body>

	</html>
<?
	if ($id != '') {
		print "<script language='javascript'>busca_protocolo_envio($id,2)</script>";
		print "<script language='javascript'>focus_campo()</script>";
	}

	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
}
?>