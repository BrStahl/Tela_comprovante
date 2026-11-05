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

$localItem = "../comprovante_entrega/pesquisa_canhoto_nfe_pendente.php";
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



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Pesquisa - Protocolo Canhoto NFe</title>

	<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script> -->
    <!-- <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> -->
    <script src="../SCI/js/jquery.js"></script>
    <script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>		
	<script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

    <link rel="stylesheet" href="../SCI/css/bootstrap.css">
	<link rel="stylesheet" href="../SCI/css/estilo_bts.css">
    <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

	<link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css"/>


    <script src="../SCI/js/bootstrap.js"></script>
    
<!--	<script type="text/javascript" src="../SCA/includes/js/jquery-autocomplete/lib/jquery.js"></script> -->


    
</head>
<body>
    <div class="panel-primary col-lg-5" style="max-height: 30px;">
        <div class="panel-heading panel-legend"><b>Pesquisa -  Canhoto NFe Pendente</b></div>
    </div>
    <br>
<div class="col-lg-12">
    <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
        <div class="panel panel-default col-lg-10 panel-padrao">
            <div class="panel-body col-xs-10">
                <div class="table-responsive covre">
        
                   <table width="50%" class="table table-bordered tb-padrao">
                    <tr>
                      <td class="no-border left altura">&nbsp;</td>
                      <td class="no-border left altura"><font size="-1"><b>Nota Fiscal:</b></font></td>
                      <td colspan="3" class="no-border left altura"><div class="col-xs-2 left-inline">
                        <input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='nota_fiscal' 
                            id='nota_fiscal' size='10' maxlength='9'>
                      </div></td>
                     </tr>
                    <tr>
                        <td class="no-border left altura" width="5">&nbsp;</td>
                        <td width="127" class="no-border left altura">
                            <font size="-1"><b>Cliente/Grupo: </b></font> 
                        </td>  
                        <td width="188" class="no-border left altura"><div class="col-xs-8 left-inline">
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
                        <td width="64" class="no-border left altura"><font size="-1"><b>Nome: </b></font></td>
                        <td width="462" class="no-border left altura">
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
                        <td class="no-border left altura" width="5">&nbsp;</td>
                        <td width="127" class="no-border left altura"><font size="-1"><b>Status: </b></font></td>  
                        <td colspan="3" class="no-border left altura">
                        <div class="col-xs-12 left-inline"><span class="col-xs-2 left-inline">
                          <?php
											$query = "select 'p', 'Pendente', 1 ordem
													  UNION
													  select 'f', 'Finalizado', 2 ordem
													  UNION
													  select 'a', 'Ambos', 3 ordem
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
                        </span></div>  
                        </td> 
                    </tr>
                    <tr>
                      <td class="no-border left altura">&nbsp;</td>
                      <td class="no-border left altura"><font size="-1"><b>Período: </b></font></td>
                      <td colspan="3" class="no-border rigth altura"><div class="col-xs-2 left-inline ">
                        <div class="input-group date form_datetime col-md-10"  data-date-format="dd/mm/yyyy">
                          <input type='text' class='form-control txt-center altura-campo only-number_barra' aria-describedby='basic-addon1' name='data_ini' 
                            id='data_ini' size='9' maxlength='10' value='<?php print $data_ini ?>'>
                          <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span> </div>
                      </div>
                      <div class="col-xs-1 left-inline"> <font size="-1"><b>à&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></font> </div>
                      <div class="col-xs-2 left-inline ">
                        <div class="input-group date form_datetime col-md-10"  data-date-format="dd/mm/yyyy">
                          <input type='text' class='form-control txt-center altura-campo only-number_barra' aria-describedby='basic-addon1' name='data_fim' 
                            id='data_fim' size='9' maxlength='10' value='<?php print $data_fim ?>' >
                          <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span> </div>
                      </div></td>
                    </tr>
                    <tr> 
                        <td class="no-border left altura">&nbsp;</td>    
                        <td class="no-border left altura" width="127"><button type='button' class='btn btn-theme altura-campo' id='pesquisar' name='pesquisar' style=' color:#FFF; background-color:#095D92' onclick='javascript:filtra_pesquisa_canhoto_pendente()'>Pesquisar</button> </td>                        <td colspan="3" class="no-border rigth altura">&nbsp;</td>      
                    </tr>                                                                                    

                    </table>
                   
                </div>
            </div>
            <div class="panel-body col-xs-12">    
                
                <div id="exibe_resultado"></div>                         
                
                <center>
                <input type="button" class="btn-sm btn-danger pull-right" onClick="fechar()" name="fechar_p" id="fechar_p" value="Fechar">
                </center>
                                  
            </div>  
        </div> 
        
        
        <!-- Modal -->
        <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content col-lg-12">
                    <div class="modal-header">
                        <h4 class="modal-title"></h4>
                    </div>
                <div class="modal-body">
                <table class="table table-bordered table-hover" id="myTable4">
                    <thead>
                        <tr>
                            <th bgcolor="#d3d3d3"></a></th>
                            <th bgcolor="#d3d3d3"></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="bg-default" id=""></td>
                            <td class="bg-default" id=""></td>
                        </tr>
                    </tbody>
                </table>
                </div>
                    <div class="modal-footer">

                        <button type='button' class='btn btn-danger' data-dismiss='modal'>Fechar</button>
					 
						
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="../SCI/js/bootstrap-datetimepicker.js" charset="UTF-8"></script>
<script type="text/javascript" src="../SCI/js/locales/bootstrap-datetimepicker.pt-BR.js" charset="UTF-8"></script>
<script type="text/javascript" src="js/main_canhoto.js"></script>
</body>
</html>
<?php
	
	include("../SCI/includes/modal.php");
	



}
?>
							