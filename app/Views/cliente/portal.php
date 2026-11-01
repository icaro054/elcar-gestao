<div class="max-w-5xl mx-auto px-4 py-8">
    
    <!-- Cabeçalho do Cliente -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-elcar-blue">Olá, <?= htmlspecialchars($nome_cliente) ?>!</h1>
            <p class="text-gray-600 mt-1 font-bold">Bem-vindo(a) ao seu portal de cliente Elcar Autopeças e Serviços.</p>
        </div>
        <a href="/logout" class="bg-red-50 text-red-600 hover:bg-red-100 font-bold py-2 px-4 rounded-lg transition border border-red-200 shadow-sm flex items-center gap-2">
            Sair do Portal
        </a>
    </div>

    <!-- Seletor / Filtro de Veículos -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Filtrar Histórico por Veículo</h2>
        <div class="flex flex-wrap gap-3">
            <!-- Botão de "Todos" -->
            <a href="/cliente" class="px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-sm border border-gray-200 <?= empty($filtro_veiculo) ? 'bg-elcar-blue text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' ?>">
                Todos os Veículos
            </a>
            
            <!-- Botões dinâmicos para cada carro do cliente -->
            <?php foreach($veiculos as $v): ?>
                <a href="/cliente?veiculo_id=<?= $v['id'] ?>" class="px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-sm border border-gray-200 <?= ($filtro_veiculo == $v['id']) ? 'bg-elcar-blue text-white' : 'bg-gray-50 text-gray-700 hover:bg-gray-100' ?>">
                    <?= htmlspecialchars($v['plate_display']) ?> - <?= htmlspecialchars($v['model']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Tabela de Ordens de Serviço do Cliente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gray-50">
            <h2 class="text-xl font-bold text-elcar-blue">O seu Histórico de Serviços</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white text-gray-500 font-bold border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">OS #</th>
                        <th class="px-6 py-4">Entrada / Saída</th>
                        <th class="px-6 py-4">Veículo</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Detalhes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($servicos)): ?>
                        <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 font-bold">Nenhum serviço encontrado para este veículo.</td></tr>
                    <?php else: ?>
                        <?php foreach ($servicos as $os): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4 text-xs text-gray-600 font-bold uppercase">
                                    <span class="block"> <?= date('d/m/y', strtotime($os['created_at'])) ?></span>
                                    <?php if(!empty($os['completed_at'])): ?>
                                        <span class="block text-green-600 mt-0.5"> <?= date('d/m/y', strtotime($os['completed_at'])) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1.5 rounded font-bold uppercase border border-gray-200 text-xs">
                                        <?= htmlspecialchars($os['plate_display']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $badgeClass = match($os['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200'
                                        };
                                        $statusLabel = match($os['status']) {
                                            'pending' => 'Orçamento',
                                            'in_progress' => 'Na Oficina',
                                            'completed' => 'Concluído',
                                            default => 'Desconhecido'
                                        };
                                    ?>
                                    <span class="px-3 py-1.5 rounded-full text-xs font-bold border <?= $badgeClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 text-base">
                                    R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <!-- NOVO BOTÃO PARA ABRIR A OS DETALHADA -->
                                    <a href="/cliente/os?id=<?= $os['id'] ?>" target="_blank" class="inline-flex items-center gap-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2 px-4 rounded-lg text-sm transition border border-gray-300 shadow-sm">
                                         Ver OS
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>