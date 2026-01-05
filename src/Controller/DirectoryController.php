<?php

namespace App\Controller;

use App\Service\Elasticsearch\ElasticsearchService;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/annuaire")
 */
class DirectoryController extends AbstractController
{
    private $elasticsearchService;
    private $userRepository;

    public function __construct(
        ElasticsearchService $elasticsearchService,
        UserRepository $userRepository
    ) {
        $this->elasticsearchService = $elasticsearchService;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("", name="app_directory_index", methods={"GET"})
     */
    public function index(): Response
    {
        $stats = [
            'total_users' => $this->userRepository->count([]),
            'total_skills' => $this->getTotalSkills(),
            'recent_profiles' => 5
        ];

        return $this->render('annuaire/index.html.twig', [
            'stats' => $stats
        ]);
    }

    /**
     * @Route("/search", name="app_directory_search", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function search(Request $request): Response
    {
        $query = $request->query->get('q', '');
        $skills = $request->query->all('skills') ?? [];
        $location = $request->query->get('location', '');
        $type = $request->query->get('type', '');
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 20;

        $criteria = [
            'query' => $query,
            'skills' => is_array($skills) ? $skills : [],
            'location' => $location,
            'type' => $type,
            'from' => ($page - 1) * $perPage,
            'size' => $perPage
        ];

        $results = $this->elasticsearchService->searchUsers($criteria);
        $totalPages = (int) ceil($results['total'] / $perPage);

        return $this->render('annuaire/search.html.twig', [
            'query' => $query,
            'results' => $results['hits'],
            'facets' => $results['facets'],
            'total' => $results['total'],
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'filters' => [
                'skills' => $skills,
                'location' => $location,
                'type' => $type
            ]
        ]);
    }

    /**
     * @Route("/profile/{id}", name="app_directory_profile", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function profile(int $id): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Profil non trouvé');
        }

        $skills = [];
        if (method_exists($user, 'getSkills')) {
            foreach ($user->getSkills() as $skill) {
                $skills[] = method_exists($skill, 'getName') ? $skill->getName() : (string)$skill;
            }
        }

        $company = null;
        if (method_exists($user, 'getCompany') && $user->getCompany()) {
            $company = $user->getCompany();
        }

        $similarProfiles = [];
        if (!empty($skills)) {
            $similarResults = $this->elasticsearchService->searchUsers([
                'skills' => array_slice($skills, 0, 3),
                'size' => 4
            ]);
            
            $similarProfiles = array_filter($similarResults['hits'], function($profile) use ($id) {
                return $profile['id'] !== $id;
            });
            $similarProfiles = array_slice($similarProfiles, 0, 3);
        }

        return $this->render('annuaire/profile.html.twig', [
            'user' => $user,
            'skills' => $skills,
            'company' => $company,
            'similarProfiles' => $similarProfiles
        ]);
    }

    /**
     * @Route("/api/autocomplete", name="app_directory_autocomplete", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        if (strlen($query) < 2) {
            return $this->json([]);
        }

        $suggestions = $this->elasticsearchService->autocomplete($query, 10);

        return $this->json($suggestions);
    }

    /**
     * @Route("/api/facets", name="app_directory_facets", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function facets(Request $request): JsonResponse
    {
        $query = $request->query->get('q', '');

        $results = $this->elasticsearchService->searchUsers([
            'query' => $query,
            'size' => 0
        ]);

        return $this->json([
            'facets' => $results['facets'],
            'total' => $results['total']
        ]);
    }

    /**
     * @Route("/filter", name="app_directory_filter", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function filter(Request $request): Response
    {
        $data = $request->request->all();

        $queryParams = [
            'q' => $data['query'] ?? '',
            'skills' => $data['skills'] ?? [],
            'location' => $data['location'] ?? '',
            'type' => $data['type'] ?? '',
            'page' => 1
        ];

        return $this->redirectToRoute('app_directory_search', $queryParams);
    }

    private function getTotalSkills(): int
    {
        $results = $this->elasticsearchService->searchUsers([
            'size' => 0
        ]);

        return count($results['facets']['top_skills'] ?? []);
    }
}
