<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login_page.php");
    exit;
}

include_once 'config/database.php';
include_once 'models/Time.php';
include_once 'models/Jogador.php';
include_once 'models/Partida.php';

$database = new Database();
$db = $database->getConnection();

$time = new Time($db);
$jogador = new Jogador($db);
$partida = new Partida($db);

$total_times = $time->count();
$total_jogadores = $jogador->count();
$total_partidas = $partida->count();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gerenciamento de Futebol</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .dashboard-card {
            transition: transform 0.3s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-futbol me-2"></i>
                Sistema Futebol
            </a>
            <div class="navbar-nav">
                <a class="nav-link" href="index.php">Dashboard</a>
                <a class="nav-link" href="times.php">Times</a>
                <a class="nav-link" href="jogadores.php">Jogadores</a>
                <a class="nav-link" href="partidas.php">Partidas</a>
                <?php if ($_SESSION["username"] === "admin"): ?>
                    <a class="nav-link" href="usuarios.php">Usuários</a>
                <?php endif; ?>
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
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card text-center p-3">
                    <div class="card-body">
                        <h1 class="display-4"><?php echo $total_times; ?></h1>
                        <h5><i class="fas fa-users me-2"></i>Times</h5>
                        <a href="times.php" class="btn btn-light btn-sm mt-2">Gerenciar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card text-center p-3">
                    <div class="card-body">
                        <h1 class="display-4"><?php echo $total_jogadores; ?></h1>
                        <h5><i class="fas fa-user me-2"></i>Jogadores</h5>
                        <a href="jogadores.php" class="btn btn-light btn-sm mt-2">Gerenciar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card text-center p-3">
                    <div class="card-body">
                        <h1 class="display-4"><?php echo $total_partidas; ?></h1>
                        <h5><i class="fas fa-calendar-alt me-2"></i>Partidas</h5>
                        <a href="partidas.php" class="btn btn-light btn-sm mt-2">Gerenciar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Gerenciar Times</h5>
                    </div>
                    <div class="card-body">
                        <p>Cadastre, edite e visualize os times do campeonato.</p>
                        <a href="times.php" class="btn btn-primary">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Gerenciar Jogadores</h5>
                    </div>
                    <div class="card-body">
                        <p>Gerencie os jogadores de cada time com suas posições e números.</p>
                        <a href="jogadores.php" class="btn btn-success">Acessar</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card dashboard-card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Gerenciar Partidas</h5>
                    </div>
                    <div class="card-body">
                        <p>Registre e consulte as partidas do campeonato.</p>
                        <a href="partidas.php" class="btn btn-info">Acessar</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Instruções</h5>
                    </div>
                    <div class="card-body">
                        <ol>
                            <li>Primeiro, importe o banco de dados executando o script <code>db/futebol_db.sql</code> no phpMyAdmin</li>
                            <li>Configure as credenciais do banco no arquivo <code>config/database.php</code> se necessário</li>
                            <li>Use os menus acima para gerenciar times, jogadores e partidas</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

