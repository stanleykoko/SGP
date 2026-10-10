
<?php
include_once(__DIR__ . "/../Controller/controllerLog.php");
include_once(__DIR__ . "/../Controller/sessao.php");
verificarLogin();
$perfil = $sessao->nivelAcesso;
$nomeCompleto = $sessao->nome . " " . $sessao->apelido;
$campos = array(
    "accao" => "Ação",
    "usuario" => "Utilizador",
    "perfil" => "Perfil",
    "dataAccao" => "Data de Ação"
);
$campo = isset($_GET['campo']) ? trim($_GET['campo']) : "";
$valor = isset($_GET['valor']) ? trim($_GET['valor']) : "";
if (!array_key_exists($campo, $campos)) {
    $campo = "";
    $valor = "";
}
$logs = ($campo != "" && $valor != "") ? pesquisarLogs($campo, $valor) : listarLogs();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Listar Log</title>
    <link rel="stylesheet" href="../assets/estilo.css">
</head>
<body>
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br>DE PESSOAS</div>
        </div>
        <nav class="navlinks">
            <a href="TelaPrincipal.php">Inicio</a>
            <a href="Pessoa.php">Pessoa</a>
            <a href="Continente.php">Continente</a>
            <a href="Pais.php">Pais</a>
            <a href="Nacionalidade.php">Nacionalidade</a>
            <?php if ($perfil == 'ADMINISTRADOR') { ?>
                <a href="Cadastro.php">Cadastro de utilizadores</a>
            <?php } ?>
            <?php if ($perfil == 'ADMINISTRADOR' || $perfil == 'AUDITOR') { ?>
                <a href="log.php" class="on">Log</a>
            <?php } ?>
        </nav>
        <div class="utilizador">
            <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
            <span class="perfil"><?= htmlspecialchars($perfil) ?></span>
        </div>
        <a href="Logout.php" class="sair">Log out</a>
    </div>
    <div class="mae">
        <div class="cabecalho">
            <h1>Registo de Acções</h1>
            <form method="get" action="log.php" class="pesquisa">
                <select name="campo" id="campo" required>
                    <option value="">Pesquisar por...</option>
                    <?php foreach ($campos as $chave => $nome) { ?>
                        <option value="<?= $chave ?>" <?= ($campo == $chave) ? "selected" : "" ?>><?= $nome ?></option>
                    <?php } ?>
                </select>
                <input type="text" name="valor" placeholder="Escreva o que pretende pesquisar" value="<?= htmlspecialchars($valor) ?>">
                
            </form>
        </div>
        <div class="caixa">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Utilizador</th>
                            <th>Perfil</th>
                            <th>Acção</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($logs) == 0) { ?>
                            <tr>
                                <td colspan="5" class="vazio">Nenhum registo encontrado</td>
                            </tr>
                        <?php } else { ?>
                            <?php foreach ($logs as $log) { ?>
                                <tr>
                                    <td><?= $log->getCodigoLog() ?></td>
                                    <td><?= htmlspecialchars($log->getUsuario()) ?></td>
                                    <td><?= htmlspecialchars($log->getPerfil()) ?></td>
                                    <td><?= htmlspecialchars($log->getAccao()) ?></td>
                                    <td><?= htmlspecialchars($log->getDataAccao()) ?></td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
