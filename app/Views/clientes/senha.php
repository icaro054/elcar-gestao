<div class="max-w-md mx-auto px-4 py-12">
    <div class="bg-white p-5 sm:p-8 rounded-2xl shadow-sm border border-gray-100">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-elcar-blue">Alterar Palavra-passe</h2>
            <p class="text-gray-500 text-sm mt-2">Mantenha a sua conta segura escolhendo uma palavra-passe forte.</p>
        </div>

        <form action="/cliente/atualizar-senha" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Palavra-passe Atual *</label>
                <input type="password" name="senha_atual" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue">
            </div>
            
            <div class="border-t border-gray-100 pt-4 mt-2">
                <label class="block text-sm font-bold text-gray-700 mb-1">Nova Palavra-passe *</label>
                <input type="password" name="nova_senha" required minlength="6" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Confirmar Nova Palavra-passe *</label>
                <input type="password" name="confirmar_senha" required minlength="6" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue">
            </div>

            <div class="pt-4 flex flex-col sm:flex-row gap-3">
                <a href="/cliente" class="sm:w-1/3 text-center px-4 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition">Voltar</a>
                <button type="submit" class="sm:w-2/3 bg-elcar-blue hover:bg-blue-800 text-white font-bold py-2.5 rounded-lg transition shadow-sm">
                    Guardar Senha
                </button>
            </div>
        </form>
    </div>
</div>