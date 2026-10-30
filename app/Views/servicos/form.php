<div class="mb-6">
    <h1 class="text-3xl font-bold text-elcar-blue">Nova Ordem de Serviço</h1>
    <p class="text-gray-600">Abertura de serviço para um veículo</p>
</div>

<div class="bg-white rounded-lg shadow p-6 max-w-4xl">
    <form action="/app/servicos/salvar" method="POST">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Seleção do Veículo -->
            <div class="col-span-1 md:col-span-2">
                <label for="vehicle_id" class="block text-sm font-medium text-gray-700 mb-1">Veículo (Placa / Modelo - Cliente) *</label>
                <select id="vehicle_id" name="vehicle_id" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary bg-white">
                    <option value="">-- Selecione um veículo --</option>
                    <?php foreach ($veiculos as $v): ?>
                        <option value="<?= $v['id'] ?>">
                            <?= htmlspecialchars($v['plate_display']) ?> - <?= htmlspecialchars($v['model']) ?> 
                            (<?= htmlspecialchars($v['client_name']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Descrição do Serviço -->
            <div class="col-span-1 md:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição do Problema / Serviço Solicitado *</label>
                <textarea id="description" name="description" rows="4" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary"></textarea>
            </div>

            <!-- Status Inicial -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status da OS *</label>
                <select id="status" name="status" required class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary bg-white">
                    <option value="pending" selected>Pendente (Aguardando Orçamento/Aprovação)</option>
                    <option value="in_progress">Em Execução</option>
                    <option value="completed">Concluído</option>
                </select>
            </div>

            <!-- Valor Total (Estimativa) -->
            <div>
                <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-1">Valor Total Estimado (R$)</label>
                <input type="number" step="0.01" min="0" id="total_amount" name="total_amount" value="0.00" class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-elcar-secondary">
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t pt-4 mt-6">
            <a href="/app/servicos" class="bg-white border border-gray-300 text-gray-700 px-6 py-2 rounded shadow-sm hover:bg-gray-50 transition">Cancelar</a>
            <button type="submit" class="bg-elcar-blue text-white px-6 py-2 rounded shadow hover:bg-elcar-secondary transition">Salvar OS</button>
        </div>
    </form>
</div>