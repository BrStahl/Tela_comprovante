<?
session_name("covre_ti");
session_start();


include($_SERVER['DOCUMENT_ROOT']."/SCI/controller/ConexaoSCI.php"); //Classe de Controle SCI
require("../SCI/includes/page_func_sci.php");
require_once "../SCI/class/Autentica.php";
require_once "class/DadosCanhoto.php";


$autenticacao_sci = new Autentica();	

$con = new ConexaoSCI();

$dados = new DadosCanhoto;	


$localItem = "../comprovante_entrega/canhoto_nfe.php";
$logado    = $_SESSION["usuario_logado"];
$dados_autenticacao = $autenticacao_sci->verificaAutenticacao($localItem,$logado);
$acesso  = $dados_autenticacao->permissao;
$id_item  = $dados_autenticacao->id_item;
$usuario_id  = $dados_autenticacao->usuario_id;


//$acesso = "permitido";


if($acesso <> "permitido")
{
   print "<script language = 'JavaScript'>alert('Acesso negado para esta página');</script/>";
   
   $dados_gravacao = $autenticacao_sci->redirecionaLogin();
   $local  = $dados_gravacao->local;
   
   print "<script language = 'JavaScript'>window.location='$local';</script/>";
}
else
{
	 $dados_gravacao = $autenticacao_sci->gravaAutenticacao($id_item,$usuario_id);

    /** Buttons */
    if($fechar_p != ""){ print"<script language='javascript'>open(location, '_self').close();</script>";}
    if($buscar){}
	

	$id		= $_GET['id'];
	$data_sistema = date("d/m/Y");
	

	
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Protocolo - Canhoto NFe</title>
    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
    <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link rel="stylesheet" href="css/relsm.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" type="text/css" href="../SCI/js/datatables/datatables.css"/>
    <link rel="stylesheet" type="text/css" href="https://nightly.datatables.net/buttons/css/buttons.dataTables.min.css"/>
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

<script language="javascript">
function isValidDate(data)
{
	if(data != '')
	{
		dia = (data.substring(0,2));
		mes = (data.substring(3,5));
		ano = (data.substring(6,10));
	
		var now   = new Date();
		var year  = now.getYear();
		var month = now.getMonth();
		var day   = now.getDate() ;
		var mes_  = [];
		
		
		mes_[0] = "January";
		mes_[1] = "February";
		mes_[2] = "March";
		mes_[3] = "April";
		mes_[4] = "May";
		mes_[5] = "June";
		mes_[6] = "July";
		mes_[7] = "August";
		mes_[8] = "September";
		mes_[9] = "October";
		mes_[10] = "November";
		mes_[11] = "December";
	
		situacao = "";
		// verifica o dia valido para cada mes
		if ((dia < 01)||(dia < 01 || dia > 30) && (  mes == 04 || mes == 06 || mes == 09 || mes == 11 ) || dia > 31 ) {
		   situacao = "falsa";
		}
	
		else
		if (mes < 01 || mes > 12 ) {// verifica se o mes e valido
			situacao = "falsa";
		}
	
		else
		if (mes == 2 && ( dia < 01 || dia > 29 || ( dia > 28 && (parseInt(ano / 4) != ano / 4)))) {// verifica se e ano bissexto
		   situacao = "falsa";
		}
		else
		if (data == "") {
		   situacao = "falsa";
		}
		//else //verifica se a data digitada ï¿½ maior do que a data atual
		//if((((Date.parse(mes_[mes-1]+" "+dia+" "+ano))-(Date.parse(mes_[month]+" "+day+" "+year)))/(24*60*60*1000)).toFixed(0) > 0 ){
		//  situacao = "falsa";
		//}
	
		if (situacao == "falsa") 
		{
			$('#msg').html('Data Inválida');
			$('#myModal_alert').modal('show');
		    data.select();
		}

	}
    //dataAtual = mes[data.getMonth()] + ' ' + data.getDate() + ' ' + data.getFullYear();
   //alert( (((Date.parse("November"+" "+dia+" "+ano))-(Date.parse("November"+" "+day+" "+year)))/(24*60*60*1000)).toFixed(0) );
   //alert(day+"-"+month+"-"+year);
}
</script>

<script language="javascript">
function grava_registro(){

		var id						= document.getElementById('id').value;
		var data					= document.getElementById('data').value;
		var cliente_grupo_id		= document.getElementById('cliente_grupo_id').value;
		var nome_cliente			= document.getElementById('nome_cliente').value;
		var cliente_id				= document.getElementById('cliente_id').value;
		var grupo_cliente_id		= document.getElementById('grupo_cliente_id').value;		
		var responsavel_cliente		= document.getElementById('responsavel_cliente').value;
		var depto_cliente			= document.getElementById('depto_cliente').value;				
		var status_id				= document.getElementById('status_id').value;


		isValidDate(data);

		if (data == '')
		{
			$('#msg').html('Favor informar a data');
			$('#myModal_alert').modal('show');
		}	
		else
		if (cliente_grupo_id == '')
		{
			$('#msg').html('Favor selecionar entre Cliente ou Grupo');
			$('#myModal_alert').modal('show');
		}	
		else
		if ((cliente_grupo_id == 'C') && (nome_cliente == ''))
		{
			$('#msg').html('Favor informar o nome do cliente');
			$('#myModal_alert').modal('show');

		}	
		else
		if ((nome_cliente != '') && (cliente_id == ''))
		{
			$('#msg').html('Favor informar um cliente válido');
			$('#myModal_alert').modal('show');

		}			
		else
		if ((cliente_grupo_id == 'G') && (grupo_cliente_id == ''))
		{
			$('#msg').html('Favor selecionar o grupo do cliente');
			$('#myModal_alert').modal('show');
		}			
		else					
		{
			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_protocolo_canhoto.php", //chama uma pagina
				data: "cliente_grupo_id="+cliente_grupo_id + "&cliente_id="+cliente_id + "&nome_cliente="+nome_cliente + "&grupo_cliente_id="+grupo_cliente_id + "&responsavel_cliente="+responsavel_cliente + "&depto_cliente="+depto_cliente + "&status_id="+status_id + "&id="+id + "&data="+data, 
				success: function (msg) {
					//alert(msg);
					
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						
						var dados = msg.split("|");
						document.form1.id.value		= dados[2];
						document.form1.nota_fiscal.disabled = false;
						
						$("#num_protocolo").html(dados[2]);
						$('#num_protocolo').show();
						
						document.form1.nota_fiscal.focus();
						
					}
					
				}
			});
			
		}
	
}
</script>



