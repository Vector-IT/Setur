<?php
namespace VectorForms;

class Cheque extends Tabla {
    public function customFunc($post)
    {
        global $config;

        switch ($post['field']) {
            case "NumeEsta":
                $result = $config->ejecutarCMD("UPDATE cheques SET NumeEsta = NOT NumeEsta WHERE CodiCheq = ". $post["dato"]["CodiCheq"]);
                
                return $result;
                break;
        }
    }
}
?>