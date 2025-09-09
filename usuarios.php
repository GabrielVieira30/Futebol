<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login_page.php");
    exit;
}

// Check if user is admin
if ($_SESSION["username"] !== "admin") {
    header("Location: index.php");
    exit;
}

// Database connection
$mysqli = new mysqli("localhost", "root", "root", "login_db");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

// Handle form submission for adding new user
$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_user'])) {
    $new_username = trim($_POST["username"] ?? "");
    $new_password = $_POST["password"] ?? "";

    if (empty($new_username) || empty($new_password)) {
        $message = "Usuário e senha são obrigatórios!";
        $message_type = "danger";
    } else {
        // Check if username already exists
        $stmt = $mysqli->prepare("SELECT pk FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Este nome de usuário já existe!";
            $message_type = "danger";
        } else {
            // Insert new user
            $stmt = $mysqli->prepare("INSERT INTO usuarios (username, senha) VALUES (?, ?)");
            $stmt->bind_param("ss", $new_username, $new_password);

            if ($stmt->execute()) {
                $message = "Usuário criado com sucesso!";
                $message_type = "success";
            } else {
                $message = "Erro ao criar usuário: " . $stmt->error;
                $message_type = "danger";
            }
        }
        $stmt->close();
    }
}

// Handle user deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Don't allow deleting admin
    $stmt = $mysqli->prepare("SELECT username FROM usuarios WHERE pk = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_data = $result->fetch_assoc();
    $stmt->close();

    if ($user_data && $user_data['username'] !== 'admin') {
        $stmt = $mysqli->prepare("DELETE FROM usuarios WHERE pk = ?");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            $message = "Usuário excluído com sucesso!";
            $message_type = "success";
        } else {
            $message = "Erro ao excluir usuário: " . $stmt->error;
            $message_type = "danger";
        }
        $stmt->close();
    } else {
        $message = "Não é possível excluir o usuário admin!";
        $message_type = "danger";
    }
}

// Get all users
$result = $mysqli->query("SELECT pk, username FROM usuarios ORDER BY username");
$users = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $result->free();
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-futbol me-2"></i>
                Sistema Futebol
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="times.php">Times</a>
                <a class="nav-link" href="jogadores.php">Jogadores</a>
                <a class="nav-link" href="partidas.php">Partidas</a>
                <a class="nav-link active" href="usuarios.php">Usuários</a>
            </div>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    Bem-vindo, <?php echo htmlspecialchars($_SESSION["username"]); ?>!
                </span>
                <a href="login_page.php?logout=1" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Sair
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-users me-2"></i>Gerenciar Usuários</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Adicionar Novo Usuário</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Nome de Usuário *</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Senha *</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                    <button type="submit" name="add_user" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Adicionar Usuário
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Usuários Existentes</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($users)): ?>
                                    <p class="text-muted">Nenhum usuário encontrado.</p>
                                <?php else: ?>
                                    <div class="list-group">
                                        <?php foreach ($users as $user): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                                    <?php if ($user['username'] === 'admin'): ?>
                                                        <span class="badge bg-primary">Admin</span>
                                                    <?php endif; ?>
                                                </div>
                                                <?php if ($user['username'] !== 'admin'): ?>
                                                    <button type="button" class="btn btn-sm btn-danger"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal<?php echo $user['pk']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>

                                                    <!-- Modal de Confirmação -->
                                                    <div class="modal fade" id="deleteModal<?php echo $user['pk']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja excluir o usuário "<?php echo htmlspecialchars($user['username']); ?>"?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <a href="usuarios.php?delete=<?php echo $user['pk']; ?>" class="btn btn-danger">Excluir</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
