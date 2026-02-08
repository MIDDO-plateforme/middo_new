<?php

namespace App\Controller;

use App\Service\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/search')]
class SearchController extends AbstractController
{
    private SearchService $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * Page de recherche principale
     */
    #[Route('', name: 'app_search_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('search/index.html.twig', [
            'query' => $request->query->get('q', ''),
        ]);
    }

    /**
     * Page des résultats de recherche
     */
    #[Route('/results', name: 'search_results', methods: ['GET'])]
    public function results(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $page = max(1, (int) $request->query->get('page', 1));
        
        // Préparer les filtres
        $filters = [
            'type' => $request->query->get('type', ''),
            'category' => $request->query->get('category', ''),
            'location' => $request->query->get('location', ''),
            'sort' => $request->query->get('sort', 'relevance'),
            'date_range' => $request->query->get('date_range', ''),
        ];

        // Utiliser le SearchService pour obtenir les vrais résultats
        $results = $this->searchService->search($query, $filters, $page, 20);

        return $this->render('search/results.html.twig', [
            'results' => $results,
        ]);
    }

    /**
     * Page de recherche avancée avec filtres
     */
    #[Route('/advanced', name: 'app_search_advanced', methods: ['GET'])]
    public function advanced(): Response
    {
        return $this->render('search/advanced.html.twig', [
            'filters' => $this->getAvailableFilters(),
        ]);
    }

    /**
     * API de recherche AJAX
     */
    #[Route('/api', name: 'app_search_api', methods: ['POST'])]
    public function api(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $query = $data['query'] ?? '';
        $filters = $data['filters'] ?? [];
        $page = $data['page'] ?? 1;

        $results = $this->searchService->search($query, $filters, $page, 20);

        return $this->json($results);
    }

    /**
     * Autocomplete pour suggestions temps réel
     */
    #[Route('/autocomplete', name: 'app_search_autocomplete', methods: ['GET'])]
    public function autocomplete(Request $request): JsonResponse
    {
        $term = $request->query->get('term', '');

        if (strlen($term) < 2) {
            return $this->json([]);
        }

        // Recherche rapide pour autocomplete
        $results = $this->searchService->search($term, [], 1, 5);
        
        $suggestions = [];
        foreach ($results['items'] as $item) {
            $suggestions[] = [
                'label' => $item['name'],
                'value' => $item['name'],
                'type' => $item['type'],
            ];
        }

        return $this->json($suggestions);
    }

    /**
     * Obtenir les filtres disponibles
     */
    private function getAvailableFilters(): array
    {
        return [
            'sectors' => [
                'tech' => 'Technologie',
                'finance' => 'Finance',
                'health' => 'Santé',
                'education' => 'Éducation',
                'ecommerce' => 'E-commerce',
                'other' => 'Autre',
            ],
            'locations' => [
                'africa' => 'Afrique',
                'europe' => 'Europe',
                'asia' => 'Asie',
                'americas' => 'Amériques',
                'oceania' => 'Océanie',
            ],
            'budgetRanges' => [
                '0-10k' => '0 - 10 000 €',
                '10k-50k' => '10 000 - 50 000 €',
                '50k-100k' => '50 000 - 100 000 €',
                '100k+' => '100 000 € +',
            ],
            'userTypes' => [
                'entrepreneur' => 'Entrepreneur',
                'freelancer' => 'Freelance',
                'investor' => 'Investisseur',
                'company' => 'Entreprise',
            ],
        ];
    }
}
