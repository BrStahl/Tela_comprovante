

/** Função Página */
function pagina(janela, largura, altura, descricao) {
    tamanho = "height=" + altura + ",width=" + largura + ",scrollbars=yes,resizable=yes";
    window.open(janela, descricao, tamanho);
}

function fechar(){
    open(location, '_self').close();
}


/** PARAMETROS DATEPICKER  */


var todayDate = new Date().getDate();
var dia_inicial = new Date(new Date().setDate(todayDate - 5));
//var dia_final = new Date(new Date().setDate(todayDate + 1));

$('.form_datetime').datetimepicker({
	language: 'pt-BR',
    weekStart: 1,
	maxDate: "+1m",
    todayBtn: 1,
    autoclose: 1,
    todayHighlight: 1,
    startView: 2    ,
    forceParse: 1,
    showMeridian: 1,
     maxView: 4,
     minView: 2
	 //startDate: dia_inicial,
     //endDate: dia_final
});


/*AUTOCOMPLETE RESPONSAVEL*/
	function passaParametro1(param){
	 if ($("#responsavel").val().length >= 2) {		
			$.ajax({
			type: "POST",
			url: "includes/ajax_responsavel.php",
			data:'keyword='+param.value,
			beforeSend: function(){
				$("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
			},
			success: function(data){
				if ($.trim(data) == 'vazio')
					document.getElementById('pessoa_id').value = '';
				else
				{
					$("#suggesstion-box").show();
					$("#suggesstion-box").html(data);
					$("#busca-box").css("background","#FFF");
				}
			}
			});
      } else {
          $("#suggesstion-box").hide();
      }				
	}
	
	function selectFiltro(id,val) {
		//alert(1);
	//$("#busca-box").val(val);
		document.getElementById('responsavel').value = val;
		document.getElementById('pessoa_id').value = id;
		document.getElementById('responsavel').focus();
		$("#suggesstion-box").hide();
	}
/*FIM AUTOCOMPLETE*/


/*SOMENTE NUMERO*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number', function(e) {
    var $this = $(this);
    var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58)
      || (key == 46 && acceptDot)
      || (key == 44 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});


/*SOMENTE NUMERO BARRA*/
jQuery(function($) {
  $(document).on('keypress', 'input.only-number_barra', function(e) {
    var $this = $(this);
	var key = (window.event)?event.keyCode:e.which;
    var dataAcceptDot = $this.data('accept-dot');
    var dataAcceptComma = $this.data('accept-comma');
    var acceptDot = (typeof dataAcceptDot !== 'undefined' && (dataAcceptDot == true || dataAcceptDot == 1)?true:false);
    var acceptComma = (typeof dataAcceptComma !== 'undefined' && (dataAcceptComma == true || dataAcceptComma == 1)?true:false);

		if((key > 47 && key < 58) || (key == 47)
      || (key == 46 && acceptDot)
      || (key == 47 && acceptComma)) {
    	return true;
  	} else {
 			return (key == 8 || key == 0)?true:false;
 		}
  });
});


/*AUTOCOMPLETE RESPONSAVEL PERMISSAO*/
	function passaParametro(param){
	 if ($("#responsavel_permissao").val().length >= 2) {		
			$.ajax({
			type: "POST",
			url: "includes/ajax_responsavel_permissao.php",
			data:'keyword='+param.value,
			beforeSend: function(){
				$("#busca-box").css("background","#FFF url(LoaderIcon.gif) no-repeat 165px");
			},
			success: function(data){
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);
				$("#busca-box").css("background","#FFF");
			}
			});
      } else {
          $("#suggesstion-box").hide();
      }				
	}
	
	function selectList(val) {
		//alert(1);
	//$("#busca-box").val(val);
		document.getElementById('responsavel_permissao').value = val;
		document.getElementById('responsavel_permissao').focus();
		$("#suggesstion-box").hide();
	}
