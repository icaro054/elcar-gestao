<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-elcar-blue">Histórico por Placa</h1>
        <p class="text-gray-600 font-bold">Pesquise todo o histórico de manutenções de um veículo específico.</p>
    </div>

    <!-- Formulário de Pesquisa -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <form action="/app/historico" method="GET" class="flex flex-col sm:flex-row gap-4 items-end">
            <div class="w-full sm:w-1/3">
                <label class="block text-sm font-bold text-gray-700 mb-1">Placa do Veículo</label>
                <input type="text" name="placa" value="<?= htmlspecialchars($placa ?? '') ?>" placeholder="Ex: ABC1234 ou ABC-1234" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue uppercase font-bold text-gray-800">
            </div>
            <button type="submit" class="bg-elcar-blue hover:bg-blue-800 text-white font-bold py-2.5 px-6 rounded-lg transition shadow-sm w-full sm:w-auto">
                 Pesquisar Histórico
            </button>
        </form>
    </div>

    <?php if (!empty($placa)): ?>
        <?php if (!$veiculo): ?>
            <!-- Alerta de Veículo não encontrado -->
            <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded text-yellow-800 font-bold shadow-sm">
                Nenhum veículo encontrado com a placa "<?= htmlspecialchars($placa) ?>". Verifique se digitou corretamente ou se o veículo já foi cadastrado.
            </div>
        <?php else: ?>
            <!-- Informações do Veículo Encontrado -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($veiculo['brand'] . ' ' . $veiculo['model']) ?></h2>
                    <p class="text-gray-600 font-bold text-lg mt-1">Placa: <span class="text-elcar-blue"><?= htmlspecialchars($veiculo['plate_display']) ?></span></p>
                    <p class="text-sm text-gray-500 mt-2 font-bold border-t border-gray-100 pt-2">
                         Cliente: <?= htmlspecialchars($veiculo['client_name']) ?> <span class="mx-2">|</span>  Tel: <?= htmlspecialchars($veiculo['phone'] ?? 'Não informado') ?>
                    </p>
                </div>
            </div>

            <!-- Tabela de Ordens de Serviço -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-2">
                    <h2 class="text-lg font-bold text-elcar-blue">Ordens de Serviço do Veículo</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-white text-gray-500 font-bold border-b border-gray-100 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">OS #</th>
                                <th class="px-6 py-4">Data de Abertura</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Valor Total</th>
                                <th class="px-6 py-4 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($servicos)): ?>
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500 font-bold">Nenhum serviço registado no histórico para este veículo.</td></tr>
                            <?php else: ?>
                                <?php foreach ($servicos as $os): ?>
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 font-bold text-gray-800 text-lg">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                        <td class="px-6 py-4 text-gray-600 font-bold"><?= date('d/m/Y H:i', strtotime($os['created_at'])) ?></td>
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
                                            <span class="px-3 py-1.5 rounded-full text-xs font-bold border <?= $badgeClass ?>">
                                                <?= $statusLabel ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right font-black text-green-600 text-base">
                                            R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <!-- BOTÕES IGUAIS AOS DA TELA INICIAL -->
                                            <?php if ($os['status'] === 'completed'): ?>
                                                <a href="/app/servicos/gerenciar?id=<?= $os['id'] ?>" class="inline-flex items-center gap-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm">
                                                     Ver OS
                                                </a>
                                            <?php else: ?>
                                                <a href="/app/servicos/gerenciar?id=<?= $os['id'] ?>" class="inline-flex items-center gap-1 bg-elcar-blue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg text-sm transition shadow-sm">
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
        <?php endif; ?>
    <?php endif; ?>
</div>