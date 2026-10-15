<?php
include_once(__DIR__ . "/../Model/Continente.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listar(){
        global $conexao;
        $continentes = array();
        $sql = "select * from continente";
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {
        
                $continente = new Continente($rs['codigoContinente'], $rs['nomeContinente']);
                array_push($continentes, $continente);
            }
        }
        return $continentes;
    }
   function criar($continente){
    global $conexao;

    $sql = "select * from continente  where nomeContinente='{$continente->getNomeContinente()}'";
    $result = mysqli_query($conexao, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return false;
    } else {
        $sql = "insert into continente values(null,'{$continente->getNomeContinente()}')";
        return mysqli_query($conexao, $sql);
    }
}

     function remover($codigoContinente) {
        global $conexao;
        $sql = "delete from continente where codigoContinente={$codigoContinente}";
        return mysqli_query($conexao, $sql);
    }

    function editar($continente) {
        global $conexao;
        $sql = "UPDATE continente SET NomeContinente = '{$continente->getNomeContinente()}' where codigoContinente ='{$continente->getCodigoContinente()}'";
          return mysqli_query($conexao, $sql);
    }
    function encontrar($codigoContinente) {
        global $conexao;
        $sql = "select * from continente where codigoContinente=" . $codigoContinente;
        $result = mysqli_query($conexao, $sql);
    
        $rs = mysqli_fetch_assoc($result);
        $continente = new Continente($rs['codigoContinente'], $rs['nomeContinente']);
        return $continente;
        
        
    }
    function pesquisar($nome) {
    global $conexao;
    $continentes = array();
        $nome = mysqli_real_escape_string($conexao, $nome);
        $sql = "select * from continente where cast(codigoContinente as char) like '%$nome%' or nomeContinente like '%$nome%'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $continente = new Continente($rs['codigoContinente'], $rs['nomeContinente']);
            array_push($continentes, $continente);
        }
    }
    return $continentes;
}

    // if (isset($_POST['gravar'])) {
    //     if (isset($_POST["nomeContinente"])) {
    //         $nomeContinente = $_POST["nomeContinente"];

    //         $sql = "insert into continente values(null, '{$nomeContinente}')";
    //         $result = mysqli_query($conexao, $sql);
        
    //     }
    // }
    //     // se clicar btn actualizar
    // if (isset($_POST['actualizar'])) {
    //     if (isset($_POST["nomeContinente"]) && isset($_POST["codigoContinente"])) {
    //         $nomeContinente = $_POST["nomeContinente"];
    //         $codigoContinente = $_POST["codigoContinente"];

    //         $sql = "update continente set nomeContinente='{$nomeContinente}' where codigoContinente = {$codigoContinente}";
    //         $result = mysqli_query($conexao, $sql);
    //     }
    // }

    // // se clicar btn remover
    // if (isset($_POST['remover'])) {
    //     if (isset($_POST["codigoContinente"])) {
    //         $codigoContinente = $_POST["codigoContinente"];

    //         $sql = "delete from continente where codigoContinente = {$codigoContinente}";
    //         $result = mysqli_query($conexao, $sql);
    //     }
    // }
?>