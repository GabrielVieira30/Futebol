<?php
session_start();

if (empty($_SESSION["user_id"])) {
    header("Location: login_page.php");
    exit;
}

include_once 'config/database.php';
include_once 'models/Time.php';
include_once 'models/Jogador.php';

$database = new Database();
$db = $database->getConnection();
$time = new Time($db);
$jogador = new Jogador($db);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 10;

// Filtros
$filters = [
    'nome' => isset($_GET['nome']) ? $_GET['nome'] : '',
    'posicao' => isset($_GET['posicao']) ? $_GET['posicao'] : '',
    'time_id' => isset($_GET['time_id']) ? $_GET['time_id'] : ''
];

// Processar ações
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if($action == 'delete' && !empty($id)) {
    $jogador->id = $id;
    $result = $jogador->delete();
    if($result === true) {
        $_SESSION['message'] = "Jogador excluído com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: jogadores.php?" . http_build_query($filters));
    exit();
}

if($_POST) {
    if(empty($_POST['id'])) {
        // Criar novo jogador
        $jogador->nome = $_POST['nome'];
        $jogador->posicao = $_POST['posicao'];
        $jogador->numero_camisa = $_POST['numero_camisa'];
        $jogador->time_id = $_POST['time_id'];
        
        $result = $jogador->create();
        if($result === true) {
            $_SESSION['message'] = "Jogador criado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        // Atualizar jogador
        $jogador->id = $_POST['id'];
        $jogador->nome = $_POST['nome'];
        $jogador->posicao = $_POST['posicao'];
        $jogador->numero_camisa = $_POST['numero_camisa'];
        $jogador->time_id = $_POST['time_id'];
        
        $result = $jogador->update();
        if($result === true) {
            $_SESSION['message'] = "Jogador atualizado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: jogadores.php?" . http_build_query($filters));
    exit();
}

// Buscar jogador para edição
$edit_jogador = null;
if($action == 'edit' && !empty($id)) {
    $jogador->id = $id;
    if($jogador->readOne()) {
        $edit_jogador = array(
            'id' => $jogador->id,
            'nome' => $jogador->nome,
            'posicao' => $jogador->posicao,
            'numero_camisa' => $jogador->numero_camisa,
            'time_id' => $jogador->time_id
        );
    }
}

// Buscar jogadores
$stmt = $jogador->readAll($page, $records_per_page, $filters);
$total_records = $jogador->count($filters);
$total_pages = ceil($total_records / $records_per_page);

// Buscar times para dropdown
$times_stmt = $time->getAll();
$posicoes = $jogador->getPosicoes();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Jogadores</title>
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
                <a class="nav-link active" href="jogadores.php">Jogadores</a>
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
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-user me-2"></i>Gerenciar Jogadores</h2>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo empty($edit_jogador) ? 'Adicionar Jogador' : 'Editar Jogador'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(!empty($edit_jogador)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_jogador['id']; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome do Jogador *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?php echo !empty($edit_jogador) ? $edit_jogador['nome'] : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="posicao" class="form-label">Posição *</label>
                                        <select class="form-select" id="posicao" name="posicao" required>
                                            <option value="">Selecione...</option>
                                            <?php foreach($posicoes as $pos): ?>
                                                <option value="<?php echo $pos; ?>" 
                                                    <?php echo (!empty($edit_jogador) && $edit_jogador['posicao'] == $pos) ? 'selected' : ''; ?>>
                                                    <?php echo $pos; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="numero_camisa" class="form-label">Número *</label>
                                        <input type="number" class="form-control" id="numero_camisa" name="numero_camisa" 
                                               min="1" max="99" 
                                               value="<?php echo !empty($edit_jogador) ? $edit_jogador['numero_camisa'] : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="time_id" class="form-label">Time *</label>
                                        <select class="form-select" id="time_id" name="time_id" required>
                                            <option value="">Selecione o time...</option>
                                            <?php while ($time_row = $times_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                                <option value="<?php echo $time_row['id']; ?>" 
                                                    <?php echo (!empty($edit_jogador) && $edit_jogador['time_id'] == $time_row['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($time_row['nome']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo empty($edit_jogador) ? 'Adicionar' : 'Atualizar'; ?>
                            </button>
                            <?php if(!empty($edit_jogador)): ?>
                                <a href="jogadores.php?<?php echo http_build_query($filters); ?>" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Filtros</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="nome" class="form-control" placeholder="Nome do jogador" 
                                       value="<?php echo htmlspecialchars($filters['nome']); ?>">
                            </div>
                            <div class="col-md-2">
                                <select name="posicao" class="form-select">
                                    <option value="">Todas posições</option>
                                    <?php foreach($posicoes as $pos): ?>
                                        <option value="<?php echo $pos; ?>" 
                                            <?php echo $filters['posicao'] == $pos ? 'selected' : ''; ?>>
                                            <?php echo $pos; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="time_id" class="form-select">
                                    <option value="">Todos os times</option>
                                    <?php 
                                    $times_stmt2 = $time->getAll();
                                    while ($time_row = $times_stmt2->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?php echo $time_row['id']; ?>" 
                                            <?php echo $filters['time_id'] == $time_row['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($time_row['nome']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="jogadores.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Jogadores</h5>
                    </div>
                    <div class="card-body">
                        <?php if($total_records > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Posição</th>
                                            <th>Número</th>
                                            <th>Time</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                                <td><?php echo $row['posicao']; ?></td>
                                                <td><?php echo $row['numero_camisa']; ?></td>
                                                <td><?php echo htmlspecialchars($row['time_nome']); ?></td>
                                                <td>
                                                    <a href="jogadores.php?action=edit&id=<?php echo $row['id']; ?>&<?php echo http_build_query($filters); ?>" 
                                                       class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal<?php echo $row['id']; ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Modal de Confirmação -->
                                                    <div class="modal fade" id="deleteModal<?php echo $row['id']; ?>" tabindex="-1">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Confirmar Exclusão</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    Tem certeza que deseja excluir o jogador "<?php echo htmlspecialchars($row['nome']); ?>"?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <a href="jogadores.php?action=delete&id=<?php echo $row['id']; ?>&<?php echo http_build_query($filters); ?>" class="btn btn-danger">Excluir</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Paginação -->
                            <?php if($total_pages > 1): ?>
                                <nav>
                                    <ul class="pagination justify-content-center">
                                        <?php for($i = 1; $i <= $total_pages; $i++): ?>
                                            <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="jogadores.php?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info">
                                Nenhum jogador encontrado com os filtros aplicados.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
