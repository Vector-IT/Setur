$(document).ready(function () {
    $("[data-fancybox]").fancybox({
        iframe : {
            css : {
                width : '100%',
                height: '100%'
            },
        },
        afterClose: function () {
            cargarCheques();
        }
    });

    $("#frmPago").submit(function () {
        $("#actualizando").show();
        $('#modalPago').modal("toggle");

        $.ajax({
			type: 'POST',
			url: 'php/tablaHandler.php',
			data: { 
				operacion: '100', 
				tabla: 'cuotas', 
				field: 'Pago', 
				dato: {
                    "CodiIden": $("#modCodiIden").val(),
                    "NumeTipoPago": $("#modNumeTipoPago").val(),
                    "CodiCheq": $("#modCodiCheq").val(),
                    "ObseCuot": $("#modObseCuot").val()
                }
			},
			success: function(data) {
                $("#actualizando").hide();
				if (data.valor === true) {
                    $("#divMsj").removeClass("alert-danger");
				    $("#divMsj").addClass("alert-success");
                    $("#txtHint").html("Datos actualizados!");

                    listarcuotas();
                } else {
                    $("#divMsj").addClass("alert-danger");
				    $("#divMsj").removeClass("alert-success");
                    $("#txtHint").html(data.valor);
                }
                $("#divMsj").show();
			},
			async:true
		});

        return false;
    });
});

function verPago(strID) {
    $("#frmPago")[0].reset();

    $("#modCodiIden").val(strID);
    $("#modNumeCuot").html($("#NumeCuot" + strID).html());

    $('#modalPago').modal();
    $("#modObseCuot").autogrow({vertical: true, horizontal: false, minHeight: 36});
}

function verPagos(CodiIden) {
    location.href = "objeto/cuotaspagos&CodiIden="+CodiIden;
}

function abrirCheques() {
    $("#modalCheques").click();
}

function formasPago() {
	var formaPago = $("#modNumeTipoPago option:selected").text().toUpperCase();

	if (formaPago === "CHEQUE") {
		$("#modCodiCheq").prop("disabled", false);
	}
	else {
		$("#modCodiCheq").prop("disabled", true);
	}
}

function buscarImporte() {
	var codiCheq = $("#modCodiCheq").val();

	if (codiCheq != "") {
		$.ajax({
			type: 'POST',
			url: 'php/tablaHandler.php',
			data: { 
				operacion: '100', 
				tabla: 'cuotaspagos', 
				field: 'ImpoCheq', 
				dato: {"CodiCheq": codiCheq}
			},
			success: function(data) {
				$("#ImpoPago").val(data.valor);
			},
			async:false
		});
	}
}

function ctrlBotones() {
    $("button[id^='btnPagos']").each(function (I, btn) {
        var strID = btn.id.replace("btnPagos", "");
        var numeEsta = $("#NumeEstaCuot" + strID).val();

        if (numeEsta == "2") { //PAGADA
            $(btn).removeClass("btn-primary")
                .addClass("btn-danger")
                .html("Anular Pago")
                .attr("onclick", "anularPago("+strID+")");
        }

    });
}

function anularPago(strID) {
    if (confirm("Confirma la anulación del pago de la cuota "+ $("#NumeCuot"+strID).html())) {
        $("#actualizando").show();

        $.ajax({
            type: 'POST',
            url: 'php/tablaHandler.php',
            data: { 
                operacion: '100', 
                tabla: 'cuotas', 
                field: 'AnularPago', 
                dato: {
                    "CodiIden": strID,
                }
            },
            success: function(data) {
                $("#actualizando").hide();
                if (data.valor === true) {
                    $("#divMsj").removeClass("alert-danger");
                    $("#divMsj").addClass("alert-success");
                    $("#txtHint").html("Datos actualizados!");

                    listarcuotas();
                } else {
                    $("#divMsj").addClass("alert-danger");
                    $("#divMsj").removeClass("alert-success");
                    $("#txtHint").html(data.valor);
                }
                $("#divMsj").show();
            },
            async:true
        });
    }
}