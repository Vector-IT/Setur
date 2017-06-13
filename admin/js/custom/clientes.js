function verCuotas(strID) {
	location.href = "objeto/cuotas&NumeClie=" + strID;
}

function toggleCuotas(blnEstado) {
	if (blnEstado) {
		$("#Anticipo").prop("readonly", false);
		$("#Ancticipo").parent().parent().show();

		$("#CantCuot").prop("readonly", false);
		$("#CantCuot").parent().parent().show();

		$("#FechCuot").parent().parent().parent().show();
	} else {
		$("#CantCuot").prop("readonly", true);
		$("#CantCuot").parent().parent().hide();
		
		$("#FechCuot").parent().parent().parent().hide();
	}
	
}