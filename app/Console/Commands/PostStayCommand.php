<?php

namespace App\Console\Commands;

use App\Contact;

use App\Http\Controllers\MailgunController;
use App\MailEditor;
use App\PostStay;
use Illuminate\Console\Command;

class PostStayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'poststay';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send PostStay Email';

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
        $poststay=PostStay::find(1);


        $mg=new MailgunController();
        if($poststay->active=='y'){
            $poststay_templ=MailEditor::find($poststay->template_id);
            $user_list=[];
            $users1=\App\Contact::has('transaction','>',1)->get();
            foreach ($users1 as $u){
                if(\Carbon\Carbon::parse($u->latestTransaction[0]->checkout)->addDay($poststay->sendafter)->format('Y-m-d')==\Carbon\Carbon::now()->format('Y-m-d')){
                    array_push($user_list,$u);
                }
            }
            $users2=\App\Contact::has('transaction','=',1)->get();
            foreach ($users2 as $u){
                if(\Carbon\Carbon::parse($u->transaction[0]->checkout)->addDay($poststay->sendafter)->format('Y-m-d')==\Carbon\Carbon::now()->format('Y-m-d')){
                    array_push($user_list,$u);
                }
            }
            foreach ($user_list as $user){
               //dd($user);
                 $mg->sendmail($user,$poststay_templ,'poststay');
            }
        }


    }
}
