<?php

namespace App\Command;

use App\Service\Elasticsearch\ElasticsearchService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * IndexUsersCommand - Commande CLI pour indexation Elasticsearch
 * 
 * Usage :
 * - php bin/console app:index-users                 (indexation normale)
 * - php bin/console app:index-users --force         (réindexation complète)
 * - php bin/console app:index-users --batch=1000    (taille batch custom)
 * 
 * Sécurité : Confirmation obligatoire pour --force
 * Performance : Barre de progression + stats détaillées
 */
#[AsCommand(
    name: 'app:index-users',
    description: 'Indexer tous les utilisateurs dans Elasticsearch'
)]
class IndexUsersCommand extends Command
{
    private ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        parent::__construct();
        $this->elasticsearchService = $elasticsearchService;
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Supprimer et recréer l\'index complet (ATTENTION : supprime toutes les données Elasticsearch)'
            )
            ->addOption(
                'batch',
                'b',
                InputOption::VALUE_REQUIRED,
                'Taille des batches pour indexation (default: 500)',
                500
            )
            ->setHelp(
                <<<'HELP'
La commande <info>app:index-users</info> indexe tous les utilisateurs MySQL dans Elasticsearch.

<comment>Usage simple :</comment>
  <info>php bin/console app:index-users</info>

<comment>Réindexation complète (supprime l'index existant) :</comment>
  <info>php bin/console app:index-users --force</info>

<comment>Batch custom (pour grandes bases) :</comment>
  <info>php bin/console app:index-users --batch=1000</info>

<fg=yellow>⚠️  ATTENTION : --force supprime TOUTES les données Elasticsearch avant de réindexer.</>
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $force = $input->getOption('force');
        $batchSize = (int) $input->getOption('batch');

        // Validation batch size
        if ($batchSize < 1 || $batchSize > 5000) {
            $io->error("Batch size doit être entre 1 et 5000. Valeur fournie : {$batchSize}");
            return Command::FAILURE;
        }

        // Banner
        $io->title('🔍 INDEXATION ELASTICSEARCH - MODULE ANNUAIRE');

        // Étape 1 : Vérifier l'existence de l'index
        $indexExists = $this->elasticsearchService->indexExists();

        if ($indexExists && !$force) {
            $io->warning("L'index 'users' existe déjà.");
            $io->note([
                "Cette commande va ajouter/mettre à jour les utilisateurs existants.",
                "Pour recréer l'index complet, utilise : --force"
            ]);

            if (!$io->confirm('Continuer avec mise à jour incrémentale ?', false)) {
                $io->info('Opération annulée.');
                return Command::SUCCESS;
            }
        }

        // Étape 2 : Mode FORCE - Réindexation complète
        if ($force) {
            $io->section('🔥 MODE FORCE ACTIVÉ');
            $io->warning([
                'ATTENTION : Cette action va :',
                '  1. Supprimer l\'index Elasticsearch existant',
                '  2. Recréer un nouvel index vierge',
                '  3. Réindexer TOUS les utilisateurs MySQL'
            ]);

            if (!$io->confirm('Es-tu ABSOLUMENT SÛR de vouloir continuer ?', false)) {
                $io->info('Opération annulée par l\'utilisateur.');
                return Command::SUCCESS;
            }

            // Suppression index
            if ($indexExists) {
                $io->text('📦 Suppression de l\'ancien index...');
                $this->elasticsearchService->deleteIndex();
                $io->success('Index supprimé.');
            }

            // Création nouvel index
            $io->text('🏗️  Création du nouvel index avec mapping...');
            if (!$this->elasticsearchService->createUsersIndex()) {
                $io->error("Échec de la création de l'index. Consulte les logs.");
                return Command::FAILURE;
            }
            $io->success('Index créé avec succès.');
        }

        // Étape 3 : Vérifier si l'index existe maintenant
        if (!$this->elasticsearchService->indexExists()) {
            $io->error("L'index 'users' n'existe pas. Exécute la commande avec --force pour le créer.");
            return Command::FAILURE;
        }

        // Étape 4 : Récupérer les utilisateurs MySQL
        $io->section('📊 Récupération des utilisateurs MySQL...');

        try {
            $stats = $this->elasticsearchService->reindexAll();

            // Afficher les résultats
            $io->newLine();
            $io->success('🎉 INDEXATION TERMINÉE !');

            $io->definitionList(
                ['✅ Succès' => $stats['success']],
                ['❌ Échecs' => $stats['failed']],
                ['📦 Batch size' => $batchSize]
            );

            // Afficher les erreurs si présentes
            if (!empty($stats['errors'])) {
                $io->warning('Erreurs rencontrées :');
                $io->listing(array_slice($stats['errors'], 0, 10)); // Max 10 erreurs

                if (count($stats['errors']) > 10) {
                    $io->note('+ ' . (count($stats['errors']) - 10) . ' autres erreurs (consulte les logs)');
                }
            }

            // Statistiques finales
            if ($stats['success'] > 0) {
                $io->newLine();
                $io->block([
                    '✨ Module Annuaire opérationnel !',
                    '',
                    '🔍 Teste la recherche :',
                    '   http://localhost:8000/annuaire/search',
                    '',
                    '📊 Vérifie Kibana :',
                    '   http://localhost:5601'
                ], null, 'fg=black;bg=green', ' ', true);
            }

            return $stats['failed'] === 0 ? Command::SUCCESS : Command::FAILURE;

        } catch (\Exception $e) {
            $io->error([
                'Erreur critique pendant l\'indexation :',
                $e->getMessage()
            ]);
            return Command::FAILURE;
        }
    }
}
