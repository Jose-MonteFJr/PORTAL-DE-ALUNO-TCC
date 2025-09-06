
<?php
// Arquivo para excluir o usuário
require_once '../conexaoBD/db.php';
require_once __DIR__ . '/csrf.php';
verify_csrf_or_die();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Método não permitido.');
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit('ID inválido.');
}

$pdo = getPDO();
$stmt = $pdo->prepare("DELETE FROM aluno WHERE id = :id");
$stmt->execute([':id' => $id]);

header('Location: index.php');
exit;
