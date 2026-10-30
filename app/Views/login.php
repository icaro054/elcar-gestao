<div class="flex items-center justify-center min-h-[70vh]">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-elcar-blue">Acesso ao Sistema</h2>
            <p class="text-gray-600 text-sm mt-1">Insira as suas credenciais para continuar</p>
        </div>

        <!-- Exibição de mensagens de erro genéricas (Secção 6.2) -->
        <?php if (isset($erro)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                <span class="block sm:inline"><?= htmlspecialchars($erro) ?></span>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-elcar-text mb-1">E-mail</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary"
                    placeholder="seu@email.com">
            </div>

            <div class="mb-6 relative">
                <label for="password" class="block text-sm font-medium text-elcar-text mb-1">Senha</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary"
                        placeholder="••••••••">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-elcar-blue">
                        <!-- Ícone de Olho -->
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between mb-6">
                <a href="/recuperar-senha" class="text-sm text-elcar-secondary hover:text-elcar-blue font-medium transition">
                    Esqueci minha senha
                </a>
            </div>

            <button type="submit" class="w-full bg-elcar-blue text-white font-bold py-2 px-4 rounded hover:bg-elcar-secondary transition duration-200">
                Entrar
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-sm text-gray-500 hover:text-elcar-blue transition">
                &larr; Voltar para a página inicial
            </a>
        </div>
    </div>
</div>

<script>
    // Script para mostrar/ocultar senha (Secção 6.2)
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
    });
</script>