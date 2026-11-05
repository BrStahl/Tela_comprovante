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


$localItem = "../comprovante_entrega/envio_comprovante_rodopar.php";
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
		window.location.href='envio_comprovante_rodopar.php';
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
		<title>Protocolo - Envio de Comprovante para Digitalização Rodopar</title>
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
		<script src="../SCI/js/table_to_excel.js"></script>
		<script src="../SCI/js/export_to_xls.js"></script>





		<script language="javascript">
			$(document).on("change", "#tipo_documento_id", function(el) {
				var val = $(this).val();

				if (val == 'C') {
					$("#btIncluri").hide();
				} else {
					$("#btIncluri").show();
				}
			})

			function grava_registro() {

				document.getElementById('tipo_documento_id').disabled = false;

				var id = document.getElementById('id').value;
				var tipo_documento_id = document.getElementById('tipo_documento_id').value;
				var status_id = document.getElementById('status_id').value;

				if (tipo_documento_id == '') {
					$('#msg').html('Favor selecionar o Tipo de Documento');
					$('#myModal_alert').modal('show');
				} else {

					let dados = $("form").serialize();

					$.ajax({
						type: "POST", //define o metуdo de passagem de parametros
						url: "includes/grava_protocolo_envio_dig.php", //chama uma pagina
						data: dados, //passa os parametros, se necessбrio
						success: function(msg) {
							//alert(msg);

							if (msg.indexOf("ok") == -1) {
								$('#msg').html(msg);
								$('#myModal_alert').modal('show');
							} else {
								var dados = msg.split("|");
								document.form1.id.value = dados[2];
								document.form1.chave_acesso.disabled = false;

								$("#num_protocolo").html(dados[2]);
								$('#num_protocolo').show();

								document.form1.chave_acesso.focus();

							}

							exibe_comprovante_envio(id, 3);
						}
					});

				}

			}
		</script>

		<script language="javascript">
			function gerar_excel1(nome_arquivo, id_tabela) {

				$("#" + id_tabela).each(function() {
					var cell = $(this).find('td').eq(-2);
					var cell_chave = $(this).find('td:last');

					if (cell.length) {
						var currentValue = cell.text();

						var date_edit = currentValue.substring(5, 7) + currentValue.substring(0, 2)

						cell.text(date_edit);

						//var textch = cell_chave.text();
						//cell_chave.text('=' + textch + '*1');
					}
				});

				$("#" + id_tabela).table2excel({
					exclude: ".excludeThisClass",
					name: nome_arquivo,
					filename: nome_arquivo + ".xls", // do include extension
					preserveColors: true, // set to true if you want background colors and font colors preserved
					exclude_img: false
				});


				$("#" + id_tabela).each(function() {
					var cell = $(this).find('td').eq(-2);

					if (cell.length) {
						var currentValue = cell.text();

						var date_edit = currentValue.substring(2, 4) + '/20' + currentValue.substring(0, 2);

						cell.text(date_edit);
					}
				});
			}
		</script>

		<script language="javascript">
			//var tempo1="";

			function habilitaBtIncluiChave(elmnt, protocolo_id) {
				//console.log(14)
				var tipo_documento_id = document.getElementById('tipo_documento_id').value;

				if ((tipo_documento_id == 'C') && (elmnt.value.length == 44)) {
					inclui_chave(protocolo_id);
				} else if ((tipo_documento_id == 'F') && (elmnt.value.length == 15)) {
					inclui_chave(protocolo_id);
				}
				/*else {
					$("#btIncluri").prop("disabled", true);
				}*/
			}

			function inclui_chave(protocolo_id) {
				//alert('entrou na function');

				var tipo_documento_id = document.getElementById('tipo_documento_id').value;
				var elmnt = document.getElementById("chave_acesso");

				if (elmnt.value != '') {
					var numero_chave = elmnt.value;

					//if (((tipo_documento_id == 'C') && (elmnt.value.length == 44)) || 
					//	((tipo_documento_id == 'F') && (elmnt.value.length == 18)))						
					//{

					var verifica = numero_chave.substr(0, 3);
					/*
			if ((tipo_documento_id == 'F') && ((verifica != '001') && (verifica != '002') && (verifica != '011') && 
			    (verifica != '015')))
			{
				$('#msg').html('Chave inválida para o tipo FICHA');
				$('#myModal_alert').modal('show');
			
			}
			else
			{		
			*/
					//alert(elmnt.value);

					console.log(15)
					$.ajax({
						type: "POST", //define o metódo de passagem de parametros
						url: "includes/grava_chave_dig_rodopar.php", //chama uma pagina
						data: "chave=" + elmnt.value + "&protocolo_id=" + protocolo_id,
						success: function(msg) { //pega o retorno da pagina chamada
							console.log(msg);
							console.log(msg.indexOf("ok"));
							//alert(msg);
							if (msg.indexOf("ok") > -1) {
								console.log(555555)
								exibe_comprovante_envio(protocolo_id, 3);
								document.getElementById('finalizar').disabled = false;

							} else if (msg.indexOf("Atenção, a chave inserida ja foi enviada para digitalização no protocolo:") > -1) {
								console.log(66666)
								exibe_comprovante_envio(protocolo_id, 3);
								document.getElementById('finalizar').disabled = false;
								$('#msg').html(msg);
								$('#myModal_alert').modal('show');
							} else {
								console.log(77777)
								$('#msg').html(msg);
								$('#myModal_alert').modal('show');
							}


							document.form1.chave_acesso.value = '';
							document.form1.chave_acesso.focus();



						}
					});
					//}

					//}	

				}

			}
		</script>

		<script language="javascript">
			function imprime_protocolo(tela) {

				var id = document.getElementById('id').value;

				pagina('impressao_protocolo.php?id=' + id + '&tela=' + tela, '900', '800', 'impressao');

			}
		</script>


	</head>


	<body style="background-color: #3CB371">
		<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
			<div class="panel-heading panel-legend" id="painel-heading"><b>Protocolo - Envio de Comprovante - Digitalização Rodopar</b></div>
		</div>
		<br>
		<div class="col-xs-11">


			<form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
				<div class="panel panel-default col-lg-8 panel-padrao">
					<div class="panel-body col-xs-12">
						<font color="#0000FF"><b>Dados do Protocolo</b></font>
						<div class="table-responsive covre">
							<table width="90%" class="table table-bordered tb-padrao">
								<tbody>
									<tr>
										<td width="20" class="no-border left altura">&nbsp;</td>
										<td width="146" class="no-border left altura">
											<font size="-1"><b>Protocolo:</b></font>
										</td>
										<td align="left" class="no-border left altura">
											<font size="-1"><b>Tipo Documento:</b></font>
										</td>
										<td width="181" align="left" class="no-border left altura">
											<font size="-1"><b>Status:</b></font>
										</td>
										<td colspan="2" rowspan="2" align="left" class="no-border left altura"><input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick="javascript:grava_registro()"></td>
									</tr>

									<tr>
										<td class="no-border left altura">&nbsp;</td>
										<td class="no-border left altura">
											<font color="#0000FF" size="+1"><b>
													<div id="num_protocolo"></div>
												</b></font>
										</td>
										<td width="184" class="no-border left altura">
											<div class="col-xs-9 left-inline">
												<?php
												$query = "select 'C', 'CTE', 1 ordem
													  UNION
													  select 'F', 'FICHA', 2 ordem
													  order by ordem";
												//print $query;
												$result = $con->executar($query);

												print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='tipo_documento_id' id='tipo_documento_id' value='$tipo_documento_id' onchange='javascript:filtra_po(this.value)'><option></option>";

												while (odbc_fetch_row($result)) {

													if (odbc_result($result, 1) == $tipo_documento_id)
														$selected = "selected='selected'";
													else
														$selected = "";

													print "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
												}
												print "</select>";
												?>
											</div>
										</td>
										<td class="no-border left altura">
											<div class="col-xs-9 left-inline">
												<?php
												$query = "select 'a', 'Ativo', 1 ordem
													  UNION
													  select 'i', 'Cancelado', 2 ordem
													  order by ordem";
												//print $query;
												$result = $con->executar($query);

												print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='status_id' id='status_id' value='$status_id'>";

												while (odbc_fetch_row($result)) {

													if (odbc_result($result, 1) == $status_id)
														$selected = "selected='selected'";
													else
														$selected = "";

													print "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
												}
												print "</select>";
												?>
											</div>
										</td>
									</tr>
									<tr>
										<td class="no-border left altura">&nbsp;</td>
										<td class="no-border left altura">
											<font size="-1"><b><span class="col-xs-2 left-inline">
														<input name='id' type='hidden' class='form-control txt-center altura-campo' id='id' value="<?php print $id ?>" size="4" maxlength='44' aria-describedby='basic-addon1'>
													</span></b></font>
										</td>
										<td class="no-border left altura">&nbsp;</td>
										<td class="no-border left altura">&nbsp;</td>
										<td width="28" class="no-border left altura">&nbsp;</td>
										<td width="126" class="no-border left altura">&nbsp;</td>
									</tr>
								</tbody>
							</table>
						</div>
						<p></p>

						<table width="109%" class="table table-bordered tb-padrao">
							<tbody>
								<tr>
									<td width="177" class="no-border left altura">
										<font size="-1"><b>Chave de Acesso:</b></font>
									</td>
									<td width="936" class="no-border left altura">
										<div class="col-xs-8 left-inline">
											<input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='chave_acesso' id='chave_acesso' maxlength='44' onkeyup="javascript:habilitaBtIncluiChave(this, document.form1.id.value)" onkeydown="javascript: if (event.keyCode==13) {return false; } " disabled>
										</div>

										<!--<div class="col-xs-2 left-inline">
										<input style="margin-left: 10px;" type='button' class='btn-sm btn-primary' value='Incluir' id='btIncluri' name='inclui' disabled onClick="javascript:inclui_chave( document.form1.id.value)">
									</div>-->
									</td>
								</tr>
							</tbody>
						</table>
						<div id="exibe_resultado">
							<?php
							/*
                                $relatorio_protocolo = $dados->exibeDocumento();  
                                
                                $rel_protocolo = $relatorio_protocolo->documento;
                                
                                print $rel_protocolo;
                                */
							?>
						</div>



					</div>
					<div class="panel-body col-xs-12"></div>
					<div class="panel-body col-lg-12">
						<div class="row">
						</div>
						<input type='button' class='btn-sm btn-primary' value='Finalizar' id='finalizar' name='finalizar' onClick="javascript:finaliza_protocolo(3)" disabled>
						<input type='button' class='btn-sm btn-primary' value='Imprimir' id='imprimir' name='imprimir' onClick="javascript:imprime_protocolo(2)" disabled>

						<input type='button' class='btn-sm btn-primary' value='XLS' id='xls' name='xls' onClick="javascript:gerar_excel1('relatorio','myTable4')" disabled>

						<input type='submit' class='btn-sm btn-primary' value='Novo' id='novo' name='novo' onClick="javascript:novo_protocolo()">

						<button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>


					</div>
				</div>
		</div>
		</form>





		</div>
		<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
		<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
		<script type="text/javascript" src="js/main.js"></script>
		<script type="text/javascript" src="../SCI/js/funcoes.js"></script>




	</body>

	</html>
<?
	if ($id != '')
		print "<script language='javascript'>busca_protocolo_envio_dig($id)</script>";

	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
}
?>