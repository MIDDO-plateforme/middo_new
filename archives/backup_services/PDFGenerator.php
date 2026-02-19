<?php

namespace App\Service;

use TCPDF;

class PDFGenerator
{
    private string $projectPath;

    public function __construct(string $projectPath)
    {
        $this->projectPath = $projectPath;
    }

    /**
     * Générer un rapport PDF projet
     */
    public function generateProjectReport(array $data): string
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        // Métadonnées
        $pdf->SetCreator('MIDDO Platform');
        $pdf->SetAuthor('MIDDO Business Intelligence');
        $pdf->SetTitle('Rapport Projet MIDDO');
        $pdf->SetSubject('Rapport Projet');

        // Supprimer header/footer par défaut
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Marges
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);

        // Ajouter page
        $pdf->AddPage();

        // HEADER PERSONNALISÉ
        $this->addHeader($pdf, 'RAPPORT PROJET MIDDO');

        // INFORMATIONS PROJET
        $pdf->SetFont('helvetica', 'B', 14);
        $pdf->SetTextColor(52, 152, 219); // Bleu MIDDO
        $pdf->Cell(0, 10, 'Projet : ' . $data['project_name'], 0, 1, 'L');
        
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 6, 'ID Projet : ' . $data['project_id'], 0, 1, 'L');
        $pdf->Cell(0, 6, 'Généré le : ' . $data['generated_at'], 0, 1, 'L');
        $pdf->Ln(5);

        // STATISTIQUES GLOBALES
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'Statistiques du Projet', 0, 1, 'L');
        $pdf->Ln(2);

        // Tableau stats
        $pdf->SetFont('helvetica', '', 10);
        $pdf->SetFillColor(52, 152, 219); // Bleu
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(90, 8, 'Indicateur', 1, 0, 'L', true);
        $pdf->Cell(90, 8, 'Valeur', 1, 1, 'C', true);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(240, 240, 240);
        
        $stats = [
            'Nombre total de tâches' => $data['tasks_count'],
        ];

        $fill = false;
        foreach ($stats as $key => $value) {
            $pdf->Cell(90, 7, $key, 1, 0, 'L', $fill);
            $pdf->Cell(90, 7, $value, 1, 1, 'C', $fill);
            $fill = !$fill;
        }

        $pdf->Ln(5);

        // LISTE DES TÂCHES
        if (!empty($data['tasks'])) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, 'Liste des Tâches', 0, 1, 'L');
            $pdf->Ln(2);

            // Header tableau
            $pdf->SetFont('helvetica', 'B', 9);
            $pdf->SetFillColor(52, 152, 219);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(10, 7, 'ID', 1, 0, 'C', true);
            $pdf->Cell(70, 7, 'Titre', 1, 0, 'L', true);
            $pdf->Cell(25, 7, 'Statut', 1, 0, 'C', true);
            $pdf->Cell(25, 7, 'Priorité', 1, 0, 'C', true);
            $pdf->Cell(50, 7, 'Créé le', 1, 1, 'C', true);

            // Lignes tâches
            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;

            foreach ($data['tasks'] as $task) {
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell(10, 6, $task['id'], 1, 0, 'C', $fill);
                $pdf->Cell(70, 6, substr($task['title'], 0, 40), 1, 0, 'L', $fill);
                
                // Couleur statut
                $statusColor = $this->getStatusColor($task['status']);
                $pdf->SetTextColor($statusColor[0], $statusColor[1], $statusColor[2]);
                $pdf->Cell(25, 6, ucfirst($task['status']), 1, 0, 'C', $fill);
                
                // Couleur priorité
                $priorityColor = $this->getPriorityColor($task['priority']);
                $pdf->SetTextColor($priorityColor[0], $priorityColor[1], $priorityColor[2]);
                $pdf->Cell(25, 6, ucfirst($task['priority']), 1, 0, 'C', $fill);
                
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(50, 6, $task['created_at'], 1, 1, 'C', $fill);
                
                $fill = !$fill;
            }
        }

        // FOOTER
        $this->addFooter($pdf);

        // Générer le fichier
        $filename = 'rapport_projet_' . $data['project_id'] . '_' . date('YmdHis') . '.pdf';
        $filepath = $this->projectPath . '/public/reports/' . $filename;

        // Créer le dossier si nécessaire
        if (!is_dir($this->projectPath . '/public/reports')) {
            mkdir($this->projectPath . '/public/reports', 0777, true);
        }

        $pdf->Output($filepath, 'F');

        return '/reports/' . $filename;
    }

    /**
     * Générer un rapport stats PDF
     */
    public function generateStatsReport(array $stats): string
    {
        $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

        $pdf->SetCreator('MIDDO Platform');
        $pdf->SetAuthor('MIDDO Business Intelligence');
        $pdf->SetTitle('Rapport Statistiques MIDDO');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();

        // HEADER
        $this->addHeader($pdf, 'RAPPORT STATISTIQUES MIDDO');

        // KPI CARDS
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Indicateurs Clés de Performance (KPI)', 0, 1, 'L');
        $pdf->Ln(2);

        $kpis = [
            ['label' => 'Taux de Complétion Global', 'value' => $stats['kpi']['overall_completion'] . '%', 'color' => [46, 204, 113]],
            ['label' => 'Taux d\'Engagement', 'value' => $stats['kpi']['engagement_rate'] . '%', 'color' => [52, 152, 219]],
            ['label' => 'Score de Productivité', 'value' => $stats['kpi']['productivity_score'] . '%', 'color' => [155, 89, 182]],
        ];

        $x = 15;
        foreach ($kpis as $kpi) {
            $pdf->SetXY($x, $pdf->GetY());
            $pdf->SetFillColor($kpi['color'][0], $kpi['color'][1], $kpi['color'][2]);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('helvetica', 'B', 16);
            $pdf->Cell(60, 15, $kpi['value'], 1, 0, 'C', true);
            
            $pdf->SetXY($x, $pdf->GetY() + 15);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->MultiCell(60, 10, $kpi['label'], 0, 'C');
            
            $x += 65;
        }

        $pdf->Ln(10);

        // STATS DÉTAILLÉES
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(0, 8, 'Statistiques Détaillées', 0, 1, 'L');
        $pdf->Ln(2);

        $detailStats = [
            'Tâches' => [
                'Total' => $stats['tasks']['total'],
                'Complétées' => $stats['tasks']['completed'],
                'En cours' => $stats['tasks']['in_progress'],
                'En attente' => $stats['tasks']['pending'],
            ],
            'Notifications' => [
                'Total' => $stats['notifications']['total'],
                'Lues' => $stats['notifications']['read'],
                'Non lues' => $stats['notifications']['unread'],
            ],
            'Utilisateurs' => [
                'Total' => $stats['users']['total'],
                'Moyenne tâches/user' => $stats['users']['avg_tasks_per_user'],
            ],
        ];

        foreach ($detailStats as $category => $items) {
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->SetFillColor(52, 152, 219);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(0, 8, $category, 1, 1, 'L', true);

            $pdf->SetFont('helvetica', '', 10);
            $pdf->SetTextColor(0, 0, 0);
            $fill = false;
            foreach ($items as $key => $value) {
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell(120, 7, '  ' . $key, 1, 0, 'L', $fill);
                $pdf->Cell(60, 7, $value, 1, 1, 'C', $fill);
                $fill = !$fill;
            }
            $pdf->Ln(3);
        }

        // FOOTER
        $this->addFooter($pdf);

        // Générer
        $filename = 'rapport_stats_' . date('YmdHis') . '.pdf';
        $filepath = $this->projectPath . '/public/reports/' . $filename;

        if (!is_dir($this->projectPath . '/public/reports')) {
            mkdir($this->projectPath . '/public/reports', 0777, true);
        }

        $pdf->Output($filepath, 'F');

        return '/reports/' . $filename;
    }

    private function addHeader(TCPDF $pdf, string $title): void
    {
        $pdf->SetFont('helvetica', 'B', 18);
        $pdf->SetTextColor(52, 152, 219);
        $pdf->Cell(0, 12, $title, 0, 1, 'C');
        $pdf->SetDrawColor(52, 152, 219);
        $pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
        $pdf->Ln(5);
    }

    private function addFooter(TCPDF $pdf): void
    {
        $pdf->SetY(-20);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->SetTextColor(150, 150, 150);
        $pdf->Cell(0, 5, 'MIDDO Platform - Business Intelligence Report', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Généré le ' . date('d/m/Y à H:i:s'), 0, 0, 'C');
    }

    private function getStatusColor(string $status): array
    {
        return match($status) {
            'completed' => [46, 204, 113], // Vert
            'in_progress' => [52, 152, 219], // Bleu
            'pending' => [241, 196, 15], // Jaune
            default => [149, 165, 166], // Gris
        };
    }

    private function getPriorityColor(string $priority): array
    {
        return match($priority) {
            'urgent' => [231, 76, 60], // Rouge
            'high' => [230, 126, 34], // Orange
            'medium' => [241, 196, 15], // Jaune
            'low' => [46, 204, 113], // Vert
            default => [149, 165, 166], // Gris
        };
    }
}
