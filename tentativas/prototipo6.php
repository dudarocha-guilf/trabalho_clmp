<?php
session_start();

// --- CONFIGURAÇÕES E SIMULAÇÃO DE BANCO ---
if (!isset($_SESSION['usuario'])) {
    $_SESSION['usuario'] = null; // null, 'cliente' ou 'admin'
}

if (!isset($_SESSION['agendamentos'])) {
    $_SESSION['agendamentos'] = [
        ['id' => 1, 'paciente' => 'Carlos Silva', 'servico' => 'Reabilitação de Joelho', 'data' => '2026-03-15', 'hora' => '09:00', 'status' => 'confirmado'],
        ['id' => 2, 'paciente' => 'Ana Souza', 'servico' => 'Recovery Pós-Treino', 'data' => '2026-03-15', 'hora' => '10:30', 'status' => 'pendente'],
    ];
}

// Lógica de Login Simples (Para teste)
if (isset($_POST['login_action'])) {
    if ($_POST['email'] == 'admin@almafisio.com') {
        $_SESSION['usuario'] = 'admin';
    } else {
        $_SESSION['usuario'] = 'cliente';
    }
    header("Location: ?v=catalogo");
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?v=login");
}

$view = $_GET['v'] ?? 'login';
$isAdmin = ($_SESSION['usuario'] === 'admin');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlmaFisio | Fisioterapia Esportiva</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #0ea5e9; /* Azul Claro Esportivo */
            --secondary: #f0f9ff; /* Fundo Gelo */
            --text-dark: #1e293b;
            --text-light: #64748b;
            --white: #ffffff;
            --success: #10b981;
            --danger: #ef4444;
        }

        body { 
            font-family: 'Inter', system-ui, -apple-system, sans-serif; 
            background: var(--secondary); 
            color: var(--text-dark);
            margin: 0; 
            line-height: 1.6;
        }

        h4{
            color: #64748b;
            font-weight: normal;  
        }

        /* Header Minimalista */
        nav { 
            background: var(--white); 
            padding: 1.2rem 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .logo { 
            font-size: 1.5rem; 
            font-weight: 800; 
            color: var(--primary); 
            letter-spacing: -1px;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-light);
            margin-left: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: 0.3s;
        }

        .nav-links a:hover { color: var(--primary); }

        /* Container Principal */
        .container { max-width: 1100px; margin: 3rem auto; padding: 0 1.5rem; }

        /* Cards e Estilo Clean */
        .card { 
            background: var(--white); 
            padding: 2rem; 
            border-radius: 16px; 
            border: 1px solid rgba(14, 165, 233, 0.1);
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.02);
        }

        .grid-servicos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .servico-item {
            background: var(--white);
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }

        .servico-item:hover { transform: translateY(-5px); border-color: var(--primary); }

        /* Inputs e Botões */
        input, select { 
            width: 100%; padding: 0.8rem; margin: 0.5rem 0 1.2rem;
            border: 1px solid #e2e8f0; border-radius: 8px; background: #f8fafc;
        }

        button { 
            cursor: pointer; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; 
            background: var(--primary); color: white; font-weight: 600; width: 100%;
            transition: opacity 0.2s;
        }

        button:hover { opacity: 0.9; }
        button.outline { background: transparent; border: 1px solid var(--primary); color: var(--primary); }

        /* Tabelas */
        table { width: 100%; border-collapse: collapse; margin-top: 1.5rem; background: var(--white); border-radius: 12px; overflow: hidden; }
        th { background: #f1f5f9; padding: 1rem; text-align: left; font-size: 0.85rem; color: var(--text-light); }
        td { padding: 1.2rem 1rem; border-bottom: 1px solid #f1f5f9; }

        .status { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
        .confirmado { background: #dcfce7; color: #166534; }
        .pendente { background: #fef9c3; color: #854d0e; }

        .admin-only { background: #fff1f2; border: 1px solid #fda4af; padding: 2rem; text-align: center; border-radius: 12px; }
    </style>
</head>
<body>

<nav>
    <div class="logo"><i class="fa-solid fa-person-running"></i> AlmaFisio</div>
    <div class="nav-links">
        <?php if($_SESSION['usuario']): ?>
            <a href="?v=catalogo">Serviços</a>
            <a href="?v=meus_agendamentos">Minha Agenda</a>
            <?php if($isAdmin): ?>
                <a href="?v=admin" style="color: var(--primary); font-weight:bold;"><i class="fa-solid fa-lock"></i> Admin</a>
            <?php endif; ?>
            <a href="?logout=1"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container">

    <?php if($view == 'login'): ?>
        <div class="card" style="max-width: 400px; margin: auto;">
            <h2 style="text-align: center; margin-bottom: 0.5rem;">Bem-vindo</h2>
            <p style="text-align: center; color: var(--text-light); font-size: 0.9rem;">Entre com seus dados para agendar</p>
            <form method="POST">
                <input type="hidden" name="login_action" value="1">
                <label>E-mail</label>
                <input type="email" name="email" placeholder="ex: cliente@email.com ou admin@almafisio.com" required>
                <label>Senha</label>
                <input type="password" placeholder="••••••••" required>
                <button type="submit">Acessar Conta</button>
            </form>
            <p style="font-size: 0.8rem; text-align: center; margin-top: 1rem;">Admin teste: admin@almafisio.com</p>
        </div>

    <?php elseif($view == 'catalogo'): ?>
        <div class="header-section">
            <h1>Nossos Tratamentos</h1>
            <p>Fisioterapia de elite para atletas e entusiastas.</p>
        </div>
        
        <div class="grid-servicos">
            <?php 
                $servicos = [
                    ['nome' => 'Reabilitação Esportiva', 'icon' => 'fa-kit-medical', 'preco' => 'R$ 180'],
                    ['nome' => 'Recovery Pós-Treino', 'icon' => 'fa-battery-full', 'preco' => 'R$ 120'],
                    ['nome' => 'Osteopatia', 'icon' => 'fa-bone', 'preco' => 'R$ 220'],
                    ['nome' => 'Prevenção de Lesões', 'icon' => 'fa-shield-heart', 'preco' => 'R$ 150']
                ];
                foreach($servicos as $s): 
            ?>
                <div class="servico-item">
                    <i class="fa-solid <?php echo $s['icon']; ?>" style="font-size: 1.5rem; color: var(--primary);"></i>
                    <h3><?php echo $s['nome']; ?></h3>
                    <p style="color: var(--text-light); font-size: 0.9rem;">Sessões personalizadas de 50 minutos.</p>
                    <p><strong><?php echo $s['preco']; ?></strong></p>
                    <button class="outline" onclick="abrirAgendamento('<?php echo $s['nome']; ?>')">Mais informações</button>
                </div>
            <?php endforeach; ?>
        </div>

        <div id="areaReserva" class="card" style="display:none; margin-top: 3rem; border-top: 4px solid var(--primary);">
            <h3><span id="txtServico" style="color:var(--primary)"></span></h3>
            <h4 id="txtDescricaoServico">  Reabilitação esportiva é o processo de tratamento e recuperação de lesões causadas pela
                prática de esportes ou atividades físicas. Por meio de exercícios específicos, técnicas
                de fisioterapia e acompanhamento profissional, o objetivo é reduzir a dor, recuperar movimentos, fortalecer 
                músculos e permitir que o atleta volte às atividades com segurança e melhor desempenho. 💪🏽🏃‍♂️</h4>

                 <h3 style="color:var(--primary)">Realize o seu agendamento</h3>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Escolha a Data</label>
                    <input type="date" id="dataReserva">
                </div>
                <div>
                    <label>Horários Disponíveis</label>
                    <select id="horaReserva">
                        <option>08:00</option><option>09:00</option><option>14:00</option><option>16:00</option>
                    </select>
                </div>
            </div>
            <button onclick="salvarReserva()">Confirmar Agendamento</button>
        </div>

    <?php elseif($view == 'meus_agendamentos'): ?>
        <div class="card">
            <h2>Meus Agendamentos</h2>
            <table>
                <thead>
                    <tr><th>Tratamento</th><th>Data/Hora</th><th>Status</th><th>Opções</th></tr>
                </thead>
                <tbody>
                    <?php foreach($_SESSION['agendamentos'] as $ag): ?>
                    <tr>
                        <td><strong><?php echo $ag['servico']; ?></strong></td>
                        <td><?php echo date('d/m/Y', strtotime($ag['data'])); ?> às <?php echo $ag['hora']; ?></td>
                        <td><span class="status <?php echo $ag['status']; ?>"><?php echo strtoupper($ag['status']); ?></span></td>
                        <td><a href="#" style="color: var(--danger); font-size: 0.8rem;">Desmarcar</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif($view == 'admin'): ?>
        <?php if(!$isAdmin): ?>
            <div class="admin-only">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: var(--danger);"></i>
                <h2>Acesso Negado</h2>
                <p>Apenas administradores da AlmaFisio podem acessar este painel.</p>
                <a href="?v=catalogo">Voltar para Início</a>
            </div>
        <?php else: ?>
            <div class="card">
                <div style="display:flex; justify-content: space-between; align-items: center;">
                    <h2>Gestão da Clínica</h2>
                    <button onclick="exportarAgenda()" style="width: auto; background: #334155;"><i class="fa-solid fa-download"></i> CSV</button>
                </div>
                
                <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; margin: 1rem 0;">
                    <i class="fa-solid fa-filter"></i> Filtrar por data: <input type="date" style="width: 200px; margin: 0 10px;">
                </div>

                <table>
                    <thead>
                        <tr><th>Paciente</th><th>Serviço</th><th>Data/Hora</th><th>Status</th><th>Ação</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['agendamentos'] as $ag): ?>
                        <tr>
                            <td><?php echo $ag['paciente']; ?></td>
                            <td><?php echo $ag['servico']; ?></td>
                            <td><?php echo $ag['data']; ?> | <?php echo $ag['hora']; ?></td>
                            <td><span class="status <?php echo $ag['status']; ?>"><?php echo $ag['status']; ?></span></td>
                            <td>
                                <button style="padding: 5px 10px; width: auto;" onclick="alert('Confirmado e E-mail enviado!')">Aprovar</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>

</div>

<script>
    function abrirAgendamento(nome) {
        document.getElementById('areaReserva').style.display = 'block';
        document.getElementById('txtServico').innerText = nome;
        document.getElementById('txtServico').innerText = nome;
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
    }

    function salvarReserva() {
        const data = document.getElementById('dataReserva').value;
        const hora = document.getElementById('horaReserva').value;

        if(!data) return alert("Por favor, selecione uma data válida.");

        // Regra de Conflito: 15/03 às 09:00 já existe na sessão inicial
        if(data === '2026-03-15' && hora === '09:00') {
            alert("⚠️ Horário Indisponível! Já existe um paciente agendado para este momento.");
        } else {
            alert("✅ Solicitação enviada! \n\n[LOG]: E-mail enviado para o fisioterapeuta responsável.");
            window.location.href = "?v=meus_agendamentos";
        }
    }

    function exportarAgenda() {
        let conteudo = "Paciente,Servico,Data,Hora,Status\n";
        conteudo += "Carlos Silva,Reabilitação,2026-03-15,09:00,Confirmado";
        
        const blob = new Blob([conteudo], { type: 'text/csv' });
        const link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = 'agenda_almafisio.csv';
        link.click();
    }
</script>

</body>
</html>