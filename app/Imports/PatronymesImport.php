<?php

namespace App\Imports;

use App\Models\Patronyme;
use App\Models\Region;
use App\Models\Departement;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PatronymesImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Recherche ou création de la région
        $region = null;
        if (!empty($row['region'])) {
            $region = Region::firstOrCreate(
                ['name' => $row['region']],
                ['code' => $this->generateRegionCode($row['region'])]
            );
        }

        // Recherche ou création du département
        $departement = null;
        if (!empty($row['departement'])) {
            $departement = Departement::firstOrCreate(
                ['name' => $row['departement']],
                [
                    'code' => $this->generateDepartementCode($row['departement']),
                    'region_id' => $region ? $region->id : null
                ]
            );
        }

        return new Patronyme([
            'nom' => $row['nom'],
            'origine' => $row['origine'] ?? null,
            'signification' => $row['signification'] ?? null,
            'histoire' => $row['histoire'] ?? null,
            'region_id' => $region ? $region->id : null,
            'departement_id' => $departement ? $departement->id : null,
            'frequence' => $row['frequence'] ?? 0,
        ]);
    }

    public function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'origine' => 'nullable|string',
            'signification' => 'nullable|string',
            'histoire' => 'nullable|string',
            'frequence' => 'nullable|integer|min:0',
        ];
    }

    private function generateRegionCode($name): string
    {
        return strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
    }

    private function generateDepartementCode($name): string
    {
        return strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
    }
}
