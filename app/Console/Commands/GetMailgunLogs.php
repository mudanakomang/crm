<?php

namespace App\Console\Commands;

use App\ExcludedEmail;
use App\Http\Controllers\MailgunController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\MailgunLogs;
class GetMailgunLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getmailgunlogs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Mailgun Logs';

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

        $mg=new MailgunController();
        $logs=$mg->getLogs(null,null,null,'NOT accepted','NOT danabala72@gmail.com');
        dd($logs);
        foreach ($logs as $log){
            $time=Carbon::createFromTimestamp($log->getTimestamp())->format('Y-m-d H:i:s');
                MailgunLogs::updateOrCreate(
                    ['email_id' => $log->getId(),'recipient'=>$log->getRecipient(),'message_id'=>$log->getMessage()['headers']['message-id']],
                    ['event' => $log->getEvent(),'severity' => $log->getSeverity(), 'url' => $log->getUrl(), 'tags' => $log->getTags()[0], 'recipient' => $log->getRecipient(), 'timestamp'=>$time ,'delivery_status' => $log->getDeliveryStatus()['message']]);
            }


        $failedemails=MailgunLogs::where(['event'=>'failed','severity'=>'permanent'])->select('recipient')->get();
        foreach($failedemails as $failedemail){
            ExcludedEmail::updateOrCreate(
                ['email'=>$failedemail->recipient]
            );
        }
    

    }
}
