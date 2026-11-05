

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


/*AUTOCOMPLETE CLIENTE*/
	function passaParametro(param){
	 if ($("#nome_cliente").val().length >= 2) {	
			$.ajax({
			type: "POST",
			url: "includes/ajax_cliente.php",
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
		document.getElementById('nome_cliente').value = val;
		document.getElementById('nome_cliente').focus();
		$("#suggesstion-box").hide();
	}
/*FIM AUTOCOMPLETE*/


function busca_cliente_id(elmnt){	
	//alert(tipo);
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/busca_cliente_id.php", //chama uma pagina
		data: "cliente="+elmnt.value, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			var dados = msg.split("|");//quebra a string com base em um caracter

			if (dados[1] != '')
				document.form1.cliente_id.value 		= dados[1];
			else
				document.form1.cliente_id.value 		= '';
		
		}
	});
	
}


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




function exibe_cg(elmnt)
{	
	document.form1.nome_cliente.disabled = false;
	
	if (elmnt == 'C')
	{
		$("#grupo").hide();	
		$("#cliente").show();	
		document.getElementById('grupo_cliente_id').value = '';		
	}
	else
		if (elmnt == 'G')
		{
			$("#cliente").hide();	
			$("#grupo").show();	
			document.getElementById('nome_cliente').value = '';
			document.getElementById('cliente_id').value = '';		
		}	
		else
		{
			document.getElementById('grupo_cliente_id').value = '';	
			$("#grupo").hide();	
			$("#cliente").show();		
			document.form1.nome_cliente.disabled = true;
			document.getElementById('nome_cliente').value = '';
			document.getElementById('cliente_id').value = '';				
		}

}


function exibe_nota_fiscal(protocolo_id)
{	

	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_nota_fiscal.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			document.form1.nota_fiscal.focus();
			
			$("#exibe_resultado").html(msg);
			$('#exibe_resultado').show();	
			

			if (document.form1.aux.value == 'S')
				document.getElementById('finalizar').disabled = false;
			else
				document.getElementById('finalizar').disabled = true;
		
		
		}	
	});		
	
}

function filtra_pesquisa()
{	
	
	var num_protocolo		= document.getElementById('num_protocolo').value;
	var nota_fiscal			= document.getElementById('nota_fiscal').value;
	var cliente_grupo_id	= document.getElementById('cliente_grupo_id').value;
	var nome_cliente		= document.getElementById('nome_cliente').value;
	var cliente_id			= document.getElementById('cliente_id').value;
	var grupo_cliente_id	= document.getElementById('grupo_cliente_id').value;		
	var status_id			= document.getElementById('status_id').value;
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	
	
	if ((num_protocolo == '') && (nota_fiscal == '') && (data_ini == '') && (data_fim == ''))
	{
		$('#msg').html('Favor filtrar por Protocolo, Nota Fiscal ou Período');
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
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/filtra_pesquisa_canhoto.php", //chama uma pagina
			data: "num_protocolo="+num_protocolo + "&nota_fiscal="+nota_fiscal + "&cliente_grupo_id="+cliente_grupo_id + "&cliente_id="+cliente_id + "&grupo_cliente_id="+grupo_cliente_id + "&status_id="+status_id + "&data_ini="+data_ini + "&data_fim="+data_fim, 
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


function exibe_canhoto(protocolo_id)
{	
	//alert(protocolo_id);
	
	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/exibe_canhoto_protocolo.php", //chama uma pagina
		data: "protocolo_id="+protocolo_id, //passa os parametros, se necessário
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			document.form1.nota_fiscal.focus();
			
			$("#exibe_resultado").html(msg);
			$('#exibe_resultado').show();	
			

			if (document.form1.aux.value == 'S')
				document.getElementById('finalizar').disabled = false;
			else
				document.getElementById('finalizar').disabled = true;
			
		}	
	});		
	
}

function exclui_canhoto(id, protocolo_id){	
	//alert(id);

	if (id != "")
	{

		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/exclui_canhoto.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
								
				exibe_canhoto(protocolo_id);	
		
			}
		});
	}	
	
	
}

