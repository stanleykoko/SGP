
<?php
include_once(__DIR__ . "/../Controller/controllerNacionalidade.php");
include_once(__DIR__ . "/../Controller/sessao.php");
include_once(__DIR__ . "/../Controller/controllerLog.php");
verificarLogin();
$perfil = $sessao->nivelAcesso;
$nomeCompleto = $sessao->nome . " " . $sessao->apelido;
$erro = "";
// REMOVER NACIONALIDADE
if (isset($_POST['remover'])) {
    removerNacionalidade($_POST['id']);
    header("Location: Nacionalidade.php#lista");
    exit;
}
// CADASTRAR NACIONALIDADE
if (isset($_POST['gravar'])) {
    if (!empty($_POST['nome']) && !empty($_POST['nome_continente']) && !empty($_POST['idpais']) && !empty($_POST['idpessoa']) && !empty($_POST['dataRegisto'])) {
        $nacionalidade = new Nacionalidade(null, $_POST['nome'], $_POST['dataRegisto'], $_POST['idpessoa'], $_POST['idpais'], $_POST['nome_continente']);
        $resultado = criarNacionalidade($nacionalidade);
        if (is_string($resultado) && $resultado != "") {
            $erro = $resultado;
        } elseif ($resultado) {
            header("Location: Nacionalidade.php#lista");
            exit;
        } else {
            $erro = "Não foi possível registar a nacionalidade.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
// ATUALIZAR NACIONALIDADE
if (isset($_POST['actualizar'])) {
    if (!empty($_POST['id']) && !empty($_POST['nomeEditar']) && !empty($_POST['idpaisEditar']) && !empty($_POST['idpessoaEditar']) && !empty($_POST['nome_continenteEditar']) && !empty($_POST['dataRegistoEditar'])) {
        $nacionalidadeExistente = encontrar($_POST['id']);
        if ($nacionalidadeExistente) {
            $nacionalidadeExistente->setNomeNacionalidade($_POST['nomeEditar']);
            $nacionalidadeExistente->setDataRegisto($_POST['dataRegistoEditar']);
            $nacionalidadeExistente->setNomePessoa($_POST['idpessoaEditar']);
            $nacionalidadeExistente->setNomePais($_POST['idpaisEditar']);
            $nacionalidadeExistente->setNomeContinente($_POST['nome_continenteEditar']);
            editarNacionalidade($nacionalidadeExistente);
            header("Location: Nacionalidade.php#lista");
            exit;
        }
    }
}
// PESQUISAR E LISTAR
$pesquisa = $_GET['pesquisa'] ?? "";
$nacionalidades = pesquisar($pesquisa);
$pessoas = listarPessoasSelect();
$paises = listarPaisesSelect();
$continentes = listarContinentesSelect();
$novoContinente = $_POST['nome_continente'] ?? "";
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestão de Nacionalidades</title>
    <link rel="stylesheet" href="../assets/estilo.css">
</head>
<body>
    <!-- MENU DE NAVEGAÇÃO -->
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br>DE PESSOAS</div>
        </div>
        <nav class="navlinks">
            <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
            <a href="Pessoa.php"><span class="icone"></span>Pessoa</a>
            <a href="Continente.php"><span class="icone"></span>Continente</a>
            <a href="Pais.php"><span class="icone"></span>Pais</a>
            <a href="Nacionalidade.php" class="on"><span class="icone"></span>Nacionalidade</a>
            <?php if ($perfil == 'ADMINISTRADOR') { ?>
                <a href="Cadastro.php"><span class="icone"></span>Cadastro de utilizadores</a>
            <?php } ?>
            <?php if ($perfil == 'AUDITOR') { ?>
                <a href="log.php"><span class="icone"></span>Log</a>
            <?php } ?>
        </nav>
        <div class="utilizador">
            <div class="utilizadorNome"><?= htmlspecialchars($nomeCompleto) ?></div>
            <span class="perfil"><?= htmlspecialchars($perfil) ?></span>
        </div>
        <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
    </div>
    <div class="mae">
        <!-- CABEÇALHO -->
        <div class="cabecalho">
            <h1>Nacionalidades</h1>
            <div class="topo">
                <?php if ($erro != "") { ?>
                    <p class="erro"><?= htmlspecialchars($erro) ?></p>
                <?php } ?>
                <form class="busca" method="get" action="Nacionalidade.php">
                    <input type="text" name="pesquisa" placeholder="Pesquisar nacionalidade" value="<?= htmlspecialchars($pesquisa) ?>">
                    <button type="submit" class="botao-pesquisar"></button>
                </form>
                <a class="botao" href="#novo">Nova Nacionalidade</a>
            </div>
        </div>
        <!-- TABELA DE NACIONALIDADES -->
        <div class="caixa" id="lista">
            <div class="tabela">
                <table>
                    <thead>
                        <tr>
                            <th class="coluna-id">ID</th>
                            <th>Nacionalidade</th>
                            <th>País</th>
                            <th>Data de Registo</th>
                            <th>Pessoa</th>
                            <th>Continente</th>
                            <th class="coluna-acao">Acção</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($nacionalidades) == 0) { ?>
                            <tr><td colspan="7" class="vazio"><?= $pesquisa != "" ? "Nacionalidade não encontrada" : "Nenhuma nacionalidade registada" ?></td></tr>
                        <?php } else { ?>
                            <?php foreach ($nacionalidades as $nacionalidade) { ?>
                                <tr>
                                    <td><?= $nacionalidade->getCodigoNacionalidade() ?></td>
                                    <td><?= htmlspecialchars($nacionalidade->getNomeNacionalidade()) ?></td>
                                    <td><?= htmlspecialchars($nacionalidade->getNomePais()) ?></td>
                                    <td><?= htmlspecialchars($nacionalidade->getDataRegisto()) ?></td>
                                    <td><?= htmlspecialchars($nacionalidade->getNomePessoa()) ?></td>
                                    <td><?= htmlspecialchars($nacionalidade->getNomeContinente()) ?></td>
                                    <td>
                                        <div class="acoes">
                                            <?php if ($perfil == 'ADMINISTRADOR' || $perfil == 'AUDITOR' || $perfil == 'SUPEROPERADOR') { ?>
                                                <a href="#editar-<?= $nacionalidade->getCodigoNacionalidade() ?>" class="botao-atualizar">Editar</a>
                                                <a href="#remover-<?= $nacionalidade->getCodigoNacionalidade() ?>" class="botao-remover">Remover</a>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- MODAL: NOVA NACIONALIDADE -->
        <div class="modalFundo" id="novo">
            <a href="#" class="modalOverlay" aria-label="Fechar"></a>
            <div class="modalCaixa">
                <div class="modalTopo">
                    <h2 class="titulo">Nova Nacionalidade</h2>
                    <a href="#" class="modalFechar">x</a>
                </div>
                <form method="post" action="Nacionalidade.php#novo">
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                        <select name="nome_continente" class="input" required>
                            <option value="">Selecione um continente</option>
                            <?php foreach ($continentes as $c) { ?>
                                <option value="<?= htmlspecialchars($c['nomeContinente']) ?>" <?= $c['nomeContinente'] == $novoContinente ? 'selected' : '' ?>><?= htmlspecialchars($c['nomeContinente']) ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="filtrar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>País</strong></label></div>
                        <select name="idpais" class="input" required>
                            <option value="">Selecione um país</option>
                            <?php foreach ($paises as $pais) { ?>
                                <?php if ($pais['nomeContinente'] == $novoContinente) { ?>
                                    <option value="<?= $pais['codigoPais'] ?>"><?= htmlspecialchars($pais['nomePais']) ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                        <select name="idpessoa" class="input" required>
                            <option value="">Selecione uma pessoa</option>
                            <?php foreach ($pessoas as $pessoa) { ?>
                                <option value="<?= $pessoa['codigo'] ?>"><?= htmlspecialchars($pessoa['nome'] . " " . $pessoa['apelido']) ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                        <input type="text" name="nome" class="input" required>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                        <input type="date" name="dataRegisto" class="input" required>
                    </div>
                    <div class="acao"><button type="submit" name="gravar" class="botao">Guardar</button></div>
                </form>
            </div>
        </div>
        <!-- MODAIS: EDITAR NACIONALIDADE -->
        <?php foreach ($nacionalidades as $n) {
            $editNome = $n->getNomeNacionalidade();
            $editContinente = $n->getNomeContinente();
            $editPais = $n->getNomePais();
            $editPessoa = $n->getNomePessoa();
            $editData = $n->getDataRegisto();
            $filtrouEste = isset($_POST['filtrarEditar'], $_POST['id']) && $_POST['id'] == $n->getCodigoNacionalidade();
            if ($filtrouEste) {
                $editNome = $_POST['nomeEditar'];
                $editContinente = $_POST['nome_continenteEditar'];
                $editPais = $_POST['idpaisEditar'];
                $editPessoa = $_POST['idpessoaEditar'];
                $editData = $_POST['dataRegistoEditar'];
            }
        ?>
            <div class="modalFundo" id="editar-<?= $n->getCodigoNacionalidade() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Nacionalidade.php#editar-<?= $n->getCodigoNacionalidade() ?>">
                        <input type="hidden" name="id" value="<?= $n->getCodigoNacionalidade() ?>">
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                            <input type="text" name="nomeEditar" class="input" value="<?= htmlspecialchars($editNome) ?>" required>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                            <select name="nome_continenteEditar" class="input" required>
                                <option value="">Selecione um continente</option>
                                <?php foreach ($continentes as $c) { ?>
                                    <option value="<?= htmlspecialchars($c['nomeContinente']) ?>" <?= $c['nomeContinente'] == $editContinente ? 'selected' : '' ?>><?= htmlspecialchars($c['nomeContinente']) ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="filtrarEditar" class="botao-atualizar" formnovalidate>Filtrar países</button>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>País</strong></label></div>
                            <select name="idpaisEditar" class="input" required>
                                <option value="">Selecione um país</option>
                                <?php foreach ($paises as $pais) { ?>
                                    <?php if ($pais['nomeContinente'] == $editContinente) {
                                        $paisSelecionado = $pais['codigoPais'] == $editPais || $pais['nomePais'] == $editPais;
                                    ?>
                                        <option value="<?= $pais['codigoPais'] ?>" <?= $paisSelecionado ? 'selected' : '' ?>><?= htmlspecialchars($pais['nomePais']) ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <select name="idpessoaEditar" class="input" required>
                                <option value="">Selecione uma pessoa</option>
                                <?php foreach ($pessoas as $pessoa) {
                                    $nomeCompletoPessoa = $pessoa['nome'] . " " . $pessoa['apelido'];
                                    if ($filtrouEste) {
                                        $pessoaSelecionada = $pessoa['codigo'] == $editPessoa;
                                    } else {
                                        $pessoaSelecionada = $nomeCompletoPessoa == $editPessoa;
                                    }
                                ?>
                                    <option value="<?= $pessoa['codigo'] ?>" <?= $pessoaSelecionada ? 'selected' : '' ?>><?= htmlspecialchars($nomeCompletoPessoa) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                            <input type="date" name="dataRegistoEditar" class="input" value="<?= htmlspecialchars($editData) ?>" required>
                        </div>
                        <div class="acao"><button type="submit" name="actualizar" class="botao">Guardar</button></div>
                    </form>
                </div>
            </div>
        <?php } ?>
        <!-- MODAIS: REMOVER NACIONALIDADE -->
        <?php foreach ($nacionalidades as $nacionalidade) { ?>
            <div class="modalFundo" id="remover-<?= $nacionalidade->getCodigoNacionalidade() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Nacionalidade.php">
                        <input type="hidden" name="id" value="<?= $nacionalidade->getCodigoNacionalidade() ?>">
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>ID</strong></label></div>
                            <input type="text" class="input" value="<?= $nacionalidade->getCodigoNacionalidade() ?>" disabled>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                            <input type="text" class="input" value="<?= htmlspecialchars($nacionalidade->getNomeNacionalidade()) ?>" disabled>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>País</strong></label></div>
                            <input type="text" class="input" value="<?= htmlspecialchars($nacionalidade->getNomePais()) ?>" disabled>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <input type="text" class="input" value="<?= htmlspecialchars($nacionalidade->getNomePessoa()) ?>" disabled>
                        </div>
                        <div class="acao"><button type="submit" class="botao-remover" name="remover">Confirmar Remoção</button></div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</body>
</html>