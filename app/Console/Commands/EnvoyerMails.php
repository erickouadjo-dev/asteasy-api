<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Mail;

class EnvoyerMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:envoyer_mails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traiter la file d\'attente des mails.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            Mail::envoyer();
        }catch(\Exception $e){
            Log::error('EnvoyerMails::handle a échoué avec le message ' . $e->getMessage());
        }
    }
}
