<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
        <h1 class="text-2xl font-bold text-elcar-blue">Ordens de Serviço</h1>
        <a href="/app/servicos/novo" class="bg-elcar-blue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition shadow-sm flex items-center justify-center gap-2">
            <span>+</span> Nova OS
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-200 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4">OS #</th>
                        <th class="px-6 py-4">Data</th>
                        <th class="px-6 py-4">Cliente</th>
                        <th class="px-6 py-4">Veículo</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($servicos)): ?>
                        <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500 font-bold">Nenhuma Ordem de Serviço encontrada.</td></tr>
                    <?php else: ?>
                        <?php foreach ($servicos as $os): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-elcar-blue">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td class="px-6 py-4 text-gray-600"><?= date('d/m/Y', strtotime($os['created_at'])) ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($os['client_name']) ?></td>
                                <td class="px-6 py-4">
                                    <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded font-bold uppercase border border-gray-200 text-xs">
                                        <?= htmlspecialchars($os['plate_display']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                        $badgeClass = match($os['status']) {
                                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'completed' => 'bg-green-100 text-green-800 border-green-200',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusLabel = match($os['status']) {
                                            'pending' => 'Pendente',
                                            'in_progress' => 'Em Execução',
                                            'completed' => 'Concluído',
                                            default => 'Desconhecido'
                                        };
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold border <?= $badgeClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900">
                                    R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <?php if ($os['status'] === 'completed'): ?>
                                        <a href="/app/servicos/gerenciar?id=<?= $os['id'] ?>" class="inline-flex items-center gap-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-1.5 px-4 rounded text-sm transition shadow-sm">
                                             Ver OS
                                        </a>
                                    <?php else: ?>
                                        <a href="/app/servicos/gerenciar?id=<?= $os['id'] ?>" class="inline-flex items-center gap-1 bg-elcar-blue hover:bg-blue-700 text-white font-bold py-1.5 px-4 rounded text-sm transition shadow-sm">
                                             Gerir OS
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>