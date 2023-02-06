<?php

namespace MRGear\MRW\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class MigrationRollbackWizard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:rollback:wizard {--limit=} {--file=} {--escape} {--include}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Extra functionality for migrations';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $file = $this->option('file');
        $escape = $this->option('escape');
        $database_table_name = config('mrw.migration.database.name');
        $database_countable_row = config('mrw.migration.database.countable');
        $database_file_name_row = config('mrw.migration.database.file');
        $maximum_batch = DB::table($database_table_name)->max($database_countable_row);
        if($this->option('include')){
            foreach (config('mrw.migration.include') as $key => $file) {
                if ($limit && $limit < $key + 1) {
                    break;
                }
                $file_name = $file->getFilenameWithoutExtension();
                $base_query = DB::table($database_table_name)->where([$database_file_name_row => $file_name]);
                if ($base_query->exists()){
                    if ($escape) {
                        $base_query->delete();
                        $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                    } else {
                        $base_query->update([$database_countable_row => $maximum_batch + 1]);
                    }
                }
            }
            if (!$escape) {
                if (DB::table($database_table_name)->max($database_countable_row) >
                    $maximum_batch) {
                    $this->runCommand('migrate:rollback', [], $this->output);
                } else {
                    $this->warn('No file was prepared to migrate');
                }
            }
        }
        elseif ($file) {
            if (File::isFile(config('mrw.migration.directory') . $file . '.php')) {
                $file_name = $file;
                $base_query = DB::table($database_table_name)->where([$database_file_name_row =>
                    $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update([$database_countable_row => $maximum_batch + 1]);
                    if (DB::table($database_table_name)->max($database_countable_row) >
                        $maximum_batch) {
                        $this->runCommand('migrate:rollback', [], $this->output);
                    } else {
                        $this->warn('No file was prepared to migrate');
                    }
                }
            } else {
                $this->warn('There is no migration file with name ' . $file);
            }
        }
        elseif ($limit) {
            foreach (array_reverse(File::allFiles(config('mrw.migration.directory'))) as $key => $file) {
                if ($limit < $key + 1) {
                    break;
                }
                $file_name = $file->getFilenameWithoutExtension();
                $base_query = DB::table($database_table_name)->where([$database_file_name_row =>
                    $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update([$database_countable_row => $maximum_batch + 1]);
                }
            }
            if (!$escape) {
                if (DB::table($database_table_name)->max($database_countable_row) >
                    $maximum_batch) {
                    $this->runCommand('migrate:rollback', [], $this->output);
                } else {
                    $this->warn('No file was prepared to migrate');
                }
            }
        }
    }
}

