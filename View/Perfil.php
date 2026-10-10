<?php
require_once __DIR__ . '/../sessao.php';           // ajusta o caminho se o teu sessao.php estiver noutro sítio
require_once __DIR__ . '/../Controller/controllerPerfil.php';

verificarLogin();

// ---------- Quem está logado e que perfil tem ----------
$meuUsername = $_SESSION['username'] ?? '';
$meuPerfil   = encontraPerfilPorUsername($meuUsername);

if (!$meuPerfil) {
  header('Location: TelaPrincipal.php');
  exit;
}

$podeUtil   = $meuPerfil->getUtilizadores();  // gerir utilizadores
$podePerfis = $meuPerfil->getPerfis();        // gerir perfis
$podeLogs   = $meuPerfil->getLogs();

// Só entra quem pode gerir utilizadores ou perfis
if (!$podeUtil && !$podePerfis) {
  header('Location: TelaPrincipal.php');
  exit;
}

$SENHA_PADRAO = 'SGP@1234';

function sn($valor)
{
  return $valor ? 'Sim' : 'Não';
}

// Lê os checkboxes do formulário: marcado = 1, não marcado = 0
function lerCheck($nome)
{
  return isset($_POST['perm'][$nome]) ? 1 : 0;
}

$msg = '';
$tab = $podeUtil ? 'cad' : 'perfis';

/* ================= ACÇÕES (POST) ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? '';

  /* ----- Utilizadores (só com permissão "utilizadores") ----- */
  if ($podeUtil) {

    if ($acao === 'salvar') {
      $nome     = mysqli_real_escape_string($conexao, trim($_POST['nome']));
      $apelido  = mysqli_real_escape_string($conexao, trim($_POST['apelido']));
      $username = mysqli_real_escape_string($conexao, trim($_POST['username']));
      $contacto = mysqli_real_escape_string($conexao, trim($_POST['contacto']));
      $perfil   = (int)$_POST['perfil'];

      $result = mysqli_query($conexao, "select * from cadastro where username = '$username'");
      if (mysqli_num_rows($result) > 0) {
        $msg = 'Erro ao criar: o nome de utilizador já existe.';
      } else {
        $senhaHash = password_hash($SENHA_PADRAO, PASSWORD_DEFAULT);
        $sql = "insert into cadastro (nome, apelido, contacto, username, senha, codigoPerfil)
                values ('$nome', '$apelido', '$contacto', '$username', '$senhaHash', $perfil)";
        $msg = mysqli_query($conexao, $sql)
          ? 'Utilizador criado. Senha inicial: ' . $SENHA_PADRAO
          : 'Erro ao criar o utilizador.';
      }
    }

    if ($acao === 'atualizar') {
      $tab      = 'lista';
      $id       = (int)$_POST['id'];
      $contacto = mysqli_real_escape_string($conexao, trim($_POST['contacto']));
      $perfil   = (int)$_POST['perfil'];
      mysqli_query($conexao, "update cadastro set contacto = '$contacto', codigoPerfil = $perfil where Codigoutilizador = $id");
      $msg = 'Utilizador atualizado.';
    }

    if ($acao === 'remover') {
      $tab = 'lista';
      $id  = (int)($_POST['sel'] ?? 0);
      if ($id === 0) {
        $msg = 'Seleccione um utilizador.';
      } else {
        $result = mysqli_query($conexao, "select username from cadastro where Codigoutilizador = $id");
        $alvo = mysqli_fetch_assoc($result);
        if ($alvo && $alvo['username'] === $meuUsername) {
          $msg = 'Não pode remover o seu próprio utilizador.';
        } else {
          mysqli_query($conexao, "delete from cadastro where Codigoutilizador = $id");
          $msg = 'Utilizador removido.';
        }
      }
    }

    if ($acao === 'resetar') {
      $tab = 'lista';
      $id  = (int)($_POST['sel'] ?? 0);
      if ($id === 0) {
        $msg = 'Seleccione um utilizador.';
      } else {
        $senhaHash = password_hash($SENHA_PADRAO, PASSWORD_DEFAULT);
        mysqli_query($conexao, "update cadastro set senha = '$senhaHash' where Codigoutilizador = $id");
        $msg = 'Senha resetada para: ' . $SENHA_PADRAO;
      }
    }
  }

  /* ----- Perfis (só com permissão "perfis") ----- */
  if ($podePerfis) {

    if ($acao === 'criar_perfil') {
      $tab  = 'perfis';
      $nome = trim($_POST['nome_perfil'] ?? '');
      if ($nome === '') {
        $msg = 'O nome do perfil é obrigatório.';
      } else {
        $novo = new Perfil(null, $nome, trim($_POST['descricao'] ?? ''), 1, lerCheck('criar'), lerCheck('editar'), lerCheck('remover'), lerCheck('utilizadores'), lerCheck('logs'), lerCheck('perfis'));
        $msg = criarPerfil($novo);
      }
    }

    if ($acao === 'atualizar_perfil') {
      $tab  = 'perfis';
      $nome = trim($_POST['nome_perfil'] ?? '');
      if ($nome === '') {
        $msg = 'O nome do perfil é obrigatório.';
      } else {
        $edit = new Perfil((int)$_POST['id_perfil'], $nome, trim($_POST['descricao'] ?? ''), 1, lerCheck('criar'), lerCheck('editar'), lerCheck('remover'), lerCheck('utilizadores'), lerCheck('logs'), lerCheck('perfis'));
        $msg = actualizarPerfil($edit);
      }
    }

    if ($acao === 'remover_perfil') {
      $tab = 'perfis';
      $id  = (int)($_POST['sel_perfil'] ?? 0);
      $msg = ($id === 0) ? 'Seleccione um perfil.' : removerPerfil($id);
    }
  }
}

