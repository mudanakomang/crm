<?php

namespace App\Console\Commands;

use App\Campaign;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\MailEditor;
use App\Http\Controllers\EmailTemplateController;

class CampaignCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Email Task';

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
        $email= new EmailTemplateController();
        $campaigns=Campaign::where('status','=','Scheduled')->get();



       foreach ($campaigns as $campaign){
            $temp=$campaign->template;

            $schedule=$campaign->schedule->schedule;

          if (Carbon::now()->format('Y-m-d H:i')==Carbon::parse($schedule)->format('Y-m-d H:i')){
               foreach ($campaign->contact as $contact) {
                   if ($contact->pivot->status=='queue'){
                       $email->emailsend($contact,$temp[0],$temp[0]->subject);
                       $campaign->contact()->updateExistingPivot($contact,['status'=>'sent']);
                   }
               }
              $campaign->status='Sent';
              $campaign->save();
           }
      }
    }
}
