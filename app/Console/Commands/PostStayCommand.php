<?php

namespace App\Console\Commands;

use App\Birthday;
use App\Contact;
use App\Http\Controllers\EmailTemplateController;
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

        $email=new EmailTemplateController();
        if($poststay->active=='y'){
            $poststay_templ=MailEditor::find($poststay->template_id);
            $users=Contact::whereHas('transaction',function ($q) use ($poststay){
                return $q->whereRaw('date_format(now(),\'%y-%m-%d\')=DATE_ADD(checkout,INTERVAL \''.$poststay->sendafter.'\' day)');
            })->get();
            foreach ($users as $user){
                $email->emailsend($user,$poststay_templ,$user->gender=='M' ? $poststay_templ->subject.' Mr.'.$user->fname.' '.$user->lname:$poststay_templ->subject.' Ms./Mrs.'.$user->fname.' '.$user->lname);
            }
        }

    }
}
