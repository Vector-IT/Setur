function abrirCheques() {
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


    $("#modalCheques").click();
}

function afterLoad() {
	$("#CodiCheq").prop("disabled", true);

	cargarCheques();
	afterList();
}

function afterList() {
	var saldo = parseFloat($("#txtSaldo").html());

	if (saldo == 0) {
		$("#btnNuevo").hide();
	}
	else {
		$("#btnNuevo").show();
	}

    $("#divDatos").find("tr").each(function (I) {
        if (I > 0) {
            var strID = $(this).find("input[id^='NumePago']").val();

            if ($("#NumeEsta" + strID).val() != "1") {
                $(this).addClass("txtTachado");
            }
        }
    });
	
}

function cargarCheques() {
    $.ajax({
		url: 'php/tablaHandler.php',
		type: 'post',
		async: true,
		data: {	
			operacion: '100', 
			tabla: 'cuotaspagos', 
			field: "Cheques", 
			dato: "" 
		}, 
		success: 
			function(data) {
				$("#CodiCheq").html(data['valor']);
			}
	});
}

function buscarImporte() {
	var codiCheq = $("#CodiCheq").val();

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

function formasPago() {
	var formaPago = $("#NumeTipoPago option:selected").text().toUpperCase();

	if (formaPago === "CHEQUE") {
		$("#CodiCheq").prop("disabled", false);
		$("#ImpoPago").prop("readonly", true);
	}
	else {
		$("#CodiCheq").prop("disabled", true);
		$("#ImpoPago").prop("readonly", false);
	}
}

function cambiarEstado(strID) {
    $("#actualizando").show();

	var codiIden = $("#CodiIden"+strID).val();

	$.ajax({
		type: 'POST',
		url: 'php/tablaHandler.php',
		data: { 
			operacion: '100', 
			tabla: 'cuotaspagos', 
			field: 'NumeEsta', 
			dato: {"NumePago": strID, "CodiIden": codiIden}
		},
		success: function(data) {
            if (data.valor === true) {
                $("#txtHint").html("Datos actualizados!");
                $("#divMsj").removeClass("alert-danger");
				$("#divMsj").addClass("alert-success");

                listarcuotaspagos();
            }
            else {
                $("#txtHint").html(data.valor);
                $("#divMsj").removeClass("alert-success");
				$("#divMsj").addClass("alert-danger");
            }
            $("#actualizando").hide();
			$("#divMsj").show();
		},
		async:true
	});
}

function validar() {
	var saldo = parseFloat($("#txtSaldo").html());
	var impoPago = parseFloat($("#ImpoPago").val());
	
	var mensaje = '';

	if (saldo < impoPago) {
		mensaje+= 'El importe del pago no puede ser mayor al saldo!<br>'
	}

	var formaPago = $("#NumeTipoPago option:selected").text().toUpperCase();
	if (formaPago === "CHEQUE") {
		if ($("#CodiCheq").val() == '') {
			mensaje+= 'Debe seleccionar un cheque!<br>'
		}
	}

	if (isNaN(impoPago) || impoPago <= 0) {
		mensaje+= 'El importe del pago debe ser mayor a 0 (cero)<br>'
	}

	if (mensaje != '') {
		$("#actualizando").hide();
		$("#txtHint").html(mensaje);
		$("#divMsj").removeClass("alert-success");
		$("#divMsj").addClass("alert-danger");
		$("#divMsj").show();
		return false;
	}

	return true;
}

