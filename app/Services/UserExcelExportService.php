<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use RuntimeException;
use XMLWriter;
use ZipArchive;

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

        $zip = new ZipArchive;
        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Gagal membuat file Excel.');
        }

        $zip->addFromString('[Content_Types].xml', $this->contentTypes());
        $zip->addFromString('_rels/.rels', $this->rootRelationships());
        $zip->addFromString('docProps/app.xml', $this->appProperties());
        $zip->addFromString('docProps/core.xml', $this->coreProperties());
        $zip->addFromString('xl/workbook.xml', $this->workbook());
        $zip->addFromString('xl/_rels/workbook.xml.rels', $this->workbookRelationships());
        $zip->addFromString('xl/styles.xml', $this->styles());
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->worksheet($users, $filters));
        $zip->close();

        return $path;
    }

    /**
     * @param  Collection<int, User>  $users
     * @param  array<string, string|null>  $filters
     */
    private function worksheet(Collection $users, array $filters): string
    {
        $headers = [
            'No.', 'Nama', 'Email', 'Role', 'Status', 'Telepon', 'Sekolah', 'Kota',
            'Provinsi', 'Kelas', 'Kode Pendaftar', 'Kelompok Pendaftar', 'Kode Affiliate',
            'Direferensikan Oleh', 'Jumlah Kelas', 'Percobaan Tryout', 'Verifikasi Email',
            'Tanggal Bergabung',
        ];

        $lastColumn = $this->columnName(count($headers));
        $lastRow = max(4, $users->count() + 4);
        $filterText = $this->filterDescription($filters);

        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8', 'yes');
        $xml->startElement('worksheet');
        $xml->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

        $xml->startElement('sheetViews');
        $xml->startElement('sheetView');
        $xml->writeAttribute('workbookViewId', '0');
        $xml->writeAttribute('showGridLines', '0');
        $xml->startElement('pane');
        $xml->writeAttribute('ySplit', '4');
        $xml->writeAttribute('topLeftCell', 'A5');
        $xml->writeAttribute('activePane', 'bottomLeft');
        $xml->writeAttribute('state', 'frozen');
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();

        $xml->writeElement('sheetFormatPr');
        $xml->startElement('cols');
        $widths = [7, 25, 30, 14, 18, 18, 27, 18, 18, 16, 20, 28, 20, 25, 15, 18, 20, 22];
        foreach ($widths as $index => $width) {
            $xml->startElement('col');
            $xml->writeAttribute('min', (string) ($index + 1));
            $xml->writeAttribute('max', (string) ($index + 1));
            $xml->writeAttribute('width', (string) $width);
            $xml->writeAttribute('customWidth', '1');
            $xml->endElement();
        }
        $xml->endElement();

        $xml->startElement('sheetData');

        $this->startRow($xml, 1, 30);
        $this->inlineCell($xml, 'A1', 'Data User Puwinter', 1);
        $xml->endElement();

        $this->startRow($xml, 2, 22);
        $this->inlineCell($xml, 'A2', 'Diekspor '.now()->format('d M Y H:i').' WIB · '.$filterText, 2);
        $xml->endElement();

        $this->startRow($xml, 4, 24);
        foreach ($headers as $index => $header) {
            $this->inlineCell($xml, $this->columnName($index + 1).'4', $header, 3);
        }
        $xml->endElement();

        foreach ($users->values() as $index => $user) {
            $row = $index + 5;
            $activeSubscription = $user->subscriptions->first();
            $status = ! $user->is_active
                ? 'Nonaktif'
                : ($activeSubscription ? 'Premium'.($activeSubscription->plan?->name ? ' - '.$activeSubscription->plan->name : '') : 'Gratis');

            $this->startRow($xml, $row, 20);
            $this->numberCell($xml, 'A'.$row, $index + 1, 5);
            $this->inlineCell($xml, 'B'.$row, $user->name);
            $this->inlineCell($xml, 'C'.$row, $user->email);
            $this->inlineCell($xml, 'D'.$row, ucfirst($user->role));
            $this->inlineCell($xml, 'E'.$row, $status);
            $this->inlineCell($xml, 'F'.$row, $user->phone ?: '-');
            $this->inlineCell($xml, 'G'.$row, $user->school ?: '-');
            $this->inlineCell($xml, 'H'.$row, $user->city ?: '-');
            $this->inlineCell($xml, 'I'.$row, $user->province ?: '-');
            $this->inlineCell($xml, 'J'.$row, $user->grade?->name ?? $user->grade ?? '-');
            $this->inlineCell($xml, 'K'.$row, $user->registrationCode?->code ?? '-');
            $this->inlineCell($xml, 'L'.$row, $user->registrationCode?->name ?? '-');
            $this->inlineCell($xml, 'M'.$row, $user->affiliate_code ?: '-');
            $this->inlineCell($xml, 'N'.$row, $user->referredBy?->name ?? '-');
            $this->numberCell($xml, 'O'.$row, (int) $user->enrollments_count, 5);
            $this->numberCell($xml, 'P'.$row, (int) $user->tryout_attempts_count, 5);
            $this->inlineCell($xml, 'Q'.$row, $user->email_verified_at ? 'Terverifikasi' : 'Belum terverifikasi');
            $this->dateCell($xml, 'R'.$row, $user->created_at);
            $xml->endElement();
        }

        $xml->endElement();

        $xml->startElement('mergeCells');
        $xml->writeAttribute('count', '2');
        foreach (["A1:{$lastColumn}1", "A2:{$lastColumn}2"] as $range) {
            $xml->startElement('mergeCell');
            $xml->writeAttribute('ref', $range);
            $xml->endElement();
        }
        $xml->endElement();

        $xml->startElement('autoFilter');
        $xml->writeAttribute('ref', "A4:{$lastColumn}{$lastRow}");
        $xml->endElement();

        $xml->startElement('sheetProtection');
        $xml->writeAttribute('sheet', '0');
        $xml->endElement();
        $xml->startElement('pageMargins');
        $xml->writeAttribute('left', '0.25');
        $xml->writeAttribute('right', '0.25');
        $xml->writeAttribute('top', '0.5');
        $xml->writeAttribute('bottom', '0.5');
        $xml->writeAttribute('header', '0.2');
        $xml->writeAttribute('footer', '0.2');
        $xml->endElement();
        $xml->endElement();

        return $xml->outputMemory();
    }

    private function startRow(XMLWriter $xml, int $row, int $height): void
    {
        $xml->startElement('row');
        $xml->writeAttribute('r', (string) $row);
        $xml->writeAttribute('ht', (string) $height);
        $xml->writeAttribute('customHeight', '1');
    }

    private function inlineCell(XMLWriter $xml, string $reference, string $value, int $style = 0): void
    {
        $xml->startElement('c');
        $xml->writeAttribute('r', $reference);
        $xml->writeAttribute('t', 'inlineStr');
        if ($style > 0) {
            $xml->writeAttribute('s', (string) $style);
        }
        $xml->startElement('is');
        $xml->startElement('t');
        $xml->writeAttribute('xml:space', 'preserve');
        $xml->text($value);
        $xml->endElement();
        $xml->endElement();
        $xml->endElement();
    }

    private function numberCell(XMLWriter $xml, string $reference, int $value, int $style = 0): void
    {
        $xml->startElement('c');
        $xml->writeAttribute('r', $reference);
        $xml->writeAttribute('t', 'n');
        if ($style > 0) {
            $xml->writeAttribute('s', (string) $style);
        }
        $xml->writeElement('v', (string) $value);
        $xml->endElement();
    }

    private function dateCell(XMLWriter $xml, string $reference, $date): void
    {
        if (! $date) {
            $this->inlineCell($xml, $reference, '-');

            return;
        }

        $excelSerial = ($date->getTimestamp() / 86400) + 25569;
        $xml->startElement('c');
        $xml->writeAttribute('r', $reference);
        $xml->writeAttribute('t', 'n');
        $xml->writeAttribute('s', '4');
        $xml->writeElement('v', number_format($excelSerial, 8, '.', ''));
        $xml->endElement();
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

    private function columnName(int $number): string
    {
        $name = '';
        while ($number > 0) {
            $number--;
            $name = chr(65 + ($number % 26)).$name;
            $number = intdiv($number, 26);
        }

        return $name;
    }

    private function contentTypes(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'<Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>'
            .'<Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>'
            .'</Types>';
    }

    private function rootRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>'
            .'<Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>'
            .'</Relationships>';
    }

    private function workbook(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Data User" sheetId="1" r:id="rId1"/></sheets>'
            .'</workbook>';
    }

    private function workbookRelationships(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>';
    }

    private function styles(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<numFmts count="1"><numFmt numFmtId="164" formatCode="dd mmm yyyy hh:mm"/></numFmts>'
            .'<fonts count="5">'
            .'<font><sz val="11"/><name val="Aptos"/><family val="2"/></font>'
            .'<font><b/><sz val="16"/><color rgb="FFFFFFFF"/><name val="Aptos Display"/></font>'
            .'<font><i/><sz val="10"/><color rgb="FF64748B"/><name val="Aptos"/></font>'
            .'<font><b/><sz val="10"/><color rgb="FFFFFFFF"/><name val="Aptos"/></font>'
            .'<font><sz val="11"/><name val="Aptos"/></font>'
            .'</fonts>'
            .'<fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FF2563EB"/><bgColor indexed="64"/></patternFill></fill></fills>'
            .'<borders count="2"><border/><border><bottom style="thin"><color rgb="FFDCE5F0"/></bottom></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="6">'
            .'<xf numFmtId="0" fontId="0" fillId="0" borderId="1" xfId="0" applyBorder="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFill="1" applyFont="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="2" fillId="0" borderId="0" xfId="0" applyFont="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="0" fontId="3" fillId="2" borderId="0" xfId="0" applyFill="1" applyFont="1"><alignment horizontal="center" vertical="center" wrapText="1"/></xf>'
            .'<xf numFmtId="164" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"><alignment vertical="center"/></xf>'
            .'<xf numFmtId="1" fontId="0" fillId="0" borderId="1" xfId="0" applyNumberFormat="1" applyBorder="1"><alignment horizontal="right" vertical="center"/></xf>'
            .'</cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>';
    }

    private function appProperties(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">'
            .'<Application>Puwinter</Application></Properties>';
    }

    private function coreProperties(): string
    {
        $created = now()->utc()->format('Y-m-d\TH:i:s\Z');

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">'
            .'<dc:title>Data User Puwinter</dc:title><dc:creator>Puwinter Admin</dc:creator>'
            .'<dcterms:created xsi:type="dcterms:W3CDTF">'.$created.'</dcterms:created></cp:coreProperties>';
    }
}
