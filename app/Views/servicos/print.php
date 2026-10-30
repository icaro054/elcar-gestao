<div class="max-w-3xl mx-auto bg-white p-8 my-6 rounded-lg shadow-sm border border-gray-200 print:shadow-none print:border-none print:my-0 print:p-0">
    
    <!-- Botões de Ação (Aparecem no ecrã, somem na impressão) -->
    <div class="flex justify-between items-center mb-8 pb-4 border-b border-gray-200 print:hidden">
        <a href="/app/servicos" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-medium transition text-sm flex items-center gap-2">
            &larr; Voltar para a Lista
        </a>
        <button onclick="window.print()" class="bg-elcar-blue hover:bg-elcar-secondary text-white px-6 py-2 rounded-lg font-bold transition shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Imprimir OS
        </button>
    </div>

    <!-- Cabeçalho do Documento -->
    <div class="flex justify-between items-start border-b-2 border-elcar-blue pb-6 mb-6">
        <div>
            <h1 class="text-3xl font-black text-elcar-blue tracking-wider">ELCAR</h1>
            <p class="text-sm font-semibold text-gray-600">Autopeças e Serviços Automotivos</p>
            <p class="text-xs text-gray-500 mt-1">Fortaleza - CE | CNPJ: 00.000.000/0001-00</p>
        </div>
        <div class="text-right">
            <span class="inline-block bg-elcar-blue text-white text-xs px-3 py-1 rounded font-bold uppercase tracking-wider mb-1">Ordem de Serviço</span>
            <h2 class="text-2xl font-black text-gray-800">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></h2>
            <p class="text-xs text-gray-500 mt-1">Data: <?= date('d/m/Y H:i', strtotime($os['created_at'])) ?></p>
        </div>
    </div>

    <!-- Dados do Cliente e Veículo -->
    <div class="grid grid-cols-2 gap-6 mb-6 text-sm">
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 print:bg-white print:border-gray-300">
            <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider mb-2 border-b pb-1">Dados do Cliente</h3>
            <p><strong class="text-gray-900">Nome:</strong> <?= htmlspecialchars($os['client_name']) ?></p>
            <p><strong class="text-gray-900">Telefone:</strong> <?= htmlspecialchars($os['phone'] ?? 'Não informado') ?></p>
            <p><strong class="text-gray-900">CPF/CNPJ:</strong> <?= htmlspecialchars($os['document_number'] ?? 'Não informado') ?></p>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 print:bg-white print:border-gray-300">
            <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider mb-2 border-b pb-1">Dados do Veículo</h3>
            <p><strong class="text-gray-900">Modelo:</strong> <?= htmlspecialchars($os['model']) ?> (<?= htmlspecialchars($os['brand'] ?? '') ?>)</p>
            <p><strong class="text-gray-900">Placa:</strong> <span class="font-bold text-elcar-blue"><?= htmlspecialchars($os['plate_display']) ?></span></p>
            <p><strong class="text-gray-900">Cor / Ano:</strong> <?= htmlspecialchars($os['color'] ?? '-') ?> / <?= htmlspecialchars($os['model_year'] ?? '-') ?></p>
        </div>
    </div>

    <!-- Descrição do Serviço / Relato do Problema -->
    <div class="mb-8">
        <h3 class="font-bold text-gray-700 uppercase text-xs tracking-wider mb-2">Relato do Problema / Serviços Solicitados</h3>
        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[120px] text-gray-800 text-sm whitespace-pre-line print:bg-white print:border-gray-300">
            <?= htmlspecialchars($os['description']) ?>
        </div>
    </div>

    <!-- Status e Valor Total -->
    <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg mb-10 print:bg-gray-50 print:border print:border-gray-300">
        <div>
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider block">Status Atual</span>
            <span class="font-bold text-gray-800 uppercase text-sm">
                <?php
                    $labels = ['pending' => 'Pendente', 'in_progress' => 'Em Execução', 'completed' => 'Concluído', 'cancelled' => 'Cancelado'];
                    echo $labels[$os['status']] ?? $os['status'];
                ?>
            </span>
        </div>
        <div class="text-right">
            <span class="text-xs text-gray-500 font-bold uppercase tracking-wider block">Valor Total Estimado</span>
            <span class="text-2xl font-black text-elcar-blue">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></span>
        </div>
    </div>

    <!-- Assinaturas -->
    <div class="grid grid-cols-2 gap-12 mt-16 pt-8 text-center text-sm">
        <div class="border-t border-gray-400 pt-2">
            <p class="font-bold text-gray-700"><?= htmlspecialchars($os['client_name']) ?></p>
            <p class="text-xs text-gray-500">Assinatura do Cliente</p>
        </div>
        <div class="border-t border-gray-400 pt-2">
            <p class="font-bold text-gray-700">Elcar Autopeças e Serviços</p>
            <p class="text-xs text-gray-500">Responsável Técnico</p>
        </div>
    </div>
</div>