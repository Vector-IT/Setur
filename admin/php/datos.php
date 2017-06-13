<?php
	namespace VectorForms;

	ini_set("log_errors", 1);
	ini_set("error_log", "php-error.log");
	
	require_once 'datosdb.php';
	require_once 'vectorForms.php';

	require_once 'custom/cliente.php';
	require_once 'custom/cuota.php';
	require_once 'custom/cuotapago.php';
	require_once 'custom/cheque.php';

	//Datos de configuracion iniciales
	$config = new VectorForms($dbhost, $dbschema, $dbuser, $dbpass, $raiz, "Se-tur", "img/logo.png", false);
	$config->tbLogin = 'usuarios';
	$config->theme = 'dark';

	/**
	 * Items de menu adicionales
	 */
	$config->menuItems = [
			new MenuItem("Configuraciones", '', '', 'fa-cogs', 1, true, false),
			new MenuItem("Reportes", 'reportes.php', '', 'fa-slideshare', '', false, false),
			new MenuItem("Salir del Sistema", 'logout.php', '', 'fa-sign-out', '', false, false)
	];

	/**
	 * TABLAS
	 */

	/**
	 * USUARIOS
	 */
	$tabla = new Tabla("usuarios", "usuarios", "Usuarios", "el Usuario", true, "objeto/usuarios", "fa-users");
	$tabla->labelField = "NombPers";
	$tabla->numeCarg = 1;
	$tabla->isSubItem = true;

	$tabla->addField("NumeUser", "number", 0, "Número", false, true, true);
	$tabla->addField("NombPers", "text", 200, "Nombre Completo");
	$tabla->addField("NombUser", "text", 0, "Usuario");
	$tabla->fields['NombUser']['classControl'] = "ucase";

	$tabla->addField("NombPass", "password", 0, "Contraseña", true, false, false, false);
	$tabla->fields["NombPass"]['isMD5'] = true;
	$tabla->addField("NumeCarg", "select", 0, "Cargo", true, false, false, true, '', '', 'cargos', 'NumeCarg', 'NombCarg', '', 'NombCarg');
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["usuarios"] = $tabla;

	/**
	 * BANCOS
	 */
	$tabla = new Tabla("bancos", "bancos", "Bancos", "el Banco", true, "objeto/bancos/", "fa-bank");
	$tabla->labelField = "NombBanc";
	$tabla->isSubItem = true;

	$tabla->addField("NumeBanc", "number", 0, "Número", false, true, true);
	$tabla->addField("NombBanc", "text", 200, "Nombre");
	$tabla->fields["NombBanc"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["bancos"] = $tabla;

	/**
	 * VENDEDORES
	 */
	$tabla = new Tabla("vendedores", "vendedores", "Vendedores", "el Vendedor", true, "objeto/vendedores/", "fa-male");
	$tabla->labelField = "NombVend";
	$tabla->isSubItem = true;

	$tabla->addField("NumeVend", "number", 0, "Número", false, true, true);
	$tabla->addField("NombVend", "text", 200, "Nombre");
	$tabla->fields["NombVend"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["vendedores"] = $tabla;

	/**
	 * TIPOS DE PAGOS
	 */
	$tabla = new Tabla("tipospagos", "tipospagos", "Formas de Pago", "la Forma de Pago", true, "objeto/tipospagos/", "fa-credit-card");
	$tabla->labelField = "NombTipoPago";
	$tabla->isSubItem = true;
	$tabla->allowDelete = false;
	$tabla->allowEdit = false;
	$tabla->allowNew = false;

	$tabla->addField("NumeTipoPago", "number", 0, "Número", false, true, true);
	$tabla->addField("NombTipoPago", "text", 100, "Nombre");
	$tabla->fields["NombTipoPago"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["tipospagos"] = $tabla;

	/**
	 * PROVINCIAS
	 */
	$tabla = new Tabla("provincias", "provincias", "Provincias", "la provincia", true, "objeto/provincias/", "fa-linode");
	$tabla->labelField = "NombProv";
	$tabla->isSubItem = true;
	$tabla->allowDelete = false;
	$tabla->allowEdit = false;
	$tabla->allowNew = false;

	$tabla->addFieldId("NumeProv", "Número");
	$tabla->addField("NombProv", "text", 200, "Nombre");

	$config->tablas["provincias"] = $tabla;

	/**
	 * DESTINOS
	 */
	$tabla = new Tabla("destinos", "destinos", "Destinos", "el destino", true, "objeto/destinos/", "fa-plane", "NombDest");
	$tabla->isSubItem = true;
	$tabla->labelField = "NombDest";

	$tabla->addFieldId("NumeDest", "Número", true, true);
	$tabla->addField("NombDest", "text", 80, "Nombre");
	$tabla->fields["NombDest"]["cssControl"] = "ucase";
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["destinos"] = $tabla;

	/**
	 * RANGOS
	 */
	$tabla = new Tabla("rangos", "rangos", "Rangos Etarios", "el rango", true, "objeto/rangos/", "fa-child", "NombRang");
	$tabla->isSubItem = true;
	$tabla->labelField = "NombRang";

	$tabla->addFieldId("NumeRang", "Número", true, true);
	$tabla->addField("NombRang", "text", 80, "Nombre");
	$tabla->fields["NombRang"]["cssControl"] = "ucase";
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["rangos"] = $tabla;

	/**
	 * TEMPORADAS
	 */
	$tabla = new Tabla("temporadas", "temporadas", "Temporadas", "la temporada", true, "objeto/temporadas/", "fa-map-o", "NombTemp");
	$tabla->isSubItem = true;
	$tabla->labelField = "NombTemp";

	$tabla->addFieldId("NumeTemp", "Número", true, true);
	$tabla->addField("NombTemp", "text", 80, "Nombre");
	$tabla->fields["NombTemp"]["cssControl"] = "ucase";
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["temporadas"] = $tabla;

	/**
	* COLEGIOS
	*/
	$tabla = new Tabla("colegios", "colegios", "Colegios", "el colegio", true, "objeto/colegios/", "fa-graduation-cap", "NombCole");
	$tabla->isSubItem = true;
	$tabla->labelField = "NombCole";

	$tabla->addFieldId("NumeCole", "Número", true, true);
	$tabla->addField("NombCole", "text", 80, "Nombre");
	$tabla->fields["NombCole"]["cssControl"] = "ucase";
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["colegios"] = $tabla;

	/**
	 * CONTRATOS
	 */
	$tabla = new Tabla("contratos", "contratos", "Contratos", "el contrato", true, "objeto/contratos/", "fa-handshake-o", "FechSali DESC");
	$tabla->labelField = "NombCont";
	
	$tabla->btnList = [
			array(
					"id"=>"btnVerPasa",
					"titulo"=> 'Pasajeros',
					"onclick"=> "verPasajeros",
					"class"=> "btn-default"),
	];
	
	$tabla->jsFiles = ["admin/js/custom/contratos.js"];
	
	$tabla->addFieldId("NumeCont", "Número", true, true);
	$tabla->addField("NumeCole", "select", 80, "Colegio", true, false, false, true, '', '', 'colegios', 'NumeCole', 'NombCole', 'NumeEsta = 1', 'NombCole');
	$tabla->addField("NombCont", "text", 80, "Nombre");
	$tabla->addField("NumeRang", "select", 80, "Rango etario", true, false, false, true, '', '', 'rangos', 'NumeRang', 'NombRang', 'NumeEsta = 1', 'NombRang');
	$tabla->addField("NumeDest", "select", 80, "Destino", true, false, false, true, '', '', 'destinos', 'NumeDest', 'NombDest', 'NumeEsta = 1', 'NombDest');
	$tabla->addField("NumeTemp", "select", 80, "Temporada", true, false, false, true, '', '', 'temporadas', 'NumeTemp', 'NombTemp', 'NumeEsta = 1', 'NombTemp');
	$tabla->addField("FechSali", "date", 0, "Fecha de salida");
	$tabla->addField("CantDias", "number", 0, "Cantidad de días");
	$tabla->addField("ImpoCont", "number", 0, "Precio");
	$tabla->addField("ObseCont", "textarea", 80, "Observaciones", false);
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["contratos"] = $tabla;

	/**
	 * CLIENTES
	 */
	$tabla = new Cliente("pasajeros", "clientes", "Pasajeros", "el pasajero", false, "objeto/pasajeros/", "fa-id-card-o");
	$tabla->labelField = "NombClie";
	$tabla->masterTable = "contratos";
	$tabla->masterFieldId = "NumeCont";
	$tabla->masterFieldName = "NombCont";
	
	
	$tabla->searchFields = array("NombClie");
	
	$tabla->btnList = [
			array(
					"id"=>"btnVerCuotas",
					"titulo"=> 'Ver Cuotas',
					"onclick"=> "verCuotas",
					"class"=> "btn-default"),
	];
	
	$tabla->jsFiles = ['admin/js/custom/clientes.js'];
	$tabla->jsOnNew = "toggleCuotas(true);";	
	$tabla->jsOnEdit = "toggleCuotas(false);";
	
	$tabla->addFieldId("NumeClie", "number", true, true);
	
	$tabla->addField("NumeCont", "number", 0, "Contrato");
	$tabla->fields["NumeCont"]["isHiddenInForm"] = true;
	$tabla->fields["NumeCont"]["isHiddenInList"] = true;
	
	$tabla->addField("NombClie", "text", 200, "Nombre");
	
	$tabla->addField("NumeTele", "text", 100, "Teléfono", false);
	$tabla->fields["NumeTele"]["cssGroup"] = "form-group2";
	
	$tabla->addField("NumeCelu", "text", 100, "Celular", false);
	$tabla->fields["NumeCelu"]["cssGroup"] = "form-group2";
	
	$tabla->addField("MailClie", "email", 200, "E-mail", false);
	
	$tabla->addField("DireClie", "text", 200, "Dirección");
	$tabla->fields["DireClie"]["cssGroup"] = "form-group2";
	$tabla->fields["DireClie"]["isHiddenInList"] = true;
	
	$tabla->addField("NombBarr", "text", 200, "Barrio", false);
	$tabla->fields["NombBarr"]["cssGroup"] = "form-group2";
	$tabla->fields["NombBarr"]["isHiddenInList"] = true;
	
	$tabla->addField("NombLoca", "text", 200, "Localidad");
	$tabla->fields["NombLoca"]["cssGroup"] = "form-group2";
	
	$tabla->addField("NumeProv", "select", 200, "Provincia", true, false, false, true, '', '', 'provincias', 'NumeProv', 'NombProv', '', 'NombProv');
	$tabla->fields["NumeProv"]["cssGroup"] = "form-group2";
	$tabla->fields["NumeProv"]["isHiddenInList"] = true;
	
	$tabla->addField("CodiPost", "text", 0, "Código postal", false);
	$tabla->fields["CodiPost"]["isHiddenInList"] = true;
	
	$tabla->addField("NumeVend", "select", 80, "Vendedor", true, false, false, true, '', '', 'vendedores', 'NumeVend', 'NombVend', '', 'NombVend');
	$tabla->fields["NumeVend"]["isHiddenInList"] = true;
	
	$tabla->addField("Anticipo", "number", 0, "Anticipo");
	$tabla->fields["Anticipo"]["showOnList"] = false;

	$tabla->addField("CantCuot", "number", 0, "Cantidad de Cuotas");
	$tabla->fields["CantCuot"]["showOnList"] = false;
	$tabla->fields["CantCuot"]["cssGroup"] = "form-group2";
	
	$tabla->addField("FechCuot", "date", 0, "Fecha de comienzo");
	$tabla->fields["FechCuot"]["showOnList"] = false;
	$tabla->fields["FechCuot"]["cssGroup"] = "form-group2";
	
	$tabla->addField("Cuotas", "calcfield", 0, "Cantidad de Cuotas");
	$tabla->fields["Cuotas"]["txtAlign"] = "right";
	$tabla->fields["Cuotas"]["showOnForm"] = false;
	
	$tabla->addField("ObseClie", "textarea", 201, "Observaciones", false);
	$tabla->fields["ObseClie"]["isHiddenInList"] = true;
	
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	
	$config->tablas["pasajeros"] = $tabla;
	
	/**
	 * CUOTAS
	 */
	$tabla = new Cuota("cuotas", "cuotas", "Cuotas", "la Cuota", false, "", "fa-money");
	$tabla->labelField = "NumeCuot";
	$tabla->masterTable = "clientes";
	$tabla->masterFieldId = "NumeClie";
	$tabla->masterFieldName = "NombClie";

	$tabla->btnList = [
		array(
			"id"=>"btnPagos",
			"titulo"=>"Ver Pagos",
			"class"=>"btn-primary",
			"onclick"=>"verPagos"
		)
	];

	$tabla->jsFiles = ["admin/js/custom/cuotas.js"];

	$tabla->allowNew = false;
	$tabla->allowDelete = false;

	$tabla->addFieldId("CodiIden", "Código", true, true);
	$tabla->addField("NumeCuot", "number", 0, "Número", true, true);
	$tabla->addField("FechCuot", "date", 0, "Fecha de creación");
	$tabla->fields["FechCuot"]["showOnForm"] = false;

	$tabla->addField("NumeTipoCuot", "select", 0, "Tipo", true, false, false, true, '', '', 'tiposcuotas', 'NumeTipoCuot', 'NombTipoCuot');
	$tabla->fields["NumeTipoCuot"]["showOnForm"] = false;

	$tabla->addField("NumeClie", "select", 0, "Cliente", true, false, false, true, '', '', 'clientes', 'NumeClie', 'NombClie');
	$tabla->fields["NumeClie"]["showOnForm"] = false;
	$tabla->fields["NumeClie"]["showOnList"] = false;

	$tabla->addField("FechVenc", "date", 0, "Fecha de vencimiento");
	$tabla->fields["FechVenc"]["showOnForm"] = false;

	$tabla->addField("ImpoCuot", "number", 0, "Importe de cuota pura", true, true);
	$tabla->fields["ImpoCuot"]["step"] = "0.01";
	$tabla->fields["ImpoCuot"]["txtAlign"] = "right";

	$tabla->addField("ImpoOtro", "number", 0, "Interés", true, true);
	$tabla->fields["ImpoOtro"]["step"] = "0.01";
	$tabla->fields["ImpoOtro"]["txtAlign"] = "right";

	$tabla->addField("ImpoPago", "calcfield", 0, "Importe Pagado");
	$tabla->fields["ImpoPago"]["showOnForm"] = false;
	$tabla->fields["ImpoPago"]["txtAlign"] = "right";

	$tabla->addField("ObseCuot", "textarea", 200, "Observaciones", false);
	$tabla->fields["ObseCuot"]["isHiddenInList"] = true;

	$tabla->addField("NumeEstaCuot", "select", 0, "Estado", true, true, false, true, '1', '', 'estadoscuotas', 'NumeEstaCuot', 'NombEstaCuot', '', 'NombEstaCuot');

	$config->tablas["cuotas"] = $tabla;

	/**
	 * CUOTASPAGOS
	 */
	$tabla = new CuotaPago("cuotaspagos", "cuotaspagos", "Pagos de la Cuota", "el pago", false, "", "fa-money");
	$tabla->masterTable = "cuotas";
	$tabla->masterFieldId = "CodiIden";
	$tabla->masterFieldName = "NumeCuot";
	$tabla->allowEdit = false;
	$tabla->allowDelete = false;

	$tabla->btnForm = [
		array(
			"titulo"=> '<i class="fa fa-credit-card fa-fw" aria-hidden="true"></i> Cheques',
			"class"=> 'btn-primary',
			"onclick"=> "abrirCheques();"
		)
	];

	$tabla->btnList = [
		array(
			"id"=>'btnCambiarEstado',
			"titulo"=>"Cambiar Estado",
			"class"=>"btn-danger",
			"onclick"=>"cambiarEstado"
		)
	];

	$tabla->modalList = ["php/modals/cheques.php"];

	$tabla->jsFiles = [
		"admin/js/custom/cuotaspagos.js",
		"admin/js/custom/jquery.fancybox.min.js"
	];

	$tabla->jsOnLoad = "afterLoad();";
	$tabla->jsOnList = "afterList();";

	$tabla->cssFiles = [
		"admin/css/custom/jquery.fancybox.min.css"
	];

	$tabla->addFieldId("NumePago", "NumePago", false, false);
	$tabla->fields["NumePago"]["isHiddenInForm"] = true;
	$tabla->fields["NumePago"]["isHiddenInList"] = true;

	$tabla->addField("CodiIden", "number");
	$tabla->fields["CodiIden"]["isHiddenInForm"] = true;
	$tabla->fields["CodiIden"]["isHiddenInList"] = true;

	$tabla->addField("FechPago", "datetime", 0, "Fecha");
	$tabla->fields["FechPago"]["showOnForm"] = false;

	$tabla->addField("NumeTipoPago", "select", 80, "Forma de pago", true, false, false, true, '1', '', 'tipospagos', 'NumeTipoPago', 'NombTipoPago', "NumeEsta = 1");
	$tabla->fields["NumeTipoPago"]["onChange"] = "formasPago()";

	$tabla->addField("CodiCheq", "select", 100, "Cheque", false, false, false, true, '', '', 'cheques', 'CodiCheq', 'NumeCheq', "NumeEsta = 1", "NumeCheq");
	$tabla->fields["CodiCheq"]["itBlank"] = true;
	$tabla->fields["CodiCheq"]["onChange"] = "buscarImporte()";

	$tabla->addField("ImpoPago", "number", 0, "Importe");
	$tabla->fields["ImpoPago"]["step"] = "0.01";
	$tabla->fields["ImpoPago"]["txtAlign"] = "right";

	$tabla->addField("ObsePago", "textarea", 200, "Observaciones", false);
	$tabla->fields["ObsePago"]["isHiddenInList"] = true;

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	$tabla->fields["NumeEsta"]["isHiddenInForm"] = true;

	$config->tablas["cuotaspagos"] = $tabla;

	/**
	 * CHEQUES
	 */
	$tabla = new Cheque("cheques", "cheques", "Cheques", "el Cheque", true, "objeto/cheques/", "fa-credit-card");
	$tabla->labelField = "NumeCheq";

	$tabla->allowDelete = false;

	$tabla->btnList = [
		array(
			"id"=>'btnCambiarEstado',
			"titulo"=>"Cambiar Estado",
			"class"=>"btn-danger",
			"onclick"=>"cambiarEstado"
		)
	];
	
	$tabla->jsFiles = ["admin/js/custom/cheques.js"];
	$tabla->jsOnList = "verEstado();";
	$tabla->jsOnNew = "onNew();";
	$tabla->jsOnEdit = "onEdit();";
	
	$tabla->addFieldId("CodiCheq", "CodiCheq", true, true);
	$tabla->addField("NumeCheq", "number", 80, "Número");
	$tabla->addField("EsPropio", "checkbox", 0, "Es propio?");
	$tabla->addField("Cruzado", "checkbox", 0, "Es cruzado?");

	$tabla->addField("FechReci", "date", 0, "Fecha recibido");
	$tabla->fields["FechReci"]["cssGroup"] = "form-group3";

	$tabla->addField("FechEmis", "date", 0, "Fecha de Emisión");
	$tabla->fields["FechEmis"]["cssGroup"] = "form-group3";

	$tabla->addField("FechPago", "date", 0, "Fecha de Vencimiento");
	$tabla->fields["FechPago"]["cssGroup"] = "form-group3";

	$tabla->addField("NumeBanc", "select", 80, "Banco", true, false, false, true, '', '', "bancos", "NumeBanc", "NombBanc", "NumeEsta = 1", "NombBanc");
	$tabla->fields["NumeBanc"]["cssGroup"] = "form-group2";

	$tabla->addField("NumeTipoCheq", "select", 80, "Tipo de cheque", true, false, false, true, '', '', "tiposcheques", "NumeTipoCheq", "NombTipoCheq", "", "NombTipoCheq");
	$tabla->fields["NumeTipoCheq"]["cssGroup"] = "form-group2";

	$tabla->addField("NombTitu", "text", 100, "Titular");
	$tabla->fields["NombTitu"]["cssGroup"] = "form-group2";

	$tabla->addField("CUITTitu", "text", 100, "CUIT Titular");
	$tabla->fields["CUITTitu"]["cssGroup"] = "form-group2";
	$tabla->fields["CUITTitu"]["isHiddenInList"] = true;

	$tabla->addField("ImpoCheq", "number", 0, "Importe");
	$tabla->fields["ImpoCheq"]["step"] = "0.01";
	
	$tabla->addField("ObseCheq", "textarea", 400, "Observaciones", false);
	$tabla->fields["ObseCheq"]["isHiddenInList"] = true;

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	$tabla->fields["ObseCheq"]["isHiddenInForm"] = true;
	
	//Recibido de 
	$tabla->addField("NombReci", "text", 80, "Recibido de", true);
	$tabla->fields["NombReci"]["cssGroup"] = "form-group2";

	$tabla->addField("TeleReci", "text", 80, "Teléfono", true);
	$tabla->fields["TeleReci"]["isHiddenInList"] = true;
	$tabla->fields["TeleReci"]["cssGroup"] = "form-group2";

	$tabla->addField("DireReci", "text", 400, "Dirección", false);
	$tabla->fields["DireReci"]["isHiddenInList"] = true;

	//Entregado a
	$tabla->addField("NombEntr", "text", 80, "Entregado a", false);
	$tabla->fields["NombEntr"]["cssGroup"] = "form-group2";

	$tabla->addField("TeleEntr", "text", 80, "Teléfono", false);
	$tabla->fields["TeleEntr"]["isHiddenInList"] = true;
	$tabla->fields["TeleEntr"]["cssGroup"] = "form-group2";

	$tabla->addField("DireEntr", "text", 400, "Dirección", false);
	$tabla->fields["DireEntr"]["isHiddenInList"] = true;

	$config->tablas["cheques"] = $tabla;


	/**
	 * ADMINISTRADOR DE REPORTES
	 */
	$tabla = new Tabla("reportes", "reportes", "Administrador de Reportes", "el reporte", false);
	$tabla->labelField = "NombRepo";

	$tabla->jsFiles = ["admin/js/custom/adminReportes.js"];

	$tabla->addFieldId("NumeRepo", "Número");
	$tabla->addField("NombRepo", "text", 80, "Nombre");
	$tabla->addField("SQLRepo", "textarea", 400, "SQL");
	$tabla->fields["SQLRepo"]["isHiddenInList"] = true;

	$tabla->addField("CantParams", "number", 0, "Cantidad de parámetros");
	$tabla->fields["CantParams"]["isHiddenInList"] = true;

	$tabla->addField("TipoParam", "select", 0, "Tipo de parámetros");
	$tabla->fields["TipoParam"]["isHiddenInList"] = true;

	$tabla->addField("Tabla", "text", 80, "Tabla", false);
	$tabla->fields["Tabla"]["isHiddenInList"] = true;

	$tabla->addField("CampoNumero", "text", 80, "Campo Número", false);
	$tabla->fields["CampoNumero"]["isHiddenInList"] = true;

	$tabla->addField("CampoNombre", "text", 80, "Campo Nombre", false);
	$tabla->fields["CampoNombre"]["isHiddenInList"] = true;

	$tabla->addField("ColumnFoot", "number", 0, "Columna footer");
	$tabla->fields["ColumnFoot"]["isHiddenInList"] = true;

	$tabla->addField("FooterEsMoneda", "checkbox", 0, "Footer es moneda?");
	$tabla->fields["FooterEsMoneda"]["isHiddenInList"] = true;

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["reportes"] = $tabla;

	?>