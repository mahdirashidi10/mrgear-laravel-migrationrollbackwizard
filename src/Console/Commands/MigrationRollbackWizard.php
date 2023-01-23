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
    protected $signature = 'migrate:rollback:wizard {--limit=} {--file=} {--escape}';

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
        $maximum_batch = DB::table('migrations')->max('batch');
        if ($file) {
            if (File::isFile(base_path('database\migrations\\' . $file . '.php'))) {
                $file_name = $file;
                $base_query = DB::table('migrations')->where(['migration' => $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update(['batch' => $maximum_batch + 1]);
                    if (DB::table('migrations')->max('batch') > $maximum_batch) {
                        $this->runCommand('migrate:rollback', [], $this->output);
                    } else {
                        $this->warn('No file was prepared to migrate');
                    }
                }
            } else {
                $this->warn('There is no migration file with name ' . $file);
            }
        }
        if ($limit) {
            foreach (array_reverse(File::allFiles(base_path('database/migrations'))) as $key => $file) {
                if ($limit < $key + 1) {
                    break;
                }
                $file_name = $file->getFilenameWithoutExtension();
                $base_query = DB::table('migrations')->where(['migration' => $file_name]);
                if ($escape) {
                    $base_query->delete();
                    $this->info($file_name . ': Migration has been removed from "Migrated" list without rolling back');
                } else {
                    $base_query->update(['batch' => $maximum_batch + 1]);
                }
            }
            if (!$escape) {
                if (DB::table('migrations')->max('batch') > $maximum_batch) {
                    $this->runCommand('migrate:rollback', [], $this->output);
                } else {
                    $this->warn('No file was prepared to migrate');
                }
            }
        }
    }
}

