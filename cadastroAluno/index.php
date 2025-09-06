
<?php
// Arquivo para visualizar os usuários
require_once '../conexaoBD/db.php';
require_once '../includes/header.php';

$pdo = getPDO();

$search = trim($_GET['q'] ?? '');
if ($search !== '') {
    $stmt = $pdo->prepare("SELECT * FROM pessoa WHERE nome LIKE :q1 OR email LIKE :q2 ORDER BY id DESC"); // Alterar
    $stmt->execute([
      ':q1' => '%' . $search . '%',
      ':q2' => '%' . $search . '%'
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM pessoa ORDER BY id_pessoa DESC");
}
$pessoa = $stmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Usuários</h2>
  <a href="create.php" class="btn btn-primary">+ Novo Usuário</a>
</div>

<form class="row g-2 mb-3" method="get">
  <div class="col-auto">
    <input type="text" class="form-control" name="q" placeholder="Buscar por nome ou email" value="<?= htmlspecialchars($search) ?>">
  </div>
  <div class="col-auto">
    <button class="btn btn-outline-secondary">Buscar</button>
  </div>
  <?php if ($search !== ''): ?>
  <div class="col-auto">
    <a class="btn btn-link" href="index.php">Limpar</a>
  </div>
  <?php endif; ?>
</form>

<div class="card">
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead class="table-dark">
        <tr>
          <th>#</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Criado em</th>
          <th style="width:160px">Ações</th>
        </tr>
      </thead>
      <tbody>
      <?php if (!$pessoa): ?>
        <tr><td colspan="5" class="text-center">Nenhum usuário encontrado.</td></tr>
      <?php else: foreach ($pessoa as $u): ?>
        <tr>
          <td><?= (int)$u['id_usuario'] ?></td>
          <td><?= htmlspecialchars($u['nome']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['created_at']) ?></td>
          <td>
            <a class="btn btn-sm btn-warning" href="edit.php?id=<?= (int)$u['id_usuario'] ?>">Editar</a>
            <form action="delete.php" method="post" class="d-inline">
              <?php
              require_once __DIR__ . '/csrf.php';
              $token = csrf_token();
              ?>
              <input type="hidden" name="csrf_token" value="<?= $token ?>">
              <input type="hidden" name="id_usuario" value="<?= (int)$u['id_usuario'] ?>">
              <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir este usuário?')">Excluir</button>
            </form>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
