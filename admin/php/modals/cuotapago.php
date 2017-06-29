<div id="modalPago" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="cuotapago">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Pagar Cuota <span id="modNumeCuot"></span></h4>
            </div>
            <form id="frmPago" class="form-horizontal">
                <input type="hidden" id="modCodiIden" value="" />
                <div class="modal-body">
                    <button type="button" class="btn btn-success" onclick="abrirCheques();"><i class="fa fa-credit-card fa-fw" aria-hidden="true"></i> Cheques</button>
                    <hr>
                    <div class="form-group form-group-sm ">
                        <label for="modNumeTipoPago" class="control-label col-md-3">Forma de pago:</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm ucase " id="modNumeTipoPago" required onchange="formasPago()">
                                <?php echo $config->cargarCombo("tipospagos", "NumeTipoPago", "NombTipoPago", "NumeEsta = 1", "NumeTipoPago") ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-sm ">
                        <label for="modCodiCheq" class="control-label col-md-3">Cheque:</label>
                        <div class="col-md-9">
                            <select class="form-control input-sm ucase " id="modCodiCheq" onchange="buscarImporte()" required disabled>
                                <?php echo $config->cargarCombo("cheques", "CodiCheq", "CONCAT(NumeCheq, ' - $', ImpoCheq)", "NumeEsta = 1", "NumeCheq") ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group form-group-sm ">
                        <label for="modObseCuot" class="control-label col-md-3">Observaciones:</label>
                        <div class="col-md-9">
                            <textarea class="form-control input-sm autogrow " id="modObseCuot" style="height: 48px;"></textarea>
                        </div>
                    </div>                    
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>
                </div>
            </form>

        </div>
    </div>
</div>