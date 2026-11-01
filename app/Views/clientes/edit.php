<div class="max-w-2xl mx-auto bg-white p-5 sm:p-8 rounded-lg shadow-sm border border-gray-100 mt-6">
    
    <!-- Cabeçalho com Botão de Exclusão -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 border-b border-gray-100 pb-4">
        <div>
            <h2 class="text-2xl font-bold text-elcar-blue">Editar Cliente</h2>
            <p class="text-gray-500 text-sm">Atualizar dados de <?= htmlspecialchars($cliente['name']) ?></p>
        </div>
        <a href="/app/clientes/excluir?id=<?= $cliente['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este cliente? O registo será desativado do sistema.');" class="bg-red-50 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg font-bold transition flex items-center gap-2 text-sm border border-red-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            Excluir Cliente
        </a>
    </div>

    <!-- Formulário de Edição -->
    <form action="/app/clientes/atualizar" method="POST" class="space-y-5">
        <input type="hidden" name="id" value="<?= $cliente['id'] ?>">

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nome Completo *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($cliente['name']) ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">CPF / CNPJ</label>
                <input type="text" name="document_number" value="<?= htmlspecialchars($cliente['document_number'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Telemóvel / WhatsApp *</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($cliente['phone']) ?>" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" value="<?= htmlspecialchars($cliente['email'] ?? '') ?>" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Endereço Completo</label>
            <input type="text" name="address" value="<?= htmlspecialchars($cliente['address'] ?? '') ?>" placeholder="Rua, Número, Bairro, CEP, Cidade - UF" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 border-t border-gray-100 mt-6">
            <a href="/app/clientes" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-center">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-elcar-blue text-white font-bold rounded-lg hover:bg-elcar-secondary transition">Guardar Alterações</button>
        </div>
    </form>
</div>