/*FIM AUTOCOMPLETE*/


 $(document).ready(function () {
       $('#num_celular').mask('(00)00000-0000');
	   $('#data_ini').mask('00/00/0000');
	   $('#imei').mask('00000000000000000000');

});


function filtra_po(elmnt)
{	
	//alert(elmnt);
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/filtra_po.php", //chama uma pagina
		data: "tipo_documento_id="+elmnt, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			$("#ponto_operacao_id").html(msg);
		}
	});

}

function exibe_comprovante_envio(protocolo_id,tela)
{	

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_comprovante_envio.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id + "&tela="+tela, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			console.log(msg)
			
			document.form1.chave_acesso.focus();
			
			$("#exibe_resultado").html(msg);
			$('#exibe_resultado').show();	
			
			if (tela == 1)
			{
				if (document.form1.aux.value == 'S')
				{
					document.getElementById('gerar').disabled = false;
					document.getElementById('tipo_documento_id').disabled = true;	
				}
				else
				{
					document.getElementById('gerar').disabled = true;
					
					if (document.form1.aux.value == 'C') 
					{
						document.getElementById('tipo_documento_id').disabled = true;
						document.getElementById('ponto_operacao_id').disabled = true;
						document.getElementById('chave_acesso').disabled = true;
					}
					else	
					{
						if (document.form1.status_id.value == 'i')
						{
							document.getElementById('tipo_documento_id').disabled = true;
							document.getElementById('chave_acesso').disabled = true;
							document.getElementById('ponto_operacao_id').disabled = true;
						}
						else
						{
							document.getElementById('tipo_documento_id').disabled = false;
							document.getElementById('ponto_operacao_id').disabled = false;	
						}
					}
				}
			}
			else
			{
				if (document.form1.aux.value == 'S')
				{
					document.getElementById('finalizar').disabled = false;
					document.getElementById('tipo_documento_id').disabled = true;
				}
				else
				{
					document.getElementById('finalizar').disabled = true;
					
					if (document.form1.aux.value == 'C') 
					{
						document.getElementById('tipo_documento_id').disabled = true;
						document.getElementById('chave_acesso').disabled = true;
					}
					else	
					{
						if (document.form1.status_id.value == 'i')
						{
							document.getElementById('tipo_documento_id').disabled = true;
							document.getElementById('chave_acesso').disabled = true;
						}
						else
							document.getElementById('tipo_documento_id').disabled = false;

					}
				}
			}			
			
		}	
	});		
	
}

function exibe_comprovante_nao_conferido(protocolo_id)
{	

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_comprovante_nao_conferido.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			document.form1.chave_acesso.focus();
			
			$("#exibe_resultado1").html(msg);
			$('#exibe_resultado1').show();	
		}
	});		
	
}

function exibe_comprovante_conferido(protocolo_id)
{	

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_comprovante_conferido.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			document.form1.chave_acesso.focus();
			
			$("#exibe_resultado2").html(msg);
			$('#exibe_resultado2').show();	
		}
	});		
	
}

function focus_campo() {
	document.form1.chave_acesso.focus();	
}

function busca_protocolo_envio(id)
{	
	if (id != '')
	{
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/busca_protocolo_envio.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				
				filtra_po(dados[2]);
	
				setTimeout(function(){//atrasa a execução do codigo(nao obrigatório)	
				
					$("#num_protocolo").html(dados[1]);
					$('#num_protocolo').show();
					
					if (dados[4] != 'a')
					{
						$("#codigo_malote").html(dados[5]);
						$('#codigo_malote').show();					
					}
					
					document.getElementById('id').value	= dados[1];
					document.getElementById('tipo_documento_id').value	= dados[2];
					document.getElementById('ponto_operacao_id').value	= dados[3];
					
					if (dados[4] == 't')
					{
						$("#status_id").html("<option value='G'>EM TRANSITO</option>");
						document.getElementById('gravar').disabled = true;
						document.getElementById('gerar').disabled = true;
						document.getElementById('imprimir').disabled = false;
					}
					else
					if (dados[4] == 'f')
					{
						$("#status_id").html("<option value='G'>Finalizado</option>");
						document.getElementById('gravar').disabled = true;
						document.getElementById('gerar').disabled = true;
						document.getElementById('imprimir').disabled = false;
					}				
					else
					{
						document.getElementById('status_id').value  = dados[4];	
						document.getElementById('chave_acesso').disabled = false;
					}
					exibe_comprovante_envio(dados[1],1);	
					
				},250);				
			}
		});
	}
}


