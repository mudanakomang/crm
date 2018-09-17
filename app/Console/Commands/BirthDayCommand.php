<?php

namespace App\Console\Commands;

use App\Birthday;
use App\Contact;
use App\Http\Controllers\EmailTemplateController;
use App\MailEditor;
use Illuminate\Console\Command;

class BirthDayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdaymail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday Email';

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
        $birthday=Birthday::find(1);
        $email=new EmailTemplateController();
        if ($birthday->active=='y'){
            $template=MailEditor::find($birthday->template_id);
            $contacts=Contact::whereRaw('DATE_FORMAT(birthday,\'%m-%d\')=DATE_FORMAT(DATE_SUB(now(),INTERVAL -\''.$birthday->sendafter.'\' day),\'%m-%d\') ')->get();
            foreach ($contacts as $contact){
                $email->emailsend($contact,$template,$contact->gender=='M' ? $template->subject.' Mr.'.$contact->fname.' '.$contact->lname:$template->subject.' Ms./Mrs.'.$contact->fname.' '.$contact->lname);
            }
        }
    }
}
