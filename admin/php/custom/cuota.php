<?php
namespace VectorForms;

class Cuota extends Tabla
{
    public function customFunc($post) {
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

                $strSQL = "UPDATE cuotas SET NumeEstaCuot = 2, FechPago = SYSDATE(), NumeTipoPago = {$NumeTipoPago}, ImpoCobr = ImpoCuot + ImpoOtro";
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
                
                $strSQL = "UPDATE cuotas SET NumeEstaCuot = 1, FechPago = NULL, NumeTipoPago = NULL, ImpoCobr = NULL, CodiCheq = NULL WHERE CodiIden = ". $CodiIden;

                return $config->ejecutarCMD($strSQL);
                break;

            case "ActualizarImporte":
                $codiIden = $post["dato"];
                $datos = [];
                $datos = $config->buscarDato("SELECT NumeCont, ImpoCuot, DATE_FORMAT(FechVenc2, '%Y-%m-%d') FechVenc2, NumeEstaCuot FROM cuotas c INNER JOIN clientes cl ON c.NumeClie = cl.NumeClie WHERE c.CodiIden = ". $codiIden);
                if ($datos["NumeEstaCuot"] != "2") {
                    $datos["PorcReca"] = $config->buscarDato("SELECT PorcReca FROM contratos WHERE NumeCont = ". $datos["NumeCont"]);

                    $hoy = new \DateTime();
                    $fechVenc = new \DateTime($datos["FechVenc2"]);
                    
                    $datEdit = array("CodiIden" => $codiIden);

                    $cantDias = $hoy->diff($fechVenc)->format("%a");
                    if ($fechVenc < $hoy) {
                        $impoOtro = $datos["ImpoCuot"] * $cantDias * $datos["PorcReca"]/100;
                    } else {
                        $impoOtro = '0';
                    }

                    $datEdit["ImpoOtro"] = $impoOtro;

                    $result = \json_decode($this->editar($datEdit));
                    
                    $codiBarr = $this->CodiBarr($codiIden);
                    $this->editar(array("CodiIden"=>$codiIden, "CodiBarr"=> $codiBarr));

                    return $result->estado;                
                }
                else {
                    return true;
                }
                break;

            case 'TXT':
                $archivo = $_FILES["Archivo"]["tmp_name"];
                $handle = fopen($archivo, "r");
                $salida = [];

                if ($handle) {
                    //Encabezado archivo
                    $encabezado = fgets($handle);

                    if (\substr($encabezado, 0, 1) == "1") {
                        $formaPago = $config->buscarDato("SELECT NumeTipoPago FROM tipospagos WHERE NombTipoPago = '". trim(\substr($encabezado, 9, 25)) ."'");

                        //Lotes
                        while (($line = fgets($handle)) !== false) {
                            switch (\substr($line, 0, 1)) {
                                case "5":
                                    $fechTransfer = \substr($line, 16, 4).'-'.\substr($line, 20, 2).'-'.\substr($line, 22, 2);
                                    $codiIden = trim(\substr($line, 24, 21));
                                    $impoCobr = floatval(\substr($line, 48, 8). '.' .\substr($line, 56, 2));
                                    $terminalId = \substr($line, 58, 6);
                                    $fechPago = \substr($line, 64, 4).'-'.\substr($line, 68, 2).'-'.\substr($line, 70, 2).' '.\substr($line, 72, 2).':'.\substr($line, 74, 2);
                                    $terminalNro = intval(\substr($line, 76, 4));

                                    $this->editar(array(
                                        "CodiIden"=>$codiIden,
                                        "FechTransfer"=>$fechTransfer,
                                        "ImpoCobr"=>$impoCobr,
                                        "TerminalID"=>$terminalId,
                                        "TerminalNro"=>$terminalNro,
                                        "FechPago"=>$fechPago,
                                        "NumeTipoPago"=>$formaPago,
                                        "NumeEstaCuot"=>"2"
                                    ));
                                    break;
                            }
                        }
                    }
                    else {
                        $salida["estado"] = false;
                        $salida["mensaje"] = "Formato de archivo incorrecto!";
                    }

                    $salida["estado"] = true;
                    
                    fclose($handle);
                } else {
                    // error opening the file.
                    $salida["estado"] = false;
                    $salida["mensaje"] = "Error en lectura de archivo o archivo incorrecto!";
                } 

                return $salida;
                break;
        }
    }

    public function insertar($datos) {
        global $config;

        $result = parent::insertar($datos);

        $codiIden = \json_decode($result);
        $codiIden = $codiIden->id;

        $codiBarr = $this->CodiBarr($codiIden);

        $this->editar(array("CodiIden"=>$codiIden, "CodiBarr"=> $codiBarr));
        return $result;
    }

    public function listar($strFiltro = "", $conBotones = true, $btnList = [], $order = '') {
        global $config, $crlf;

        $saldo = $config->buscarDato("SELECT SUM(ImpoCuot + ImpoOtro) FROM cuotas WHERE NumeEstaCuot <> 2 AND NumeClie = ".$_REQUEST[$this->masterFieldId]);
        
        $saldo = number_format($saldo, 2, ".", "");

        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
        parent::listar($strFiltro, $conBotones, $btnList, $order);
        echo $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
    }

    function CodiBarr($codiIden) {
        global $config;
        
        $datos = $config->buscarDato("SELECT NumeClie, FechVenc, FechVenc2, ImpoCuot, ImpoOtro FROM cuotas WHERE CodiIden = ". $codiIden);
        $contrato = $config->buscarDato("SELECT PorcVenc2, PorcReca FROM contratos WHERE NumeCont IN (SELECT NumeCont FROM clientes WHERE NumeClie = {$datos["NumeClie"]})");

        $hoy = new \DateTime();
        $fechVenc2 = new \DateTime($datos["FechVenc2"]);
        
        if ($hoy < $fechVenc2) {
            $datos["ImpoCuot2"] = $datos["ImpoCuot"] + ($datos["ImpoCuot"] * $contrato["PorcVenc2"] / 100);
        }
        else {
            $datos["ImpoCuot"] = $datos["ImpoCuot"] + $datos["ImpoOtro"];
            $datos["ImpoCuot2"] = $datos["ImpoCuot"];

            $datos["FechVenc"] = $hoy->format("Y-m-d");
            $datos["FechVenc2"] = $hoy->format("Y-m-d");
        }

        $CodiBarr = '4207';

        //Importe
        $aux = number_format($datos["ImpoCuot"], 2, "", "");
        $aux = substr('00000000'.$aux, -8);
        $CodiBarr.= $aux;

        //Fecha de vencimiento
        $aux = new \DateTime($datos["FechVenc"]);
        $CodiBarr.= $aux->format('m');
        $CodiBarr.= substr('000'.($aux->format('z')+1), -3);

        //Cliente
        $aux = substr('00000000000000'.$codiIden, -14);
        $CodiBarr.= $aux;

        //Moneda
        $CodiBarr.= '0';

        //Recargo 2do vencimiento
        $aux = $datos["ImpoCuot2"] - $datos["ImpoCuot"];
        $aux = number_format($aux, 2, "", "");
        $aux = substr('000000'.$aux, -6);
        $CodiBarr.= $aux;
        
        //Fecha vencimiento 2
        $aux = new \DateTime($datos["FechVenc"]);
        $aux2 = new \DateTime($datos["FechVenc2"]);
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
