<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    
     $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_match = getOneByColumn("matchs", "id", $id);
    if(!$is_match){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/matchs");
    }else{
        if(delete("matchs", $id)){
            $message = "matchs supprimé";
            setFlashMessage($message, "success");
        }else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
        redirectToUrl("/admin/matchs");
    }
?>