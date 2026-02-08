<?php

namespace App\Service;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class SearchService
{
    private ProjectRepository $projectRepository;
    private UserRepository $userRepository;

    public function __construct(
        ProjectRepository $projectRepository,
        UserRepository $userRepository
    ) {
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Recherche globale (projets + utilisateurs)
     */
    public function search(string $query, array $filters = [], int $page = 1, int $limit = 20): array
    {
        $results = [
            'total' => 0,
            'items' => [],
            'currentPage' => $page,
            'totalPages' => 1,
        ];

        if (empty($query)) {
            return $results;
        }

        $type = $filters['type'] ?? '';

        // Rechercher dans les projets
        if ($type === '' || $type === 'project') {
            $projects = $this->searchProjects($query, $filters, $page, $limit);
            $results['items'] = array_merge($results['items'], $projects);
        }

        // Rechercher dans les utilisateurs
        if ($type === '' || $type === 'entrepreneur') {
            $users = $this->searchUsers($query, $filters, $page, $limit);
            $results['items'] = array_merge($results['items'], $users);
        }

        $results['total'] = count($results['items']);
        $results['totalPages'] = (int) ceil($results['total'] / $limit);

        // Limiter les résultats pour la pagination
        $offset = ($page - 1) * $limit;
        $results['items'] = array_slice($results['items'], $offset, $limit);

        return $results;
    }

    /**
     * Recherche dans les projets
     */
    private function searchProjects(string $query, array $filters, int $page, int $limit): array
    {
        $qb = $this->projectRepository->createQueryBuilder('p')
            ->where('p.title LIKE :query')
            ->orWhere('p.description LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('p.createdAt', 'DESC');

        $projects = $qb->getQuery()->getResult();

        $results = [];
        foreach ($projects as $project) {
            $owner = $project->getOwner();
            $ownerName = $owner ? $owner->getFirstName() . ' ' . $owner->getLastName() : 'Anonyme';
            
            $results[] = [
                'name' => $project->getTitle(),
                'type' => 'project',
                'description' => $project->getDescription() ?? 'Aucune description',
                'category' => 'Projet',
                'location' => 'Créé par ' . $ownerName,
                'date' => $project->getCreatedAt(),
                'url' => '/project/' . $project->getId(),
            ];
        }

        return $results;
    }

    /**
     * Recherche dans les utilisateurs
     */
    private function searchUsers(string $query, array $filters, int $page, int $limit): array
    {
        $qb = $this->userRepository->createQueryBuilder('u')
            ->where('u.firstName LIKE :query')
            ->orWhere('u.lastName LIKE :query')
            ->orWhere('u.email LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('u.id', 'DESC');

        $users = $qb->getQuery()->getResult();

        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'name' => $user->getFirstName() . ' ' . $user->getLastName(),
                'type' => 'entrepreneur',
                'description' => 'Entrepreneur sur MIDDO',
                'category' => 'Profil utilisateur',
                'location' => 'MIDDO',
                'date' => new \DateTime(),
                'url' => '/profile/view/' . $user->getId(),
            ];
        }

        return $results;
    }
}
