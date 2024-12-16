<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class TestCasesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $project;
    protected $columns;

    public function __construct($project, $columns)
    {
        $this->project = $project;
        $this->columns = $columns;
    }

    public function collection()
    {
        // Fetch test cases related to the project, eager load 'tester' relationship
        $testCases = $this->project->testCases()->with('tester')->get();

        // Map the data based on selected columns
        return $testCases->map(function ($testCase) {
            $row = [];

            // Dynamically include selected columns
            foreach ($this->columns as $column) {
                if ($column === 'test_status') {
                    $row[$column] = $this->getStatusText($testCase->test_status);
                } elseif ($column === 'tested_by') {
                    $row[$column] = $testCase->tester->name ?? 'N/A';
                } else {
                    $row[$column] = $testCase->$column ?? '';
                }
            }

            return $row;
        });
    }

    public function headings(): array
    {
        // Use selected columns as headings
        return array_map(function ($column) {
            return ucwords(str_replace('_', ' ', $column));
        }, $this->columns);
    }

    private function getStatusText($status)
    {
        return match ($status) {
            0 => 'Pending',
            1 => 'Pass',
            2 => 'Fail',
            default => 'Unknown',
        };
    }
}
