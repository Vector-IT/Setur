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
            <h2><i class="fa fa-fw fa-barcode" aria-hidden="true"></i> Código de Barras</h2>
        </div>

        <div class="text-center">
            <img src="barcode/barcode.php?size=60&text=<?php echo $_REQUEST["code"]?>" alt="Codigo de barras" />
            <br>
            <span class="txtMediano txtRed"><?php echo $_REQUEST["code"]?></span>
        </div>
    </div>
    
    <?php require_once 'php/footer.php';?>
</body>
</html>
