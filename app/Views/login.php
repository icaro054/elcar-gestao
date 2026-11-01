<div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-5 sm:p-8 relative">
    
    <!-- Botão de Voltar à Landing Page -->
    <a href="/" class="absolute top-6 left-6 text-gray-400 hover:text-elcar-blue transition flex items-center gap-1 text-sm font-bold" title="Voltar ao início">
        &larr; Voltar
    </a>

    <div class="text-center mb-8 mt-2">
        <h1 class="text-3xl font-black text-elcar-blue tracking-tight uppercase">ELCAR</h1>
        <p class="text-gray-500 font-bold text-sm tracking-widest uppercase mt-1">Gestão e Serviços</p>
    </div>

    <!-- Alertas de Erro ou Sucesso -->
    <?php if (isset($erro)): ?>
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm text-sm font-bold">
            <?= htmlspecialchars($erro) ?>
        </div>
    <?php endif; ?>
    <?php if (isset($sucesso)): ?>
        <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm text-sm font-bold">
            <?= htmlspecialchars($sucesso) ?>
        </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" required placeholder="Digite o seu e-mail" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue focus:border-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>
        
        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="block text-sm font-bold text-gray-700">Palavra-passe</label>
            </div>
            <input type="password" name="password" required placeholder="••••••••" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-blue focus:border-elcar-blue outline-none transition bg-gray-50 text-gray-800 font-medium">
        </div>

        <div class="text-right">
            <a href="/recuperar-senha" class="text-sm font-bold text-elcar-blue hover:text-blue-800 transition">Primeiro Acesso / Esqueci a senha</a>
        </div>

        <button type="submit" class="w-full bg-elcar-blue hover:bg-blue-800 text-white font-black py-3 rounded-lg transition shadow-md mt-2 text-lg">
            Entrar no Sistema
        </button>
    </form>
</div>