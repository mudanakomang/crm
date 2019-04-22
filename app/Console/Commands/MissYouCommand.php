<?php

namespace App\Console\Commands;


use App\Contact;

use App\Http\Controllers\MailgunController;
use App\MailEditor;
use App\MissYou;
use Illuminate\Console\Command;

class MissYouCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'missyou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Miss You Letter';

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
        $miss=MissYou::find(1);
        $mg=new MailgunController();

        if($miss->active=='y'){
            $miss_templ=MailEditor::find($miss->template_id);
            $users=Contact::whereHas('transaction',function ($q) use ($miss){
                return $q->whereRaw('DATE(checkout) = DATE(NOW() - INTERVAL \''.$miss->sendafter.'\' MONTH)');
            })->get();

            foreach ($users as $user){
                $mg->sendmail($user,$miss_templ,'missyou');
            }
        }
    }
}
