function verCuotas(strID) {
	location.href = "objeto/cuotas&NumeClie=" + strID;
}

function toggleCuotas(blnEstado) {
	if (blnEstado) {
		habilitar($("#Anticipo"));
		$("#Anticipo").parent().parent().show();
		habilitar($("#CantCuot"));
		$("#CantCuot").parent().parent().show();
		habilitar($("#FechCuot"));
		$("#FechCuot").parent().parent().parent().show();

		var fecha = moment().add(1, 'M');
		$("#FechCuot").val(fecha.format("YYYY-MM"));
		calcularCuotas();
	} else {
		deshabilitar($("#Anticipo"));
		$("#Anticipo").parent().parent().hide();
		deshabilitar($("#CantCuot"));
		$("#CantCuot").parent().parent().hide();
		deshabilitar($("#FechCuot"));
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