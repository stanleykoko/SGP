<?php
include_once(__DIR__ . "/../Model/Log.php");
$conexaoLog = mysqli_connect("localhost", "root", "", "sgpessoa");

function registarLog($usuario, $perfil, $accao){
    global $conexaoLog;
    $sql = "insert into log (usuario, perfil, accao, dataAccao) values (?, ?, ?, now())";
    $stmt = mysqli_prepare($conexaoLog, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $usuario, $perfil, $accao);
    return mysqli_stmt_execute($stmt);
}

function listarLogs($campo = "", $valor = ""){
    global $conexaoLog;
    $logs = array();
    $colunas = array("codigoLog", "usuario", "perfil", "accao", "dataAccao");
    $sql = "select * from log";

    if (in_array($campo, $colunas, true) && $valor !== "") {
        $sql .= " where $campo like ?";
        $stmt = mysqli_prepare($conexaoLog, $sql . " order by dataAccao desc");
        $valor = "%" . $valor . "%";
        mysqli_stmt_bind_param($stmt, "s", $valor);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
    } else {
        $resultado = mysqli_query($conexaoLog, $sql . " order by dataAccao desc");
    }

    if ($resultado) {
        while ($rs = mysqli_fetch_assoc($resultado)) {
            $logs[] = new log($rs['codigoLog'], $rs['usuario'], $rs['perfil'], $rs['accao'], $rs['dataAccao']);
        }
    }
    return $logs;
}
?>