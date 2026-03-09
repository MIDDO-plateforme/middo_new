<?php

namespace App\Service\Search;

class SearchEngine
{
    /**
     * Recherche simple dans un tableau d’éléments.
     *
     * @param array $items      Liste des éléments
     * @param string $query     Texte recherché
     * @param callable $extractor Fonction qui extrait la valeur textuelle d’un item
     */
    public function search(array $items, string $query, callable $extractor): array
    {
        $query = mb_strtolower($query);

        return array_values(array_filter($items, function ($item) use ($query, $extractor) {
            $value = mb_strtolower($extractor($item));
            return str_contains($value, $query);
        }));
    }
}
