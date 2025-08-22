<?php
session_start();
include_once 'config/database.php';
include_once 'models/Time.php';

$database = new Database();
$db = $database->getConnection();
$time = new Time($db);

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$records_per_page = 10;

// Processar ações
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? $_GET['id'] : '';

if($action == 'delete' && !empty($id)) {
    $time->id = $id;
    $result = $time->delete();
    if($result === true) {
        $_SESSION['message'] = "Time excluído com sucesso!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = $result;
        $_SESSION['message_type'] = "danger";
    }
    header("Location: times.php");
    exit();
}

if($_POST) {
    if(empty($_POST['id'])) {
        // Criar novo time
        $time->nome = $_POST['nome'];
        $time->cidade = $_POST['cidade'];
        
        if($time->create()) {
            $_SESSION['message'] = "Time criado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao criar time.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        // Atualizar time
        $time->id = $_POST['id'];
        $time->nome = $_POST['nome'];
        $time->cidade = $_POST['cidade'];
        
        if($time->update()) {
            $_SESSION['message'] = "Time atualizado com sucesso!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Erro ao atualizar time.";
            $_SESSION['message_type'] = "danger";
        }
    }
    header("Location: times.php");
    exit();
}

// Buscar time para edição
$edit_time = null;
if($action == 'edit' && !empty($id)) {
    $time->id = $id;
    if($time->readOne()) {
        $edit_time = array(
            'id' => $time->id,
            'nome' => $time->nome,
            'cidade' => $time->cidade
        );
    }
}

// Buscar times
$stmt = $time->readAll($page, $records_per_page, $search);
$total_records = $time->count($search);
$total_pages = ceil($total_records / $records_per_page);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Times</title>
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
                <a class="nav-link active" href="times.php">Times</a>
                <a class="nav-link" href="jogadores.php">Jogadores</a>
                <a class="nav-link" href="partidas.php">Partidas</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2><i class="fas fa-users me-2"></i>Gerenciar Times</h2>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show">
                        <?php echo $_SESSION['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo empty($edit_time) ? 'Adicionar Time' : 'Editar Time'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <?php if(!empty($edit_time)): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_time['id']; ?>">
                            <?php endif; ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome do Time *</label>
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?php echo !empty($edit_time) ? $edit_time['nome'] : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="cidade" class="form-label">Cidade *</label>
                                        <input type="text" class="form-control" id="cidade" name="cidade" 
                                               value="<?php echo !empty($edit_time) ? $edit_time['cidade'] : ''; ?>" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                <?php echo empty($edit_time) ? 'Adicionar' : 'Atualizar'; ?>
                            </button>
                            <?php if(!empty($edit_time)): ?>
                                <a href="times.php" class="btn btn-secondary">Cancelar</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Lista de Times</h5>
                        <form method="GET" class="d-flex">
                            <input type="text" name="search" class="form-control me-2" placeholder="Buscar time..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </div>
                    <div class="card-body">
                        <?php if($total_records > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>Cidade</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                                <td><?php echo htmlspecialchars($row['cidade']); ?></td>
                                                <td>
                                                    <a href="times.php?action=edit&id=<?php echo $row['id']; ?>" 
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
                                                                    Tem certeza que deseja excluir o time "<?php echo htmlspecialchars($row['nome']); ?>"?
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                                    <a href="times.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-danger">Excluir</a>
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
                                                <a class="page-link" href="times.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>
                                    </ul>
                                </nav>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="alert alert-info">
                                <?php echo empty($search) ? 'Nenhum time cadastrado.' : 'Nenhum time encontrado com o termo "' . htmlspecialchars($search) . '".'; ?>
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
