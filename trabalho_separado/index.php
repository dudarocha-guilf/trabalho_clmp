<?php
session_start();

// --- SIMULAÇÃO DE BANCO DE DADOS ---
if (!isset($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [
        'admin@almafisio.com' => ['nome' => 'Diretor Alma', 'senha' => '123', 'tipo' => 'admin']
    ];
}

if (!isset($_SESSION['agendamentos'])) {
    $_SESSION['agendamentos'] = [
        ['paciente' => 'Carlos Silva', 'servico' => 'Reabilitação Esportiva', 'data' => '2026-03-15', 'hora' => '09:00', 'status' => 'confirmado'],
        ['paciente' => 'Ana Souza', 'servico' => 'Recovery Pós-Treino', 'data' => '2026-03-18', 'hora' => '14:00', 'status' => 'pendente'],
        ['paciente' => 'Marcos Dias', 'servico' => 'Reabilitação Esportiva', 'data' => '2026-03-20', 'hora' => '10:00', 'status' => 'confirmado']
    ];
}

$view = $_GET['v'] ?? 'portal';
$user = $_SESSION['user_logged'] ?? null;
$isAdmin = ($user && $user['tipo'] === 'admin');

// --- LÓGICA DE AÇÕES ---

// Cadastro
if (isset($_POST['registrar'])) {
    $email = $_POST['email'];
    $_SESSION['usuarios'][$email] = [
        'nome' => $_POST['nome'],
        'senha' => $_POST['senha'],
        'tipo' => 'cliente'
    ];
    header("Location: ?v=login&tipo=paciente&msg=Sucesso! Faça login.");
}

// Login
if (isset($_POST['login_action'])) {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    if (isset($_SESSION['usuarios'][$email]) && $_SESSION['usuarios'][$email]['senha'] === $senha) {
        $_SESSION['user_logged'] = $_SESSION['usuarios'][$email];
        $_SESSION['user_logged']['email'] = $email;
        header("Location: ?v=catalogo");
    } else {
        $erro = "E-mail ou senha incorretos.";
    }
}

// Agendar
if (isset($_POST['confirmar_agendamento'])) {
    $_SESSION['agendamentos'][] = [
        'paciente' => $user['nome'] ?? 'Anônimo',
        'servico' => $_POST['servico_nome'],
        'data' => $_POST['data'],
        'hora' => $_POST['hora'],
        'status' => 'pendente'
    ];
    header("Location: ?v=meus_agendamentos");
}

if (isset($_GET['logout'])) { session_destroy(); header("Location: ?v=portal"); }

// --- LÓGICA DO DASHBOARD (Apenas Admin) ---
$stats = [];
if ($isAdmin) {
    foreach ($_SESSION['agendamentos'] as $ag) {
        $s = $ag['servico'];
        $stats[$s] = ($stats[$s] ?? 0) + 1;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlmaFisio | Sistema</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<?php if($view !== 'portal'): ?>
<nav>
    <a href="?v=catalogo" class="logo"><i class="fa-solid fa-person-running"></i> AlmaFisio</a>
    <div class="nav-links">
        <?php if($isAdmin): ?>
            <a href="?v=admin"><b><i class="fa-solid fa-chart-line"></i> Dashboard Admin</b></a>
        <?php else: ?>
            <a href="?v=catalogo">Serviços</a>
            <a href="?v=meus_agendamentos">Minha Agenda</a>
        <?php endif; ?>
        <a href="?logout=1" class="btn-sair">Sair</a>
    </div>
</nav>
<?php endif; ?>

<div class="container">

    <?php if($view == 'portal'): ?>
        <div class="portal-container">
            <h1 class="logo-grande">AlmaFisio</h1>
            <p>Selecione seu perfil de acesso</p>
            <div class="portal-grid">
                <a href="?v=login&tipo=paciente" class="portal-card">
                    <i class="fa-solid fa-user-injured fa-3x"></i>
                    <h3>Paciente</h3>
                </a>
                <a href="?v=login&tipo=admin" class="portal-card admin">
                    <i class="fa-solid fa-user-md fa-3x"></i>
                    <h3>Administrador</h3>
                </a>
            </div>
        </div>

    <?php elseif($view == 'login'): ?>
        <div class="card login-card">
            <h2>Login</h2>
            <?php if(isset($erro)) echo "<p class='bad'>$erro</p>"; ?>
            <form method="POST">
                <input type="hidden" name="login_action" value="1">
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Senha" required>
                <button type="submit">Entrar</button>
            </form>
            <p>Não tem conta? <a href="?v=cadastro">Cadastre-se aqui</a></p>
        </div>

    <?php elseif($view == 'cadastro'): ?>
        <div class="card login-card">
            <h2>Criar Conta Paciente</h2>
            <form method="POST">
                <input type="hidden" name="registrar" value="1">
                <input type="text" name="nome" placeholder="Nome Completo" required>
                <input type="email" name="email" placeholder="E-mail" required>
                <input type="password" name="senha" placeholder="Crie uma Senha" required>
                <button type="submit" style="background:var(--success)">Finalizar Cadastro</button>
            </form>
            <p><a href="?v=login">Já tenho conta</a></p>
        </div>

    <?php elseif($view == 'catalogo'): ?>
        <h1>Tratamentos Disponíveis</h1>
        <div class="grid-servicos">
            <?php 
            $servicos = [
                ['n' => 'Reabilitação Esportiva', 'i' => 'fa-kit-medical', 'd' => 'Foco em retorno seguro ao esporte após lesões musculares ou articulares.'],
                ['n' => 'Recovery Pós-Treino', 'i' => 'fa-battery-full', 'd' => 'Uso de botas pneumáticas e terapia manual para reduzir a fadiga.'],
                ['n' => 'Osteopatia', 'i' => 'fa-bone', 'd' => 'Alinhamento estrutural para melhora de dores crônicas.'],
                ['n' => 'Prevenção de Lesões', 'i' => 'fa-shield-heart', 'd' => 'Treinamento preventivo baseado em análise biomecânica.']
            ];
            foreach($servicos as $s): ?>
                <div class="servico-item">
                    <i class="fa-solid <?php echo $s['i']; ?> fa-2x"></i>
                    <h3><?php echo $s['n']; ?></h3>
                    <button class="outline" onclick="abrirDetalhes('<?php echo $s['n']; ?>', '<?php echo $s['d']; ?>')">Mais informações</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="areaDetalhes" class="card" style="display:none; margin-top:2rem;">
            <h2 id="detalheTitulo"></h2>
            <p id="detalheTexto"></p>
            <div class="pergunta-agendar">
                <p>Deseja realizar o agendamento?</p>
                <button onclick="mostrarFormAgendamento()" class="btn-sim">Sim</button>
                <button onclick="fecharDetalhes()" class="btn-nao">Não</button>
            </div>
        </div>

        <div id="areaReserva" class="card" style="display:none; margin-top:1rem; border-top: 4px solid var(--success);">
            <h3>Agendar Horário</h3>
            <form method="POST">
                <input type="hidden" name="confirmar_agendamento" value="1">
                <input type="hidden" name="servico_nome" id="inputServicoHidden">
                <input type="date" name="data" required>
                <select name="hora">
                    <option>08:00</option><option>10:00</option><option>14:00</option><option>16:00</option>
                </select>
                <button type="submit">Confirmar Agora</button>
            </form>
        </div>

    <?php elseif($view == 'meus_agendamentos'): ?>
        <div class="card">
            <h2>Meus Agendamentos</h2>
            <div class="table-responsive">
                <table>
                    <thead><tr><th>Serviço</th><th>Data/Hora</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach($_SESSION['agendamentos'] as $ag): 
                            if($ag['paciente'] == $user['nome']): ?>
                            <tr>
                                <td><?php echo $ag['servico']; ?></td>
                                <td><?php echo date('d/m/y', strtotime($ag['data'])); ?> - <?php echo $ag['hora']; ?></td>
                                <td><span class="status <?php echo $ag['status']; ?>"><?php echo $ag['status']; ?></span></td>
                            </tr>
                        <?php endif; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif($view == 'admin' && $isAdmin): ?>
        <div class="admin-layout">
            <div class="card dash-stats">
                <h3><i class="fa-solid fa-chart-bar"></i> Serviços mais buscados (Mês)</h3>
                <div class="chart-container">
                    <?php foreach($stats as $nome => $qtd): 
                        $percent = ($qtd / count($_SESSION['agendamentos'])) * 100; ?>
                        <div class="bar-row">
                            <span><?php echo $nome; ?> (<?php echo $qtd; ?>)</span>
                            <div class="bar-bg"><div class="bar-fill" style="width:<?php echo $percent; ?>%"></div></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card" style="margin-top:2rem;">
                <h3><i class="fa-solid fa-list-check"></i> Todos os Agendamentos da Clínica</h3>
                <div class="table-responsive">
                    <table>
                        <thead><tr><th>Paciente</th><th>Serviço</th><th>Data</th><th>Status</th></tr></thead>
                        <tbody>
                            <?php foreach($_SESSION['agendamentos'] as $ag): ?>
                                <tr>
                                    <td><b><?php echo $ag['paciente']; ?></b></td>
                                    <td><?php echo $ag['servico']; ?></td>
                                    <td><?php echo date('d/m/y', strtotime($ag['data'])); ?></td>
                                    <td><span class="status <?php echo $ag['status']; ?>"><?php echo $ag['status']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>

<script src="script.js"></script>
</body>
</html>