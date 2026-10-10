<?php
include_once(__DIR__ . "/../Model/Nacionalidade.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerContinente.php");
    verificarLogin();
$conexao = mysqli_connect("localhost", "root", "", "sgpessoa");

function listarNacionalidades(){
    global $conexao;
    $nacionalidades = array();
    $sql = "select n.id_nacionalidade, n.nome, n.dataRegisto,
            p.nomePais, c.nomeContinente, concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente
            inner join pessoa pe on n.id_pessoa = pe.codigo";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {

            $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["dataRegisto"], $rs["nomePessoa"], $rs["nomePais"], $rs["nomeContinente"]);
            array_push($nacionalidades, $nacionalidade);
        }
    }
    return $nacionalidades;
}

// aqui getNomePessoa() e getNomePais() trazem os IDs que vieram da view
// getNomeContinente() traz o nome do continente escolhido
function criarNacionalidade($nacionalidade){
    global $conexao;

    // o pais escolhido tem de pertencer ao continente escolhido
    $sql = "select * from pais p
            inner join continente c on p.idcontinente = c.codigoContinente
            where p.codigoPais={$nacionalidade->getNomePais()}
            and c.nomeContinente='{$nacionalidade->getNomeContinente()}'";
    $result = mysqli_query($conexao, $sql);
    if (mysqli_num_rows($result) == 0) {
        return "O pais nao pertence ao continente escolhido";
    }

    $sql = "select * from nacionalidade
            where nome='{$nacionalidade->getNomeNacionalidade()}'
            and id_pessoa={$nacionalidade->getNomePessoa()}";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        return "A nacionalidade já existe para esta pessoa";
    }

    $sql = "insert into nacionalidade values(null,
            '{$nacionalidade->getNomeNacionalidade()}',
            '{$nacionalidade->getDataRegisto()}',
            {$nacionalidade->getNomePais()},
            {$nacionalidade->getNomePessoa()})";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Registou uma nacionalidade");
        return true;
    }
    return "Ocorreu um erro ao gravar os dados";
}

function removerNacionalidade($codigoNacionalidade) {
    global $conexao;
    $sql = "delete from nacionalidade where id_nacionalidade={$codigoNacionalidade}";
    $result = mysqli_query($conexao, $sql);
    
    if ($result && mysqli_affected_rows($conexao) > 0) {
        registarAccao("Removeu uma nacionalidade");
    }
    //header('location:index.php');
}

function editarNacionalidade($nacionalidade) {
    global $conexao;

    $sql = "select * from pais p
            inner join continente c on p.idcontinente = c.codigoContinente
            where p.codigoPais={$nacionalidade->getNomePais()}
            and c.nomeContinente='{$nacionalidade->getNomeContinente()}'";
    $result = mysqli_query($conexao, $sql);
    if (mysqli_num_rows($result) == 0) {
        return "O pais nao pertence ao continente escolhido";
    }

    $sql = "UPDATE nacionalidade SET nome = '{$nacionalidade->getNomeNacionalidade()}',
            dataRegisto = '{$nacionalidade->getDataRegisto()}',
            id_pais = {$nacionalidade->getNomePais()},
            id_pessoa = {$nacionalidade->getNomePessoa()}
            where id_nacionalidade = '{$nacionalidade->getCodigoNacionalidade()}'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        registarAccao("Editou uma nacionalidade");
       // header('location:index.php');
    } else
        echo "ocorreu um erro ao actualizar os dados";
}

function encontrar($codigoNacionalidade) {
    global $conexao;
    $sql = "select n.id_nacionalidade, n.nome, n.dataRegisto,
            p.nomePais, c.nomeContinente, concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente
            inner join pessoa pe on n.id_pessoa = pe.codigo
            where n.id_nacionalidade=" . $codigoNacionalidade;
    $result = mysqli_query($conexao, $sql);

    $rs = mysqli_fetch_assoc($result);
    $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["dataRegisto"], $rs["nomePessoa"], $rs["nomePais"], $rs["nomeContinente"]);
    return $nacionalidade;
}

function pesquisar($nome) {
    global $conexao;
    $nacionalidades = array();
    $nome = mysqli_real_escape_string($conexao, $nome);
    $sql = "select n.id_nacionalidade, n.nome, n.dataRegisto,
            p.nomePais, c.nomeContinente, concat(pe.nome, ' ', pe.apelido) as nomePessoa
            from nacionalidade n
            inner join pais p on n.id_pais = p.codigoPais
            inner join continente c on p.idcontinente = c.codigoContinente
            inner join pessoa pe on n.id_pessoa = pe.codigo
            where n.nome like '%$nome%'
            or n.dataRegisto like '%$nome%'
            or p.nomePais like '%$nome%'
            or c.nomeContinente like '%$nome%'
            or concat(pe.nome, ' ', pe.apelido) like '%$nome%'";
    $result = mysqli_query($conexao, $sql);
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            $nacionalidade = new Nacionalidade($rs["id_nacionalidade"], $rs["nome"], $rs["dataRegisto"], $rs["nomePessoa"], $rs["nomePais"], $rs["nomeContinente"]);
            array_push($nacionalidades, $nacionalidade);
        }
    }
    return $nacionalidades;
}

function listarPessoasSelect(){
    global $conexao;
    $lista = array();
    $result = mysqli_query($conexao, "select codigo, nome, apelido from pessoa");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function listarPaisesSelect(){
    global $conexao;
    $lista = array();
    $result = mysqli_query($conexao, "select p.codigoPais, p.nomePais, c.nomeContinente
                                      from pais p
                                      inner join continente c on p.idcontinente = c.codigoContinente");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}

function listarContinentesSelect(){
    global $conexao;
    $lista = array();
    $result = mysqli_query($conexao, "select codigoContinente, nomeContinente from continente");
    if ($result) {
        while ($rs = mysqli_fetch_assoc($result)) {
            array_push($lista, $rs);
        }
    }
    return $lista;
}
?>