function verCuotas(strID) {
	location.href = "objeto/cuotas&NumeClie=" + strID;
}

function toggleCuotas(blnEstado) {
	if (blnEstado) {
		$("#Anticipo").parent().parent().show();
		$("#CantCuot").parent().parent().show();
		$("#FechCuot").parent().parent().parent().show();

		var fecha = moment().add(1, 'M');
		$("#FechCuot").val(fecha.format("YYYY-MM") + "-01");
		calcularCuotas();
	} else {
		$("#Anticipo").parent().parent().hide();
		$("#CantCuot").parent().parent().hide();
		$("#FechCuot").parent().parent().parent().hide();
	}
}

function calcularCuotas() {
	$.ajax({
		type: 'POST',
		url: 'php/tablaHandler.php',
		data: { 
			operacion: '100', 
			tabla: 'pasajeros', 
			field: 'CalcCuotas', 
			dato: {
				"NumeCont": $("#NumeCont").val(),
				"FechCuot": $("#FechCuot").val(),
				"Anticipo": $("#Anticipo").val()
			}
		},
		success: function(data) {
			$("#CantCuot").html(data.valor);
			
		},
		async:true
	});
}