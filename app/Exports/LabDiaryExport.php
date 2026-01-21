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
        return 'A4';
    }
    public function collection()
    {
        return $this->events;
    }

    public function headings(): array
    {
        return [
            'STT',
            'Tiêu đề',
            'Phân loại',
            'Bắt đầu',
            'Kết thúc',
            'Phòng lab',
            'Người đăng ký',
            'Đăng ký cho',
            'Feedback',
        ];
    }

    public function map($event): array
    {
        $this->counter++;
        $categoryMap = [
            'work' => "Làm việc-Nghiên cứu",
            'seminar' => "Hội thảo-Seminar",
            'other' => "Khác"
        ];
        return [
            $this->counter,
            $event->title,
            $categoryMap[$event->category]?? '',
            optional($event->start)->format('d/m/Y H:i'),
            optional($event->end)->format('d/m/Y H:i'),
            $event->lab?->name ?? $event->lab_code,
            $event->user?->full_name ?? '',
            $event->group?->name ?: ($event->user?->full_name ?? ''),
            $event->feedback ?? '',
        ];
    }




    public function styles(Worksheet $sheet)
    {
        $text = '';

        if (!empty($this->start)) {
            $text .= 'Từ ngày ' . \Carbon\Carbon::parse($this->start)->format('d/m/Y');
        }

        if (!empty($this->end)) {
            if (!empty($text)) {
                $text .= ' ';
            }
            $text .= 'đến ngày ' . \Carbon\Carbon::parse($this->end)->format('d/m/Y');
        }

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'NHẬT KÝ SỬ DỤNG PHÒNG LAB');

        $sheet->mergeCells('A2:I2');

        $sheet->setCellValue('A2', $text);


        $sheet->mergeCells('A3:I3');
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
        // Freeze header row
        $sheet->freezePane('A5');

        // Header style
        $sheet->getStyle('A4:I4')->applyFromArray([
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
        $sheet->getStyle("A4:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ]);
        $sheet->setAutoFilter("A4:I{$lastRow}");

        // Align columns
        $sheet->getStyle('B:C')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('E')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('I')->getAlignment()->setWrapText(true);
    }
}