/* ================= BOTÕES EDITAR (GET) ================= */
$ed = null;
if ($podeUtil && ($_GET['acao'] ?? '') === 'editar') {
  $tab = 'lista';
  $id  = (int)($_GET['sel'] ?? 0);
  $result = mysqli_query($conexao, "select * from cadastro where Codigoutilizador = $id");
  $ed = mysqli_fetch_assoc($result);
  if (!$ed) $msg = 'Seleccione um utilizador.';
}

$edPerfil = null;
if ($podePerfis && ($_GET['acao'] ?? '') === 'editar_perfil') {
  $tab = 'perfis';
  $edPerfil = encontraPerfilPorId($_GET['sel_perfil'] ?? 0);
  if (!$edPerfil) {
    $msg = 'Seleccione um perfil.';
  } elseif ($edPerfil->getCodigoPerfil() <= 4) {
    $msg = 'Os perfis base não se editam.';
    $edPerfil = null;
  }
}

/* ================= DADOS PARA MOSTRAR ================= */
$perfis = listarPerfis();

$lista = array();
$sql = "select c.Codigoutilizador, c.nome, c.apelido, c.contacto, c.username, p.nomePerfil
        from cadastro c
        inner join perfil p on p.codigoPerfil = c.codigoPerfil
        order by c.nome";
$result = mysqli_query($conexao, $sql);
while ($linha = mysqli_fetch_assoc($result)) {
  array_push($lista, $linha);
}
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Administração | Gestão de Pessoas</title>
  <link rel="stylesheet" href="../assets/perfil.css">
</head>