function busca_protocolo_envio_dig(id)
{	
	if (id != '')
	{

		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/busca_protocolo_envio_dig.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				console.log(msg);
				var dados = msg.split("|");
				
	
				setTimeout(function(){//atrasa a execução do codigo(nao obrigatório)	
				
					$("#num_protocolo").html(dados[1]);
					$('#num_protocolo').show();
					
					document.getElementById('id').value	= dados[1];
					document.getElementById('tipo_documento_id').value	= dados[2];
					

					if (dados[3] == 'f')
					{
						$("#status_id").html("<option value='G'>Finalizado</option>");
						document.getElementById('gravar').disabled = true;
						document.getElementById('finalizar').disabled = true;
						document.getElementById('imprimir').disabled = false;
						document.getElementById('xls').disabled = false;
					}				
					else
					{
						document.getElementById('status_id').value  = dados[3];	
						document.getElementById('chave_acesso').disabled = false;
					}
					exibe_comprovante_envio(dados[1],2);	
					
				},250);				
			}
		});
	}
}

function busca_protocolo_envio_rodopar(id)
{	


	if (id != '')
	{

		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/busca_protocolo_envio_dig.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				console.log(1111111111,msg);
				var dados = msg.split("|");
				
	
				setTimeout(function(){//atrasa a execução do codigo(nao obrigatório)	
				
					$("#num_protocolo").html(dados[1]);
					$('#num_protocolo').show();
					
					document.getElementById('id').value	= dados[1];
					document.getElementById('tipo_documento_id').value	= dados[2];
					

					if (dados[3] == 'f')
					{
						$("#status_id").html("<option value='G'>Finalizado</option>");
						document.getElementById('gravar').disabled = true;
						document.getElementById('finalizar').disabled = true;
						document.getElementById('imprimir').disabled = false;
						document.getElementById('xls').disabled = false;
					}				
					else
					{
						document.getElementById('status_id').value  = dados[3];	
						document.getElementById('chave_acesso').disabled = false;
						document.getElementById('finalizar').disabled = false;
					}
					exibe_comprovante_envio(dados[1],3);	
					
				},250);				
			}
		});
	}
}

function gera_edi()
{	
	//alert();
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	
	if ((data_ini == '') && (data_fim == ''))
	{
		$('#msg').html('Favor informar o Período');
		$('#myModal_alert').modal('show');
	}	
	else		
	if ((data_ini == '') && (data_fim != ''))
	{
		$('#msg').html('Favor inserir a data inicial');
		$('#myModal_alert').modal('show');
	}	
	else	
	if ((data_ini != '') && (data_fim == ''))
	{
		$('#msg').html('Favor inserir a data final');
		$('#myModal_alert').modal('show');
	}		
	else	
	{
		$("#procura").html("<font color='blue'>Gerando o arquivo </font><img src='../../images/carregando_vermelho.gif' width='15' height='15' alt='Espere'/>");
		$("#procura").show();
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/edi.php", //chama uma pagina
			data: "data_ini="+data_ini + "&data_fim="+data_fim, 
			success: function(msg){  //pega o retorno da pagina chamada
				$("#procura").hide();
				//alert(msg);
				if(msg.indexOf("SUCESSO") > -1)
				{
					$('#msg_success').html('Arquivo gerado com sucesso');
					$('#myModal_alert_sucess').modal('show');						
				}
				else
				{
					$('#msg').html('Arquivo não gerado');
					$('#myModal_alert').modal('show');					
				}
				
			}
		});
	}
	
}

