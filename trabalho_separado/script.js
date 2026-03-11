function abrirDetalhes(nome, descricao) {
    const area = document.getElementById('areaDetalhes');
    area.style.display = 'block';
    document.getElementById('detalheTitulo').innerText = nome;
    document.getElementById('detalheTexto').innerText = descricao;
    document.getElementById('inputServicoHidden').value = nome;
    
    // Esconde o form de agendamento caso estivesse aberto de outro item
    document.getElementById('areaReserva').style.display = 'none';

    window.scrollTo({ top: area.offsetTop - 100, behavior: 'smooth' });
}

function fecharDetalhes() {
    document.getElementById('areaDetalhes').style.display = 'none';
    document.getElementById('areaReserva').style.display = 'none';
}

function mostrarFormAgendamento() {
    document.getElementById('areaReserva').style.display = 'block';
    window.scrollTo({ top: document.getElementById('areaReserva').offsetTop - 100, behavior: 'smooth' });
}