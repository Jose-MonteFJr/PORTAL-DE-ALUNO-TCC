
<?php
// Arquivo para editar o usuário
require_once '../conexaoBD/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM aluno WHERE id = :id");
$stmt->execute([':id' => $id]);
$aluno = $stmt->fetch();

if (!$aluno) {
    http_response_code(404);
    exit('Usuário não encontrado.');
}

$errors = [];
$nome = $aluno['nome'];
$email = $aluno['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_die();
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($nome === '')  $errors[] = 'Nome é obrigatório.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';

    if (!$errors) {
        try {
            if ($pass !== '') {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE aluno SET nome=:n, email=:e, password_hash=:p WHERE id=:id");
                $stmt->execute([':n'=>$nome, ':e'=>$email, ':p'=>$hash, ':id'=>$id]);
            } else {
                $stmt = $pdo->prepare("UPDATE aluno SET nome=:n, email=:e WHERE id=:id");
                $stmt->execute([':n'=>$nome, ':e'=>$email, ':id'=>$id]);
            }
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Email já cadastrado.';
            } else {
                $errors[] = 'Erro ao atualizar: ' . $e->getMessage();
            }
        }
    }
}

require_once '../includes/header.php';
$token = csrf_token();
?>
<div class="row">
  <div class="col-lg-6">
    <h2>Editar Usuário #<?= (int)$aluno['id'] ?></h2>
    <?php if ($errors): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $token ?>">
      <div class="mb-3">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Senha (deixe em branco para manter)</label>
        <input type="password" name="password" class="form-control">
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
