<?php

namespace App\Exports;

use App\Models\Patronyme;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PatronymesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Patronyme::with(['region', 'departement'])->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Origine',
            'Signification',
            'Histoire',
            'Région',
            'Département',
            'Fréquence',
        ];
    }

    public function map($patronyme): array
    {
        return [
            $patronyme->nom,
            $patronyme->origine,
            $patronyme->signification,
            $patronyme->histoire,
            $patronyme->region->name ?? '',
            $patronyme->departement->name ?? '',
            $patronyme->frequence,
        ];
    }
}
