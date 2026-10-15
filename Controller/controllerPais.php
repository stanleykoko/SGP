<?php
include_once(__DIR__ . "/../Model/Pais.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listarPaises($busca = null){
        global $conexao;
        $paises = array();
        $sql = "select p.codigoPais, p.nomePais, c.nomeContinente
                from pais p
                inner join continente c on p.idcontinente = c.codigoContinente";
        if (!empty($busca)) {
            $busca = mysqli_real_escape_string($conexao, $busca);
            $sql .= " where cast(p.codigoPais as char) like '%{$busca}%'
                      or p.nomePais like '%{$busca}%'
                      or c.nomeContinente like '%{$busca}%'";
        }
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {

                $pais = new Pais($rs['codigoPais'], $rs['nomePais'], $rs['nomeContinente']);
                array_push($paises, $pais);
            }
        }
        return $paises;
    }

    function criarPais($pais){
        global $conexao;

        $sql = "select * from pais p
                inner join continente c on p.idcontinente = c.codigoContinente
                where p.nomePais='{$pais->getNomePais()}' and c.nomeContinente='{$pais->getNomeContinente()}'";
        $result = mysqli_query($conexao, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            return false;
        } else {
            $sql = "insert into pais values(null,'{$pais->getNomePais()}',(select codigoContinente from continente where nomeContinente = '{$pais->getNomeContinente()}'))";
            return mysqli_query($conexao, $sql);
        }
    }

    function removerPais($codigoPais) {
        global $conexao;
        $sql = "delete from pais where codigoPais={$codigoPais}";
        return mysqli_query($conexao, $sql);
    }

    function editarPais($pais) {
        global $conexao;
        $sql = "UPDATE pais SET nomePais = '{$pais->getNomePais()}',
                idcontinente = (select codigoContinente from continente where nomeContinente = '{$pais->getNomeContinente()}')
                where codigoPais = '{$pais->getCodigoPais()}'";
        return mysqli_query($conexao, $sql);
    }

    function encontrarPais($codigoPais) {
        global $conexao;
        $sql = "select p.codigoPais, p.nomePais, c.nomeContinente
                from pais p
                inner join continente c on p.idcontinente = c.codigoContinente
                where p.codigoPais=" . $codigoPais;
        $result = mysqli_query($conexao, $sql);

        $rs = mysqli_fetch_assoc($result);
        $pais = new Pais($rs['codigoPais'], $rs['nomePais'], $rs['nomeContinente']);
        return $pais;
    }

    function listarContinentes() {
        global $conexao;
        $sql = "select * from continente";
        return mysqli_query($conexao, $sql);
    }
?>