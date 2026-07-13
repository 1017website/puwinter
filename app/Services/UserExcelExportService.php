<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use RuntimeException;

class UserExcelExportService
{
    /**
     * @param  Collection<int, User>  $users
     * @param  array<string, string|null>  $filters
     */
    public function create(Collection $users, array $filters = []): string
    {
        $directory = storage_path('app/temp');
        File::ensureDirectoryExists($directory);

        $basePath = tempnam($directory, 'users_export_');
        if ($basePath === false) {
            throw new RuntimeException('Gagal membuat file export sementara.');
        }

        $path = $basePath.'.xlsx';
        @unlink($basePath);

        $spreadsheet = new Spreadsheet;
        $spreadsheet->getProperties()
            ->setCreator('Puwinter Admin')
            ->setTitle('Data User Puwinter')
            ->setSubject('Export data user')
            ->setDescription('Data user yang diekspor dari halaman admin Puwinter.');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data User');
        $sheet->setShowGridlines(false);
        $sheet->freezePane('A5');

        $headers = [
            'No.', 'Nama', 'Email', 'Role', 'Status', 'Telepon', 'Sekolah', 'Kota',
            'Provinsi', 'Kelas', 'Kode Pendaftar', 'Kelompok Pendaftar', 'Kode Affiliate',
            'Direferensikan Oleh', 'Jumlah Kelas', 'Percobaan Tryout', 'Verifikasi Email',
            'Tanggal Bergabung',
        ];

        $sheet->mergeCells('A1:R1');
        $sheet->setCellValue('A1', 'Data User Puwinter');
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $sheet->mergeCells('A2:R2');
        $sheet->setCellValue('A2', 'Diekspor '.now()->format('d M Y H:i').' WIB · '.$this->filterDescription($filters));
        $sheet->getStyle('A2:R2')->applyFromArray([
            'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(22);

        $sheet->fromArray($headers, null, 'A4');
        $sheet->getStyle('A4:R4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2563EB']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(28);

        foreach ($users->values() as $index => $user) {
            $row = $index + 5;
            $activeSubscription = $user->subscriptions->first();
            $status = ! $user->is_active
                ? 'Nonaktif'
                : ($activeSubscription ? 'Premium'.($activeSubscription->plan?->name ? ' - '.$activeSubscription->plan->name : '') : 'Gratis');

            $sheet->setCellValue('A'.$row, $index + 1);
            $this->setText($sheet, 'B'.$row, $user->name);
            $this->setText($sheet, 'C'.$row, $user->email);
            $this->setText($sheet, 'D'.$row, ucfirst($user->role));
            $this->setText($sheet, 'E'.$row, $status);
            $this->setText($sheet, 'F'.$row, $user->phone ?: '-');
            $this->setText($sheet, 'G'.$row, $user->school ?: '-');
            $this->setText($sheet, 'H'.$row, $user->city ?: '-');
            $this->setText($sheet, 'I'.$row, $user->province ?: '-');
            $this->setText($sheet, 'J'.$row, $user->grade?->name ?? $user->grade ?? '-');
            $this->setText($sheet, 'K'.$row, $user->registrationCode?->code ?? '-');
            $this->setText($sheet, 'L'.$row, $user->registrationCode?->name ?? '-');
            $this->setText($sheet, 'M'.$row, $user->affiliate_code ?: '-');
            $this->setText($sheet, 'N'.$row, $user->referredBy?->name ?? '-');
            $sheet->setCellValue('O'.$row, (int) $user->enrollments_count);
            $sheet->setCellValue('P'.$row, (int) $user->tryout_attempts_count);
            $this->setText($sheet, 'Q'.$row, $user->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi');

            if ($user->created_at) {
                $sheet->setCellValue('R'.$row, Date::PHPToExcel($user->created_at));
                $sheet->getStyle('R'.$row)->getNumberFormat()->setFormatCode('dd mmm yyyy hh:mm');
            }

            $sheet->getRowDimension($row)->setRowHeight(21);
        }

        $lastRow = max(5, $users->count() + 4);
        if ($users->isEmpty()) {
            $sheet->mergeCells('A5:R5');
            $sheet->setCellValue('A5', 'Tidak ada data user untuk filter yang dipilih.');
            $sheet->getStyle('A5:R5')->applyFromArray([
                'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }

        $sheet->setAutoFilter('A4:R'.$lastRow);
        $sheet->getStyle('A5:R'.$lastRow)->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DCE5F0']],
            ],
        ]);
        $sheet->getStyle('A5:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('O5:P'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('F5:F'.$lastRow)->getNumberFormat()->setFormatCode('@');
        $sheet->getStyle('K5:N'.$lastRow)->getNumberFormat()->setFormatCode('@');

        $widths = [
            'A' => 7, 'B' => 25, 'C' => 30, 'D' => 14, 'E' => 18, 'F' => 18,
            'G' => 27, 'H' => 18, 'I' => 18, 'J' => 16, 'K' => 20, 'L' => 28,
            'M' => 20, 'N' => 25, 'O' => 15, 'P' => 18, 'Q' => 20, 'R' => 22,
        ];
        foreach ($widths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

        $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(4, 4);
        $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.5)->setBottom(0.5)->setLeft(0.25)->setRight(0.25);
        $sheet->setSelectedCell('A1');

        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }

    private function setText(Worksheet $sheet, string $cell, string $value): void
    {
        $sheet->setCellValueExplicit($cell, $value, DataType::TYPE_STRING);
    }

    /** @param array<string, string|null> $filters */
    private function filterDescription(array $filters): string
    {
        $parts = [];
        if (! empty($filters['role'])) {
            $parts[] = 'Role: '.$filters['role'];
        }
        if (! empty($filters['status'])) {
            $parts[] = 'Status: '.$filters['status'];
        }
        if (! empty($filters['search'])) {
            $parts[] = 'Pencarian: '.$filters['search'];
        }

        return $parts ? implode(' · ', $parts) : 'Semua user';
    }
}
