<?php
    include_once(__DIR__ . "/../Model/CadastroUser.php");
    $conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

    function listar($busca = ""){
        global $conexao;
        $cadastros = array();
        $sql = "select * from cadastro";
        if ($busca !== "") {
            $busca = mysqli_real_escape_string($conexao, $busca);
            $sql .= " where cast(CodigoUtilizador as char) like '%$busca%'
                      or nome like '%$busca%'
                      or apelido like '%$busca%'
                      or contacto like '%$busca%'
                      or email like '%$busca%'
                      or username like '%$busca%'
                      or nivelAcesso like '%$busca%'";
        }
        $result = mysqli_query($conexao, $sql);
        if ($result) {
            while ($rs = mysqli_fetch_assoc($result)) {

                $cadastro = new CadastroUser($rs["CodigoUtilizador"], $rs["nome"], $rs["apelido"], $rs["contacto"], $rs["email"], $rs["username"], $rs["senha"],$rs["nivelAcesso"]);
                array_push($cadastros, $cadastro);
            }
        }
        return $cadastros;
    }
    function criar($cadastro){
    global $conexao;

    $sql = "select * from cadastro  where username = '{$cadastro->getUserName()}'";
    $result = mysqli_query($conexao, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        return false;
    } else {
        // NOVO: transforma a senha em hash antes de gravar
        $senhaHash = password_hash($cadastro->getSenha(), PASSWORD_DEFAULT);

        $sql = "insert into cadastro values( null,'{$cadastro->getNome()}',
                '{$cadastro->getApelido()}','{$cadastro->getContacto()}',
                '{$cadastro->getEmail()}','{$cadastro->getUserName()}',
                '{$senhaHash}','{$cadastro->getNivelAcesso()}')";

        return mysqli_query($conexao, $sql);
    }
}

    function remover($CodigoUtilizador) {
        global $conexao;
        $sql = "delete from cadastro where CodigoUtilizador ={$CodigoUtilizador}";
        return mysqli_query($conexao, $sql);
    }

    function editar($cadastro) {
        global $conexao;
        $sql = "UPDATE cadastro SET Nome = '{$cadastro->getNome()}', apelido ='{$cadastro->getApelido()}', contacto ='{$cadastro->getContacto()}', email ='{$cadastro->getEmail()}', username ='{$cadastro->getUserName()}',senha ='{$cadastro->getSenha()}', nivelAcesso ='{$cadastro->getNivelAcesso()}' where CodigoUtilizador ='{$cadastro->getCodigo()}'";
        return mysqli_query($conexao, $sql);
    }
    function encontrar($CodigoUtilizador) {
        global $conexao;
        $sql = "select * from cadastro where CodigoUtilizador=" . $CodigoUtilizador;
        $result = mysqli_query($conexao, $sql);
    
        $rs = mysqli_fetch_assoc($result);
        $cadastro = new CadastroUser($rs['CodigoUtilizador'], $rs['nome'], $rs['apelido'], $rs['contacto'], $rs['email'], $rs['username'], $rs['senha'], $rs['nivelAcesso']);
        return $cadastro;          
    }

    function resetSenha($CodigoUtilizador) {
    global $conexao;
    // NOVO: a senha padrão também é guardada em hash
    $senhaHash = password_hash('Pessoa2026#', PASSWORD_DEFAULT);
    $sql = "update cadastro 
            set senha = '{$senhaHash}' 
            where CodigoUtilizador = {$CodigoUtilizador}";
    return mysqli_query($conexao, $sql);
}

    function senhaCorresponde($senhaInformada, $senhaGuardada) {
        if ($senhaInformada === null || $senhaGuardada === null) {
            return false;
        }

        if (password_verify($senhaInformada, $senhaGuardada)) {
            return true;
        }

        return hash_equals((string) $senhaInformada, (string) $senhaGuardada);
    }

        // NOVO: login. Procura por username ou email e confere a senha
    function autenticar($username, $senha) {
        global $conexao;

        // protege contra aspas e caracteres especiais escritos no login
        $username = mysqli_real_escape_string($conexao, $username);

        $sql = "select * from cadastro where username = '{$username}' or email = '{$username}'";
        $result = mysqli_query($conexao, $sql);

        if ($result) {
            $rs = mysqli_fetch_assoc($result);

            if ($rs != null && senhaCorresponde($senha, $rs['senha'])) {
                return new CadastroUser($rs['CodigoUtilizador'], $rs['nome'], $rs['apelido'], $rs['contacto'], $rs['email'], $rs['username'], $rs['senha'], $rs['nivelAcesso']);
            }
        }
        return null;
    }

?>


