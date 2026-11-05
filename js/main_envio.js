let timeout = null;

function inclui_chave(elmnt, protocolo_id) {
  console.log("entrei met_");
  clearTimeout(timeout);

  var tipo_documento_id = document.getElementById("tipo_documento_id").value;
  var ponto_operacao_id = document.getElementById("ponto_operacao_id").value;
  var sistema = document.getElementById("sistema").value;

  timeout = setTimeout(() => {
    console.log("entrei fu_");

    if (elmnt.value != "") {
      var numero_chave = elmnt.value;

      if (
        (tipo_documento_id == "C" && elmnt.value.length == 44) ||
        (tipo_documento_id == "F" && elmnt.value.length == 15)
      ) {
        var verifica = numero_chave.substr(0, 3);

        $.ajax({
          type: "POST", //define o metódo de passagem de parametros
          url: "includes/grava_chave.php", //chama uma pagina
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
            //console.log(msg)
            if (msg.indexOf("ok") > -1) {
              exibe_comprovante_envio(protocolo_id, 1);
              document.getElementById("gerar").disabled = false;
            } else if (
              msg.indexOf(
                "Atenção, esse documento ja foi vinculado ao protocolo:"
              ) > -1
            ) {
              exibe_comprovante_envio(protocolo_id, 1);
              document.getElementById("gerar").disabled = false;
              $("#msg").html(msg);
              $("#myModal_alert").modal("show");
            } else {
              $("#msg").html(msg);
              $("#myModal_alert").modal("show");
            }

            document.form1.chave_acesso.value = "";
            document.form1.chave_acesso.focus();
          },
        });
      }
    }
  }, 300);
}

function grava_registro() {
  document.getElementById("tipo_documento_id").disabled = false;
  document.getElementById("ponto_operacao_id").disabled = false;

  var id = document.getElementById("id").value;
  var tipo_documento_id = document.getElementById("tipo_documento_id").value;
  var ponto_operacao_id = document.getElementById("ponto_operacao_id").value;
  var status_id = document.getElementById("status_id").value;
  var sistema = document.getElementById("sistema").value;

  if (tipo_documento_id == "") {
    $("#msg").html("Favor selecionar o Tipo de Documento");
    $("#myModal_alert").modal("show");
  } else if (ponto_operacao_id == "") {
    $("#msg").html("Favor selecionar o Ponto de Operação");
    $("#myModal_alert").modal("show");
  } else {
    let dados = $("form").serialize();

    $.ajax({
      type: "POST", //define o metуdo de passagem de parametros
      url: "includes/grava_protocolo_envio.php", //chama uma pagina
      data: dados, //passa os parametros, se necessбrio
      success: function (msg) {
        console.log(msg);

        if (msg.indexOf("ok") == -1) {
          $("#msg").html(msg);
          $("#myModal_alert").modal("show");
        } else {
          var dados = msg.split("|");
          document.form1.id.value = dados[2];
          document.form1.chave_acesso.disabled = false;
          //document.form1.acesso.disabled = true;

          $("#num_protocolo").html(dados[2]);
          $("#num_protocolo").show();

          document.form1.chave_acesso.focus();
        }

        exibe_comprovante_envio(id, 1);
      },
    });
  }
}