$(document).on("change","#tipo_sistema", function(){
	var sel = $(this).val();

	if(sel == 1){
		$("#po_rodopar").show();
		$("#po_ecargo").hide();
	} else {
		$("#po_rodopar").hide();
		$("#po_ecargo").show();
	}
})

function filtra_relatorio()
{	
	var sistema	= document.getElementById('tipo_sistema').value;
	var ponto_operacao_id	= document.getElementById('ponto_operacao_id').value;
	var ponto_operacao_rodo_id	= document.getElementById('ponto_operacao_rodo_id').value;
	var tipo_documento_id	= document.getElementById('tipo_documento_id').value;
	var num_documento		= document.getElementById('num_documento').value;
	var status_id			= document.getElementById('status_id').value;
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	console.log(ponto_operacao_rodo_id)
	if ((tipo_documento_id == '') && (data_ini == ''))
	{
		$('#msg').html('Favor filtrar por Documento ou Período');
		$('#myModal_alert').modal('show');
	}	
	else
	if ((tipo_documento_id == '') && (num_documento != ''))
	{
		$('#msg').html('Favor selecionar o tipo do documento');
		$('#myModal_alert').modal('show');
	}	
	else
	if ((tipo_documento_id != '') && (data_ini == '') && (num_documento == ''))
	{
		$('#msg').html('Favor inserir o número do documento ou o Período');
		$('#myModal_alert').modal('show');
	}	
	else		
	if ((data_ini == '') && (data_fim != ''))
	{
		$('#msg').html('Favor inserir a data inicial');
		$('#myModal_alert').modal('show');
	}	
	else	
	if ((data_ini != '') && (data_fim == ''))
	{
		$('#msg').html('Favor inserir a data final');
		$('#myModal_alert').modal('show');
	}		
	else	
	{
		
		$("#procura").html("<font color='blue'>Carregando o relatório</font><img src='../../images/carregando_vermelho.gif' width='15' height='15' alt='Espere'/>");	
		$("#procura").show();	
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/filtra_relatorio.php", //chama uma pagina
			data: "ponto_operacao_id="+ponto_operacao_id + "&tipo_documento_id="+tipo_documento_id + "&num_documento="+num_documento + "&status_id="+status_id + "&data_ini="+data_ini + "&data_fim="+data_fim+"&sistema="+sistema+"&ponto_operacao_rodopar="+ponto_operacao_rodo_id, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				$("#procura").hide();	
				
				if(msg.indexOf("data_ini_maior") > -1)
				{
					$('#exibe_resultado').hide();	
					$('#msg').html('Data inicial maior que a data final');
					$('#myModal_alert').modal('show');						
				}	
				else
				{
					$("#exibe_resultado").html(msg);
					$('#exibe_resultado').show();	
				}
			
				
			}
		});
	}
	
}