<script language="javascript">
function insere_nota(){

		var id						= document.getElementById('id').value;
		var nota_fiscal				= document.getElementById('nota_fiscal').value;


		if (nota_fiscal == '')
		{
			$('#msg').html('Favor inserir a Nota Fiscal');
			$('#myModal_alert').modal('show');
		}	
		else					
		{
			$.ajax({
				type: "POST", //define o metуdo de passagem de parametros
				url: "includes/grava_nota_fiscal.php", //chama uma pagina
				data: "id="+id + "&nota_fiscal="+nota_fiscal, 
				success: function (msg) {
					//alert(msg);
					
					if(msg.indexOf("ok") == -1)
					{
						$('#msg').html(msg);
						$('#myModal_alert').modal('show');
					}
					else
					{
						
						exibe_canhoto(id);			
						
						document.form1.nota_fiscal.value	= '';
						document.form1.nota_fiscal.focus();
						
						//document.getElementById('finalizar').disabled = false;

					}
					
				}
			});
			
		}
	
}
</script>

<script language="javascript">
jQuery(function($) {
	
	$('#nota_fiscal').keypress(function(event) {
		if(event.keyCode == 13) {
			//e.preventDefault(); // Stop the default behaviour
		   //console.log(123);
		   insere_nota();
		}
	});
});

</script>

<script language="javascript">
function verifica_data(data){

	if (document.getElementById('data').value == '')
		document.getElementById('data').value = data;
}
</script>


