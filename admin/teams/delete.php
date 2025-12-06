<?php
    require_once __DIR__ . '/../../functions.php';
    isAdmin();
    
     $id = (isset($_GET['id']) && is_numeric($_GET['id'])) ? (int) $_GET['id'] : null;

    if(!$id){
        redirectToHome();
    }

    $is_team = getOneByColumn("teams", "id", $id);
    if(!$is_team){
        setFlashMessage("ID #$id inconnu.", "error");
        redirectToUrl("/admin/teams");
    }else{
        if(delete("teams", $id)){
            $message = "teams ".$is_team['name']." supprimé";
            setFlashMessage($message, "success");
        }else {
            setFlashMessage("Une erreur est survenue, veuillez réessayer plus tard", 'error');
        }
        redirectToUrl("/admin/teams");
    }
?>