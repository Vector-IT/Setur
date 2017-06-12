<?php
namespace VectorForms;

class Cuota extends Tabla
{
    public function customFunc($post)
    {
        global $config;
        
        switch ($post['field']) {
            case "ImpoPago":
                return $config->buscarDato("SELECT COALESCE(SUM(ImpoPago), 0) FROM cuotaspagos WHERE NumeEsta = 1 AND CodiIden = ". $post["dato"]);
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

        $cuotas = $config->buscarDato("SELECT SUM(ImpoCuot + ImpoOtro) FROM cuotas WHERE NumeClie = ".$_REQUEST[$this->masterFieldId]);
        $pagos = $config->buscardato("SELECT SUM(ImpoPago) FROM cuotaspagos WHERE NumeEsta = 1 AND CodiIden IN (SELECT CodiIden FROM cuotas WHERE NumeClie = ".$_REQUEST[$this->masterFieldId].")");
        $saldo = number_format($cuotas - $pagos, 2, ".", "");

        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
        parent::listar($strFiltro, $conBotones, $btnList, $order);
        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
    }
}
