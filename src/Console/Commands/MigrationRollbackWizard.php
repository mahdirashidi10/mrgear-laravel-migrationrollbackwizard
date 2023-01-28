<?php

namespace Danny\Migrations\Console\Commands;

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
        $maximum_batch = DB::table(config('mrw.migration.database.name'))->max(config('mrw.migration.database.countable'));
        if($this->option('include')){
            foreach (config('mrw.migration.include') as $key => $file) {
                if ($limit && $limit < $key + 1) {
                    break;
                }
                $file_name = $file->getFilenameWithoutExtension();
                $base_query = DB::table(config('mrw.migration.database.name'))->where([config('mrw.migration.database.file') => $file_name]);
                if ($base_query->exists()){
                    if ($escape) {
                        $base_query->delete();
                        $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                    } else {
                        $base_query->update([config('mrw.migration.database.countable') => $maximum_batch + 1]);
                    }
                }
            }
            if (!$escape) {
                if (DB::table(config('mrw.migration.database.name'))->max(config('mrw.migration.database.countable')) >
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
                $base_query = DB::table(config('mrw.migration.database.name'))->where([config('mrw.migration.database.file') =>
                    $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update([config('mrw.migration.database.countable') => $maximum_batch + 1]);
                    if (DB::table(config('mrw.migration.database.name'))->max(config('mrw.migration.database.countable')) >
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
                $base_query = DB::table(config('mrw.migration.database.name'))->where([config('mrw.migration.database.file') =>
                    $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update([config('mrw.migration.database.countable') => $maximum_batch + 1]);
                }
            }
            if (!$escape) {
                if (DB::table(config('mrw.migration.database.name'))->max(config('mrw.migration.database.countable')) >
                    $maximum_batch) {
                    $this->runCommand('migrate:rollback', [], $this->output);
                } else {
                    $this->warn('No file was prepared to migrate');
                }
            }
        }
    }
}

