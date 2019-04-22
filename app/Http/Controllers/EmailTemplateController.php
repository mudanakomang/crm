<?php

namespace App\Http\Controllers;

use App\Config;
use App\Configuration;
use App\MailEditor;
use App\Contact;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DB;
use Mailgun\Mailgun;


class EmailTemplateController extends Controller
{

    public function sendmail()
    {

        $user = Contact::find(785);
        $template = MailEditor::where('name', 'oke')->first();
        Mail::send([], [], function ($message) use ($template, $user) {

            $data = [
                'firstname' => $user->fname,
                'title' => $user->salutation
            ];

            $message->to('mudanakomang@hotmail.com', $user->fname)
                ->subject($template->subject)
                ->setBody($template->parse($data), 'text/html');

        });
        return response('OK', 200);
    }

    public function emailsend($user, $template, $subject)
    {

        Mail::send([], [], function ($message) use ($template, $user, $subject) {
            $config = Config::find(1);
            $data = [

                'firstname' => $user->fname,
                'lastname' => $user->lname,
                'title' => $user->salutation,
                'hotelname' => $config->hotel_name,
                'gmname' => $config->gm_name,

            ];
            $message->to($user->email, $user->salutation . ' ' . $user->fname)
                ->subject($subject)
                ->setBody($template->parse($data), 'text/html');
        });
    }

    private function setting($param){
        if ($param=='key'){
            return env('MAILGUN_SECRET');
        } else{
            return env('MAILGUN_DOMAIN');
        }
    }
    public function testmail($request)
    {

        $config=Configuration::find(1);
        $mg=Mailgun::create($config->mailgun_apikey);
        $template=MailEditor::find($request->id);
        $contact=$request->email;
            $data=[
                'firstname'=>'{firstname}' ,
                'lastname'=>'{lastname}' ,
                'title'=>'{title}',
                'hotelname'=>'{hotelname}',
                'gmname'=>'{gmname}',
            ];
                $mg->messages()->send($config->mailgun_domain,
                    [
                        'from' => $config->sender_name.'<'.$config->sender_email.'>',
                        'to' =>'Mr. Tester'.'<'.$contact.'>',
                        'subject' => $template->subject,
                        'html' => $template->parse($data),
                        'o:tracking-opens' => true,
                        'o:tracking-clicks' => true,
                        'o:tag'=>['Testing']
                    ]);

        }

        public function testX(){

            $domain=$this->setting('domain');
            $key=$this->setting('key');

            $mg=Mailgun::create($key);
           // dd($mg);
            $template=MailEditor::first();
            $contact='danabala72@gmail.com';
            $data=[
                'firstname'=>'{firstname}' ,
                'lastname'=>'{lastname}' ,
                'title'=>'{title}',
                'hotelname'=>'{hotelname}',
                'gmname'=>'{gmname}',
            ];
            $mg->messages()->send($domain,
                [
                    'from' => 'Mr. Tester<sales@kutaseaviewhotel.com>',
                    'to' =>'Mr. Tester'.'<'.$contact.'>',
                    'subject' => $template->subject,
                    'html' => $template->parse($data),
                    'o:tracking-opens' => true,
                    'o:tracking-clicks' => true,
                    'o:tag'=>['Testing']
                ]);

        }




    public function birthdaymail()
    {
        $contacts = Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?', [\Carbon\Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?', [\Carbon\Carbon::now()->addDays(7)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'), 'asc')->limit(10)->get();
        $template = MailEditor::where('name', 'happy_birthday')->first();
        foreach ($contacts as $contact) {
            $this->emailsend($contact, $template, $template->subject . ' ' . $contact->gender == 'M' ? 'Mr.' : 'Ms. /Mrs.' . ' ' . $contact->fname . ' ' . $contact->lname);
        }
    }

    public function getTemplate(Request $request)
    {
        $template = MailEditor::find($request->id);
        return response($template, 200);
    }
}