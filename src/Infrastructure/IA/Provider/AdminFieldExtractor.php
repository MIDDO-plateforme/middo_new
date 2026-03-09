<?php

namespace App\Infrastructure\IA\Provider;

class AdminFieldExtractor
{
    public function extract(string $text): array
    {
        $clean = $this->normalize($text);

        return [
            'name' => $this->extractName($clean),
            'address' => $this->extractAddress($clean),
            'id_number' => $this->extractIdNumber($clean),
            'income' => $this->extractIncome($clean),
            'dates' => $this->extractDates($clean),
            'amounts' => $this->extractAmounts($clean),
        ];
    }

    private function normalize(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text);
        return mb_strtolower(trim($text));
    }

    private function extractName(string $text): ?string
    {
        if (preg_match('/nom[:\s]+([a-zàâçéèêëîïôûùüÿñ\- ]{3,})/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractAddress(string $text): ?string
    {
        if (preg_match('/adresse[:\s]+(.+?)(?=code postal|cp|$)/i', $text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    private function extractIdNumber(string $text): ?string
    {
        if (preg_match('/(numéro|numero|n°)[^\d]*(\d{6,15})/i', $text, $m)) {
            return $m[2];
        }
        return null;
    }

    private function extractIncome(string $text): ?string
    {
        if (preg_match('/revenu[s]?[^\d]*(\d+[.,]?\d*)/i', $text, $m)) {
            return $m[1];
        }
        return null;
    }

    private function extractDates(string $text): array
    {
        preg_match_all('/\b(\d{1,2}\/\d{1,2}\/\d{2,4})\b/', $text, $matches);
        return $matches[1] ?? [];
    }

    private function extractAmounts(string $text): array
    {
        preg_match_all('/\b(\d+[.,]\d{2})\b/', $text, $matches);
        return $matches[1] ?? [];
    }
}
