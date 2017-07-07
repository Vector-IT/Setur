<?php 
namespace VectorForms;

class Contrato extends Tabla
{
    public function customFunc($post) {
		global $config;
		
		switch ($post["field"]) {
			case "CantPasa":
				$numeCont = $post["dato"];

				$cantTota = $config->buscarDato("SELECT COUNT(*) FROM clientes WHERE NumeCont = ". $numeCont);
				$cantActi = $config->buscarDato("SELECT COUNT(*) FROM clientes WHERE NumeEsta = 1 AND NumeCont = ". $numeCont);

				$strSalida = $cantTota. " / " .$cantActi;

				return $strSalida;
				break;
		}
	}
}