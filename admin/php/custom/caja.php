<?php
namespace VectorForms;

class Caja extends Tabla
{
    public function customFunc($post)
    {
        global $config;
        
        switch ($post['field']) {
            case "NumeEsta":
                return $config->ejecutarCMD("UPDATE caja SET NumeEsta = NOT NumeEsta WHERE NumeCaja = ". $post["dato"]["NumeCaja"]);
                break;
        }
    }

    public function listar($strFiltro = "", $conBotones = true, $btnList = [], $order = '')
    {
        global $config, $crlf;

        $filtro = "";

        $strSQL = "SELECT c.NumeCaja, c.FechCaja, c.NombCaja, c.NumeTipoCaja, c.ImpoCaja, c.NumeEsta, c.NumeUser,";
        $strSQL.= $crlf." u.NombPers, tc.NombTipoCaja, tc.NumeTipoOper, e.NombEsta";
        $strSQL.= $crlf." FROM (SELECT * FROM caja";
        if ($strFiltro == "") {
            $strSQL.= $crlf." WHERE FechCaja > DATE_ADD(SYSDATE(), INTERVAL -30 DAY)";
        } else {
            if (isset($strFiltro["FechCaja"])) {
                if ($strFiltro["FechCaja"] != 'TODOS') {
                    if ($filtro != "") {
                        $filtro.= $crlf." AND ";
                    }
                    $filtro.= "DATE_FORMAT(FechCaja, '%Y-%m') = '{$strFiltro["FechCaja"]}'";
                }
            }

            if (isset($strFiltro["NombCaja"])) {
                if ($filtro != "") {
                    $filtro.= $crlf." AND ";
                }
                $filtro.= "NombCaja LIKE '%{$strFiltro["NombCaja"]}%'";
            }

            if (isset($strFiltro["NumeTipoCaja"])) {
                if ($filtro != "") {
                    $filtro.= $crlf." AND ";
                }
                $filtro.= "NumeTipoCaja = {$strFiltro["NumeTipoCaja"]}";
            }

            if ($filtro != '') {
                $strSQL.= $crlf." WHERE ". $filtro;
            }
        }
        $strSQL.= $crlf." ) c";
        $strSQL.= $crlf." INNER JOIN usuarios u ON c.NumeUser = u.NumeUser";
        $strSQL.= $crlf." INNER JOIN tiposcaja tc ON c.NumeTipoCaja = tc.NumeTipoCaja";
        $strSQL.= $crlf." INNER JOIN estados e ON c.NumeEsta = e.NumeEsta";
        
        $strSQL.= $crlf." ORDER BY c.NumeCaja DESC";

        $tabla = $config->cargarTabla($strSQL);

        $strSalida = '';

        $credito = $config->buscarDato("SELECT SUM(ImpoCaja) FROM caja WHERE NumeEsta = 1 AND NumeTipoCaja IN (SELECT NumeTIpoCaja FROM tiposcaja WHERE NumeTipoOper = 1)");
        $debito = $config->buscarDato("SELECT SUM(ImpoCaja) FROM caja WHERE NumeEsta = 1 AND NumeTipoCaja IN (SELECT NumeTIpoCaja FROM tiposcaja WHERE NumeTipoOper = 2)");
        $saldo = floatval($credito) - floatval($debito);

        if ($saldo >= 0) {
            $strSalida.= $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: $ '.$saldo.'</h4>';
        } else {
            $strSalida.= $crlf.'<h4 id="txtSaldo" class="well well-sm text-right">Saldo: <span class="txtRojo">$ '.$saldo.'</span></h4>';
        }

        if ($tabla) {
            if ($tabla->num_rows > 0) {
                $strSalida.= $crlf.'<table class="table table-striped table-bordered table-hover table-condensed table-responsive">';

                $strSalida.= $crlf.'<tr>';
                $strSalida.= $crlf.'<th>Número</th>';
                $strSalida.= $crlf.'<th>Fecha</th>';
                $strSalida.= $crlf.'<th>Usuario</th>';
                $strSalida.= $crlf.'<th>Descripción</th>';
                $strSalida.= $crlf.'<th>Tipo de operación</th>';
                $strSalida.= $crlf.'<th>Crédito</th>';
                $strSalida.= $crlf.'<th>Débito</th>';
                $strSalida.= $crlf.'<th>Estado</th>';
                $strSalida.= $crlf.'<th></th>';
                $strSalida.= $crlf.'</tr>';

                while ($fila = $tabla->fetch_assoc()) {
                    $col = 0;
                    
                    $strSalida.= $crlf.'<tr class="'.($fila["NumeEsta"] != "1"?'txtTachado':'').'">';
                    
                    $strSalida.= $crlf.'<td id="NumeCaja'. $fila[$this->IDField].'">'.$fila['NumeCaja'].'</td>';
                    $strSalida.= $crlf.'<td id="FechCaja'. $fila[$this->IDField].'">'.$fila['FechCaja'].'</td>';
                    
                    $strSalida.= $crlf.'<td class="ucase">'.$fila["NombPers"];
                    $strSalida.= $crlf.'<input type="hidden" id="NumeUser'. $fila[$this->IDField].'" value="'.$fila["NumeUser"].'" />';
                    $strSalida.= $crlf.'</td>';
                    
                    $strSalida.= $crlf.'<td id="NombCaja'. $fila[$this->IDField].'" class="ucase">'.$fila['NombCaja'].'</td>';

                    $strSalida.= $crlf.'<td class="ucase">'.$fila["NombTipoCaja"];
                    $strSalida.= $crlf.'<input type="hidden" id="NumeTipoCaja'. $fila[$this->IDField].'" value="'.$fila["NumeTipoCaja"].'" />';
                    $strSalida.= $crlf.'</td>';

                    if ($fila["NumeTipoOper"] == "1") {
                        $strSalida.= $crlf.'<td class="txtBold">$ '.$fila['ImpoCaja'].'<span class="hide" id="ImpoCaja'. $fila[$this->IDField].'">'.$fila['ImpoCaja'].'</span></td>';
                        $strSalida.= $crlf.'<td></td>';
                    } else {
                        $strSalida.= $crlf.'<td></td>';
                        $strSalida.= $crlf.'<td class="txtBold txtRojo">$ '.$fila['ImpoCaja'].'<span class="hide" id="ImpoCaja'. $fila[$this->IDField].'">'.$fila['ImpoCaja'].'</span></td>';
                    }

                    $strSalida.= $crlf.'<td id="NombEsta'.$fila[$this->IDField].'" class="ucase">'.$fila["NombEsta"];
                    $strSalida.= $crlf.'<input type="hidden" id="NumeEsta'.$fila[$this->IDField].'" value="'.$fila["NumeEsta"].'" />';
                    $strSalida.= $crlf.'</td>';

                    //Botones
                    if ($fila["NumeEsta"] == "1") {
                        $strSalida.= $crlf.'<td class="text-center"><button class="btn btn-sm btn-danger" onclick="cambiarEstado(\''.$fila[$this->IDField].'\')">INACTIVAR</button></td>';
                    } else {
                        $strSalida.= $crlf.'<td class="text-center"><button class="btn btn-sm btn-success" onclick="cambiarEstado(\''.$fila[$this->IDField].'\')">ACTIVAR</button></td>';
                    }
                    
                    $strSalida.= $crlf.'</tr>';
                }

                $strSalida.= $crlf.'</table>';
            } else {
                $strSalida.= "<h3>No hay datos para mostrar</h3>";
            }
            $tabla->free();
        } else {
            $strSalida.= "<h3>No hay datos para mostrar</h3>";
        }
            
        echo $strSalida;
    }

