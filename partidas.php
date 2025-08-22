<?php
session_start();
include_once 'config/database.php';
include_once 'models/Time.php';
include_once 'models/Partida.php';

$database = new Database();
$db = $database->getConnection();
$time = new Time($db);
$partida = new Partida($db);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$records_per_page = 10;

// Filtros
$filters = [
    'time_id' => isset($_GET['time_id']) ? $_GET['time_id'] : '',
    'data_inicio' => isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '',
    'data_fim' => isset($_GET['data_fim']) ? $_GET['data_fim'] : '',
    'resultado' => isset($_GET['resultado']) ? $_GET['resultado'] : ''
];

// Processar ações
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if($action == 'delete' && !empty($id)) {
    $partida->id = $id;
    $result = $partida->delete();
    if($result === true) {
        $_SESSION['message'] = "Partida excluída com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: partidas.php?" . http_build_query($filters));
    exit();
}

if($_POST) {
    if(empty($_POST['id'])) {
        // Criar nova partida
        $partida->time_casa_id = $_POST['time_casa_id'];
        $partida->time_fora_id = $_POST['time_fora_id'];
        $partida->data_jogo = $_POST['data_jogo'];
        $partida->gols_casa = $_POST['gols_casa'];
        $partida->gols_fora = $_POST['gols_fora'];
        
        $result = $partida->create();
        if($result === true) {
            $_SESSION['message'] = "Partida criada com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    } else {
        // Atualizar partida
        $partida->id = $_POST['id'];
        $partida->time_casa_id = $_POST['time_casa_id'];
        $partida->time_fora_id = $_POST['time_fora_id'];
        $partida->data_jogo = $_POST['data_jogo'];
        $partida->gols_casa = $_POST['gols_casa'];
        $partida->gols_fora = $_POST['gols_fora'];
        
        $result = $partida->update();
        if($result === true) {
            $_SESSION['message'] = "Partida atualizada com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result;
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: partidas.php?" . http_build_query($filters));
    exit();
}

// Buscar partida para edição
$edit_partida = null;
if($action == 'edit' && !empty($id)) {
    $partida->id = $id;
    if($partida->readOne()) {
        $edit_partida = array(
            'id' => $partida->id,
            'time_casa_id' => $partida->time_casa_id,
            'time_fora_id' => $partida->time_fora_id,
            'data_jogo' => $partida->data_jogo,
            'gols_casa' => $partida->gols_casa,
            'gols_fora' => $partida->gols_fora
        );
    }
}

// Buscar partidas
$stmt = $partida->readAll($page, $records_per_page, $filters);
$total_records = $partida->count($filters);
$total_pages = ceil($total_records / $records_per_page);

// Buscar times para dropdown
$times_stmt = $time->getAll();
$resultados_options = $partida->getResultadosOptions();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Partidas</title>
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
                <a class="nav-link active" href="partidas.php">Partidas</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-calendar-alt me-2"></i>Gerenciar Partidas</h2>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo empty($edit_partida) ? 'Adicionar Partida' : 'Editar Partida'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(!empty($edit_partida)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_partida['id']; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="time_casa_id" class="form-label">Time Mandante *</label>
                                        <select class="form-select" id="time_casa_id" name="time_casa_id" required>
                                            <option value="">Selecione...</option>
                                            <?php 
                                            $times_stmt1 = $time->getAll();
                                            while ($time_row = $times_stmt1->fetch(PDO::FETCH_ASSOC)): 
                                            ?>
                                                <option value="<?php echo $time_row['id']; ?>" 
                                                    <?php echo (!empty($edit_partida) && $edit_partida['time_casa_id'] == $time_row['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($time_row['nome']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="time_fora_id" class="form-label">Time Visitante *</label>
                                        <select class="form-select" id="time_fora_id" name="time_fora_id" required>
                                            <option value="">Selecione...</option>
                                            <?php 
                                            $times_stmt2 = $time->getAll();
                                            while ($time_row = $times_stmt2->fetch(PDO::FETCH_ASSOC)): 
                                            ?>
                                                <option value="<?php echo $time_row['id']; ?>" 
                                                    <?php echo (!empty($edit_partida) && $edit_partida['time_fora_id'] == $time_row['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($time_row['nome']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="data_jogo" class="form-label">Data e Hora *</label>
                                        <input type="datetime-local" class="form-control" id="data_jogo" name="data_jogo" 
                                               value="<?php echo !empty($edit_partida) ? date('Y-m-d\TH:i', strtotime($edit_partida['data_jogo'])) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="gols_casa" class="form-label">Gols Casa</label>
                                        <input type="number" class="form-control" id="gols_casa" name="gols_casa" 
                                               min="0" value="<?php echo !empty($edit_partida) ? $edit_partida['gols_casa'] : '0'; ?>">
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="mb-3">
                                        <label for="gols_fora" class="form-label">Gols Fora</label>
                                        <input type="number" class="form-control" id="gols_fora" name="gols_fora" 
                                               min="0" value="<?php echo !empty($edit_partida) ? $edit_partida['gols_fora'] : '0'; ?>">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo empty($edit_partida) ? 'Adicionar' : 'Atualizar'; ?>
                            </button>
                            <?php if(!empty($edit_partida)): ?>
                                <a href="partidas.php?<?php echo http_build_query($filters); ?>" class="btn btn-secondary">Cancelar</a>
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
                                <select name="time_id" class="form-select">
                                    <option value="">Todos os times</option>
                                    <?php 
                                    $times_stmt3 = $time->getAll();
                                    while ($time_row = $times_stmt3->fetch(PDO::FETCH_ASSOC)): 
                                    ?>
                                        <option value="<?php echo $time_row['id']; ?>" 
                                            <?php echo $filters['time_id'] == $time_row['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($time_row['nome']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="data_inicio" class="form-control" 
                                       value="<?php echo htmlspecialchars($filters['data_inicio']); ?>" 
                                       placeholder="Data início">
                            </div>
                            <div class="col-md-2">
                                <input type="date" name="data_fim" class="form-control" 
                                       value="<?php echo htmlspecialchars($filters['data_fim']); ?>" 
                                       placeholder="Data fim">
                            </div>
                            <div class="col-md-2">
                                <select name="resultado" class="form-select">
                                    <?php foreach($resultados_options as $value => $label): ?>
                                        <option value="<?php echo $value; ?>" 
                                            <?php echo $filters['resultado'] == $value ? 'selected' : ''; ?>>
                                            <?php echo $label; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-filter me-1"></i>Filtrar
                                </button>
                            </div>
                            <div class="col-md-2">
                                <a href="partidas.php" class="btn btn-outline-secondary w-100">
                                    <i class="fas fa-times me-1"></i>Limpar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Partidas</h5>
                    </div>
                    <div class="card-body">
                        <?php if($total_records > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>Partida</th>
                                            <th>Placar</th>
                                            <th>Resultado</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): 
                                            $resultado = '';
                                            $badge_class = '';
                                            if($row['gols_casa'] > $row['gols_fora']) {
                                                $resultado = 'Vitória do ' . $row['time_casa_nome'];
                                                $badge_class = 'bg-success';
                                            } elseif($row['gols_casa'] < $row['gols_fora']) {
                                                $resultado = 'Vitória do ' . $row['time_fora_nome'];
                                                $badge_class = 'bg-success';
                                            } else {
                                                $resultado = 'Empate';
                                                $badge_class = 'bg-warning text-dark';
                                            }
                                        ?>
                                            <tr>
                                                <td><?php echo date('d/m/Y H:i', strtotime($row['data_jogo'])); ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($row['time_casa_nome']); ?> 
                                                    <strong>vs</strong> 
                                                    <?php echo htmlspecialchars($row['time_fora_nome']); ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo $row['gols_casa']; ?> - <?php echo $row['gols_fora']; ?></strong>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $badge_class; ?>">
                                                        <?php echo $resultado; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="partidas.php?action=edit&id=<?php echo $row['id']; ?>&<?php echo http_build_query($filters); ?>" 
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
                                                                    Tem certeza que deseja excluir a partida entre 
                                                                    "<?php echo htmlspecialchars($row['time_casa_nome']); ?>" e 
                                                                    "<?php echo htmlspecialchars($row['time_fora_nome']); ?>"?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <a href="partidas.php?action=delete&id=<?php echo $row['id']; ?>&<?php echo http_build_query($filters); ?>" class="btn btn-danger">Excluir</a>
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
                                                <a class="page-link" href="partidas.php?page=<?php echo $i; ?>&<?php echo http_build_query($filters); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info">
                                Nenhuma partida encontrada com os filtros aplicados.
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
