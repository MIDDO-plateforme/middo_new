<?php

namespace App\Service\Search;

use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use App\Repository\SkillRepository;

class SearchService
{
    public function __construct(
        private UserRepository $userRepository,
        private ProjectRepository $projectRepository,
        private SkillRepository $skillRepository,
        private SearchEngine $engine
    ) {}

    public function searchUsers(string $query): array
    {
        $users = $this->userRepository->findAll();

        return $this->engine->search($users, $query, fn($u) => $u->getEmail());
    }

    public function searchProjects(string $query): array
    {
        $projects = $this->projectRepository->findAll();

        return $this->engine->search($projects, $query, fn($p) => $p->getTitle());
    }

    public function searchSkills(string $query): array
    {
        return $this->skillRepository->search($query);
    }
}
