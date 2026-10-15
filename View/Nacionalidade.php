<?php
    include_once(__DIR__ . "/../Controller/controllerNacionalidade.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    include_once(__DIR__ . "/../Controller/controllerLog.php");
    verificarLogin();
    Sessao::exigirPermissao('ver');

    $erro = "";

    // REMOVER NACIONALIDADE
    if (isset($_POST['remover'])) {
        Sessao::exigirPermissao('remover');
        if (remover($_POST['id'])) {
            registarLog(Sessao::getUsername(), Sessao::getNivelAcesso(), "Removeu nacionalidade " . $_POST['id']);
            header("Location: Nacionalidade.php");
            exit;
        }
    }

    // CADASTRAR NACIONALIDADE
    if (isset($_POST['gravar'])) {
        Sessao::exigirPermissao('criar');
        if (!empty($_POST['nome']) && !empty($_POST['nome_continente']) && !empty($_POST['idpais']) && !empty($_POST['idpessoa']) && !empty($_POST['dataRegisto'])) {
            $nacionalidade = new Nacionalidade(null, $_POST['nome'], $_POST['dataRegisto'], $_POST['idpessoa'], $_POST['idpais'], $_POST['nome_continente']);
            $resultado = criar($nacionalidade);

            if (is_string($resultado) && $resultado !== "") {
                $erro = $resultado;
            } elseif ($resultado) {
                registarLog(Sessao::getUsername(), Sessao::getNivelAcesso(), "Criou nacionalidade " . $_POST['nome']);
                header("Location: Nacionalidade.php");
                exit;
            } else {
                $erro = "Não foi possível registar a nacionalidade.";
            }
        } else {
            $erro = "Preencha todos os campos obrigatórios.";
        }
    }

    // ACTUALIZAR NACIONALIDADE
    if (isset($_POST['actualizar'])) {
        Sessao::exigirPermissao('editar');
        if (!empty($_POST['id']) && !empty($_POST['nomeEditar']) && !empty($_POST['idpaisEditar']) && !empty($_POST['idpessoaEditar']) && !empty($_POST['nome_continenteEditar']) && !empty($_POST['dataRegistoEditar'])) {
            $nacionalidadeExistente = encontrar($_POST['id']);
            if ($nacionalidadeExistente) {
                $nacionalidadeExistente->setNomeNacionalidade($_POST['nomeEditar']);
                $nacionalidadeExistente->setDataRegisto($_POST['dataRegistoEditar']);
                $nacionalidadeExistente->setNomePessoa($_POST['idpessoaEditar']);
                $nacionalidadeExistente->setNomePais($_POST['idpaisEditar']);
                $nacionalidadeExistente->setNomeContinente($_POST['nome_continenteEditar']);
                if (editar($nacionalidadeExistente)) {
                    registarLog(Sessao::getUsername(), Sessao::getNivelAcesso(), "Editou nacionalidade " . $_POST['id']);
                    header("Location: Nacionalidade.php");
                    exit;
                }
            }
        }
    }

    // PESQUISAR E LISTAR
    $pesquisa = isset($_GET['pesquisa']) ? $_GET['pesquisa'] : "";
    $nacionalidades = pesquisar($pesquisa);
    $pessoas = listarPessoasSelect();
    $paises = listarPaisesSelect();
    $continentes = listarContinentesSelect();

    // PRESERVAR OS DADOS DO FORMULÁRIO NOVO
    $novoNome = "";
    $novoContinente = "";
    $novoPais = "";
    $novoPessoa = "";
    $novoData = "";
    if (isset($_POST['filtrar'])) {
        $novoNome = $_POST['nome'] ?? "";
        $novoContinente = $_POST['nome_continente'] ?? "";
        $novoPais = $_POST['idpais'] ?? "";
        $novoPessoa = $_POST['idpessoa'] ?? "";
        $novoData = $_POST['dataRegisto'] ?? "";
    }
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Listar Nacionalidade</title>
    <link rel="stylesheet" href="../assets/estilo.css">
</head>

<body>
    <!-- MENU DE NAVEGAÇÃO -->
    <div class="navbar">
        <div class="marca">
            <div class="titulo">GESTAO<br />DE PESSOAS</div>
        </div>
        <nav class="navlinks">
            <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
            <a href="Pessoa.php"><span class="icone"></span>Pessoa</a>
            <a href="Continente.php"><span class="icone"></span>Continente</a>
            <a href="Pais.php"><span class="icone"></span>Pais</a>
            <a href="Nacionalidade.php" class="on"><span class="icone"></span>Nacionalidade</a>
                <?php Sessao::linkMenu('Cadastro.php', 'Cadastro', 'cadastro'); ?>
                <?php Sessao::linkMenu('Perfil.php', 'Perfis', 'perfis'); ?>
                <?php Sessao::linkMenu('log.php', 'Log', 'logs'); ?>
        </nav>
        <div class="utilizador">
            <div class="utilizadorNome"><?= Sessao::getNomeCompleto() ?></div>
            <span class="perfil"><?= Sessao::getNivelAcesso() ?></span>
        </div>
        <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
    </div>

    <div class="mae">
        <!-- CABEÇALHO -->
        <div class="cabecalho">
            <h1>Nacionalidades</h1>
            <div class="topo">
                <?php if ($erro != "") { ?>
                    <p class="erro"><?= $erro ?></p>
                <?php } ?>
                <form class="busca" method="get" action="Nacionalidade.php">
                    <input type="text" name="pesquisa" placeholder="Pesquisar nacionalidade" value="<?= $pesquisa ?>" />
                </form>
                <?php if (Sessao::podeFazer('criar')) { ?>
                    <a class="botao" href="#novo">Nova Nacionalidade</a>
                <?php } ?>
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
                            <th>Pais</th>
                            <th>Data de Registo</th>
                            <th>Pessoa</th>
                            <th>Continente</th>
                            <th class="coluna-acao">Accao</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($nacionalidades) == 0) { ?>
                            <tr><td colspan="7" class="vazio"><?= $pesquisa != "" ? "Nacionalidade nao encontrada" : "Nenhuma nacionalidade registada" ?></td></tr>
                        <?php } else { foreach ($nacionalidades as $n) { ?>
                            <tr>
                                <td><?= $n->getCodigoNacionalidade() ?></td>
                                <td><?= $n->getNomeNacionalidade() ?></td>
                                <td><?= $n->getNomePais() ?></td>
                                <td><?= $n->getDataRegisto() ?></td>
                                <td><?= $n->getNomePessoa() ?></td>
                                <td><?= $n->getNomeContinente() ?></td>
                                <td>
                                    <div class="acoes">
                                        <?php if (Sessao::podeFazer('editar')) { ?>
                                            <a class="botao-atualizar" href="#editar-<?= $n->getCodigoNacionalidade() ?>">Editar</a>
                                        <?php } ?>
                                        <?php if (Sessao::podeFazer('remover')) { ?>
                                            <a class="botao-remover" href="#remover-<?= $n->getCodigoNacionalidade() ?>">Remover</a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } } ?>
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
                            <option value="" disabled <?= $novoContinente == "" ? "selected" : "" ?>>Selecione</option>
                            <?php foreach ($continentes as $c) { ?>
                                <option value="<?= $c['nomeContinente'] ?>" <?= $c['nomeContinente'] == $novoContinente ? "selected" : "" ?>><?= $c['nomeContinente'] ?></option>
                            <?php } ?>
                        </select>
                        <button type="submit" name="filtrar" class="botao-atualizar" formnovalidate>Escolher continente</button>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Pais</strong></label></div>
                        <select name="idpais" class="input" required>
                            <?php if ($novoContinente == "") { ?>
                                <option value="">Escolha primeiro o continente</option>
                            <?php } else { ?>
                                <option value="">Selecione um pais</option>
                                <?php foreach ($paises as $p) { if ($p['nomeContinente'] == $novoContinente) { ?>
                                    <option value="<?= $p['codigoPais'] ?>" <?= (string)$p['codigoPais'] === (string)$novoPais ? "selected" : "" ?>><?= $p['nomePais'] ?></option>
                                <?php } } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                        <input type="text" name="nome" class="input" value="<?= $novoNome ?>" required />
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                        <select name="idpessoa" class="input" required>
                            <option value="">Selecione uma pessoa</option>
                            <?php foreach ($pessoas as $pe) { ?>
                                <option value="<?= $pe['codigo'] ?>" <?= (string)$pe['codigo'] === (string)$novoPessoa ? "selected" : "" ?>><?= $pe['nome'] . " " . $pe['apelido'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="campo">
                        <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                        <input type="date" name="dataRegisto" class="input" value="<?= $novoData ?>" required />
                    </div>
                    <div class="acao">
                        <button type="submit" name="gravar" class="botao">Guardar</button>
                    </div>
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
            $filtrouEste = isset($_POST['filtrarEditar']) && isset($_POST['id']) && (string)$_POST['id'] === (string)$n->getCodigoNacionalidade();
            if ($filtrouEste) {
                $editNome = $_POST['nomeEditar'] ?? "";
                $editContinente = $_POST['nome_continenteEditar'] ?? "";
                $editPais = "";
                $editPessoa = $_POST['idpessoaEditar'] ?? "";
                $editData = $_POST['dataRegistoEditar'] ?? "";
            } ?>
            <div class="modalFundo" id="editar-<?= $n->getCodigoNacionalidade() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Editar Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Nacionalidade.php#editar-<?= $n->getCodigoNacionalidade() ?>">
                        <input type="hidden" name="id" value="<?= $n->getCodigoNacionalidade() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Continente</strong></label></div>
                            <select name="nome_continenteEditar" class="input" required>
                                <?php foreach ($continentes as $c) { ?>
                                    <option value="<?= $c['nomeContinente'] ?>" <?= $c['nomeContinente'] == $editContinente ? "selected" : "" ?>><?= $c['nomeContinente'] ?></option>
                                <?php } ?>
                            </select>
                            <button type="submit" name="filtrarEditar" class="botao-atualizar" formnovalidate>Escolher continente</button>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pais</strong></label></div>
                            <select name="idpaisEditar" class="input" required>
                                <option value="">Selecione um pais</option>
                                <?php foreach ($paises as $p) { if ($p['nomeContinente'] == $editContinente) { ?>
                                    <option value="<?= $p['codigoPais'] ?>" <?= $p['nomePais'] == $editPais ? "selected" : "" ?>><?= $p['nomePais'] ?></option>
                                <?php } } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                            <input type="text" name="nomeEditar" class="input" value="<?= $editNome ?>" required />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <select name="idpessoaEditar" class="input" required>
                                <?php foreach ($pessoas as $pe) {
                                    $nomeCompleto = $pe['nome'] . " " . $pe['apelido'];
                                    $pessoaCerta = $filtrouEste ? (string)$pe['codigo'] === (string)$editPessoa : $nomeCompleto === $editPessoa; ?>
                                    <option value="<?= $pe['codigo'] ?>" <?= $pessoaCerta ? "selected" : "" ?>><?= $nomeCompleto ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Data de Registo</strong></label></div>
                            <input type="date" name="dataRegistoEditar" class="input" value="<?= $editData ?>" required />
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao" name="actualizar">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>

        <!-- MODAIS: REMOVER NACIONALIDADE -->
        <?php foreach ($nacionalidades as $n) { ?>
            <div class="modalFundo" id="remover-<?= $n->getCodigoNacionalidade() ?>">
                <a href="#" class="modalOverlay" aria-label="Fechar"></a>
                <div class="modalCaixa">
                    <div class="modalTopo">
                        <h2 class="titulo">Remover Nacionalidade</h2>
                        <a href="#" class="modalFechar">x</a>
                    </div>
                    <form method="post" action="Nacionalidade.php">
                        <input type="hidden" name="id" value="<?= $n->getCodigoNacionalidade() ?>" />
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>ID</strong></label></div>
                            <input type="text" class="input" value="<?= $n->getCodigoNacionalidade() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Nome da nacionalidade</strong></label></div>
                            <input type="text" class="input" value="<?= $n->getNomeNacionalidade() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pais</strong></label></div>
                            <input type="text" class="input" value="<?= $n->getNomePais() ?>" disabled />
                        </div>
                        <div class="campo">
                            <div class="campoTipo"><label class="label"><strong>Pessoa</strong></label></div>
                            <input type="text" class="input" value="<?= $n->getNomePessoa() ?>" disabled />
                        </div>
                        <div class="acao">
                            <button type="submit" class="botao-remover" name="remover">Confirmar Remocao</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php } ?>
    </div>
</body>

</html>