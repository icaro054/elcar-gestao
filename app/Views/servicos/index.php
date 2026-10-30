<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold text-elcar-blue">Ordens de Serviço</h1>
        <p class="text-gray-600">Gestão de manutenções e reparações</p>
    </div>
    <a href="/app/servicos/novo" class="bg-elcar-secondary text-white font-bold px-4 py-2 rounded shadow hover:bg-elcar-blue transition">
        + Nova OS
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <input type="text" placeholder="Pesquisar por OS, placa ou cliente..." class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <th class="py-3 px-6 font-semibold">OS #</th>
                    <th class="py-3 px-6 font-semibold">Veículo / Placa</th>
                    <th class="py-3 px-6 font-semibold">Cliente</th>
                    <th class="py-3 px-6 font-semibold text-center">Status</th>
                    <th class="py-3 px-6 font-semibold text-right">Valor Total</th>
                    <th class="py-3 px-6 font-semibold text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (empty($servicos)): ?>
                    <tr>
                        <td colspan="6" class="py-6 px-6 text-center text-gray-500">Nenhuma Ordem de Serviço registada.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($servicos as $os): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-6 font-bold text-gray-800">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td class="py-3 px-6">
                                <span class="font-bold text-elcar-blue"><?= htmlspecialchars($os['plate_display']) ?></span>
                                <br>
                                <span class="text-xs text-gray-500"><?= htmlspecialchars($os['model']) ?></span>
                            </td>
                            <td class="py-3 px-6"><?= htmlspecialchars($os['client_name']) ?></td>
                            <td class="py-3 px-6 text-center">
                                <?php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'in_progress' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'Pendente',
                                        'in_progress' => 'Em Execução',
                                        'completed' => 'Concluído',
                                        'cancelled' => 'Cancelado'
                                    ];
                                    $color = $statusColors[$os['status']] ?? 'bg-gray-100 text-gray-800';
                                    $label = $statusLabels[$os['status']] ?? $os['status'];
                                ?>
                                <span class="<?= $color ?> text-xs px-2 py-1 rounded-full"><?= $label ?></span>
                            </td>
                            <td class="py-3 px-6 text-right font-medium">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></td>
                            <td class="py-3 px-6 text-center">
                                <a href="/app/servicos/imprimir?id=<?= $os['id'] ?>" class="bg-elcar-blue text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-elcar-secondary transition shadow-sm">
                                    Imprimir OS
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>