<script language="javascript">
function imprime_protocolo(){

	var id	= document.getElementById('id').value;

	pagina('impressao_protocolo_canhoto.php?id='+id,'900','800','impressao');
	
}
</script>

<script language="javascript">
function novo_canhoto(){

	window.location.href='canhoto_nfe.php';
	
}
</script>

</head>


<body style="background-color: #3CB371" >
<div class="panel-primary col-lg-5 col-xs-6" style="max-height: 30px;">
        <div class="panel-heading panel-legend" id="painel-heading"><b>Protocolo - Canhoto NFe</b></div>
    </div>
    <br>
<div class="col-xs-11">


    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-9 panel-padrao">
            <div >
				<font color="#0000FF"><b>Dados do Protocolo</b></font>
              	<div class="table-responsive covre">
                    <table width="90%" class="table table-bordered tb-padrao">
                        <tbody>
                           	<tr>
                            	<td width="20" class="no-border left altura">&nbsp;</td>
								<td width="118" class="no-border left altura">
								<font size="-1"><b>Protocolo:</b></font></td>
								<td width="157" class="no-border left altura"><font size="-1"><b>Data:</b></font></td>
								<td align="left" class="no-border left altura"><font size="-1"><b>Cliente/Grupo:</b></font></td>
								<td colspan="4" align="left" class="no-border left altura"><font size="-1"><b>Nome:</b></font></td>
							</tr>    
                            
                            <tr>  
	                            <td class="no-border left altura">&nbsp;</td>                            
								<td class="no-border left altura"><font color="#0000FF" size="+1"><b><div id="num_protocolo"></div></b></font></td>
								<td class="no-border left altura"><div class="col-xs-11 left-inline ">
								  <div class="input-group date form_datetime col-md-10"  data-date-format="dd/mm/yyyy">
								    <input type='text' class='form-control txt-center altura-campo only-number_barra' aria-describedby='basic-addon1' name='data' id='data' size='9' maxlength='10' value='<?php print $data ?>'>
								    <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span></span></div>
							    </div></td>
							  <td width="145"  class="no-border left altura" ><div class="col-xs-9 left-inline">
							    <?php
											$query = "select 'C', 'Cliente', 1 ordem
													  UNION
													  select 'G', 'Grupo', 2 ordem
													  order by ordem";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='tipo_documento_id' id='cliente_grupo_id' value='$cliente_grupo_id' onchange='javascript:exibe_cg(this.value)'><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $cliente_grupo_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
						      </div></td>
								<td colspan="4"  class="no-border left altura" >
                                <div class="col-xs-11 left-inline">
                                	<div id="grupo" style="display:none">
								  	<?php
                                   
											$query = "select GRUPO_CLIENTE_ID, grupo
														from cargosol..grupo_cliente gc with (nolock)
														where TAB_STATUS_ID = 1
														and GRUPO_CLIENTE_ID not in (84,70,37)
														order by grupo";
											//print $query;
											$result = $con->executar($query);								   
								   
								   
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='ponto_operacao_id' id='grupo_cliente_id' value='$grupo_cliente_id'><option></option>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $grupo_cliente_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                                    </div>
                     				<div id="cliente">
                                    	<input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='nome_cliente' id='nome_cliente' maxlength='44' onkeyup="passaParametro(this)" onblur="javascript:busca_cliente_id(this, 1)" disabled>
                                        <div id="suggesstion-box" ></div>
                                    	<span class="col-xs-4 left-inline">
                                    	<input type='hidden' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='cliente_id' 
									id='cliente_id' size='8' maxlength='4' onkeypress='somente_numero(this)'>
                                   	</span>                                    </div>
							    </div>
                                </td>
							</tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td class="no-border left altura"><font size="-1"><b><span class="col-xs-2 left-inline">
                                <input name='id' type='hidden' class='form-control txt-center altura-campo' id='id' value="<?php print $id ?>" size="4"  aria-describedby='basic-addon1' >
                              </span></b></font></td>
                              <td class="no-border left altura">&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td colspan="4"  class="no-border left altura" >&nbsp;</td>
                            </tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td class="no-border left altura"><font size="-1"><b>Responsável:</b></font></td>
                              <td class="no-border left altura">&nbsp;</td>
                              <td  class="no-border left altura" >&nbsp;</td>
                              <td colspan="2"  class="no-border left altura" ><font size="-1"><b>Departamento:</b></font></td>
                              <td width="153"  class="no-border left altura" ><font size="-1"><b>Status:</b></font></td>
                              <td width="107" rowspan="2"  class="no-border left altura" ><input type='button' class='btn-sm btn-primary' value='Gravar' id='gravar' name='gravar' onClick="javascript:grava_registro()"></td>
                            </tr>
                            <tr>
                              <td class="no-border left altura">&nbsp;</td>
                              <td colspan="3" class="no-border left altura"><div class="col-xs-11 left-inline">
                                <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='responsavel_cliente' id='responsavel_cliente' maxlength='44'>
                              </div></td>
                              <td colspan="2"  class="no-border left altura" ><div class="col-xs-11 left-inline">
                                <input type='text' class='form-control txt-center altura-campo' aria-describedby='basic-addon1' name='depto_cliente' id='depto_cliente' maxlength='44'>
                              </div></td>
                              <td  class="no-border left altura" ><div class="col-xs-9 left-inline">
                                <?php
											$query = "select 'a', 'Ativo', 1 ordem
													  UNION
													  select 'i', 'Cancelado', 2 ordem
													  order by ordem";
											//print $query;
                                            $result = $con->executar($query);
                                    
                                            print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='status_id' id='status_id' value='$status_id'>";
                                    
                                            while(odbc_fetch_row($result))
                                            {
                                    
                                                if (odbc_result($result, 1) == $status_id)
                                                    $selected = "selected='selected'";
                                                else
                                                    $selected = "";
                                    
                                                 print "<option value='".odbc_result($result, 1)."'$selected>".utf8_encode(odbc_result($result, 2))."</option>";
                                            }
                                            print"</select>";
                                    ?>
                              </div></td>
                            </tr>
                            <tr>
                              <td colspan="8" class="no-border left altura"><div align="center"></div></td>
                            </tr>
                        </tbody>
                    </table>
              	</div>
              <p></p>
              	
                  <table width="109%" class="table table-bordered tb-padrao">
                        <tbody>
                           	<tr>
                            	<td width="100" class="no-border left altura"><font size="-1"><b>Nota Fiscal:</b></font></td>
                                <td width="183" class="no-border left altura"><div class="col-xs-11 left-inline">
                                  <input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='nota_fiscal' id='nota_fiscal' maxlength='9' disabled>
                                </div></td>
                                <td width="815" class="no-border left altura"><input type='button' class='btn-sm btn-primary' value=' Inserir ' id='inserir' name='inserir' onClick="javascript:insere_nota()"></td>
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
                <input type='button' class='btn-sm btn-primary' value='Finalizar' id='finalizar' name='finalizar' onClick="javascript:finaliza_protocolo()" disabled>
                <input type='button' class='btn-sm btn-primary' value='Imprimir' id='imprimir' name='imprimir' onClick="javascript:imprime_protocolo()" disabled>
                <input type='button' class='btn-sm btn-primary' value='Novo' id='novo' name='novo' onClick="javascript:novo_canhoto()" >                
              <!--  
                <input type='submit' class='btn-sm btn-primary' value='Novo' id='novo' name='novo' onClick="javascript:novo_protocolo()">   -->              
               <button type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p">Fechar</button>
                 
                 
            </div>  
        </div>  
    </div>
</form>


 


</div>
<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main_canhoto.js"></script>
<script type="text/javascript" src="../SCI/js/funcoes.js"></script>




</body>
</html>
<? 
	if ($id != '')
		 print "<script language='javascript'>busca_protocolo_canhoto($id)</script>";	
	else 
		print "<script language='javascript'>verifica_data('$data_sistema')</script>";	
	
	include("../SCI/includes/modal.php");
	include("../SCI/includes/modal_list.php");
	

	
	
} 
?>