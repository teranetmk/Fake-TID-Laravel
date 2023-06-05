<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Carbon\Carbon;
use File;
class DatabaseBackup extends Command{    
 
     protected $signature = 'database:backup';   

       protected $description = 'Create copy of mysql dump for existing database.';    
      
       public function __construct()    {
                parent::__construct();    
            }   
             /**     * Execute the console command.     *     * @return int     */
        public function handle()    {
            $filename = "backup-" . Carbon::now()->format('Y-m-d') . ".sql";

                            // Create backup folder and set permission if not exist.       
             $storageAt = storage_path() . "/app/backup/";        
             if(!File::exists($storageAt)) {            
                File::makeDirectory($storageAt, 0755, true, true);        
            }       
             $command = "".env('DB_DUMP_PATH', 'mysqldump')." --user=" . env('DB_USERNAME') ." --password=" . env('DB_PASSWORD') . " --host=" . env('DB_HOST') . " " . env('DB_DATABASE') . "  | gzip > " . $storageAt . $filename;  
                  
             $returnVar = NULL;        
             $output = NULL;        
             exec($command, $output, $returnVar); 
                
            }
        }