    public function insertar($datos)
    {
        global $config;

        $datos["FechCaja"] = $config->buscarDato("SELECT SYSDATE()");
        $datos["NumeUser"] = $_SESSION["NumeUser"];

        return parent::insertar($datos);
    }

    protected function createField($field, $prefix = '')
    {
        global $crlf, $config;

        $strSalida = '';

        if ($prefix == '') {
            $fname = $field['name'];
        } else {
            $fname = $prefix  .'-'. $field['name'];
        }

        if ($fname == "search-FechCaja") {
            $strSalida.= $crlf.'<div class="form-group form-group-sm '.$field['cssGroup'].'">';
            $strSalida.= $crlf.'<label for="'.$fname.'" class="control-label col-md-2 col-lg-2">'.$field['label'].':</label>';

            if ($field['size'] <= 20) {
                $strSalida.= $crlf.'<div class="col-md-2 col-lg-2">';
            } elseif ($field['size'] <= 40) {
                $strSalida.= $crlf.'<div class="col-md-3 col-lg-3">';
            } elseif ($field['size'] <= 80) {
                $strSalida.= $crlf.'<div class="col-md-4 col-lg-4">';
            } elseif ($field['size'] <= 160) {
                $strSalida.= $crlf.'<div class="col-md-5 col-lg-5">';
            } elseif ($field['size'] <= 200) {
                $strSalida.= $crlf.'<div class="col-md-6 col-lg-6">';
            } else {
                $strSalida.= $crlf.'<div class="col-md-10 col-lg-10">';
            }

            $strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$fname.'">';
            $strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'"size="16" value="'.$field["value"].'" readonly />';
            $strSalida.= $crlf.'<span class="input-group-addon add-on clickable" title="Limpiar"><i class="fa fa-times fa-fw"></i></span>';
            $strSalida.= $crlf.'<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
            $strSalida.= $crlf.'</div>';
            $strSalida.= $crlf.'<script type="text/javascript">';
            $strSalida.= $crlf.'$(".inp'.$fname.'").datetimepicker({';
            $strSalida.= $crlf.'	language: "es",';
            $strSalida.= $crlf.'	format: "yyyy-mm",';
            $strSalida.= $crlf.'	minView: 3,';
            $strSalida.= $crlf.'	startView: 3,';
            $strSalida.= $crlf.'	autoclose: true,';
            $strSalida.= $crlf.'	todayBtn: true,';
            $strSalida.= $crlf.'	todayHighlight: false,';
            $strSalida.= $crlf.'	pickerPosition: "bottom-left"';

            if ($field['mirrorField'] != '') {
                $strSalida.= $crlf.'	linkField: "'. $field['mirrorField'] .'",';
                $strSalida.= $crlf.'	linkFormat: "'. $field['mirrorFormat'] .'",';
            }

            if ($field['dtpOnRender'] != '') {
                $strSalida.= $crlf.'	onRender: function(date) {';
                $strSalida.= $crlf.'			return '. $field['dtpOnRender'];
                $strSalida.= $crlf.'		},';
            }

            if ($field['onChange'] == '') {
                $strSalida.= $crlf.'	});';
            } else {
                $strSalida.= $crlf.'	}).on("changeDate", function(ev){';
                $strSalida.= $crlf.'		'. $field['onChange'];
                $strSalida.= $crlf.'	});';
            }

            $strSalida.= $crlf.'</script>';
            $strSalida.= $crlf.'</div>'; //col-md
            $strSalida.= $crlf.'</div>'; //form-group
        } else {
            $strSalida.= parent::createField($field, $prefix);
        }

        return $strSalida;
    }
}
