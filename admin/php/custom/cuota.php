<?php
namespace VectorForms;

class Cuota extends Tabla
{
    public function customFunc($post)
    {
        global $config;
        
        switch ($post['field']) {
            case "Pago":
                $CodiIden = $post["dato"]["CodiIden"];
                $NumeTipoPago = $post["dato"]["NumeTipoPago"];
                $CodiCheq = $post["dato"]["CodiCheq"];
                $ObseCuot = $post["dato"]["ObseCuot"];

                $strSQL = "UPDATE cuotas SET NumeEstaCuot = 2, FechPago = SYSDATE(), NumeTipoPago = {$NumeTipoPago}";
                if ($CodiCheq != '') {
                    $strSQL.= ", CodiCheq = ". $CodiCheq;
                }

                if (trim($ObseCuot) != ''){
                    $strSQL.= ", ObseCuot = '$ObseCuot'";
                }

                $strSQL.= " WHERE CodiIden = ". $CodiIden;

                return $config->ejecutarCMD($strSQL);
                break;
            
            case "AnularPago":
                $CodiIden = $post["dato"]["CodiIden"];
                
                $strSQL = "UPDATE cuotas SET NumeEstaCuot = 1, FechPago = NULL, NumeTipoPago = NULL, CodiCheq = NULL WHERE CodiIden = ". $CodiIden;

                return $config->ejecutarCMD($strSQL);
                break;
        }
    }

    public function editar($datos)
    {
        global $config;

        $result = parent::editar($datos);

        $numeClie = $config->buscarDato("SELECT NumeClie FROM cuotas WHERE CodiIden = ". $datos["CodiIden"]);

        $total = intval($config->buscarDato("SELECT COUNT(*) FROM cuotas WHERE NumeClie = ".$numeClie));
        $pagadas = intval($config->buscarDato("SELECT COUNT(*) FROM cuotas WHERE NumeClie = ".$numeClie." AND NumeEstaCuot = 3"));
        $pagoparcial = intval($config->buscarDato("SELECT COUNT(*) FROM cuotas WHERE NumeClie = ".$numeClie." AND NumeEstaCuot = 2"));

        $datos2 = [];
        $datos2["NumeClie"] = $numeClie;

        $saldo = $total - $pagadas - $pagoparcial;

        if ($saldo == 0) {
            if ($pagoparcial == 0) { //TODAS PAGADAS
                $datos2["NumeEstaClie"] = "4";
            }
            else {
                $datos2["NumeEstaClie"] = "3";
            }
        }
        else {
            if (($pagadas + $pagoparcial) <= 1) {
                $datos2["NumeEstaClie"] = "2";
            }
            else {
                $datos2["NumeEstaClie"] = "3";
            }
        }
        $cliente = $config->getTabla("clientes");
        $cliente->editar($datos2);

        return $result;
    }
    
    public function listar($strFiltro = "", $conBotones = true, $btnList = [], $order = '')
    {
        global $config, $crlf;

        $saldo = $config->buscarDato("SELECT SUM(ImpoCuot + ImpoOtro) FROM cuotas WHERE NumeEstaCuot <> 2 AND NumeClie = ".$_REQUEST[$this->masterFieldId]);
        
        $saldo = number_format($saldo, 2, ".", "");

        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
        parent::listar($strFiltro, $conBotones, $btnList, $order);
        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
    }
}
