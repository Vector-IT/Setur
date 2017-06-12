<?php 
namespace VectorForms;

class Cliente extends Tabla
{
	public function insertar($datos) {
		global $config;
		
		$cantCuot = $datos["CantCuot"];
		unset($datos["CantCuot"]);
		
		$fechVenc = new \DateTime($datos["FechCuot"]);
		unset($datos["FechCuot"]);
		
		$result = parent::insertar($datos);
		
		$aux = json_decode($result);
		$datos2 = [];
		
		if ($aux->estado === true) {
			$cuotas = $config->getTabla("cuotas");
			
			$datos2["CodiIden"] = "";
			$datos2["NumeClie"] = $aux->id;
			$datos2["FechVenc"] = $fechVenc->format("Y-m-d"); 
			
			$impoCont = $config->buscarDato("SELECT ImpoCont FROM contratos WHERE NumeCont = ". $datos["NumeCont"]);
			
			$datos2["ImpoCuot"] = number_format($impoCont / $cantCuot, 2, ".", "");
			$datos2["ImpoOtro"] = "0";
			$datos2["NumeEstaCuot"] = "1";
			
			for ($I = 1; $I <= $cantCuot; $I++) {
				$datos2["NumeCuot"] = $I;
				
				$cuotas->insertar($datos2);
				
				$fechVenc->add(new \DateInterval("P1M"));
				$datos2["FechVenc"] = $fechVenc->format("Y-m-d");
			}
		}
		return $result;
	}
	
	public function editar($datos) {
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
			case "Cuotas":
				return $config->buscarDato("SELECT COUNT(*) FROM cuotas WHERE NumeClie = ". $post["dato"]);
				
				break;
		}
	}
}
?>