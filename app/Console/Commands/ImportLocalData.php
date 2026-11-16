<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Charge dans la base courante le fichier produit par `app:export-local-data`.
 * Ne fait rien si la base contient déjà des données : on peut la relancer sans risque.
 */
class ImportLocalData extends Command
{
    protected $signature = 'app:import-local-data {--path=database/data/export.json}';

    protected $description = 'Importe les données exportées depuis la base locale';

    public function handle(): int
    {
        $path = base_path($this->option('path'));
        if (! is_file($path)) {
            $this->warn("Aucun export à importer ({$path}).");

            return self::SUCCESS;
        }

        if (DB::table('patronymes')->exists() || DB::table('users')->exists()) {
            $this->info('La base contient déjà des données : import ignoré.');

            return self::SUCCESS;
        }

        $export = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        $tables = array_values(array_filter(array_keys($export), fn ($table) => Schema::hasTable($table)));

        DB::transaction(function () use ($export, $tables) {
            foreach ($this->sortByDependencies($tables) as $table) {
                $columns = collect(Schema::getColumns($table))->keyBy('name');
                $rows = [];
                foreach ($export[$table] as $row) {
                    $clean = [];
                    foreach ($row as $column => $value) {
                        if (! $columns->has($column)) {
                            continue;
                        }
                        // Valeur absente sur une colonne obligatoire : on laisse la valeur par défaut de la base.
                        if ($value === null && ! $columns[$column]['nullable']) {
                            continue;
                        }
                        // SQLite stocke les booléens en 0/1, PostgreSQL attend de vrais booléens.
                        if ($value !== null && $columns[$column]['type_name'] === 'bool') {
                            $value = (bool) $value;
                        }
                        $clean[$column] = $value;
                    }
                    $rows[] = $clean;
                }
                // Ligne par ligne : les lignes n'ont pas toutes les mêmes colonnes renseignées.
                foreach ($rows as $row) {
                    DB::table($table)->insert($row);
                }
                $this->line(sprintf('%-32s %d', $table, count($rows)));

                $this->resetSequence($table, $columns->has('id'));
            }
        });

        $this->info('Import terminé.');

        return self::SUCCESS;
    }

    /** Range les tables pour que chaque table référencée soit remplie avant celles qui en dépendent. */
    private function sortByDependencies(array $tables): array
    {
        $dependencies = [];
        foreach ($tables as $table) {
            $dependencies[$table] = [];
            foreach (Schema::getForeignKeys($table) as $foreignKey) {
                $parent = $foreignKey['foreign_table'];
                if ($parent !== $table && in_array($parent, $tables, true)) {
                    $dependencies[$table][] = $parent;
                }
            }
        }

        $sorted = [];
        while ($dependencies) {
            $ready = array_keys(array_filter($dependencies, fn ($parents) => ! array_diff($parents, $sorted)));
            if (! $ready) {
                // Dépendance circulaire : on prend le reste tel quel.
                $ready = array_keys($dependencies);
            }
            foreach ($ready as $table) {
                $sorted[] = $table;
                unset($dependencies[$table]);
            }
        }

        return $sorted;
    }

    /** Après des insertions avec des id explicites, la séquence PostgreSQL doit repartir du plus grand id. */
    private function resetSequence(string $table, bool $hasId): void
    {
        if (! $hasId || DB::getDriverName() !== 'pgsql') {
            return;
        }
        DB::statement(
            "SELECT setval(pg_get_serial_sequence('\"{$table}\"', 'id'), COALESCE((SELECT MAX(id) FROM \"{$table}\"), 1))"
        );
    }
}
