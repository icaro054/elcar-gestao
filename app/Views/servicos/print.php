<div class="max-w-4xl mx-auto bg-white p-4 sm:p-8 print:p-0 print:shadow-none shadow-sm border border-gray-100 mt-6 mb-8 rounded-lg text-gray-900">
    
    <!-- Cabeçalho da Oficina e Dados da OS -->
    <div class="flex flex-col sm:flex-row sm:justify-between print:flex-row print:justify-between items-start gap-4 border-b-2 border-gray-800 pb-4 mb-6">
        <div>
            <h1 class="text-3xl font-black uppercase tracking-wider">ELCAR</h1>
            <p class="text-sm font-bold uppercase tracking-widest text-gray-700">Autopeças e Serviços</p>
            <p class="text-xs mt-2 text-gray-600">Rodovia CE 040, km 12, 99 - Aquiraz, CE</p>
            <p class="text-xs text-gray-600">Telefone/WhatsApp: (85) 99120-4317</p>
        </div>
        <div class="text-left sm:text-right print:text-right">
            <h2 class="text-2xl font-bold uppercase text-gray-800">Ordem de Serviço</h2>
            <p class="text-2xl font-black text-red-600">#<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></p>
            
            <div class="mt-2 bg-gray-50 border border-gray-300 p-2 rounded text-left inline-block print:bg-white">
                <p class="text-xs text-gray-800 mb-1"><span class="font-bold"> DATA DE CHEGADA:</span> <?= date('d/m/Y H:i', strtotime($os['created_at'])) ?></p>
                <p class="text-xs text-gray-800"><span class="font-bold"> DATA DE SAÍDA:</span> <?= !empty($os['completed_at']) ? date('d/m/Y H:i', strtotime($os['completed_at'])) : 'EM EXECUÇÃO' ?></p>
            </div>
        </div>
    </div>

    <!-- Secção 2 Colunas: Cliente e Veículo -->
    <div class="grid grid-cols-1 sm:grid-cols-2 print:grid-cols-2 gap-6 mb-6">
        <!-- Bloco Cliente -->
        <div class="border border-gray-400 rounded-lg p-3">
            <h3 class="font-black uppercase text-xs mb-2 border-b border-gray-300 pb-1">Dados do Cliente</h3>
            <p class="text-sm mb-1"><span class="font-bold">Nome:</span> <?= htmlspecialchars($os['client_name']) ?></p>
            <p class="text-sm mb-1"><span class="font-bold">CPF/CNPJ:</span> <?= htmlspecialchars($os['document_number'] ?? 'Não informado') ?></p>
            <p class="text-sm mb-1"><span class="font-bold">Telemóvel:</span> <?= htmlspecialchars($os['phone'] ?? 'Não informado') ?></p>
            <p class="text-sm"><span class="font-bold">Endereço:</span> <?= htmlspecialchars($os['address'] ?? 'Não informado') ?></p>
        </div>
        
        <!-- Bloco Veículo -->
        <div class="border border-gray-400 rounded-lg p-3">
            <h3 class="font-black uppercase text-xs mb-2 border-b border-gray-300 pb-1">Dados do Veículo</h3>
            <p class="text-sm mb-1"><span class="font-bold">Placa:</span> <?= htmlspecialchars($os['plate_display']) ?></p>
            <p class="text-sm mb-1"><span class="font-bold">Veículo:</span> <?= htmlspecialchars($os['brand'] . ' ' . $os['model']) ?></p>
            <p class="text-sm mb-1"><span class="font-bold">Ano/Cor:</span> <?= htmlspecialchars($os['model_year'] ?? 'N/I') ?> / <?= htmlspecialchars($os['color'] ?? 'N/I') ?></p>
            <p class="text-sm"><span class="font-bold">Chassi:</span> <?= htmlspecialchars($os['chassis'] ?? 'Não informado') ?></p>
        </div>
    </div>

    <!-- Relato / Descrição -->
    <div class="border border-gray-400 rounded-lg p-3 mb-6 bg-gray-50 print:bg-white">
        <h3 class="font-black uppercase text-xs mb-2">Relato Inicial do Cliente</h3>
        <p class="text-sm whitespace-pre-wrap"><?= htmlspecialchars($os['description']) ?: 'Sem relato inicial.' ?></p>
    </div>

    <!-- TABELA DE PEÇAS E SERVIÇOS -->
    <div class="mb-6">
        <h3 class="font-black uppercase text-sm mb-2 border-b-2 border-gray-800 pb-1">Peças Aplicadas e Materiais</h3>
        <div class="overflow-x-auto print:overflow-visible">
        <table class="w-full min-w-[600px] sm:min-w-0 print:min-w-0 text-left text-sm border-collapse border border-gray-400">
            <thead>
                <tr class="bg-gray-100 text-gray-900 text-xs uppercase font-bold print:bg-gray-100">
                    <th class="py-2 px-2 border border-gray-400 w-20">Código</th>
                    <th class="py-2 px-2 border border-gray-400">Material / Serviço</th>
                    <th class="py-2 px-2 border border-gray-400 text-center w-12">Qtd</th>
                    <th class="py-2 px-2 border border-gray-400 text-right w-24">V. Unit</th>
                    <th class="py-2 px-2 border border-gray-400 text-right w-24">Total</th>
                    <th class="py-2 px-2 border border-gray-400 w-24">Mecânico</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($itens)): ?>
                    <tr><td colspan="6" class="py-4 text-center text-gray-500 border border-gray-400 font-bold">Nenhum registo de peças.</td></tr>
                <?php else: ?>
                    <?php foreach($itens as $item): ?>
                        <tr class="text-xs">
                            <td class="py-1.5 px-2 border border-gray-400"><?= htmlspecialchars($item['code'] ?? '') ?></td>
                            <td class="py-1.5 px-2 border border-gray-400 font-bold uppercase"><?= htmlspecialchars($item['description']) ?></td>
                            <td class="py-1.5 px-2 border border-gray-400 text-center"><?= (float)$item['quantity'] ?></td>
                            <td class="py-1.5 px-2 border border-gray-400 text-right">R$ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                            <td class="py-1.5 px-2 border border-gray-400 text-right font-bold">R$ <?= number_format((float)$item['quantity'] * (float)$item['unit_price'], 2, ',', '.') ?></td>
                            <td class="py-1.5 px-2 border border-gray-400 uppercase"><?= htmlspecialchars($item['mechanic_name'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Valor Total Separado (Peças + Mão de Obra) -->
    <div class="flex justify-end mb-12">
        <div class="w-full sm:w-2/3 bg-gray-50 border border-gray-400 rounded-lg p-3 text-right print:bg-white">
            <?php 
                $totalPecas = (float)$os['total_amount'] - (float)($os['labor_cost'] ?? 0); 
            ?>
            <div class="flex justify-between border-b border-gray-300 pb-1 mb-1 text-sm">
                <span class="font-bold text-gray-600 uppercase">Subtotal Peças:</span>
                <span class="font-bold text-gray-800">R$ <?= number_format($totalPecas, 2, ',', '.') ?></span>
            </div>
            <div class="flex justify-between border-b border-gray-800 pb-1 mb-2 text-sm">
                <span class="font-bold text-gray-600 uppercase">Valor Mão de Obra:</span>
                <span class="font-bold text-gray-800">R$ <?= number_format((float)($os['labor_cost'] ?? 0), 2, ',', '.') ?></span>
            </div>
            <div class="flex justify-between items-end mt-2">
                <span class="text-xs font-black uppercase tracking-wider text-gray-900">Total a Pagar:</span>
                <span class="text-3xl font-black text-gray-900">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- Assinaturas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 print:grid-cols-2 gap-12 mt-12 px-4 sm:px-8 text-center print:break-inside-avoid">
        <div>
            <div class="border-t border-gray-800 w-full mb-2"></div>
            <p class="text-sm font-bold uppercase">Assinatura do Cliente</p>
        </div>
        <div>
            <div class="border-t border-gray-800 w-full mb-2"></div>
            <p class="text-sm font-bold uppercase">Assinatura da Oficina</p>
        </div>
    </div>

    <!-- Botões de Ação (Não saem na impressão) -->
    <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row justify-center gap-4 print:hidden">
        <a href="/app/servicos/gerenciar?id=<?= $os['id'] ?>" class="px-6 py-2.5 bg-gray-200 text-gray-800 font-bold rounded-lg hover:bg-gray-300 transition">
            &larr; Voltar para OS
        </a>
        <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-elcar-blue text-white font-bold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
             Imprimir OS Completa
        </button>
    </div>
</div>