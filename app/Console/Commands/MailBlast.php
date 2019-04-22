<?php

namespace App\Console\Commands;

use App\Blast;
use App\Blastlist;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Controllers\MailgunController;

class MailBlast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mailblast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $mg=new MailgunController();
        $blasts=Blast::where('status','=','queue')->get();
        foreach ($blasts as $blast){
            $template=\App\MailEditor::find($blast->template_id);
            //dd($template->subject);
            if(Carbon::now()->format('Y-m-d H:i:s')>=\Carbon\Carbon::parse($blast->schedule)->format('Y-m-d H:i:s')){
                 foreach ($blast->email as $email){
                     $mg->blast($email,$template,'emailblast');
                 }
            }
            $blast->status='sent';
            $blast->save();
        }
    }
}
