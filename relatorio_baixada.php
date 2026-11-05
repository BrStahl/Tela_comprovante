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

$localItem = "../comprovante_entrega/relatorio_baixada.php";
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



?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Relatório - Status dos Documentos</title>

        <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
        <!-- <script src="http://code.jquery.com/jquery-1.9.1.js"></script> -->
        <!-- <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script> -->
        <script src="../SCI/js/jquery.js"></script>
        <script type="text/javascript" src="../SCA/includes/calendario/_scripts/jquery.click-calendario-1.0-min.js"></script>
        <script type="text/javascript" src="../SCA/includes/calendario/_scripts/exemplo-calendario.js"></script>

        <link rel="stylesheet" href="../SCI/css/bootstrap.css">
        <link rel="stylesheet" href="../SCI/css/estilo_bts.css">
        <link href="../SCI/css/bootstrap-datetimepicker.min.css" rel="stylesheet" media="screen">

        <link href="../SCA/includes/calendario/_style/jquery.click-calendario-1.0.css" rel="stylesheet" type="text/css" />
        <script src="../SCI/js/bootstrap.js"></script>
        <script src="../SCI/js/table_to_excel.js"></script>
        <script src="../SCI/js/export_to_xls.js"></script>

    </head>

    <body>
        <div class="panel-primary col-lg-5" style="max-height: 30px;">
            <div class="panel-heading panel-legend"><b>Controle de Retorno Comprovante</b></div>
        </div>
        <br>
        <div class="col-lg-12">
            <form name="form1" method="post" id="form1" action="" enctype="multipart/form-data">
                <div class="panel panel-default col-lg-12 panel-padrao">
                    
                    <!-- ========================================== -->
                    <!-- FORMULÁRIO DE FILTROS (Lado Esquerdo) -->
                    <!-- ========================================== -->
                    <div class="panel-body col-xs-5">
                        <div class="table-responsive covre">

                            <table width="50%" class="table table-bordered tb-padrao">
                              
                             
                                <tr>
                                    <td class="no-border left altura">&nbsp;</td>
                                    <td class="no-border left altura">
                                        <font size="-1"><b>Nº CT-e: </b></font>
                                    </td>
                                    <td class="no-border left altura">
                                        <div class="col-xs-4 left-inline">
                                            <input type='text' class='form-control txt-center altura-campo only-number' aria-describedby='basic-addon1' name='num_documento'
                                                id='num_documento' size='15' maxlength='15'>
                                        </div>
                                    </td>
                                </tr>
                               
                                <tr>
                                    <td class="no-border left altura" width="1">&nbsp;</td>
                                    <td width="126" class="no-border left altura">
                                        <font size="-1"><b>Status: </b></font>
                                    </td>
                                    <td class="no-border left altura">
                                        <div class="col-xs-12 left-inline"><span class="col-xs-4 left-inline">
                                                <?php
                                                $query = "select 'D', 'DIGITALIZADO' descricao
													  UNION
													  select 'R', 'RECEBIDO'
													  UNION
													  select 'T', 'EM TRANSITO'
													  UNION
													  select 'S', 'A ENVIAR'
													  order by descricao";
                                                //print $query;
                                                $result = $con->executar($query);

                                                print "<select class='form-control txt-center altura-campo' aria-describedby='basic-addon1' 
													name='status_id' id='status_id' value='$status_id'><option></option>";

                                                while (odbc_fetch_row($result)) {

                                                    if (odbc_result($result, 1) == $status_id)
                                                        $selected = "selected='selected'";
                                                    else
                                                        $selected = "";

                                                    print "<option value='" . odbc_result($result, 1) . "'$selected>" . utf8_encode(odbc_result($result, 2)) . "</option>";
                                                }
                                                print "</select>";
                                                ?>
                                            </span></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border left altura">&nbsp;</td>
                                    <td class="no-border left altura">
                                        <font size="-1"><b>Período: </b></font>
                                    </td>
                                    <td class="no-border rigth altura">
                                        <div class="col-xs-4 left-inline ">
                                            <div class="input-group date form_datetime col-md-10" data-date-format="dd/mm/yyyy">
                                                <input type='text' class='form-control txt-center altura-campo only-number_barra' aria-describedby='basic-addon1' name='data_ini'
                                                    id='data_ini' size='9' maxlength='10' value='<?php print $data_ini ?>'>
                                                <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span>
                                            </div>
                                        </div>
                                        <div class="col-xs-1 left-inline">
                                            <font size="-1"><b>&nbsp;&nbsp;à&nbsp;&nbsp;</b></font>
                                        </div>
                                        <div class="col-xs-4 left-inline ">
                                            <div class="input-group date form_datetime col-md-10" data-date-format="dd/mm/yyyy">
                                                <input type='text' class='form-control txt-center altura-campo only-number_barra' aria-describedby='basic-addon1' name='data_fim'
                                                    id='data_fim' size='9' maxlength='10' value='<?php print $data_fim ?>'>
                                                <span class="input-group-addon altura-campo"> <span class="glyphicon glyphicon-calendar"></span> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="no-border left altura">&nbsp;</td>
                                    <td class="no-border left altura" width="126"><button type='button' class='btn btn-theme altura-campo' id='pesquisar' name='pesquisar' style=' color:#FFF; background-color:#095D92' onclick='javascript:filtra_relatorio_baixada()'>Pesquisar</button> </td>
                                    <td class="no-border rigth altura">&nbsp;</td>
                                </tr>

                            </table>

                        </div>
                    </div>

              
