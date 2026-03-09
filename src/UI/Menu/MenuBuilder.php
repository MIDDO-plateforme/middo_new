<?php

namespace App\UI\Menu;

use App\IA\Cortex\CortexEngine;
use App\IA\Autonomous\AutonomousState;

class MenuBuilder
{
    public function __construct(
        private CortexEngine $cortex,
        private AutonomousState $autonomousState,
    ) {
    }

    public function build(): array
    {
        $items = [];

        // =========================
        // MENU PRINCIPAL (toujours visible)
        // =========================
        $items[] = new MenuItem('Accueil', 'app_dashboard', 'home');
        $items[] = new MenuItem('Annuaire', 'app_directory', 'users');
        $items[] = new MenuItem('Banque', 'app_bank', 'credit-card');
        $items[] = new MenuItem('Projets', 'app_projects', 'folder');
        $items[] = new MenuItem('Messages', 'app_messages', 'message-circle');
        $items[] = new MenuItem('Visio', 'app_visio', 'video');
        $items[] = new MenuItem('Analytics', 'app_analytics', 'bar-chart-2');
        $items[] = new MenuItem('Travail', 'app_work', 'briefcase');
        $items[] = new MenuItem('Paramètres', 'app_settings', 'settings');

        // =========================
        // MENU IA PREMIUM (dynamique)
        // =========================
        if ($this->shouldShowIaMenu()) {
            $iaChildren = [
                new MenuItem('Dashboard IA', 'app_ia_dashboard', 'cpu'),
                new MenuItem('Cortex', 'app_cortex', 'brain'),
                new MenuItem('Vision', 'app_vision', 'eye'),
                new MenuItem('Mode autonome', 'app_autonomous', 'infinity'),
                new MenuItem('Documents & OCR', 'app_documents_ocr', 'file-text'),
                new MenuItem('Workflows IA', 'app_ia_workflows', 'repeat'),
                new MenuItem('Agents IA', 'app_ia_agents', 'users'),
                new MenuItem('Pipelines IA', 'app_ia_pipelines', 'git-branch'),
                new MenuItem('CountryAware', 'app_country_aware', 'globe'),
                new MenuItem('Système IA', 'app_ia_system', 'sliders'),
                new MenuItem('Debug IA', 'app_ia_debug', 'alert-triangle'),
            ];

            $items[] = new MenuItem(
                label: 'Intelligence Artificielle',
                route: '#',
                icon: 'zap',
                children: $iaChildren,
                visible: true,
            );
        }

        return $items;
    }

    private function shouldShowIaMenu(): bool
    {
        // Exemple de logique dynamique :
        // - si le Cortex a un état "ia_enabled"
        // - ou si le mode autonome a déjà tourné
        // - ou si un flag "expert_mode" est présent

        $iaEnabled = $this->cortex->getState('ia_enabled') ?? false;
        $expertMode = $this->cortex->getState('expert_mode') ?? false;
        $autonomousIterations = $this->autonomousState->iteration;

        return $iaEnabled === true
            || $expertMode === true
            || $autonomousIterations > 0;
    }
}
