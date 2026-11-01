<div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-5 sm:p-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-black text-elcar-blue uppercase">Primeiro Acesso</h1>
        <p class="text-gray-500 font-bold text-sm mt-2">Valide os seus dados para criar ou recuperar a sua palavra-passe.</p>
    </div>

    <?php if (isset($erro)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm text-sm font-bold">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>

    <form action="/redefinir-senha" method="POST" class="space-y-4">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">E-mail de Cadastro *</label>
            <input type="email" name="email" required placeholder="exemplo@email.com" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>
        
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">CPF ou CNPJ (Apenas números) *</label>
            <input type="text" name="document_number" required placeholder="00000000000" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>

        <div class="border-t border-gray-100 pt-4 mt-2">
            <label class="block text-sm font-bold text-gray-700 mb-1">Nova Palavra-passe *</label>
            <input type="password" name="nova_senha" required minlength="6" placeholder="Mínimo 6 caracteres" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Nova Palavra-passe *</label>
            <input type="password" name="confirmar_senha" required minlength="6" placeholder="Repita a palavra-passe" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>

        <div class="pt-4 flex flex-col gap-3">
            <button type="submit" class="w-full bg-elcar-blue hover:bg-blue-800 text-white font-black py-3 rounded-lg transition shadow-md">
                Guardar Palavra-passe
            </button>
            <a href="/login" class="text-center text-sm font-bold text-gray-500 hover:text-gray-800 transition py-2">
                &larr; Voltar ao Login
            </a>
        </div>
    </form>
</div>