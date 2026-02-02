<?php

namespace App\Service;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Yaml\Yaml;

class MenuBuilder
{
    private AuthorizationCheckerInterface $authChecker;
    private string $projectDir;

    public function __construct(
        AuthorizationCheckerInterface $authChecker,
        string $projectDir
    ) {
        $this->authChecker = $authChecker;
        $this->projectDir = $projectDir;
    }

    public function getMainMenu(): array
    {
        $config = Yaml::parseFile($this->projectDir . '/config/menu.yaml');
        $menu = $config['middo']['main_menu'] ?? [];

        return $this->filterByRoles($menu);
    }

    private function filterByRoles(array $items): array
    {
        $filtered = [];

        foreach ($items as $key => $item) {
            $roles = $item['roles'] ?? ['ROLE_USER'];
            
            if ($this->hasAccess($roles)) {
                if (isset($item['children'])) {
                    $item['children'] = $this->filterByRoles($item['children']);
                }
                $filtered[$key] = $item;
            }
        }

        return $filtered;
    }

    private function hasAccess(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }
}
