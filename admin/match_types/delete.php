<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    
     $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_sport = getOneByColumn("match_types", "id", $id);
    if(!$is_sport){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/match_types");
    }else{
        if(delete("match_types", $id)){
            $message = "match_types ".$is_sport['name']." supprimé";
            setFlashMessage($message, "success");
        }else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
        redirectToUrl("/admin/match_types");
    }
?>