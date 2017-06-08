$(document).ready(function () {
    var strCombo = "";
    strCombo+= '<option value="0">NUMERO</option>';
    strCombo+= '<option value="1">FECHA</option>';
    strCombo+= '<option value="2">MES</option>';
    strCombo+= '<option value="3">TABLA</option>';
    
    $("#TipoParam").html(strCombo);
});