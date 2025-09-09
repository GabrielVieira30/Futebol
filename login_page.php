<?php
// login_page.php

// 1) Conexão
$mysqli = new mysqli("localhost", "root", "root", "login_db");
if ($mysqli->connect_errno) {
    die("Erro de conexão: " . $mysqli->connect_error);
}

session_start();

// 2) Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login_page.php");
    exit;
}

// 3) Login
$msg = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user = $_POST["username"] ?? "";
    $pass = $_POST["password"] ?? "";

    $stmt = $mysqli->prepare("SELECT pk, username, senha FROM usuarios WHERE username=? AND senha=?");
    $stmt->bind_param("ss", $user, $pass);
    $stmt->execute();
    $result = $stmt->get_result();
    $dados = $result->fetch_assoc();
    $stmt->close();

    if ($dados) {
        $_SESSION["user_id"] = $dados["pk"];
        $_SESSION["username"] = $dados["username"];
        header("Location: index.php");
        exit;
    } else {
        $msg = "Usuário ou senha incorretos!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema Futebol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header .fas {
            font-size: 48px;
            color: #667eea;
            margin-bottom: 20px;
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            color: white;
            font-weight: 600;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <i class="fas fa-futbol"></i>
        <h2>Sistema Futebol</h2>
        <p>Faça login para acessar o sistema</p>
    </div>

    <?php if (!empty($_SESSION["user_id"])): ?>
        <div class="text-center">
            <h4>Bem-vindo, <?= htmlspecialchars($_SESSION["username"]) ?>!</h4>
            <p>Você já está logado.</p>
            <a href="index.php" class="btn btn-login mb-2">Acessar Sistema</a>
            <br>
            <a href="?logout=1" class="text-muted">Sair</a>
        </div>
    <?php else: ?>
        <?php if ($msg): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= $msg ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">
                    <i class="fas fa-user me-2"></i>Usuário
                </label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">
                    <i class="fas fa-lock me-2"></i>Senha
                </label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Entrar
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">Dica: admin / 123456</small>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
