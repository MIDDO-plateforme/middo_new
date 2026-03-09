<?php

namespace App\UI\Menu;

class MenuService
{
    public function __construct(
        private MenuBuilder $builder,
    ) {
    }

    public function getMenu(): array
    {
        return array_map(
            fn (MenuItem $item) => $item->toArray(),
            $this->builder->build()
        );
    }
}
