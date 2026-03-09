<?php

namespace App\UI\Menu;

class MenuItem
{
    public function __construct(
        public string $label,
        public string $route,
        public string $icon = 'circle',
        public array $children = [],
        public bool $visible = true,
    ) {
    }

    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'route' => $this->route,
            'icon' => $this->icon,
            'visible' => $this->visible,
            'children' => array_map(
                fn (MenuItem $child) => $child->toArray(),
                $this->children
            ),
        ];
    }
}
