let timeout = null;

function inclui_chave(elmnt, protocolo_id) {
  console.log("entrei met__");
  console.log("ponto_operacao_id:", ponto_operacao_id);
console.log("tipo_documento_id:", tipo_documento_id);
console.log("sistema:", sistema);
  clearTimeout(timeout);

  var tipo_documento_id = document.getElementById("tipo_documento_id").value;
  var ponto_operacao_id = document.getElementById("ponto_operacao_id").value;
  var sistema = document.getElementById("sistema").value;

  console.log("ponto enviado:", ponto_operacao_id); // confirma o valor

  timeout = setTimeout(() => {
    console.log("entrei fun__");

    if (elmnt.value != "") {
      var numero_chave = elmnt.value;

      if (
        (tipo_documento_id == "C" && elmnt.value.length == 44) ||
        (tipo_documento_id == "F" && elmnt.value.length == 15)
      ) {
        var verifica = numero_chave.substr(0, 3);

        $.ajax({
          type: "POST", //define o metódo de passagem de parametros
          url: "includes/grava_chave_sts.php", //chama uma pagina
         /* data:
            "chave=" +
            elmnt.value +
            "&protocolo_id=" +
            protocolo_id +
            "&sistema=" +
            sistema +
            "&tipo_documento=" +
            tipo_documento_id, */ // 16:19 20/02
            data:
    "chave=" +
    elmnt.value +
    "&protocolo_id=" +
    protocolo_id +
    "&sistema=" +
    sistema +
    "&tipo_documento=" +
    tipo_documento_id +
    "&ponto_operacao_id=" +   // <- ADICIONAR ISSO
    ponto_operacao_id,

            
          success: function (msg) {
            //pega o retorno da pagina chamada
            //console.log(msg)
            if (msg.indexOf("ok") > -1) {
              exibe_comprovante_envio_sts(protocolo_id, 1);
              document.getElementById("gerar").disabled = false;
            } else if (
              msg.indexOf(
                "Atenção, esse documento ja foi vinculado ao protocolo:"
              ) > -1
            ) {
              exibe_comprovante_envio_sts(protocolo_id, 1);
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
      url: "includes/grava_protocolo_envio_sts.php", //chama uma pagina
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

        //exibe_comprovante_envio_sts(id, 1); -- alteradoi isso 16:09 20/02
        exibe_comprovante_envio_sts(dados[2], 1);
      },
    });
  }
}

function filtra_po_sts(elmnt)
{    
    $.ajax({
        type: "POST",
        url: "includes/filtra_po_sts.php",
        data: "tipo_documento_id="+elmnt,
        success: function(msg){
            console.log(msg)
            $("#ponto_operacao_id").html(msg);
        }
    });
}

function imprime_protocolo_sts(tela) {
  var id = document.getElementById('id').value;
  pagina('impressao_protocolo_sts.php?id=' + id + '&tl=' + tela, '900', '800', 'impressao');
}