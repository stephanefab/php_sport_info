<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    
     $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_country = getOneByColumn("pays", "id", $id);
    if(!$is_country){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/countries");
    }else{
        if(delete("pays", $id)){
            $message = "pays ".$is_country['name']." supprimé";
            setFlashMessage($message, "success");
        }else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
        redirectToUrl("/admin/countries");
    }
?>