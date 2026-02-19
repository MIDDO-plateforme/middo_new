<?php

namespace App\Service;

class CSVExporter
{
    private string $projectPath;

    public function __construct(string $projectPath)
    {
        $this->projectPath = $projectPath;
    }

    /**
     * Exporter des données en CSV
     */
    public function export(array $data, string $filename, array $headers = null): string
    {
        if (empty($data)) {
            throw new \Exception('No data to export');
        }

        // Créer le dossier exports si nécessaire
        $exportsDir = $this->projectPath . '/public/exports';
        if (!is_dir($exportsDir)) {
            mkdir($exportsDir, 0777, true);
        }

        // Générer nom de fichier unique
        $filename = $filename . '_' . date('YmdHis') . '.csv';
        $filepath = $exportsDir . '/' . $filename;

        // Ouvrir le fichier
        $handle = fopen($filepath, 'w');
        if ($handle === false) {
            throw new \Exception('Cannot create CSV file');
        }

        // BOM UTF-8 pour Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

        // Headers
        if ($headers === null) {
            $headers = array_keys($data[0]);
        }
        fputcsv($handle, $headers, ';');

        // Données
        foreach ($data as $row) {
            fputcsv($handle, array_values($row), ';');
        }

        fclose($handle);

        return '/exports/' . $filename;
    }

    /**
     * Exporter les tâches en CSV
     */
    public function exportTasks(array $tasks): string
    {
        return $this->export($tasks, 'tasks_export');
    }

    /**
     * Exporter les notifications en CSV
     */
    public function exportNotifications(array $notifications): string
    {
        return $this->export($notifications, 'notifications_export');
    }

    /**
     * Exporter l'activité des utilisateurs en CSV
     */
    public function exportUsersActivity(array $activity): string
    {
        return $this->export($activity, 'users_activity_export');
    }

    /**
     * Exporter les statistiques en CSV
     */
    public function exportStats(array $stats): string
    {
        // Transformer les stats en tableau plat
        $flatData = [];

        // Tasks
        foreach ($stats['tasks'] as $key => $value) {
            $flatData[] = [
                'Categorie' => 'Tâches',
                'Indicateur' => ucfirst(str_replace('_', ' ', $key)),
                'Valeur' => $value,
            ];
        }

        // Notifications
        foreach ($stats['notifications'] as $key => $value) {
            $flatData[] = [
                'Categorie' => 'Notifications',
                'Indicateur' => ucfirst(str_replace('_', ' ', $key)),
                'Valeur' => $value,
            ];
        }

        // Users
        foreach ($stats['users'] as $key => $value) {
            $flatData[] = [
                'Categorie' => 'Utilisateurs',
                'Indicateur' => ucfirst(str_replace('_', ' ', $key)),
                'Valeur' => $value,
            ];
        }

        // KPI
        foreach ($stats['kpi'] as $key => $value) {
            $flatData[] = [
                'Categorie' => 'KPI',
                'Indicateur' => ucfirst(str_replace('_', ' ', $key)),
                'Valeur' => $value . '%',
            ];
        }

        return $this->export($flatData, 'stats_export', ['Categorie', 'Indicateur', 'Valeur']);
    }
}
