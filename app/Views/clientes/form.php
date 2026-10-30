<div class="mb-6">
    <h1 class="text-3xl font-bold text-elcar-blue">Novo Cliente</h1>
    <p class="text-gray-600">Preencha os dados do proprietário</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <form action="/app/clientes/salvar" method="POST">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Nome (Obrigatório) -->
            <div class="col-span-1 md:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome Completo / Razão Social *</label>
                <input type="text" id="name" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- CPF/CNPJ -->
            <div>
                <label for="document_number" class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
                <input type="text" id="document_number" name="document_number" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- Telefone (Obrigatório) -->
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone / WhatsApp *</label>
                <input type="text" id="phone" name="phone" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- E-mail -->
            <div class="col-span-1 md:col-span-2">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                <input type="email" id="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>
            
            <!-- Endereço -->
            <div class="col-span-1 md:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Endereço Completo</label>
                <input type="text" id="address" name="address" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-4 mt-6">
            <a href="/app/clientes" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded shadow-sm hover:bg-gray-50 transition">Cancelar</a>
            <button type="submit" class="bg-elcar-blue text-white px-6 py-2 rounded shadow hover:bg-elcar-secondary transition">Salvar Cliente</button>
        </div>
    </form>
</div>