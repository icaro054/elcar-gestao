<div class="max-w-5xl mx-auto px-4 py-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-bold text-elcar-blue">Lista de Reposição</h1>
            <p class="text-gray-600 font-bold mt-1">Controlo rápido de peças que precisam de ser compradas.</p>
        </div>
        
        <!-- Botoes de Ação no Topo -->
        <div class="flex flex-col sm:flex-row flex-wrap gap-2">
            <form action="/app/estoque/compras/importar" method="POST">
                <button type="submit" onclick="return confirm('Deseja puxar todas as peças das Ordens de Serviço CONCLUÍDAS que ainda não estão nesta lista?')" class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-4 rounded-lg transition shadow-sm border border-green-800 flex items-center justify-center gap-2">
                     Puxar Peças Vendidas
                </button>
            </form>
            <a href="/app/dashboard" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-2.5 px-4 rounded-lg transition shadow-sm border border-gray-300 text-center">
                &larr; Voltar
            </a>
        </div>
    </div>

    <!-- Formulário para Adicionar Peça Manualmente -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="w-full md:w-auto">
            <h3 class="font-bold text-gray-800">Adicionar Item Manualmente</h3>
            <p class="text-xs text-gray-500">Ferramentas ou peças que não vieram de uma OS.</p>
        </div>
        <form action="/app/estoque/compras/adicionar" method="POST" class="flex flex-col md:flex-row gap-2 w-full md:w-auto items-end">
            <div class="w-full md:w-auto">
                <input type="text" name="code" placeholder="Cód (Opcional)" class="w-full md:w-32 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-elcar-blue uppercase text-sm font-bold">
            </div>
            <div class="w-full md:w-auto">
                <input type="text" name="description" required placeholder="NOME DA PEÇA *" class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-elcar-blue uppercase text-sm font-bold">
            </div>
            <div class="w-full md:w-auto">
                <input type="number" step="0.01" min="0.01" name="quantity" required value="1" placeholder="Qtd" class="w-full md:w-24 px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-elcar-blue text-sm font-bold">
            </div>
            <button type="submit" class="w-full md:w-auto bg-gray-800 hover:bg-gray-900 text-white font-bold py-2 px-4 rounded transition shadow-sm whitespace-nowrap text-sm">
                + Inserir
            </button>
        </form>
    </div>

    <!-- Tabela da Lista de Compras -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 bg-gray-50 flex flex-wrap justify-between items-center gap-2">
            <h2 class="text-lg font-bold text-elcar-blue">Aguardando Compra</h2>
            <span class="bg-elcar-yellow text-elcar-text px-2 py-1 rounded text-xs font-bold shadow-sm">Para reposição</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-white text-gray-500 font-bold border-b border-gray-100 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-4 w-32">Código</th>
                        <th class="px-6 py-4">Peça / Material</th>
                        <th class="px-6 py-4 text-center w-24">Qtd</th>
                        <th class="px-6 py-4 text-center w-40">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($itens)): ?>
                        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 font-bold">A sua lista de reposição está vazia! Tudo em dia.</td></tr>
                    <?php else: ?>
                        <?php foreach ($itens as $item): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 text-gray-500 font-bold"><?= htmlspecialchars($item['code'] ?? '-') ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800 uppercase text-base"><?= htmlspecialchars($item['description']) ?></td>
                                <td class="px-6 py-4 text-center text-xl font-black text-elcar-blue"><?= (float)$item['quantity'] ?></td>
                                <td class="px-6 py-4 text-center">
                                    <form action="/app/estoque/compras/remover" method="POST" onsubmit="return confirm('Esta peça já chegou à oficina? Ela será removida da lista.');">
                                        <input type="hidden" name="id" value="<?= $item['id'] ?>">
                                        <button type="submit" class="bg-green-50 hover:bg-green-100 text-green-700 font-bold py-2 px-4 rounded-lg border border-green-200 transition shadow-sm text-xs flex items-center justify-center gap-1 mx-auto w-full">
                                             Já Comprado
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>