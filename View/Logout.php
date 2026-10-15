<?php
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerLog.php");
    if (Sessao::estaLogado()) {
        registarLog(Sessao::getUsername(), Sessao::getNivelAcesso(), "Terminou sessão");
    }
    Sessao::terminarSessao();
    header("Location: Login.php");
    exit;
?>