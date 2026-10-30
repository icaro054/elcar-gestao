<!-- Contentor Centralizado para não esticar no monitor todo -->
<div class="max-w-6xl mx-auto px-4 py-10 w-full">
    
    <!-- Cabeçalho Modernizado com Gradiente -->
    <div class="mb-10 flex flex-col md:flex-row justify-between items-center bg-gradient-to-r from-elcar-blue to-elcar-secondary text-white p-8 rounded-2xl shadow-lg">
        <div class="mb-4 md:mb-0 text-center md:text-left">
            <h1 class="text-3xl font-extrabold mb-1">Olá, <?= htmlspecialchars($nome_cliente) ?>!</h1>
            <p class="text-blue-100 font-medium">Bem-vindo ao seu Portal de Cliente Elcar</p>
        </div>
        <a href="/logout" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2.5 rounded-lg font-bold transition shadow flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Sair da Conta
        </a>
    </div>

    <!-- Secção: Minhas Ordens de Serviço -->
    <div class="mb-12">
        <div class="flex items-center gap-3 mb-6 border-b-2 border-gray-100 pb-3">
            <svg class="w-7 h-7 text-elcar-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <h2 class="text-2xl font-bold text-gray-800">Ordens de Serviço em Aberto</h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <?php if (empty($servicos)): ?>
                <div class="col-span-1 md:col-span-2 bg-gray-50 p-8 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-500">
                    <p class="font-medium">Não existem ordens de serviço para os seus veículos neste momento.</p>
                </div>
            <?php else: ?>
                <?php foreach ($servicos as $os): ?>
                    <?php
                        $borda = 'border-gray-200';
                        $badge = 'bg-gray-100 text-gray-800';
                        $label = 'Desconhecido';

                        if ($os['status'] === 'pending') {
                            $borda = 'border-yellow-400';
                            $badge = 'bg-yellow-100 text-yellow-800';
                            $label = 'Pendente / Orçamento';
                        } elseif ($os['status'] === 'in_progress') {
                            $borda = 'border-blue-400';
                            $badge = 'bg-blue-100 text-blue-800';
                            $label = 'Em Execução';
                        } elseif ($os['status'] === 'completed') {
                            $borda = 'border-green-400';
                            $badge = 'bg-green-100 text-green-800';
                            $label = 'Concluído';
                        }
                    ?>
                    
                    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 <?= $borda ?> hover:shadow-md transition">
                        <div class="flex justify-between items-start mb-4">
                            <span class="font-black text-xl text-elcar-blue">OS #<?= str_pad((string)$os['id'], 5, '0', STR_PAD_LEFT) ?></span>
                            <span class="<?= $badge ?> text-xs px-3 py-1.5 rounded-full font-bold uppercase tracking-wider"><?= $label ?></span>
                        </div>
                        
                        <p class="text-gray-600 mb-5 line-clamp-2 min-h-[3rem]" title="<?= htmlspecialchars($os['description']) ?>">
                            <?= htmlspecialchars($os['description']) ?>
                        </p>
                        
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end border-t border-gray-100 pt-4 mt-2">
                            <div class="text-sm text-gray-500 mb-3 sm:mb-0">
                                <p class="mb-1"><strong class="text-gray-700">Veículo:</strong> <?= htmlspecialchars($os['model']) ?> (<?= htmlspecialchars($os['plate_display']) ?>)</p>
                                <p><strong class="text-gray-700">Abertura:</strong> <?= date('d/m/Y', strtotime($os['created_at'])) ?></p>
                            </div>
                            <div class="text-left sm:text-right bg-gray-50 px-4 py-2 rounded-lg">
                                <span class="block text-xs text-gray-500 font-medium">Valor Total Estimado</span>
                                <strong class="text-xl text-elcar-blue">R$ <?= number_format((float)$os['total_amount'], 2, ',', '.') ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Secção: Meus Veículos -->
    <div>
        <div class="flex items-center gap-3 mb-6 border-b-2 border-gray-100 pb-3">
            <svg class="w-7 h-7 text-elcar-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            <h2 class="text-2xl font-bold text-gray-800">Meus Veículos Registados</h2>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-5">
            <?php if (empty($veiculos)): ?>
                <div class="col-span-1 sm:col-span-2 md:col-span-3 bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-500">
                    Nenhum veículo vinculado à sua conta.
                </div>
            <?php else: ?>
                <?php foreach ($veiculos as $v): ?>
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:border-elcar-secondary transition">
                        <div class="bg-blue-50 p-4 rounded-xl text-elcar-blue">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-800 text-xl tracking-wide"><?= htmlspecialchars($v['plate_display']) ?></h3>
                            <p class="text-sm font-medium text-gray-500 uppercase"><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>