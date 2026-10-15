<?php
include_once(__DIR__ . "/../Model/Pessoa.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listar(){
    global $conexao;
    $pessoas = array();
    $sql = "select * from pessoa";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {

            $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
            array_push($pessoas, $pessoa);
        }
    }
    return $pessoas;
}

function criar($pessoa){
    global $conexao;

    $sql = "select * from pessoa where nome='{$pessoa->getNome()}' and apelido='{$pessoa->getApelido()}'";
    $result = mysqli_query($conexao, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return false;
    } else {
        $sql = "insert into pessoa values(null,'{$pessoa->getNome()}','{$pessoa->getApelido()}','{$pessoa->getGenero()}','{$pessoa->getEstadoCivil()}','{$pessoa->getAnoNascimento()}')";
        return mysqli_query($conexao, $sql);
    }
}

function remover($codigo) {
    global $conexao;
    $sql = "delete from pessoa where codigo={$codigo}";
    return mysqli_query($conexao, $sql);
}

function editar($pessoa) {
    global $conexao;
    $sql = "UPDATE pessoa SET nome = '{$pessoa->getNome()}', apelido = '{$pessoa->getApelido()}', genero = '{$pessoa->getGenero()}', estadoCivil = '{$pessoa->getEstadoCivil()}', anoNascimento = '{$pessoa->getAnoNascimento()}' where codigo ='{$pessoa->getCodigo()}'";
    return mysqli_query($conexao, $sql);
}

function encontrar($codigo) {
    global $conexao;
    $sql = "select * from pessoa where codigo=" . $codigo;
    $result = mysqli_query($conexao, $sql);

    $rs = mysqli_fetch_assoc($result);
    $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
    return $pessoa;
}

function pesquisar($nome) {
    global $conexao;
    $pessoas = array();
    $nome = mysqli_real_escape_string($conexao, $nome);
    $sql = "select * from pessoa
            where cast(codigo as char) like '%$nome%'
            or nome like '%$nome%'
            or apelido like '%$nome%'
            or genero like '%$nome%'
            or estadoCivil like '%$nome%'
            or cast(anoNascimento as char) like '%$nome%'
            or cast(year(curdate()) as char) like '%$nome%'
            or cast(year(curdate()) - anoNascimento as char) like '%$nome%'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $pessoa = new Pessoa($rs['codigo'], $rs['nome'], $rs['apelido'], $rs['genero'], $rs['estadoCivil'], $rs['anoNascimento']);
            array_push($pessoas, $pessoa);
        }
    }
    return $pessoas;
}
?>