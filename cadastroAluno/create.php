
<?php
//Arquivo para criar um novo usuário no sistema.
require_once '../conexaoBD/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$errors = [];
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Verifica o token CSRF
    verify_csrf_or_die();
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name === '')  $errors[] = 'Nome é obrigatório.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (strlen($pass) < 8) $errors[] = 'Senha deve ter pelo menos 8 caracteres.';

    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash) VALUES (:n, :e, :p)");
            $stmt->execute([':n' => $name, ':e' => $email, ':p' => $hash]);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Email já cadastrado.';
            } else {
                $errors[] = 'Erro ao inserir: ' . $e->getMessage();
            }
        }
    }
}

require_once '../includes/header.php';
$token = csrf_token();
?>
<div class="row">
  <div class="col-lg-6">
    <h2>Novo Usuário</h2>
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
      <!-- BLOCO DADOS PESSOAIS -->
      <fieldset>
        <legend>DADOS PESSOAIS</legend>
        <!-- NOME-->
        <div class="mb-3">
          <label class="form-label" for="Iname">NOME COMPLETO: </label> 
          <input type="text" name="name" id="Iname" class="form-control" value="<?= htmlspecialchars($name) ?>" placeholder="Digite o nome completo" required>
        </div>
        <!-- CPF -->
        <div class="mb-3">
          <label class="form-label" for="Icpf">CPF: </label> 
          <input type="text" name="cpf" id="Icpf" class="form-control" value="<?= htmlspecialchars($name) ?>" placeholder="Digite o CPF" required>
        </div>

      </fieldset>
      <!-- BLOCO ENDEREÇO -->
      <fieldset>
        <legend>ENDEREÇO</legend>

      </fieldset>
      <!-- BLOCO CONTATO -->
      <fieldset>
        <legend>CONTATO</legend>
        

      </fieldset>
      <!-- BLOCO IDENTIFICAÇÃO -->
      <fieldset>
        <legend>IDENTIFICAÇÃO</legend>
        

      </fieldset>
      <!-- BOTÕES -->
      <div class="d-flex gap-2">
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
