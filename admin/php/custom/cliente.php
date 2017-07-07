<?php 
namespace VectorForms;

class Cliente extends Tabla
{
	public function insertar($datos) {
		global $config;

		$anticipo = $datos["Anticipo"];
		unset($datos["Anticipo"]);
		
		$cantCuot = $datos["CantCuot"];
		unset($datos["CantCuot"]);
		
		$fechVenc = new \DateTime($datos["FechCuot"]);
		unset($datos["FechCuot"]);

		$fechFina = $fechVenc->add(new \DateInterval("P".$cantCuot."M"));
		$fechSali = $config->buscarDato("SELECT FechSali FROM contratos WHERE NumeCont = ". $datos["NumeCont"]);
		$fechSali = new \DateTime($fechSali);

		if ($fechFina > $fechSali) {
			$result["estado"] = "La fecha del último pago es posterior a la fecha de salida.";

			return json_encode($result);
		}
		
		$result = parent::insertar($datos);
		
		$aux = json_decode($result);
		$datos2 = [];
		
		if ($aux->estado === true) {
			$cuotas = $config->getTabla("cuotas");

			//Creo el anticipo
			$datos2 = array(
				"CodiIden"=>"",
				"NumeCuot"=>"0",
				"NumeClie"=>$aux->id,
				"NumeTipoCuot"=>"1",
				"FechVenc"=>$fechVenc->format("Y-m-d"),
				"ImpoCuot"=>$anticipo,
				"ImpoOtro"=>"0",
				"NumeEstaCuot"=>"1"
			);

			$cuotas->insertar($datos2);
			

			$impoCont = $config->buscarDato("SELECT ImpoCont FROM contratos WHERE NumeCont = ". $datos["NumeCont"]);
			
			$saldo = number_format($impoCont - $anticipo, 2, ".", "");
			$impoCuot = number_format($saldo / $cantCuot, 2, ".", "");

			$datos2["ImpoOtro"] = "0";
			$datos2["NumeEstaCuot"] = "1";
			
			for ($I = 1; $I <= $cantCuot; $I++) {
				$datos2 = array(
					"CodiIden"=>"",
					"NumeCuot"=>$I,
					"NumeClie"=>$aux->id,
					"NumeTipoCuot"=>"2",
					"FechVenc"=>$fechVenc->format("Y-m-d"),
					"ImpoCuot"=>$impoCuot,
					"ImpoOtro"=>"0",
					"NumeEstaCuot"=>"1"
				);
				
				$cuotas->insertar($datos2);
				
				$fechVenc->add(new \DateInterval("P1M"));
			}
		}
		return $result;
	}
	
	public function editar($datos) {
		unset($datos["Anticipo"]);
		unset($datos["CantCuot"]);
		unset($datos["FechCuot"]);
		
		return parent::editar($datos);
	}
	
	public function borrar($datos, $filtro = '') {
		global $config;

		$result2 = $config->ejecutarCMD("DELETE FROM cuotas WHERE NumeClie = ". $datos["NumeClie"]);
		if ($result2 === true) {
			return parent::borrar($datos, $filtro);
		} else {
			$result["estado"] = $result2;

            return json_encode($result);
		}
	}

	public function customFunc($post) {
		global $config;
		
		switch ($post["field"]) {
			case "CalcCuotas":
				$numeCont = $post["dato"]["NumeCont"];
				$fechCuot = $post["dato"]["FechCuot"];

				$datos = $config->buscarDato("SELECT TIMESTAMPDIFF(MONTH, '{$fechCuot}', FechSali) CantCuot, ImpoCont FROM contratos WHERE NumeCont = ". $numeCont);

				$saldo = $datos["ImpoCont"] - floatval($post["dato"]["Anticipo"]);

				$cantCuot = ($datos["CantCuot"] != ""? $datos["CantCuot"]: "1");

				$strSalida = "";
				if ($cantCuot > 3) {
					$strSalida.= '<option value="3">3 CUOTAS DE $'.number_format($saldo/3, 2).'</option>';
				}
				if ($cantCuot > 6) {
					$strSalida.= '<option value="6">6 CUOTAS DE $'.number_format($saldo/6, 2).'</option>';
				}
				if ($cantCuot > 10) {
					$strSalida.= '<option value="10">10 CUOTAS DE $'.number_format($saldo/10, 2).'</option>';
				}

				$strSalida.= '<option value="'.$cantCuot.'">'.$cantCuot.' CUOTAS DE $'.number_format($saldo/$cantCuot, 2).'</option>';

				return $strSalida;
				break;

			case "Cuotas":
				return $config->buscarDato("SELECT COUNT(*) FROM cuotas WHERE NumeTipoCuot = 2 AND NumeClie = ". $post["dato"]);
				
				break;
		}
	}
}
?>