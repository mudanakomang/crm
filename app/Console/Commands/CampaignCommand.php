<?php

namespace App\Console\Commands;

use App\Campaign;
use App\Http\Controllers\MailgunController;
use Carbon\Carbon;
use Illuminate\Console\Command;


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

        $campaigns=Campaign::where('status','=','Scheduled')->get();
        $mg=new MailgunController();
       // dd($campaigns);
       foreach ($campaigns as $campaign){

            $schedule=$campaign->schedule->schedule;

          if (Carbon::now()->format('Y-m-d H:i')>=Carbon::parse($schedule)->format('Y-m-d H:i')){
              if($campaign->type=='external'){
              $mg->sendexternalcampaign($campaign->id);
              $campaign->status='Sent';
              $campaign->save();
              $count=0;
              foreach($campaign->external as $contact){
                  $count++;
                  $campaign->external()->updateExistingPivot($contact,['status'=>'sent']);
                  if(($count%100)==0)
                  {
                      sleep(3600);
                  }                 
              }
            }else {    
               $mg->sendcampaign($campaign->id);
               $campaign->status='Sent';
               $campaign->save();
               $count=0;
               foreach ($campaign->contact as $key => $contact) {
                   $count++;
                   $campaign->contact()->updateExistingPivot($contact,['status'=>'sent']);
                   if(($count%100)==0)
                    {
                        sleep(3600);
                    }
               }
            }
           }
      }
    }
}
