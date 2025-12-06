<?php

namespace App\Infrastructure\Services;

use App\Domain\Application\DTOs\ConsolidatedListResult;
use App\Domain\Reporting\Ports\ReportGeneratorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class ExcelReportGenerator implements ReportGeneratorInterface
{
    public function generateExcel(ConsolidatedListResult $data): string
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Application ID');
        $sheet->setCellValue('B1', 'Candidate Name');
        $sheet->setCellValue('C1', 'Candidate Email');
        $sheet->setCellValue('D1', 'Years of Experience');
        $sheet->setCellValue('E1', 'Evaluator Name');
        $sheet->setCellValue('F1', 'Assigned At');
        $sheet->setCellValue('G1', 'Total Applications for Evaluator');
        $sheet->setCellValue('H1', 'Evaluator Candidates Emails');

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        $row = 2;
        foreach ($data->items as $item) {
            $sheet->setCellValue('A'.$row, $item->applicationId);
            $sheet->setCellValue('B'.$row, $item->candidateName);
            $sheet->setCellValue('C'.$row, $item->candidateEmail);
            $sheet->setCellValue('D'.$row, $item->yearsOfExperience);
            $sheet->setCellValue('E'.$row, $item->evaluatorName);
            $sheet->setCellValue('F'.$row, $item->assignedAt);
            $sheet->setCellValue('G'.$row, $item->totalApplicationsForEvaluator);
            $sheet->setCellValue('H'.$row, $item->evaluatorCandidatesEmails);
            $row++;
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $fileName = 'consolidated_applications_'.date('Y-m-d_His').'.xlsx';
        $filePath = storage_path('app/reports/'.$fileName);

        if (! is_dir(storage_path('app/reports'))) {
            mkdir(storage_path('app/reports'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return $filePath;
    }
}
