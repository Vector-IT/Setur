function cambiarEstado(strID) {
    $("#actualizando").show();

	$.ajax({
		type: 'POST',
		url: 'php/tablaHandler.php',
		data: { 
			operacion: '100', 
			tabla: 'caja', 
			field: 'NumeEsta', 
			dato: {"NumeCaja": strID, "NumeEsta": $("#NumeEsta"+strID).val()}
		},
		success: function(data) {
            if (data.valor === true) {
                $("#txtHint").html("Datos actualizados!");
                $("#divMsj").removeClass("alert-danger");
				$("#divMsj").addClass("alert-success");

                listarcaja();
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

function iniciar() {
	//$("#search-FechCaja").parent().parent().parent().hide();
}

function verTodos() {
	$("#search-FechCaja").val('TODOS');
	listarcaja();
}