<div class="mb-6 flex flex-col sm:flex-row sm:justify-between sm:items-end gap-4">
    <div>
        <h1 class="text-3xl font-bold text-elcar-blue">Dashboard</h1>
        <p class="text-gray-600">Resumo operacional da oficina</p>
        
        <!-- NOVO BOTÃO DA LISTA DE REPOSIÇÃO -->
        <div class="mt-3">
            <a href="/app/estoque/compras" class="inline-flex items-center gap-2 bg-elcar-blue hover:bg-blue-800 text-white font-bold py-1.5 px-4 rounded text-sm transition shadow-sm">
                 Lista de Reposição
            </a>
        </div>
    </div>
    
    <!-- Atalhos (Secção 6.3) -->
    <div class="flex flex-col sm:flex-row flex-wrap gap-2">
        <a href="/app/clientes/novo" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50 transition text-center">Novo Cliente</a>
        <a href="/app/veiculos/novo" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded shadow-sm hover:bg-gray-50 transition text-center">Novo Veículo</a>
        <a href="/app/servicos/novo" class="bg-elcar-secondary text-white font-bold px-4 py-2 rounded shadow hover:bg-elcar-blue transition text-center">+ Novo Serviço</a>
    </div>
</div>

<!-- Cards de Resumo (Secção 6.3) -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-elcar-yellow">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Serviços Pendentes</h3>
        <p class="text-3xl font-bold text-elcar-text mt-2"><?= $metrics['total_pendentes'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-blue-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Em Execução</h3>
        <p class="text-3xl font-bold text-elcar-text mt-2"><?= $metrics['total_em_execucao'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Total de Clientes</h3>
        <p class="text-3xl font-bold text-elcar-text mt-2"><?= $metrics['total_clientes'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow border-l-4 border-purple-500">
        <h3 class="text-sm font-semibold text-gray-500 uppercase">Total de Veículos</h3>
        <p class="text-3xl font-bold text-elcar-text mt-2"><?= $metrics['total_veiculos'] ?? 0 ?></p>
    </div>
</div>

<!-- Pesquisa Rápida (Histórico por placa) -->
<div class="bg-white p-6 rounded-lg shadow mb-8">
    <h2 class="text-lg font-bold text-elcar-blue mb-4">Consultar Histórico</h2>
    <form action="/app/historico" method="GET" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
        <input type="text" name="placa" placeholder="Digite a placa (ex: ABC-1234 ou ABC1D23)" class="flex-grow px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary" required>
        <button type="submit" class="bg-elcar-blue text-white px-6 py-2 rounded hover:bg-elcar-secondary transition sm:shrink-0">Pesquisar</button>
    </form>
</div>

<!-- Serviços Recentes (Secção 6.3) -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-lg font-bold text-elcar-blue">Serviços Recentes</h2>
        <a href="/app/servicos" class="text-sm text-elcar-secondary font-bold hover:underline">Ver todos &rarr;</a>
    </div>
    
    <?php if (empty($ultimas_os)): ?>
        <div class="p-6 text-center text-gray-500">
            <p>Ainda não há serviços registados no sistema.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 text-xs uppercase border-b">
                        <th class="py-3 px-6 font-semibold">OS #</th>
                        <th class="py-3 px-6 font-semibold">Veículo / Placa</th>
                        <th class="py-3 px-6 font-semibold">Cliente</th>
                        <th class="py-3 px-6 font-semibold text-center">Status</th>
                        <th class="py-3 px-6 font-semibold text-right">Valor</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    <?php foreach ($ultimas_os as $os): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-6 font-bold text-elcar-blue">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="py-3 px-6 font-bold"><?= htmlspecialchars($os['plate_display']) ?> <span class="text-xs font-normal text-gray-500">(<?= htmlspecialchars($os['model']) ?>)</span></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($os['client_name']) ?></td>
                            <td class="py-3 px-6 text-center">
                                <?php
                                    $badge = match($os['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    $label = match($os['status']) {
                                        'pending' => 'Pendente',
                                        'in_progress' => 'Em Execução',
                                        'completed' => 'Concluído',
                                        default => $os['status']
                                    };
                                ?>
                                <span class="<?= $badge ?> text-xs px-2.5 py-1 rounded-full font-bold"><?= $label ?></span>
                            </td>
                            <td class="py-3 px-6 text-right font-medium">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>