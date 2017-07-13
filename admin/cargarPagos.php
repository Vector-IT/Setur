<?php
    namespace VectorForms;

    ini_set("log_errors", 1);
    ini_set("error_log", "php-error.log");

    session_start();
    require_once 'php/datos.php';

    $urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
    $urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";
    
	if (!isset($_SESSION['is_logged_in'])) {
	    header($urlLogin);
	    die();
	}
	    
	if ($_SESSION['is_logged_in'] !== 1) {
	    header($urlLogin);
	    die();
	}
	    
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html>
<head>
    <?php require_once 'php/linksHeader.php';?>

    <script src="<?php $raiz?>js/custom/cargarPagos.js"></script>
</head>
<body>
    <?php
        if (!isset($_REQUEST["menu"]) || $_REQUEST["menu"] == 1) {
            $config->crearMenu();
        }
        
        require_once 'php/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="page-header">
            <h2><i class="fa fa-fw fa-money" aria-hidden="true"></i> Cargar Pagos</h2>
        </div>

        <form id="frmCarga" class="form-horizontal marginTop20" method="post" onsubmit="return false;">
            <div class="form-group form-group-sm ">
				<label for="Archivo" class="control-label col-md-2">Archivo:</label>
				<div class="col-md-6">
					<input type="file" class="form-control input-sm " id="Archivo" required>
				</div>
            </div>
            <div class="form-group">
				<div class="col-md-offset-2 col-lg-offset-2 col-md-4 col-lg-4">
					<button type="submit" id="btnAceptar" class="btn btn-sm btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
					&nbsp;
					<button type="reset" id="btnCancelar" class="btn btn-sm btn-default"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>
				</div>
			</div>
        </form>
        
        <div id="actualizando" class="alert alert-info" role="alert">
            <i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
        </div>
        
        <div id="divMsj" class="alert alert-danger" role="alert">
            <span id="txtHint">Info</span>
        </div>

    </div>
    
    <?php require_once 'php/footer.php';?>
</body>
</html>