<body>

  <div class="app-container">
    <aside>
      <div class="title">
        <h1>Gestão de Pessoas</h1>
      </div>
      <nav>
        <ul>
          <li class="secnav">Menu Principal</li>
          <li><a href="TelaPrincipal.php">Início</a></li>
          <li><a href="Pessoa.php">Pessoas</a></li>
          <li class="secnav">Cadastros</li>
          <li><a href="Continente.php">Continente</a></li>
          <li><a href="Pais.php">País</a></li>
          <li><a href="Nacionalidade.php">Nacionalidade</a></li>
          <li class="secnav">Acções</li>
          <li><a href="administracao.php" class="active">Administração</a></li>
          <?php if ($podeLogs): ?><li><a href="Logs.php">Auditoria</a></li><?php endif; ?>
          <li><a href="LogOut.php">Sair</a></li>
        </ul>
      </nav>
    </aside>

    <div class="containermain">
      <header>
        <div class="pagetitle">
          <h2>Sistema de Gestão de Pessoas</h2>
          <p>Administração de utilizadores</p>
        </div>
        <div class="userdetails">
          <div class="userdetailstxt">
            <p><?= htmlspecialchars($meuUsername) ?></p>
            <p><?= htmlspecialchars($meuPerfil->getNomePerfil()) ?></p>
          </div>
        </div>
      </header>

      <main>
        <div class="card">
          <input type="radio" name="tabs" id="tab-cad" class="tab-toggle" <?= $tab === 'cad' ? 'checked' : '' ?>>
          <input type="radio" name="tabs" id="tab-lista" class="tab-toggle" <?= $tab === 'lista' ? 'checked' : '' ?>>
          <input type="radio" name="tabs" id="tab-perfis" class="tab-toggle" <?= $tab === 'perfis' ? 'checked' : '' ?>>

          <div class="card-header">
            <div class="tabs">
              <?php if ($podeUtil): ?>
                <label for="tab-cad" class="tab-label">Cadastrar Utilizador</label>
                <label for="tab-lista" class="tab-label">Informações de Utilizador</label>
              <?php endif; ?>
              <?php if ($podePerfis): ?>
                <label for="tab-perfis" class="tab-label">Perfis</label>
              <?php endif; ?>
            </div>
          </div>

          <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
          <?php endif; ?>

          <?php if ($podeUtil): ?>
            <!-- ===== CADASTRAR UTILIZADOR ===== -->
            <section class="panel panel-cad">
              <form method="post" action="administracao.php">
                <input type="hidden" name="acao" value="salvar">
                <div class="form-grid">
                  <div class="label-group"><label for="cad-nome">Primeiro Nome *</label><input type="text" id="cad-nome" name="nome" required></div>
                  <div class="label-group"><label for="cad-apelido">Último Nome *</label><input type="text" id="cad-apelido" name="apelido" required></div>
                  <div class="label-group"><label for="cad-username">Nome de Utilizador *</label><input type="text" id="cad-username" name="username" required></div>
                  <div class="label-group">
                    <label for="cad-perfil">Perfil do Utilizador</label>
                    <select id="cad-perfil" name="perfil">
                      <?php foreach ($perfis as $p): ?>
                        <option value="<?= $p->getCodigoPerfil() ?>"><?= htmlspecialchars($p->getNomePerfil()) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="label-group full"><label for="cad-contacto">Contacto</label><input type="tel" id="cad-contacto" name="contacto"></div>
                </div>
                <div class="table-actions">
                  <button class="btn btn-primary" type="submit">Salvar Utilizador</button>
                </div>
              </form>
            </section>

            <!-- ===== LISTA DE UTILIZADORES ===== -->
            <section class="panel panel-lista">
              <form method="post" action="administracao.php">
                <div class="table-wrapper">
                  <table>
                    <thead>
                      <tr>
                        <th class="col-sel"></th>
                        <th>Código</th>
                        <th>Nome Completo</th>
                        <th>Username</th>
                        <th>Contacto</th>
                        <th>Perfil</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($lista as $u): ?>
                        <tr>
                          <td><input type="radio" name="sel" value="<?= $u['Codigoutilizador'] ?>"></td>
                          <td><?= $u['Codigoutilizador'] ?></td>
                          <td><?= htmlspecialchars($u['nome'] . ' ' . $u['apelido']) ?></td>
                          <td><?= htmlspecialchars($u['username']) ?></td>
                          <td><?= htmlspecialchars($u['contacto']) ?></td>
                          <td><?= htmlspecialchars($u['nomePerfil']) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <div class="table-actions">
                  <button class="btn btn-secondary btn-sm" type="submit" name="acao" value="remover">Remover Seleccionado</button>
                  <button class="btn btn-secondary btn-sm" type="submit" name="acao" value="resetar">Resetar Senha</button>
                  <button class="btn btn-primary btn-sm" type="submit" name="acao" value="editar" formmethod="get">Editar Utilizador</button>
                </div>
              </form>
            </section>
          <?php endif; ?>

          <?php if ($podePerfis): ?>
            <!-- ===== PERFIS ===== -->
            <section class="panel panel-perfis">
              <!-- Criar perfil novo -->
              <form method="post" action="administracao.php">
                <input type="hidden" name="acao" value="criar_perfil">
                <div class="form-grid">
                  <div class="label-group">
                    <label for="perfil-nome">Nome do Novo Perfil *</label>
                    <input type="text" id="perfil-nome" name="nome_perfil" required>
                  </div>
                  <div class="label-group">
                    <label for="perfil-desc">Descrição</label>
                    <input type="text" id="perfil-desc" name="descricao">
                  </div>
                  <div class="label-group full">
                    <label>Permissões</label>
                    <div class="perm-grid">
                      <label class="perm-check"><input type="checkbox" checked disabled> Ver</label>
                      <label class="perm-check"><input type="checkbox" name="perm[criar]" value="1"> Criar</label>
                      <label class="perm-check"><input type="checkbox" name="perm[editar]" value="1"> Editar</label>
                      <label class="perm-check"><input type="checkbox" name="perm[remover]" value="1"> Remover</label>
                      <label class="perm-check"><input type="checkbox" name="perm[utilizadores]" value="1"> Gerir utilizadores</label>
                      <label class="perm-check"><input type="checkbox" name="perm[logs]" value="1"> Ver logs</label>
                      <label class="perm-check"><input type="checkbox" name="perm[perfis]" value="1"> Gerir perfis</label>
                    </div>
                  </div>
                </div>
                <div class="table-actions">
                  <button class="btn btn-primary btn-sm" type="submit">Salvar Perfil</button>
                </div>
              </form>

              <!-- Lista de perfis -->
              <form method="post" action="administracao.php">
                <div class="table-wrapper">
                  <table>
                    <thead>
                      <tr>
                        <th class="col-sel"></th>
                        <th>Código</th>
                        <th>Perfil</th>
                        <th>Descrição</th>
                        <th>Ver</th>
                        <th>Criar</th>
                        <th>Editar</th>
                        <th>Remover</th>
                        <th>Utilizadores</th>
                        <th>Logs</th>
                        <th>Perfis</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($perfis as $p): ?>
                        <tr>
                          <td><input type="radio" name="sel_perfil" value="<?= $p->getCodigoPerfil() ?>"></td>
                          <td><?= $p->getCodigoPerfil() ?></td>
                          <td><?= htmlspecialchars($p->getNomePerfil()) ?></td>
                          <td><?= htmlspecialchars($p->getDescricao()) ?></td>
                          <td><?= sn($p->getVer()) ?></td>
                          <td><?= sn($p->getCriar()) ?></td>
                          <td><?= sn($p->getEditar()) ?></td>
                          <td><?= sn($p->getRemover()) ?></td>
                          <td><?= sn($p->getUtilizadores()) ?></td>
                          <td><?= sn($p->getLogs()) ?></td>
                          <td><?= sn($p->getPerfis()) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                <div class="table-actions">
                  <button class="btn btn-secondary btn-sm" type="submit" name="acao" value="remover_perfil">Remover Perfil</button>
                  <button class="btn btn-primary btn-sm" type="submit" name="acao" value="editar_perfil" formmethod="get">Editar Perfil</button>
                </div>
              </form>
            </section>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <?php if ($podeUtil): ?>
    <!-- ============ MODAL: EDITAR UTILIZADOR ============ -->
    <input type="checkbox" id="m-edit-user" class="modal-toggle" <?= $ed ? 'checked' : '' ?>>
    <div class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h2>Editar Utilizador</h2><label for="m-edit-user" class="modal-close">&times;</label>
        </div>
        <form method="post" action="administracao.php">
          <input type="hidden" name="acao" value="atualizar">
          <input type="hidden" name="id" value="<?= $ed ? $ed['Codigoutilizador'] : '' ?>">
          <div class="modal-body">
            <div class="form-grid">
              <div class="label-group"><label>Primeiro Nome</label><input type="text" value="<?= htmlspecialchars($ed ? $ed['nome'] : '') ?>" readonly></div>
              <div class="label-group"><label>Último Nome</label><input type="text" value="<?= htmlspecialchars($ed ? $ed['apelido'] : '') ?>" readonly></div>
              <div class="label-group full">
                <label for="eu-perfil">Perfil</label>
                <select id="eu-perfil" name="perfil">
                  <?php foreach ($perfis as $p): ?>
                    <option value="<?= $p->getCodigoPerfil() ?>" <?= ($ed && $ed['codigoPerfil'] == $p->getCodigoPerfil()) ? 'selected' : '' ?>><?= htmlspecialchars($p->getNomePerfil()) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="label-group full"><label for="eu-contacto">Contacto</label><input type="tel" id="eu-contacto" name="contacto" value="<?= htmlspecialchars($ed ? $ed['contacto'] : '') ?>"></div>
            </div>
          </div>
          <div class="modal-footer">
            <label for="m-edit-user" class="btn btn-secondary">Cancelar</label>
            <button class="btn btn-primary" type="submit">Atualizar Utilizador</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

  <?php if ($podePerfis): ?>
    <!-- ============ MODAL: EDITAR PERFIL ============ -->
    <input type="checkbox" id="m-edit-perfil" class="modal-toggle" <?= $edPerfil ? 'checked' : '' ?>>
    <div class="modal-overlay">
      <div class="modal">
        <div class="modal-header">
          <h2>Editar Perfil</h2><label for="m-edit-perfil" class="modal-close">&times;</label>
        </div>
        <form method="post" action="administracao.php">
          <input type="hidden" name="acao" value="atualizar_perfil">
          <input type="hidden" name="id_perfil" value="<?= $edPerfil ? $edPerfil->getCodigoPerfil() : '' ?>">
          <div class="modal-body">
            <div class="form-grid">
              <div class="label-group full">
                <label for="ep-nome">Nome do Perfil *</label>
                <input type="text" id="ep-nome" name="nome_perfil" required value="<?= htmlspecialchars($edPerfil ? $edPerfil->getNomePerfil() : '') ?>">
              </div>
              <div class="label-group full">
                <label for="ep-desc">Descrição</label>
                <input type="text" id="ep-desc" name="descricao" value="<?= htmlspecialchars($edPerfil ? $edPerfil->getDescricao() : '') ?>">
              </div>
              <div class="label-group full">
                <label>Permissões</label>
                <div class="perm-grid">
                  <label class="perm-check"><input type="checkbox" checked disabled> Ver</label>
                  <label class="perm-check"><input type="checkbox" name="perm[criar]" value="1" <?= ($edPerfil && $edPerfil->getCriar()) ? 'checked' : '' ?>> Criar</label>
                  <label class="perm-check"><input type="checkbox" name="perm[editar]" value="1" <?= ($edPerfil && $edPerfil->getEditar()) ? 'checked' : '' ?>> Editar</label>
                  <label class="perm-check"><input type="checkbox" name="perm[remover]" value="1" <?= ($edPerfil && $edPerfil->getRemover()) ? 'checked' : '' ?>> Remover</label>
                  <label class="perm-check"><input type="checkbox" name="perm[utilizadores]" value="1" <?= ($edPerfil && $edPerfil->getUtilizadores()) ? 'checked' : '' ?>> Gerir utilizadores</label>
                  <label class="perm-check"><input type="checkbox" name="perm[logs]" value="1" <?= ($edPerfil && $edPerfil->getLogs()) ? 'checked' : '' ?>> Ver logs</label>
                  <label class="perm-check"><input type="checkbox" name="perm[perfis]" value="1" <?= ($edPerfil && $edPerfil->getPerfis()) ? 'checked' : '' ?>> Gerir perfis</label>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <label for="m-edit-perfil" class="btn btn-secondary">Cancelar</label>
            <button class="btn btn-primary" type="submit">Atualizar Perfil</button>
          </div>
        </form>
      </div>
    </div>
  <?php endif; ?>

</body>

</html>