function filtra_pesquisa(tela)
{	
	var num_protocolo		= document.getElementById('num_protocolo').value;
	var tipo_documento_id	= document.getElementById('tipo_documento_id').value;
	var num_documento		= document.getElementById('num_documento').value;
	var status_id			= document.getElementById('status_id').value;
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	
	if ((num_protocolo == '') && (tipo_documento_id == '') && (data_ini == ''))
	{
		$('#msg').html('Favor filtrar por protocolo, Tipo de Documento ou Período');
		$('#myModal_alert').modal('show');
	}	
	else
	if ((tipo_documento_id != '') && (num_documento == ''))
	{
		$('#msg').html('Favor inserir o número do documento');
		$('#myModal_alert').modal('show');
	}	
	else		
	if ((data_ini == '') && (data_fim != ''))
	{
		$('#msg').html('Favor inserir a data inicial');
		$('#myModal_alert').modal('show');
	}	
	else	
	if ((data_ini != '') && (data_fim == ''))
	{
		$('#msg').html('Favor inserir a data final');
		$('#myModal_alert').modal('show');
	}		
	else	
	{
		if (tela == 1)
			var tela_pesquisa = 'filtra_pesquisa.php';
		else
			var tela_pesquisa = 'filtra_pesquisa_dig.php';		
		
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/"+tela_pesquisa, //chama uma pagina
			data: "num_protocolo="+num_protocolo + "&tipo_documento_id="+tipo_documento_id + "&num_documento="+num_documento + "&status_id="+status_id + "&data_ini="+data_ini + "&data_fim="+data_fim, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				if(msg.indexOf("data_ini_maior") > -1)
				{
					$('#exibe_resultado').html('');	
					$('#msg').html('Data inicial maior que a data final');
					$('#myModal_alert').modal('show');						
				}	
				else
				{
					$("#exibe_resultado").html(msg);
					$('#exibe_resultado').show();	
				}
			}
		});
	}
	
}

function gera_protocolo(tela)
{	

	var id	= document.getElementById('id').value;

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/gera_protocolo.php", //chama uma pagina
		data: "id="+id, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);

			if(msg.indexOf("Ok") > -1)
			{			
				var dados = msg.split("|");
				var num_malote	= dados[1];
				
				$("#status_id").html("<option value='t'>EM TRANSITO</option>");
				$('#codigo_malote').html(dados[2]);
				
				document.getElementById('gravar').disabled = true;
				document.getElementById('gerar').disabled = true;
				document.getElementById('imprimir').disabled = false;
				
				$('#msg_success').html('Protocolo enviado com sucesso, vinculado ao malote '+num_malote);
				$('#myModal_alert_sucess').modal('show');			
			}
			exibe_comprovante_envio(id,tela);
		}
	});		
	
}


function gera_protocolo_mais(protocolo_id)
{	
	//alert(protocolo_id);
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/gera_protocolo_mais.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			var dados = msg.split("|");
			
			if(msg.indexOf("Ok") > -1)
			{
				document.getElementById('gera_protocolo_extra').disabled = true;
				
				$('#msg_success').html('Protocolo '+dados[2]+' gerado com sucesso');
				$('#myModal_alert_sucess').modal('show');
			}

		}
	});		
	
}


function finaliza_protocolo(tela)
{	
	
	var id	= document.getElementById('id').value;


	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/finaliza_protocolo.php", //chama uma pagina
		data: "id="+id + "&tela="+tela, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			document.getElementById('chave_acesso').disabled = true;
			document.getElementById('finalizar').disabled = true;
			
			if (tela == 1)
			{
				$("#status_receb").html('<font size="+1">FINALIZADO</font>');
				$('#status_receb').show();	
				
				exibe_comprovante_nao_conferido(id);		
				exibe_comprovante_conferido(id);		
			}
			else
			{
				$("#status_id").html("<option value='f'>Finalizado</option>");
				document.getElementById('gravar').disabled = true;
				exibe_comprovante_envio(id,tela);	
				document.getElementById('imprimir').disabled = false;
				document.getElementById('xls').disabled = false;
				
			}
			
	
			$('#msg_success').html('Protocolo finalizado com sucesso');
			$('#myModal_alert_sucess').modal('show');			

		}
	});		
	
}

function grava_qtde(elmnt, id, tela)
{	

	if (elmnt.value > 0)
	{
	
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/grava_qtde_ressalva.php", //chama uma pagina
			data: "qtde="+elmnt.value + "&id="+id + "&tela="+tela, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
		
			}
		});		
	}
	else
	{
		$('#msg').html('Favor inserir um número maior que zero');
		$('#myModal_alert').modal('show');		
		document.getElementById('qtde_ressalva'+id).value = 1;
	}
	
}

