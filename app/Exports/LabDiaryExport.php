<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;



class LabDiaryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell
{
    protected Collection $events;
    protected int $counter = 0;
    protected string $start;
    protected string $end;
    public function __construct(Collection $events, string $start, string $end)
    {
        $this->events = $events;
        $this->start = $start;
        $this->end = $end;
    }

    public function startCell(): string
    {
        return 'A5';
    }
    public function collection()
    {
        return $this->events;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Mục đích sử dụng',
            'Ngày',
            'Giờ',
            // 'Phòng lab',
            'Người sử dụng',
            'Tình trạng',
        ];
    }

    public function map($event): array
    {
        $this->counter++;
        $categoryMap = [
            'work' => "Làm việc-Nghiên cứu - ",
            'seminar' => "Hội thảo-Seminar - ",
            'other' => ""
        ];
        return [
            $this->counter,
            $categoryMap[$event->category].$event->title?? '',
            optional($event->start)->format('d/m/Y'),
            optional($event->start)->format('H:i').'-'.optional($event->end)->format('H:i'),
            // $event->lab?->name ?? $event->lab_code, 
            $event->group?->name ?: ($event->user?->full_name ?? ''),
            $event->feedback ?? '',
        ];
    }




    public function styles(Worksheet $sheet)
    {
        $text = ' ';

        if (!empty($this->start)) {
            $text .= 'Từ ngày ' . \Carbon\Carbon::parse($this->start)->format('d/m/Y');
        }

        if (!empty($this->end)) {
            if (!empty($text)) {
                $text .= ' ';
            }
            $text .= 'đến ngày ' . \Carbon\Carbon::parse($this->end)->format('d/m/Y');
        }

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'NHẬT KÝ SỬ DỤNG PHÒNG LAB');

        $sheet->mergeCells('A2:F2');
        $sheet->setCellValue('A2', 'Phát triển phần mềm và hệ thống thông minh');

        $sheet->mergeCells('A3:F3');
        $sheet->setCellValue('A4', $text);


        $sheet->mergeCells('A4:F4');
        $sheet->setCellValue(
            'A3',
            'Ngày xuất báo cáo: ' . now()->format('d/m/Y H:i')
        );

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 18,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'size' => 14,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A6');

        // Header style
        $sheet->getStyle('A5:F5')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '107C41'], // Excel green
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        // Border for all data
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A5:F{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);
        $sheet->setAutoFilter("A5:F{$lastRow}");

        // Align columns
        $sheet->getStyle("A5:E{$lastRow}")->getAlignment()->setHorizontal('center');
        $sheet->getStyle('B')->getAlignment()->setHorizontal('left');
        $sheet->getStyle('F')->getAlignment()->setWrapText(true);
    }
}
