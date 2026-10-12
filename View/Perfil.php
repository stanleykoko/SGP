<?php
require_once __DIR__ . '/../Controller/controllerPerfil.php';

// O perfil de quem está logado vem da sessão
$meuPerfil = encontraPerfilDaSessao();

// Só entra quem tem a permissão "Gerir perfis"
if (!$meuPerfil || !$meuPerfil->getPerfis()) {
  header('Location: TelaPrincipal.php');
  exit;
}
$podeLogs = $meuPerfil->getLogs();

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
$tab = 'lista';

/* ================= ACÇÕES (POST) ================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $acao = $_POST['acao'] ?? '';

  if ($acao === 'criar_perfil') {
    $nome = trim($_POST['nome_perfil'] ?? '');
    if ($nome === '') {
      $tab = 'cad';
      $msg = 'O nome do perfil é obrigatório.';
    } else {
      $novo = new Perfil(null, $nome, trim($_POST['descricao'] ?? ''), 1, lerCheck('criar'), lerCheck('editar'), lerCheck('remover'), lerCheck('utilizadores'), lerCheck('logs'), lerCheck('perfis'));
      $msg = criarPerfil($novo);
      $tab = ($msg === 'Perfil criado') ? 'lista' : 'cad';
    }
  }

  if ($acao === 'atualizar_perfil') {
    $nome = trim($_POST['nome_perfil'] ?? '');
    if ($nome === '') {
      $msg = 'O nome do perfil é obrigatório.';
    } else {
      $edit = new Perfil((int)$_POST['id_perfil'], $nome, trim($_POST['descricao'] ?? ''), 1, lerCheck('criar'), lerCheck('editar'), lerCheck('remover'), lerCheck('utilizadores'), lerCheck('logs'), lerCheck('perfis'));
      $msg = actualizarPerfil($edit);
    }
  }

  if ($acao === 'remover_perfil') {
    $id  = (int)($_POST['sel_perfil'] ?? 0);
    $msg = ($id === 0) ? 'Seleccione um perfil.' : removerPerfil($id);
  }
}

/* ================= BOTÃO EDITAR (GET) ================= */
$edPerfil = null;
if (($_GET['acao'] ?? '') === 'editar_perfil') {
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
?>
<!DOCTYPE html>
<html lang="pt">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfis | Gestão de Pessoas</title>
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
          <li><a href="Cadastro.php">Utilizadores</a></li>
          <li><a href="Perfil.php" class="active">Perfis</a></li>
          <?php if ($podeLogs): ?><li><a href="Logs.php">Auditoria</a></li><?php endif; ?>
          <li><a href="LogOut.php">Sair</a></li>
        </ul>
      </nav>
    </aside>

    <div class="containermain">
      <header>
        <div class="pagetitle">
          <h2>Sistema de Gestão de Pessoas</h2>
          <p>Gestão de perfis</p>
        </div>
        <div class="userdetails">
          <div class="userdetailstxt">
            <p><?= htmlspecialchars($_SESSION['username'] ?? '') ?></p>
            <p><?= htmlspecialchars($meuPerfil->getNomePerfil()) ?></p>
          </div>
        </div>
      </header>

      <main>
        <div class="card">
          <input type="radio" name="tabs" id="tab-cad" class="tab-toggle" <?= $tab === 'cad' ? 'checked' : '' ?>>
          <input type="radio" name="tabs" id="tab-lista" class="tab-toggle" <?= $tab === 'lista' ? 'checked' : '' ?>>

          <div class="card-header">
            <div class="tabs">
              <label for="tab-cad" class="tab-label">Criar Perfil</label>
              <label for="tab-lista" class="tab-label">Lista de Perfis</label>
            </div>
          </div>

          <?php if ($msg): ?>
            <p class="msg"><?= htmlspecialchars($msg) ?></p>
          <?php endif; ?>

          <!-- ===== CRIAR PERFIL ===== -->
          <section class="panel panel-cad">
            <form method="post" action="Perfil.php">
              <input type="hidden" name="acao" value="criar_perfil">
              <div class="form-grid">
                <div class="label-group">
                  <label for="perfil-nome">Nome do Perfil *</label>
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
                <button class="btn btn-primary" type="submit">Salvar Perfil</button>
              </div>
            </form>
          </section>

          <!-- ===== LISTA DE PERFIS ===== -->
          <section class="panel panel-lista">
            <form method="post" action="Perfil.php">
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
        </div>
      </main>
    </div>
  </div>

  <!-- ============ MODAL: EDITAR PERFIL ============ -->
  <input type="checkbox" id="m-edit-perfil" class="modal-toggle" <?= $edPerfil ? 'checked' : '' ?>>
  <div class="modal-overlay">
    <div class="modal">
      <div class="modal-header">
        <h2>Editar Perfil</h2><label for="m-edit-perfil" class="modal-close">&times;</label>
      </div>
      <form method="post" action="Perfil.php">
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

</body>

</html>