function busca_protocolo_canhoto(id)
{	
	if (id != '')
	{
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/busca_protocolo_canhoto.php", //chama uma pagina
			data: "id="+id, //passa os parametros, se necessário
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);
				var dados = msg.split("|");
				
				setTimeout(function(){//atrasa a execução do codigo(nao obrigatório)	
				
					$("#num_protocolo").html(dados[1]);
					$('#num_protocolo').show();
					
						
					document.getElementById('id').value						= dados[1];
					document.getElementById('data').value					= dados[2];
					document.getElementById('cliente_grupo_id').value		= dados[3];
					document.getElementById('cliente_id').value				= dados[4];
					
					if (dados[3] == 'C')
					{
						$("#grupo").hide();	
						$("#cliente").show();							
						document.getElementById('grupo_cliente_id').value = '';
						document.form1.nome_cliente.disabled = false;
						document.getElementById('nome_cliente').value			= dados[5];
					}
					else
					{
						$("#cliente").hide();	
						$("#grupo").show();							
						document.getElementById('nome_cliente').value = '';
						document.getElementById('cliente_id').value = '';
						document.form1.nome_cliente.disabled = true;						
						document.getElementById('grupo_cliente_id').value		= dados[6];
					}
					document.getElementById('responsavel_cliente').value	= dados[7];
					document.getElementById('depto_cliente').value			= dados[8];					
					

					if (dados[9] == 'f')
					{
						$("#status_id").html("<option value='G'>Finalizado</option>");
						document.getElementById('gravar').disabled = true;
						document.getElementById('finalizar').disabled = true;
						document.getElementById('imprimir').disabled = false;
					}				
					else
					{
						document.getElementById('status_id').value  = dados[9];	
						document.getElementById('nota_fiscal').disabled = false;
					}
					
					exibe_canhoto(id);	
					
				},250);				
			}
		});
	}
}

function finaliza_protocolo()
{	
	
	var id	= document.getElementById('id').value;


	$.ajax({type: "POST",//define o metódo de passagem de parametros
		url: "includes/finaliza_protocolo_canhoto.php", //chama uma pagina
		data: "id="+id, 
		success: function(msg){  //pega o retorno da pagina chamada
			//alert(msg);
			
			$("#status_id").html("<option value='f'>Finalizado</option>");
			document.getElementById('nota_fiscal').disabled = true;
			document.getElementById('finalizar').disabled = true;
			document.getElementById('gravar').disabled = true;
			document.getElementById('inserir').disabled = true;
			exibe_canhoto(id);	
			document.getElementById('imprimir').disabled = false;
		
	
			$('#msg_success').html('Protocolo finalizado com sucesso');
			$('#myModal_alert_sucess').modal('show');			

		}
	});		
	
}


function filtra_pesquisa_canhoto_pendente()
{	

	var nota_fiscal			= document.getElementById('nota_fiscal').value;
	var cliente_grupo_id	= document.getElementById('cliente_grupo_id').value;
	var nome_cliente		= document.getElementById('nome_cliente').value;
	var cliente_id			= document.getElementById('cliente_id').value;
	var grupo_cliente_id	= document.getElementById('grupo_cliente_id').value;		
	var status_id			= document.getElementById('status_id').value;
	var data_ini			= document.getElementById('data_ini').value;
	var data_fim			= document.getElementById('data_fim').value;
	
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
	if ((nota_fiscal == '') && (data_ini == '') && (data_fim == ''))
	{
		$('#msg').html('Favor filtrar por Nota Fiscal ou Período');
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
			url: "includes/filtra_pesquisa_canhoto_pendente.php", //chama uma pagina
			data: "nota_fiscal="+nota_fiscal + "&cliente_grupo_id="+cliente_grupo_id + "&cliente_id="+cliente_id + "&grupo_cliente_id="+grupo_cliente_id + "&status_id="+status_id + "&data_ini="+data_ini + "&data_fim="+data_fim, 
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

function gera_protocolo()
{	

  var pacote = document.querySelectorAll('[name=Pacote]:checked');
  var values = [];
  for (var i = 0; i < pacote.length; i++) {
    // utilize o valor aqui, adicionei ao array para exemplo
    values.push(pacote[i].value);
  }
  
  	//alert(values);

	if (values == '')
	{
		$('#msg').html('Favor selecionar pelo menos uma Nota Fiscal');
		$('#myModal_alert').modal('show');		
	}
	else
	{
		//values = ','+values;
		
		$.ajax({type: "POST",//define o metódo de passagem de parametros
			url: "includes/grava_canhoto_nfe_pendente.php", //chama uma pagina
			data: "values="+values, 
			success: function(msg){  //pega o retorno da pagina chamada
				//alert(msg);

				var dados = msg.split("|");
				
				if(msg.indexOf("ok") > -1)
				{
					$('#msg_success').html('Protocolo '+dados[2]+' gerado com sucesso');
					$('#myModal_alert_sucess').modal('show');
					
					filtra_pesquisa_canhoto_pendente();
				}							
				
			}
		});
	}
	
}