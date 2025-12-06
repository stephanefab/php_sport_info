<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    
     $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_sport = getOneByColumn("sports", "id", $id);
    if(!$is_sport){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/sports");
    }else{
        if(delete("sports", $id)){
            $message = "sports ".$is_sport['name']." supprimé";
            setFlashMessage($message, "success");
        }else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
        redirectToUrl("/admin/sports");
    }
?>