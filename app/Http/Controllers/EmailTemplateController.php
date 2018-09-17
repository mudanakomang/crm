<?php

namespace App\Http\Controllers;

use App\Config;
use App\MailEditor;
use App\Contact;
use App\PostStay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DB;

class EmailTemplateController extends Controller
{

        public function sendmail(){

            $user=Contact::find(785);
            $template = MailEditor::where('name', 'oke')->first();
            Mail::send([], [], function($message) use ($template, $user)
            {

                    $data = [
                        'firstname' => $user->fname,
                        'title'=>$user->salutation
                    ];

                $message->to('mudanakomang@hotmail.com', $user->fname)
                    ->subject($template->subject)
                    ->setBody($template->parse($data), 'text/html');

            });
            return response('OK',200);
        }

        public function emailsend($user,$template,$subject){

            Mail::send([],[],function($message) use ($template,$user,$subject){
                $config=Config::find(1);
                $data=[
                    'firstname'=>$user->fname,
                    'lastname'=>$user->lname,
                    'title'=>$user->salutation,
                    'hotelname'=>$config->hotel_name,
                    'gmname'=>$config->gm_name,

                ];
                $message->to($user->email,$user->salutation.' '.$user->fname)
                    ->subject($subject)
                    ->setBody($template->parse($data),'text/html');
            });
        }

        public function birthdaymail(){
            $contacts=Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[\Carbon\Carbon::now()->format('m-d')])
                ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[\Carbon\Carbon::now()->addDays(7)->format('m-d')])
                ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->limit(10)->get();
                $template=MailEditor::where('name','happy_birthday')->first();
            foreach ($contacts as $contact){
                $this->emailsend($contact,$template,$template->subject.' '.$contact->gender=='M' ? 'Mr.':'Ms. /Mrs.'.' '.$contact->fname.' '.$contact->lname);
            }
        }
        public function getTemplate(Request $request){
            $template=MailEditor::find($request->id);
            return response($template,200);
        }


}
