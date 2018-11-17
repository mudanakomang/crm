<?php

namespace App\Console\Commands;

use App\EmailResponse;
use Illuminate\Console\Command;
use \App\Http\Controllers\MailgunController;

class SyncEmailResponse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncemailresponse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Email Response';

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
     * @return mixed
     */
    public function handle()
    {
        //

        $mg = new MailgunController();
        $logs = $mg->getLogs();
        foreach ($logs as $log) {
            if (isset($log->getTags()[1])) {
                    $email = EmailResponse::updateOrCreate(
                        ['email_id' => $log->getId()],
                        ['campaign_id' => $log->getTags()[1], 'event' => $log->getEvent(), 'url' => $log->getUrl(), 'tags' => serialize($log->getTags()), 'recepient' => $log->getTags()[0]]);
            }
        }
    }
}
