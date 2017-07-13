$(document).ready(function() {
	$("#actualizando").hide();
	$("#divMsj").hide();
	$("#frmCarga").submit(function() {aceptarcarga();});
});

function aceptarcarga() {
    $("#btnAceptar, #btnCancelar").addClass("disabled");
	$("#actualizando").show();

	var frmData = new FormData();
	frmData.append("operacion", 100);
	frmData.append("tabla", 'cuotas');
	frmData.append("field", 'TXT');

	frmData.append("Archivo", $("#Archivo").get(0).files[0]);

	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			var respuesta = JSON.parse(xmlhttp.responseText);

			if (respuesta.valor.estado) {
				$("#txtHint").html("Carga Exitosa!");

				$("#divMsj").removeClass("alert-danger");
				$("#divMsj").addClass("alert-success");

				$("#divMsj").show();

				$('#frmCarga')[0].reset();
				$("#actualizando").hide();

				$("#btnAceptar, #btnCancelar").removeClass("disabled");
			}
			else {
				$("#txtHint").html("Error en la Carga!<br>"+ respuesta.valor.mensaje);
				$("#divMsj").removeClass("alert-success");
				$("#divMsj").addClass("alert-danger");

				$("#actualizando").hide();						
				$("#divMsj").show();
				$("#btnAceptar, #btnCancelar").removeClass("disabled");
			}
		}
	};
	
	xmlhttp.open("POST","php/tablaHandler.php",true);
	xmlhttp.send(frmData);
}