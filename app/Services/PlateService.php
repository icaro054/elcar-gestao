<?php

namespace App\Services;

class PlateService
{
    /**
     * Valida se a placa está no formato Nacional (ABC-1234, ABC1234) ou Mercosul (ABC1D23)
     */
    public static function validate(string $plate): bool
    {
        // Remove espaços e traços
        $cleanPlate = preg_replace('/[^a-zA-Z0-9]/', '', $plate);
        
        // Verifica se tem exatamente 7 caracteres
        if (strlen($cleanPlate) !== 7) {
            return false;
        }

        // Regex para validar Nacional (ABC1234) ou Mercosul (ABC1D23)
        // 3 Letras + 1 Número + 1 Letra/Número + 2 Números
        return preg_match('/^[a-zA-Z]{3}[0-9][a-zA-Z0-9][0-9]{2}$/', $cleanPlate) === 1;
    }

    /**
     * Normaliza a placa para guardar na base de dados (tudo maiúsculo e sem traços)
     */
    public static function normalize(string $plate): string
    {
        $cleanPlate = preg_replace('/[^a-zA-Z0-9]/', '', $plate);
        return strtoupper($cleanPlate);
    }

    /**
     * Formata a placa para exibição no ecrã (adiciona o traço se for modelo antigo)
     */
    public static function formatDisplay(string $plate): string
    {
        $normalized = self::normalize($plate);
        
        // Se o 5º caractere for um número, é formato antigo (ABC-1234)
        if (isset($normalized[4]) && is_numeric($normalized[4])) {
            return substr($normalized, 0, 3) . '-' . substr($normalized, 3);
        }
        
        // Se for Mercosul, devolve sem traço (ABC1D23)
        return $normalized;
    }
}