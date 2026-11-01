<div class="max-w-3xl mx-auto bg-white p-5 sm:p-8 rounded-lg shadow-sm border border-gray-100 mt-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-elcar-blue">Abertura de Ordem de Serviço</h2>
        <p class="text-gray-500 text-sm">Inicie um novo atendimento registando o veículo e o relato do cliente.</p>
    </div>

    <form action="/app/servicos/salvar" method="POST" class="space-y-6">
        
        <!-- CAMPO VEÍCULO DIGITÁVEL -->
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Selecionar Veículo (Digite a placa ou carro) *</label>
            <select name="vehicle_id" id="vehicle-select" required class="w-full" placeholder="Comece a digitar a placa ou nome...">
                <option value="">Selecione um veículo...</option>
                <?php foreach ($veiculos as $v): ?>
                    <option value="<?= $v['id'] ?>">
                        <?= htmlspecialchars($v['plate_display']) ?> - <?= htmlspecialchars($v['model']) ?> (Dono: <?= htmlspecialchars($v['client_name']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Relato do Problema / Serviço Inicial *</label>
            <textarea name="description" rows="4" required placeholder="Descreva o problema relatado..." class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary resize-none"></textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Status Inicial</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50">
                    <option value="pending">Pendente / Orçamento</option>
                    <option value="in_progress">Em Execução</option>
                    <option value="completed">Concluído</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Valor Estimado (R$)</label>
                <input type="number" step="0.01" min="0" name="total_amount" value="0.00" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            </div>
        </div>

        <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 border-t border-gray-100">
            <a href="/app/servicos" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 text-center">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-elcar-blue text-white font-bold rounded-lg hover:bg-elcar-secondary">Abrir OS</button>
        </div>
    </form>
</div>

<!-- Scripts para fazer o campo Select aceitar digitação -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        new TomSelect('#vehicle-select', {
            create: false,
            sortField: { field: "text", direction: "asc" }
        });
    });
</script>