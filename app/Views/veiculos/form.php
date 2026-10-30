<div class="mb-6">
    <h1 class="text-3xl font-bold text-elcar-blue">Novo Veículo</h1>
    <p class="text-gray-600">Registe os dados do veículo e vincule ao cliente</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-3xl">
    <form action="/app/veiculos/salvar" method="POST">
        
        <!-- Seleção de Cliente (Secção 6.5) -->
        <div class="mb-6">
            <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Cliente Proprietário *</label>
            <select id="client_id" name="client_id" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary bg-white">
                <option value="">-- Selecione um cliente --</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= $cliente['id'] ?>">
                        <?= htmlspecialchars($cliente['name']) ?> 
                        <?= $cliente['document_number'] ? ' (' . htmlspecialchars($cliente['document_number']) . ')' : '' ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Placa (Secção 5.2 e 6.5) -->
            <div class="col-span-1">
                <label for="plate" class="block text-sm font-medium text-gray-700 mb-1">Placa *</label>
                <input type="text" id="plate" name="plate" required placeholder="ABC-1234 ou ABC1D23" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary uppercase">
            </div>

            <!-- Marca -->
            <div class="col-span-1">
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Marca</label>
                <input type="text" id="brand" name="brand" placeholder="Ex: Chevrolet" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- Modelo -->
            <div class="col-span-1">
                <label for="model" class="block text-sm font-medium text-gray-700 mb-1">Modelo / Nome *</label>
                <input type="text" id="model" name="model" required placeholder="Ex: Onix 1.0" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- Cor -->
            <div class="col-span-1">
                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Cor</label>
                <input type="text" id="color" name="color" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- Ano -->
            <div class="col-span-1">
                <label for="model_year" class="block text-sm font-medium text-gray-700 mb-1">Ano / Modelo</label>
                <input type="number" id="model_year" name="model_year" min="1950" max="<?= date('Y') + 1 ?>" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>

            <!-- Chassi -->
            <div class="col-span-1">
                <label for="chassis" class="block text-sm font-medium text-gray-700 mb-1">Chassi</label>
                <input type="text" id="chassis" name="chassis" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary uppercase">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-4 mt-6">
            <a href="/app/veiculos" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded shadow-sm hover:bg-gray-50 transition">Cancelar</a>
            <button type="submit" class="bg-elcar-blue text-white px-6 py-2 rounded shadow hover:bg-elcar-secondary transition">Salvar Veículo</button>
        </div>
    </form>
</div>