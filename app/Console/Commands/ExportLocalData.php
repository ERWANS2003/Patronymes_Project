<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Exporte le contenu de la base courante vers un fichier JSON,
 * pour le recharger ailleurs avec `app:import-local-data`.
 */
class ExportLocalData extends Command
{
    protected $signature = 'app:export-local-data {--path=database/data/export.json}';

    protected $description = 'Exporte les données de la base courante vers un fichier JSON';

    /** Tables techniques : leur contenu ne se transporte pas. */
    public const SKIPPED = [
        'migrations', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches',
        'failed_jobs', 'password_reset_tokens', 'personal_access_tokens', 'sqlite_sequence',
    ];

    public function handle(): int
    {
        $export = [];
        foreach (Schema::getTableListing(schemaQualified: false) as $table) {
            if (in_array($table, self::SKIPPED, true)) {
                continue;
            }
            $rows = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
            $export[$table] = $rows;
            $this->line(sprintf('%-32s %d', $table, count($rows)));
        }

        $path = base_path($this->option('path'));
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
        file_put_contents($path, json_encode($export, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->info("Export écrit dans {$path}");

        return self::SUCCESS;
    }
}
