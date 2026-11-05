$(document).on("change", "#tipo_documento_id", function (el) {
  var val = $(this).val();

  if (val == "C") {
    $("#btIncluri").hide();
  } else {
    $("#btIncluri").show();
  }
});

function grava_registro() {
  document.getElementById("tipo_documento_id").disabled = false;

  var id = document.getElementById("id").value;
  var tipo_documento_id = document.getElementById("tipo_documento_id").value;
  var status_id = document.getElementById("status_id").value;
  var sistema = document.getElementById("sistema").value;

  if (tipo_documento_id == "") {
    $("#msg").html("Favor selecionar o Tipo de Documento");
    $("#myModal_alert").modal("show");
  } else if (sistema == "") {
    $("#msg").html("Favor selecionar o Sistema");
    $("#myModal_alert").modal("show");
  } else {
    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_protocolo_envio_dig.php", //chama uma pagina
      data: dados, //passa os parametros, se necessбrio
      success: function (msg) {
        //alert(msg);

        if (msg.indexOf("ok") == -1) {
          $("#msg").html(msg);
          $("#myModal_alert").modal("show");
        } else {
          var dados = msg.split("|");
          document.form1.id.value = dados[2];
          document.form1.chave_acesso.disabled = false;

          $("#num_protocolo").html(dados[2]);
          $("#num_protocolo").show();

          document.form1.chave_acesso.focus();
        }

        exibe_comprovante_envio(id, 2);
      },
    });
  }
}

function gerar_excel1(nome_arquivo, id_tabela) {
  var id = $("#id").val();

  window.open("includes/gera_excel_cr2.php?protocolo=" + id, "_blank");
}

function habilitaBtIncluiChave(elmnt, protocolo_id) {
  var tipo_documento_id = document.getElementById("tipo_documento_id").value;

  if (tipo_documento_id == "C" && elmnt.value.length == 44) {
    inclui_chave(protocolo_id);
  } else if (tipo_documento_id == "F" && elmnt.value.length == 15) {
    inclui_chave(protocolo_id);
  } else {
    $("#btIncluri").prop("disabled", true);
  }
}

//let ajaxInProgress = false;

function inclui_chave(protocolo_id) {
  //if (ajaxInProgress) return;

  var tipo_documento_id = document.getElementById("tipo_documento_id").value;
  var sistema = document.getElementById("sistema").value;
  var elmnt = document.getElementById("chave_acesso");

  if (elmnt.value != "") {
    var numero_chave = elmnt.value;

    if (
      (tipo_documento_id == "C" && elmnt.value.length == 44) ||
      (tipo_documento_id == "F" &&
        (elmnt.value.length == 15 ||
          elmnt.value.length == 19 ||
          elmnt.value.length == 18))
    ) {
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
      $.ajax({
        type: "POST", //define o metódo de passagem de parametros
        url: "includes/grava_chave_dig.php", //chama uma pagina
        data:
          "chave=" +
          elmnt.value +
          "&protocolo_id=" +
          protocolo_id +
          "&sistema=" +
          sistema +
          "&tipo_documento=" +
          tipo_documento_id,
        success: function (msg) {
          //pega o retorno da pagina chamada
          console.log(msg);
          //ajaxInProgress = true;
          //alert(msg);
          if (msg.indexOf("ok") > -1) {
            console.log(protocolo_id, 2);
            document.getElementById("finalizar").disabled = false;
          } else if (
            msg.indexOf(
              "Atenção, a chave inserida ja foi enviada para digitalização no protocolo:"
            ) > -1
          ) {
            document.getElementById("finalizar").disabled = false;
            $("#msg").html(msg);
            $("#myModal_alert").modal("show");
          } else {
            $("#msg").html(msg);
            $("#myModal_alert").modal("show");
          }

          exibe_comprovante_envio(protocolo_id, 2);
          document.form1.chave_acesso.value = "";
          document.form1.chave_acesso.focus();
        },
      });
    }
  }
}

function imprime_protocolo(tela) {
  var id = document.getElementById("id").value;

  pagina(
    "impressao_protocolo.php?id=" + id + "&tela=" + tela,
    "900",
    "800",
    "impressao"
  );
}

function imprime_protocolo_sts(tela) {
  var id = document.getElementById("id").value;

  pagina(
    "impressao_protocolo_sts.php?id=" + id + "&tela=" + tela,
    "900",
    "800",
    "impressao"
  );
}