function ressalva_todos(protocolo_id, tela, aux){
	

		if ($("#ressalvatodos").is(':checked'))
		{
			if (aux == 1)
			{
				$(".classe_checkbox").prop("checked",true);
				$(".classe_campo").attr("disabled",false);
				$(".classe_campo").val(1);
			}
			var ressalva = 's';
		}
		else
		{
			if (aux == 1)
			{
				$(".classe_checkbox").prop("checked",false);
				$(".classe_campo").val('');
				$(".classe_campo").attr("disabled",true);	
			}
			var ressalva = 'n';	
		}
	
		
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/grava_ressalva_todos.php", //chama uma pagina
		data: "ressalva="+ressalva + "&protocolo_id="+protocolo_id + "&tela="+tela + "&aux="+aux, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
	
		}
	});			
		
}


function grava_ressalva(elmnt, id, tela, protocolo_id)
{	

		if (elmnt.checked == true)
		{
			ressalva = 's';
			document.getElementById('qtde_ressalva'+id).disabled = false;	
			document.getElementById('qtde_ressalva'+id).value = 1;
		}
		else
		{
			ressalva = 'n';
			document.getElementById('qtde_ressalva'+id).value = '';
			document.getElementById('qtde_ressalva'+id).disabled = true;
			
			$(".ressalva_t").attr("checked",false);
			
			ressalva_todos(protocolo_id, tela,2);
		}

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/grava_ressalva.php", //chama uma pagina
		data: "ressalva="+ressalva + "&id="+id + "&tela="+tela, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
	
		}
	});		

	
}



function exclui_comprovante(id, protocolo_id, tela){	
	//alert(id);

	if (id != "")
	{
		if (tela == 1)
			var pagina = 'exclui_comprovante.php';
		else 
			var pagina = 'exclui_comprovante_dig.php';		

		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/exclui_comprovante.php", //chama uma pagina
			data: "id="+id + "&tela="+tela, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
								
				exibe_comprovante_envio(protocolo_id,tela);	
		
			}
		});
	}	
	
	
}


function exclui_comprovante_conf(id, protocolo_id){	
	//alert(id);

	if (id != "")
	{

		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/exclui_comprovante_conf.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
								
				exibe_comprovante_nao_conferido(protocolo_id);		
				exibe_comprovante_conferido(protocolo_id);	
		
			}
		});
	}	
		
}

function filtra_docto_dig()
{	
	var num_documento		= document.getElementById('num_documento').value;
	var tipo_documento_id	= document.getElementById('tipo_documento_id').value;	
	var status_id			= document.getElementById('status_id').value;
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	
	if ((num_documento == '') && (data_ini == ''))
	{
		$('#msg').html('Favor filtrar por Nº Documento ou Período');
		$('#myModal_alert').modal('show');
	}	
	else		
	if ((data_ini == '') && (data_fim != ''))
	{
		$('#msg').html('Favor inserir a data inicial');
		$('#myModal_alert').modal('show');
	}	
	else	
	if ((data_ini != '') && (data_fim == ''))
	{
		$('#msg').html('Favor inserir a data final');
		$('#myModal_alert').modal('show');
	}		
	else	
	{
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/filtra_docto_dig.php", //chama uma pagina
			data: "tipo_documento_id="+tipo_documento_id + "&num_documento="+num_documento + "&status_id="+status_id + "&data_ini="+data_ini + "&data_fim="+data_fim, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				if(msg.indexOf("data_ini_maior") > -1)
				{
					$('#exibe_resultado').html('');	
					$('#msg').html('Data inicial maior que a data final');
					$('#myModal_alert').modal('show');						
				}	
				else
				{
					$("#exibe_resultado").html(msg);
					$('#exibe_resultado').show();	
				}
			}
		});
	}
	
}