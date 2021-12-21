<?php

namespace App\Exports;

use App\Models\Complaint;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ComplaintsExport implements FromCollection, WithStrictNullComparison, WithHeadings, WithColumnWidths, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $complaints = Complaint::select(
            'complaints.cod',
            'complaints.address',
            'complaints.latitude',
            'complaints.longitude',
            'complaints.description',
            'complaints.name_offender',
            DB::raw('DATE_FORMAT(complaints.created_at, "%d-%M-%Y")'),
            'complaint_types.name as type_complaint',
            DB::raw('CONCAT(users.name, " ", users.last_name) AS informer'),
            DB::raw('CONCAT(user_asigne.name, " ", user_asigne.last_name) AS user_asigne'),
            DB::raw('CONCAT(user_inquest.name, " ", user_inquest.last_name) AS user_inquest'),
            'state_complaints.name as state'
        )
            ->join('complaint_types', 'complaints.id_complaint_type', 'complaint_types.id')
            ->leftjoin('users', 'complaints.id_user', 'users.id')
            ->leftjoin('users as user_asigne', 'complaints.id_user_asigne', 'user_asigne.id')
            ->leftjoin('users as user_inquest', 'complaints.id_user_inquest', 'user_inquest.id')
            ->join('state_complaints', 'complaints.id_state', 'state_complaints.id')
            ->get();


        return $complaints;
    }

    public function headings(): array
    {
        return [
            "COD",
            "DIRECCION",
            "LATITUD",
            "LONGITUD",
            "DESCRIPCIÓN",
            "INFRACTOR",
            "FECHA CREACIÓN",
            "TIPO DENUNCIA",
            "DENUNCIANTE",
            "FUNCIONARIO ASIGNADO",
            "ABOGADO ASIGNADO",
            "ESTADO"
        ];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 30,
            'F' => 25,
            'G' => 20,
            'H' => 23,
            'I' => 23,
            'J' => 23,
            'K' => 23,
            'L' => 15
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]]
        ];
    }
}
