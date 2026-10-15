<?php
include_once(__DIR__ . "/../Model/Log.php");
$conexaoLog = mysqli_connect("localhost", "root", "", "sgpessoa");
mysqli_set_charset($conexaoLog, "utf8mb4");

function registarLog($usuario, $perfil, $accao){
    global $conexaoLog;
    $sql = "insert into log (usuario, perfil, accao, dataAccao) values (?, ?, ?, now())";
    $stmt = mysqli_prepare($conexaoLog, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $usuario, $perfil, $accao);
    return mysqli_stmt_execute($stmt);
}

// $campo vazio = pesquisa em todos os campos
// contem o texto (maiusculas/minusculas tanto faz); nao ha filtro por ID
function listarLogs($campo = "", $valor = ""){
    global $conexaoLog;
    $logs = array();
    $colunas = array("usuario", "perfil", "accao", "dataAccao");
    $sql = "select * from log";
    $tipos = "";
    $params = array();

    if ($valor !== "") {
        $like = "%" . $valor . "%";

        if (in_array($campo, $colunas, true)) {
            $sql .= " where CAST($campo AS CHAR) like ?";
            $tipos = "s";
            $params = array($like);
        } else {
            $sql .= " where usuario like ? or perfil like ? or accao like ? or CAST(dataAccao AS CHAR) like ?";
            $tipos = "ssss";
            $params = array($like, $like, $like, $like);
        }
    }

    $sql .= " order by dataAccao desc, codigoLog desc";

    if ($valor === "") {
        $resultado = mysqli_query($conexaoLog, $sql);
    } else {
        $stmt = mysqli_prepare($conexaoLog, $sql);
        if (!$stmt) {
            return $logs;
        }
        mysqli_stmt_bind_param($stmt, $tipos, ...$params);
        mysqli_stmt_execute($stmt);
        $resultado = mysqli_stmt_get_result($stmt);
    }

    if ($resultado) {
        while ($rs = mysqli_fetch_assoc($resultado)) {
            $logs[] = new Log($rs['codigoLog'], $rs['usuario'], $rs['perfil'], $rs['accao'], $rs['dataAccao']);
        }
    }
    return $logs;
}
?>