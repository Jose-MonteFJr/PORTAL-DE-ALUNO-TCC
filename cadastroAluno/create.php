
<?php
//Arquivo para criar um novo usuário no sistema.
require_once '../conexaoBD/db.php';
require_once __DIR__ . '/csrf.php';

$pdo = getPDO();
$errors = [];
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Verifica o token CSRF
    verify_csrf_or_die();
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($nome === '')  $errors[] = 'Nome é obrigatório.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido.';
    if (strlen($pass) < 8) $errors[] = 'Senha deve ter pelo menos 8 caracteres.';

    if (!$errors) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO aluno (nome, email, password_hash) VALUES (:n, :e, :p)");
            $stmt->execute([':n' => $nome, ':e' => $email, ':p' => $hash]);
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
      
      <fieldset> <!-- BLOCO DADOS PESSOAIS -->
        <legend>DADOS PESSOAIS</legend>
        
        <div class="mb-3"> <!-- NOME -->
          <label class="form-label" for="iNome">NOME COMPLETO: </label> 
          <input type="text" name="nome" id="iNome" class="form-control" value="<?= htmlspecialchars($nome) ?>" placeholder="Digite o nome completo" required>
        </div>
        
       
      </fieldset>
     
      <fieldset>  <!-- BLOCO ENDEREÇO -->
        <legend>ENDEREÇO</legend>

      </fieldset>
      
      <fieldset> <!-- BLOCO CONTATO -->
        <legend>CONTATO</legend>
        
        <div class="mb-3"> <!-- EMAIL -->
        <label class="form-label" for="iEmail">EMAIL: </label> 
        <input type="email" name="email" id="iEmail" class="form-control" value="<?= htmlspecialchars($email) ?>" placeholder="Digite o email" required>
      </div>
      </fieldset>
     
      <fieldset>  <!-- BLOCO IDENTIFICAÇÃO -->
        <legend>IDENTIFICAÇÃO</legend>
        
          <div class="mb-3"> <!-- SENHA  -->
            <label class="form-label" for="iSenha">SENHA: </label> 
            <input type="password" name="password" id="iSenha" class="form-control" placeholder="Digite a senha" required>
        </div>
      </fieldset>
      
      <div class="d-flex gap-2"> <!-- BOTÕES -->
        <button class="btn btn-primary">Salvar</button>
        <a class="btn btn-secondary" href="index.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php require_once '../includes/footer.php'; ?>
