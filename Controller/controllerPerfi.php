<?php
include_once(__DIR__ . "/../Model/Perfil.php");
include_once(__DIR__ . "/sessao.php");
verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

// Cria um objecto Perfil a partir de uma linha da BD
function montarPerfil($rs)
{
    return new Perfil(
        $rs['codigoPerfil'],
        $rs['nomePerfil'],
        $rs['descricao'],
        $rs['ver'],
        $rs['criar'],
        $rs['editar'],
        $rs['remover'],
        $rs['utilizadores'],
        $rs['logs'],
        $rs['perfis']
    );
}

function listarPerfis()
{
    global $conexao;
    $perfis = array();
    $result = mysqli_query($conexao, "select * from perfil order by codigoPerfil");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($perfis, montarPerfil($rs));
        }
    }
    return $perfis;
}

function encontraPerfilPorId($id)
{
    global $conexao;
    $id = (int)$id;
    $result = mysqli_query($conexao, "select * from perfil where codigoPerfil = $id");
    $rs = mysqli_fetch_assoc($result);
    if (!$rs) {
        return null;
    }
    return montarPerfil($rs);
}

function encontraPerfilPorNome($nome)
{
    global $conexao;
    $nome = mysqli_real_escape_string($conexao, $nome);
    $result = mysqli_query($conexao, "select * from perfil where nomePerfil = '$nome'");
    $rs = mysqli_fetch_assoc($result);
    if (!$rs) {
        return null;
    }
    return montarPerfil($rs);
}

// Perfil de quem está logado.
// Funciona se nivelAcesso na sessão for o código (1, 2, 3...) ou o nome (ADMINISTRADOR...)
function encontraPerfilDaSessao()
{
    $nivel = $_SESSION['nivelAcesso'] ?? '';
    if (is_numeric($nivel)) {
        return encontraPerfilPorId($nivel);
    }
    return encontraPerfilPorNome($nivel);
}

function criarPerfil($perfil)
{
    global $conexao;
    $nome = mysqli_real_escape_string($conexao, $perfil->getNomePerfil());
    $descricao = mysqli_real_escape_string($conexao, $perfil->getDescricao());

    $result = mysqli_query($conexao, "select * from perfil where nomePerfil = '$nome'");
    if (mysqli_num_rows($result) > 0) {
        return "O perfil já existe";
    }

    $sql = "insert into perfil (nomePerfil, descricao, ver, criar, editar, remover, utilizadores, logs, perfis)
            values ('$nome', '$descricao', 1,
            {$perfil->getCriar()}, {$perfil->getEditar()}, {$perfil->getRemover()},
            {$perfil->getUtilizadores()}, {$perfil->getLogs()}, {$perfil->getPerfis()})";
    if (mysqli_query($conexao, $sql)) {
        registarAccao("Criou o perfil: " . $perfil->getNomePerfil());
        return "Perfil criado";
    }
    return "Ocorreu um erro ao gravar os dados";
}

function actualizarPerfil($perfil)
{
    global $conexao;
    $id = (int)$perfil->getCodigoPerfil();
    $nome = mysqli_real_escape_string($conexao, $perfil->getNomePerfil());
    $descricao = mysqli_real_escape_string($conexao, $perfil->getDescricao());

    // Os perfis base (1 a 4) não se editam
    if ($id <= 4) {
        return "Os perfis base não se editam";
    }

    // Não deixar usar um nome que já pertence a outro perfil
    $result = mysqli_query($conexao, "select * from perfil where nomePerfil = '$nome' and codigoPerfil <> $id");
    if (mysqli_num_rows($result) > 0) {
        return "Já existe outro perfil com esse nome";
    }

    $sql = "update perfil set nomePerfil = '$nome', descricao = '$descricao',
            criar = {$perfil->getCriar()},
            editar = {$perfil->getEditar()},
            remover = {$perfil->getRemover()},
            utilizadores = {$perfil->getUtilizadores()},
            logs = {$perfil->getLogs()},
            perfis = {$perfil->getPerfis()}
            where codigoPerfil = $id";
    if (mysqli_query($conexao, $sql)) {
        registarAccao("Actualizou o perfil: " . $perfil->getNomePerfil());
        return "Perfil actualizado";
    }
    return "Ocorreu um erro ao actualizar";
}

function removerPerfil($id)
{
    global $conexao;
    $id = (int)$id;

    if ($id <= 4) {
        return "Os perfis base não se removem";
    }

    // Não remover se ainda houver utilizadores com este perfil
    $result = mysqli_query($conexao, "select * from cadastro where nivelAcesso = $id");
    if (mysqli_num_rows($result) > 0) {
        return "Não se pode remover: há utilizadores com este perfil";
    }

    if (mysqli_query($conexao, "delete from perfil where codigoPerfil = $id")) {
        registarAccao("Removeu o perfil ID " . $id);
        return "Perfil removido";
    }
    return "Ocorreu um erro ao remover";
}