<div class="panel-body col-xs-7">
    <div style='background: #e3f2fd; padding: 12px; border-radius: 5px; border: 1px solid #90caf9;'>
        <h4 style='margin: 0 0 10px 0; color: #1565c0; font-size: 16px;'><b>📊 Legenda</b></h4>
        
        <table style='width: 100%; font-size: 12px;'>
            <tr>
                <td style='padding: 4px;'>
                    <span style='background: #fff3cd; padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px;'>A ENVIAR</span>
                </td>
                <td style='padding: 4px; font-size: 12px;'>Não enviado</td>
            </tr>
            <tr>
                <td style='padding: 4px;'>
                    <span style='background: #cfe2ff; padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px;'>EM TRÂNSITO</span>
                </td>
                <td style='padding: 4px; font-size: 12px;'>Enviado, aguardando conferência</td>
            </tr>
            <tr>
                <td style='padding: 4px;'>
                    <span style='background: #d1e7dd; padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px;'>RECEBIDO</span>
                </td>
                <td style='padding: 4px; font-size: 12px;'>Conferido no destino</td>
            </tr>
            <tr>
                <td style='padding: 4px;'>
                    <span style='background: #e2e3e5; padding: 3px 8px; border-radius: 3px; font-weight: bold; font-size: 11px;'>DIGITALIZADO</span>
                </td>
                <td style='padding: 4px; font-size: 12px;'>Processo completo</td>
            </tr>
        </table>

        <hr style='margin: 10px 0; border-top: 1px solid #90caf9;'>
        
        <p style='margin: 5px 0; font-size: 12px;'><strong style='color: #1565c0;'>🔵 CTEs Duplicados:</strong></p>
        <table style='width: 100%; font-size: 12px;'>
            <tr>
                <td style='padding: 3px;'>
                    <span style='background: #bbdefb; padding: 2px 6px; border-radius: 3px; font-size: 11px;'>Linha Azul</span>
                </td>
                <td style='padding: 3px; font-size: 11px;'>CTE duplicado/original</td>
            </tr>
            <tr>
                <td style='padding: 3px;'><strong style='color: #0d47a1; font-size: 11px;'>📌 ORIGINAL</strong></td>
                <td style='padding: 3px; font-size: 11px;'>Primeiro CTE da NF</td>
            </tr>
            <tr>
                <td style='padding: 3px;'><span style='color: #1565c0; font-style: italic; font-size: 11px;'>⚠️ Ref.</span></td>
                <td style='padding: 3px; font-size: 11px;'>CTE original correto</td>
            </tr>
        </table>
    </div>
</div>
                   
                    <div class="panel-body col-xs-12">
                        <div id="procura" style="display:none"></div>
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
        <script type="text/javascript" src="js/main.js"></script>
    </body>

    </html>
<?php

    include("../SCI/includes/modal.php");
}
?>