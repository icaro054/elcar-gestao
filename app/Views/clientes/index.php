<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-3xl font-bold text-elcar-blue">Clientes</h1>
        <p class="text-gray-600">Gestão de proprietários e responsáveis</p>
    </div>
    <a href="/app/clientes/novo" class="bg-elcar-secondary text-white font-bold px-4 py-2 rounded shadow hover:bg-elcar-blue transition">
        + Novo Cliente
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
        <!-- Barra de pesquisa simples (Visual) -->
        <input type="text" placeholder="Pesquisar por nome, CPF ou telefone..." class="w-full max-w-md px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-100 text-gray-600 text-sm uppercase">
                    <th class="py-3 px-6 font-semibold">Nome</th>
                    <th class="py-3 px-6 font-semibold">Telefone</th>
                    <th class="py-3 px-6 font-semibold">E-mail</th>
                    <th class="py-3 px-6 font-semibold text-center">Status</th>
                    <th class="py-3 px-6 font-semibold text-center">Ações</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if (empty($clientes)): ?>
                    <tr>
                        <td colspan="5" class="py-6 px-6 text-center text-gray-500">Nenhum cliente cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($clientes as $cliente): ?>
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="py-3 px-6 font-medium text-elcar-blue"><?= htmlspecialchars($cliente['name']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($cliente['phone']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($cliente['email'] ?? '-') ?></td>
                            <td class="py-3 px-6 text-center">
                                <?php if ($cliente['is_active']): ?>
                                    <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Ativo</span>
                                <?php else: ?>
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <a href="/app/clientes/editar?id=<?= $cliente['id'] ?>" class="text-blue-500 hover:text-blue-700 mr-3">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>