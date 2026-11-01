<div class="max-w-2xl mx-auto bg-white p-5 sm:p-8 rounded-lg shadow-sm border border-gray-100 mt-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-elcar-blue">Novo Cliente</h2>
        <p class="text-gray-500 text-sm">Registe os dados do proprietário do veículo.</p>
    </div>

    <form action="/app/clientes/salvar" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Nome Completo *</label>
            <input type="text" name="name" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">CPF / CNPJ</label>
                <input type="text" name="document_number" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Telemóvel / WhatsApp *</label>
                <input type="text" name="phone" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">E-mail</label>
            <input type="email" name="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div>
            <label class="block text-sm font-bold text-gray-700 mb-1">Endereço Completo</label>
            <input type="text" name="address" placeholder="Rua, Número, Bairro, CEP, Cidade - UF" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-elcar-secondary">
        </div>

        <div class="pt-4 flex flex-col-reverse sm:flex-row gap-3 border-t border-gray-100">
            <a href="/app/clientes" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200 transition text-center">Cancelar</a>
            <button type="submit" class="px-6 py-2.5 bg-elcar-blue text-white font-bold rounded-lg hover:bg-elcar-secondary transition">Guardar Cliente</button>
        </div>
    </form>
</div>