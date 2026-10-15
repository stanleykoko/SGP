<?php
    include_once(__DIR__ . "/../Controller/controllerLog.php");
    include_once(__DIR__ . "/../Controller/sessao.php");
    Sessao::exigirPermissao('logs');

    $campos = array(
        "accao"     => "Ação",
        "usuario"   => "Utilizador",
        "perfil"    => "Perfil",
        "dataAccao" => "Data de Ação"
    );

    $campo = isset($_GET['campo']) ? trim($_GET['campo']) : "";
    $valor = isset($_GET['valor']) ? trim($_GET['valor']) : "";

    // se o campo for invalido, pesquisa em todos os campos (mantem o texto escrito)
    if ($campo !== "" && !array_key_exists($campo, $campos)) {
        $campo = "";
    }

    $logs = listarLogs($campo, $valor);
?>
<!DOCTYPE html>
<html lang="pt">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Listar Log</title>

        <link rel="stylesheet" href="../assets/estilo.css">
        <style>
            .cabecalho .pesquisa {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .cabecalho .pesquisa select,
            .cabecalho .pesquisa input[type="text"] {
                height: 48px;
                padding: 0 18px;
                background: #fff;
                border: 1px solid #e6d5bf;
                border-radius: 999px;
                font-family: inherit;
                font-size: 14px;
                color: inherit;
                outline: none;
                box-sizing: border-box;
            }

            .cabecalho .pesquisa select {
                cursor: pointer;
            }

            .cabecalho .pesquisa input[type="text"] {
                width: 260px;
            }

            .cabecalho .pesquisa select:focus,
            .cabecalho .pesquisa input[type="text"]:focus {
                border-color: #6b4435;
            }
        </style>
    </head>

    <body>
        <div class="navbar">
            <div class="marca">
                <div class="titulo">GESTAO<br />DE PESSOAS</div>
            </div>
            <nav class="navlinks">
                <a href="TelaPrincipal.php"><span class="icone"></span>Inicio</a>
                <a href="Pessoa.php"><span class="icone"></span>Pessoa</a>
                <a href="Continente.php"><span class="icone"></span>Continente</a>
                <a href="Pais.php"><span class="icone"></span>Pais</a>
                <a href="Nacionalidade.php"><span class="icone"></span>Nacionalidade</a>
                <?php Sessao::linkMenu('Cadastro.php', 'Cadastro', 'cadastro'); ?>
                <?php Sessao::linkMenu('Perfil.php', 'Perfis', 'perfis'); ?>
                <?php Sessao::linkMenu('log.php', 'Log', 'logs', true); ?>

            </nav>
            <div class="utilizador">
                <div class="utilizadorNome"><?= Sessao::getNomeCompleto() ?></div>
                <span class="perfil"><?= Sessao::getNivelAcesso() ?></span>
            </div>
            <a href="Logout.php" class="sair"><span class="icone"></span>Log out</a>
        </div>

        <div class="mae">
            <div class="cabecalho">
                <h1>Registo de Accoes</h1>

                <form method="get" action="" class="pesquisa">
                    <select name="campo" id="campo">
                        <option value="">Todos os campos</option>
                        <?php foreach ($campos as $chave => $nome) { ?>
                            <option value="<?= $chave ?>" <?= ($chave === $campo) ? "selected" : "" ?>><?= $nome ?></option>
                        <?php } ?>
                    </select>

                    <input type="text" name="valor" id="valor"
                           placeholder="Pesquisar e carregar Enter"
                           value="<?= htmlspecialchars($valor) ?>">
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
                                <th>Accao</th>
                                <th>Data</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($logs) == 0) { ?>
                    <tr><td colspan="5" class="vazio">Nenhum registo encontrado</td></tr>
                    <?php } else { foreach ($logs as $log) { ?>
                            <tr>
                                <td><?= $log->getCodigoLog() ?></td>
                                <td><?= $log->getUsuario() ?></td>
                                <td><?= $log->getPerfil() ?></td>
                                <td><?= $log->getAccao() ?></td>
                                <td><?= $log->getDataAccao() ?></td>
                            </tr>
                            <?php } } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </body>
</html>