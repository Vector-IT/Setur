<?php
namespace VectorForms;

class Cuota extends Tabla
{
    public function customFunc($post)
    {
        global $config;
        
        switch ($post['field']) {
            case "NombEstaCuot":
                $hoy = new \DateTime();
                $datos = $config->buscarDato("SELECT NumeEstaCuot, FechVenc2 FROM cuotas WHERE CodiIden = ". $post["dato"]);
                $fechVenc2 = new \DateTime($datos["FechVenc2"]);

                if ($datos["NumeEstaCuot"] != "2") {
                    if ($hoy > $fechVenc2) {
                        $this->editar(array(
                            "CodiIden" => $post["dato"],
                            "NumeEstaCuot" => "3"
                        ));
                        
                    }
                }
                $result = $config->buscarDato("SELECT NombEstaCuot FROM estadoscuotas WHERE NumeEstaCuot IN (SELECT NumeEstaCuot FROM cuotas WHERE CodiIden = {$post["dato"]})");
                if ($result == 'VENCIDA') {
                    $result = '<span class="txtRed">VENCIDA</span>';
                }

                return $result;
                break;

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

            case "ActualizarImporte":
                $CodiIden = $post["dato"];
                $datos = [];
                $datos = $config->buscarDato("SELECT NumeCont, ImpoCuot, DATE_FORMAT(FechVenc2, '%Y-%m-%d') FechVenc2, NumeEstaCuot FROM cuotas c INNER JOIN clientes cl ON c.NumeClie = cl.NumeClie WHERE c.CodiIden = ". $CodiIden);
                if ($datos["NumeEstaCuot"] != "2") {
                    $datos["PorcReca"] = $config->buscarDato("SELECT PorcReca FROM contratos WHERE NumeCont = ". $datos["NumeCont"]);

                    $hoy = new \DateTime();
                    $fechVenc = new \DateTime($datos["FechVenc2"]);
                    
                    $datEdit = array("CodiIden" => $CodiIden);

                    $cantDias = $hoy->diff($fechVenc)->format("%a");
                    if ($fechVenc < $hoy) {
                        $impoOtro = $datos["ImpoCuot"] * $cantDias * $datos["PorcReca"]/100;
                    } else {
                        $impoOtro = '0';
                    }

                    $datEdit["ImpoOtro"] = $impoOtro;

                    $result = \json_decode($this->editar($datEdit));
                    return $result->estado;                
                }
                else {
                    return true;
                }
                break;
        }
    }

    public function editar($datos)
    {
        global $config;

        $datos2 = $config->buscarDato("SELECT NumeClie, FechVenc, FechVenc2, ImpoCuot, ImpoOtro FROM cuotas WHERE CodiIden = ". $datos["CodiIden"]);
        $contrato = $config->buscarDato("SELECT PorcVenc2, PorcReca FROM contratos WHERE NumeCont IN (SELECT NumeCont FROM clientes WHERE NumeClie = {$datos2["NumeClie"]})");

        $hoy = new \DateTime();
        $fechVenc2 = new \DateTime($datos2["FechVenc2"]);
        
        if ($hoy < $fechVenc2) {
            $datos2["ImpoCuot2"] = $datos2["ImpoCuot"] + ($datos2["ImpoCuot"] * $contrato["PorcVenc2"] / 100);
        }
        else {
            $datos2["ImpoCuot"] = $datos2["ImpoCuot"] + $datos2["ImpoOtro"];
            $datos2["ImpoCuot2"] = $datos2["ImpoCuot"];

            $datos2["FechVenc"] = $hoy->format("Y-m-d");
            $datos2["FechVenc2"] = $hoy->format("Y-m-d");
        }

        $CodiBarr = $this->CodiBarr($datos2["ImpoCuot"], $datos2["FechVenc"], $datos2["ImpoCuot2"], $datos2["FechVenc2"], $datos["CodiIden"]);

        $datos["CodiBarr"] = $CodiBarr;

        return parent::editar($datos);
    }
    
    public function insertar($datos)
    {
        global $config;

        $result = parent::insertar($datos);

        $codiIden = \json_decode($result);
        $codiIden = $codiIden->id;

        $this->editar(array("CodiIden"=>$codiIden));
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

    function CodiBarr($ImpoVenc1, $FechVenc1, $ImpoVenc2, $FechVenc2, $CodiIden) {
        $CodiBarr = '4207';

        //Importe
        $aux = number_format($ImpoVenc1, 2, "", "");
        $aux = substr('00000000'.$aux, -8);
        $CodiBarr.= $aux;

        //Fecha de vencimiento
        $aux = new \DateTime($FechVenc1);
        $CodiBarr.= $aux->format('m');
        $CodiBarr.= substr('000'.$aux->format('z'), -3);

        //Cliente
        $aux = substr('00000000000000'.$CodiIden, -14);
        $CodiBarr.= $aux;

        //Moneda
        $CodiBarr.= '0';

        //Recargo 2do vencimiento
        $aux = $ImpoVenc2 - $ImpoVenc1;
        $aux = number_format($aux, 2, "", "");
        $aux = substr('000000'.$aux, -6);
        $CodiBarr.= $aux;
        
        //Fecha vencimiento 2
        $aux = new \DateTime($FechVenc1);
        $aux2 = new \DateTime($FechVenc2);
        $aux = $aux2->diff($aux)->format("%a");
        $aux = substr('00'.$aux, -2);
        $CodiBarr.= $aux;

        //Digito verificador
        $serie = array(1,3,5,7,9);
        //Itero 2 veces porque son 2 digitos
        for ($k = 0; $k < 2; $k++) {
            $j = 0;
            $sum = 0;

            for ($I = 0; $I < strlen($CodiBarr); $I++) {
                $sum+= substr($CodiBarr, $I, 1) * $serie[$j];

                if ($j < 4) {
                    $j++;
                }
                else {
                    $j = 1;
                }
            }

            $aux = intval($sum / 2);
            $aux = $aux % 10;
            $CodiBarr.= $aux;
        }

        return $CodiBarr;
    }
}
