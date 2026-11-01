<?php $isCompleted = ($os['status'] === 'completed'); ?>
<div class="max-w-6xl mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-elcar-blue break-words">Ordem de Serviço #<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></h1>
            <p class="text-gray-600 font-bold break-words">Cliente: <?= htmlspecialchars($os['client_name']) ?> | Veículo: <?= htmlspecialchars($os['plate_display']) ?></p>
            <div class="flex flex-wrap gap-x-2 gap-y-1 mt-3 text-sm font-bold bg-gray-50 p-2 rounded-lg border border-gray-200 inline-block">
                <span class="text-gray-600"> Chegada: <?= date('d/m/Y H:i', strtotime($os['created_at'])) ?></span>
                <span class="text-gray-300">|</span>
                <?php if($isCompleted): ?>
                    <span class="text-green-600"> Saída: <?= date('d/m/Y H:i', strtotime($os['completed_at'])) ?></span>
                <?php else: ?>
                    <span class="text-yellow-600"> Em andamento</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
            <a href="/app/servicos/imprimir?id=<?= $os['id'] ?>" target="_blank" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2.5 rounded-lg font-bold transition shadow-sm border border-gray-300 text-center"> Imprimir OS</a>
            <a href="/app/servicos" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2.5 rounded-lg font-bold transition shadow-sm border border-gray-300 text-center">Voltar</a>
        </div>
    </div>

    <?php if($isCompleted): ?>
        <!-- AVISO DE OS BLOQUEADA -->
        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <span class="text-green-500 text-xl mr-3"></span>
                <div>
                    <h3 class="font-bold text-green-800">Ordem de Serviço Concluída e Bloqueada</h3>
                    <p class="text-sm text-green-700">Esta OS foi finalizada e serve apenas como histórico. Não é possível alterar peças, valores ou status.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna da Esquerda -->
        <div class="lg:col-span-1 space-y-6">
            
            <?php if(!$isCompleted): ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Atualizar OS</h3>
                <form action="/app/servicos/atualizar-status" method="POST" class="flex flex-col gap-4">
                    <input type="hidden" name="id" value="<?= $os['id'] ?>">
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Status do Serviço</label>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-bold text-gray-800 focus:ring-2 focus:ring-elcar-blue">
                            <option value="pending" <?= $os['status'] === 'pending' ? 'selected' : '' ?>>Pendente / Orçamento</option>
                            <option value="in_progress" <?= $os['status'] === 'in_progress' ? 'selected' : '' ?>>Em Execução</option>
                            <option value="completed" <?= $os['status'] === 'completed' ? 'selected' : '' ?>>Concluído (Regista Saída)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1">Mão de Obra do Mecânico (R$)</label>
                        <input type="number" step="0.01" min="0" name="labor_cost" value="<?= (float)($os['labor_cost'] ?? 0) ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg font-bold text-blue-700 focus:ring-2 focus:ring-elcar-blue bg-blue-50">
                    </div>

                    <button type="submit" class="w-full bg-elcar-blue hover:bg-blue-800 text-white font-bold py-2.5 rounded-lg transition shadow-sm">Guardar Alterações</button>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-3">Detalhes do Fecho</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                        <span class="text-gray-500 font-bold">Subtotal Peças:</span>
                        <span class="text-gray-800 font-bold">R$ <?= number_format((float)$os['total_amount'] - (float)($os['labor_cost'] ?? 0), 2, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-sm border-b border-gray-100 pb-2">
                        <span class="text-gray-500 font-bold">Mão de Obra:</span>
                        <span class="text-gray-800 font-bold">R$ <?= number_format((float)($os['labor_cost'] ?? 0), 2, ',', '.') ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Valor Total</span>
                <span class="text-3xl sm:text-4xl font-black text-green-600">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></span>
            </div>
        </div>

        <!-- Coluna da Direita (Grelha) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-elcar-blue mb-4">Peças e Materiais</h3>
            
            <?php if(!$isCompleted): ?>
            <form action="/app/servicos/adicionar-item" method="POST" class="bg-gray-50 p-5 rounded-xl border border-gray-200 mb-6">
                <input type="hidden" name="service_order_id" value="<?= $os['id'] ?>">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="sm:col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Código</label>
                        <input type="text" name="code" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-elcar-blue text-sm uppercase">
                    </div>
                    <div class="sm:col-span-2 md:col-span-2">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Material / Peça *</label>
                        <input type="text" name="description" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-elcar-blue text-sm uppercase">
                    </div>
                    <div class="sm:col-span-2 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Mecânico (Opcional)</label>
                        <input type="text" name="mechanic_name" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-elcar-blue text-sm uppercase">
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 items-end">
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">Qtd *</label>
                        <input type="number" step="0.01" min="0.01" name="quantity" value="1" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-elcar-blue text-sm">
                    </div>
                    <div class="col-span-1 md:col-span-1">
                        <label class="block text-xs font-bold text-gray-700 mb-1">V. Unit (R$) *</label>
                        <input type="number" step="0.01" min="0" name="unit_price" required class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-elcar-blue text-sm">
                    </div>
                    <div class="sm:col-span-2 md:col-span-2">
                        <button type="submit" class="w-full bg-elcar-blue hover:bg-blue-700 text-white font-bold py-2 rounded transition shadow-sm text-sm">+ Inserir Peça</button>
                    </div>
                </div>
            </form>
            <?php endif; ?>

            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 text-xs uppercase font-bold border-b border-gray-200">
                            <th class="py-2 px-3">Material</th>
                            <th class="py-2 px-3 text-center">Qtd</th>
                            <th class="py-2 px-3 text-right">Unitário</th>
                            <th class="py-2 px-3 text-right">Total</th>
                            <?php if(!$isCompleted): ?>
                            <th class="py-2 px-3 text-center">Ação</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($itens)): ?>
                            <tr><td colspan="5" class="py-6 text-center text-gray-400 font-bold">Nenhuma peça adicionada.</td></tr>
                        <?php else: ?>
                            <?php foreach($itens as $item): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-2 px-3 font-bold text-gray-800 uppercase text-xs">
                                        <span class="block text-gray-400 font-normal"><?= htmlspecialchars($item['code'] ?? '') ?></span>
                                        <?= htmlspecialchars($item['description']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-center"><?= (float)$item['quantity'] ?></td>
                                    <td class="py-2 px-3 text-right text-gray-600">R$ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                                    <td class="py-2 px-3 text-right font-bold text-elcar-blue">R$ <?= number_format((float)$item['quantity'] * (float)$item['unit_price'], 2, ',', '.') ?></td>
                                    
                                    <?php if(!$isCompleted): ?>
                                    <td class="py-2 px-3 text-center">
                                        <form action="/app/servicos/remover-item" method="POST" onsubmit="return confirm('Deseja remover esta peça da OS?');">
                                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                                            <input type="hidden" name="os_id" value="<?= $os['id'] ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-bold bg-red-50 hover:bg-red-100 px-2 py-1 rounded text-xs transition">Remover</button>
                                        </form>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>