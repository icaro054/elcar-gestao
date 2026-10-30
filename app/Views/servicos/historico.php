<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-elcar-blue">Histórico do Veículo</h1>
            <p class="text-gray-600">Registo completo de manutenções e reparações por placa</p>
        </div>
        <a href="/app/dashboard" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition text-sm">
            &larr; Voltar ao Dashboard
        </a>
    </div>

    <!-- Barra de Pesquisa -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8">
        <form action="/app/historico" method="GET" class="flex flex-col sm:flex-row gap-4">
            <input type="text" name="placa" value="<?= htmlspecialchars($placa ?? '') ?>" placeholder="Digite a placa (ex: ABC-1234 ou ABC1D23)" class="flex-grow px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-elcar-secondary" required>
            <button type="submit" class="bg-elcar-blue text-white px-6 py-2.5 rounded-xl font-bold hover:bg-elcar-secondary transition shadow-sm">Pesquisar Placa</button>
        </form>
    </div>

    <?php if (!empty($placa) && empty($veiculo)): ?>
        <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-6 rounded-2xl text-center">
            <p class="font-bold">Nenhum veículo encontrado com a placa "<?= htmlspecialchars($placa) ?>".</p>
            <p class="text-sm mt-1">Verifique se a placa foi digitada corretamente ou se o veículo está registado no sistema.</p>
        </div>
    <?php elseif (!empty($veiculo)): ?>
        <!-- Dados do Veículo e Cliente -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Veículo</span>
                <h3 class="text-2xl font-black text-elcar-blue"><?= htmlspecialchars($veiculo['plate_display']) ?></h3>
                <p class="text-gray-700 font-medium"><?= htmlspecialchars($veiculo['brand'] . ' ' . $veiculo['model']) ?> (<?= htmlspecialchars($veiculo['model_year'] ?? 'Ano N/D') ?>)</p>
                <p class="text-sm text-gray-500 mt-1">Cor: <?= htmlspecialchars($veiculo['color'] ?? 'Não informada') ?></p>
            </div>
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400 block mb-1">Proprietário (Cliente)</span>
                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($veiculo['client_name']) ?></h3>
                <p class="text-sm text-gray-600 mt-1">Telemóvel: <?= htmlspecialchars($veiculo['phone'] ?? 'Não informado') ?></p>
            </div>
        </div>

        <!-- Linha do Tempo / Lista de OS do Veículo -->
        <h2 class="text-xl font-bold text-gray-800 mb-4">Ordens de Serviço Registadas (<?= count($servicos) ?>)</h2>
        
        <?php if (empty($servicos)): ?>
            <div class="bg-gray-50 p-6 rounded-2xl border border-dashed border-gray-200 text-center text-gray-500">
                Este veículo ainda não possui ordens de serviço registadas.
            </div>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($servicos as $os): ?>
                    <?php
                        $badge = match($os['status']) {
                            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'in_progress' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'completed' => 'bg-green-100 text-green-800 border-green-300',
                            default => 'bg-gray-100 text-gray-800 border-gray-300'
                        };
                        $label = match($os['status']) {
                            'pending' => 'Pendente',
                            'in_progress' => 'Em Execução',
                            'completed' => 'Concluído',
                            default => $os['status']
                        };
                    ?>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-black text-lg text-elcar-blue">OS #<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></span>
                                <span class="<?= $badge ?> text-xs px-3 py-1 rounded-full font-bold uppercase border"><?= $label ?></span>
                                <span class="text-xs text-gray-400">Abertura: <?= date('d/m/Y H:i', strtotime($os['created_at'])) ?></span>
                            </div>
                            <p class="text-gray-700 text-sm max-w-2xl"><?= htmlspecialchars($os['description']) ?></p>
                        </div>
                        <div class="flex flex-row md:flex-col justify-between items-end w-full md:w-auto border-t md:border-t-0 pt-3 md:pt-0 gap-2">
                            <div class="text-left md:text-right">
                                <span class="text-xs text-gray-400 block">Valor Total</span>
                                <strong class="text-lg text-gray-800">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></strong>
                            </div>
                            <a href="/app/servicos/imprimir?id=<?= $os['id'] ?>" class="bg-elcar-secondary hover:bg-elcar-blue text-white text-xs px-4 py-2 rounded-lg font-bold transition shadow-sm">
                                Ver / Imprimir
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>