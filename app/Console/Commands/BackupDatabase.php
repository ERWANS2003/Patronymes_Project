<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Create a database backup';

    public function handle()
    {
        $this->info('Creating database backup...');
        
        $filename = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $path = 'backups/' . $filename;
        
        // Créer le backup (simulation - en production, utiliser mysqldump ou pg_dump)
        $tables = DB::select('SHOW TABLES');
        $backup = "-- Database backup created on " . date('Y-m-d H:i:s') . "\n\n";
        
        foreach ($tables as $table) {
            $tableName = array_values((array) $table)[0];
            $backup .= "-- Table: $tableName\n";
            $backup .= "SELECT * FROM $tableName;\n\n";
        }
        
        Storage::disk('local')->put($path, $backup);
        
        $this->info("Backup created: $path");
        
        // Nettoyer les anciens backups (garder seulement les 7 derniers)
        $this->cleanOldBackups();
    }
    
    private function cleanOldBackups()
    {
        $files = Storage::disk('local')->files('backups');
        $backupFiles = array_filter($files, function($file) {
            return strpos($file, 'backup_') === 0;
        });
        
        if (count($backupFiles) > 7) {
            sort($backupFiles);
            $filesToDelete = array_slice($backupFiles, 0, count($backupFiles) - 7);
            
            foreach ($filesToDelete as $file) {
                Storage::disk('local')->delete($file);
            }
            
            $this->info('Old backups cleaned up');
        }
    }
}
