<?php

namespace App\Http\Controllers;

require base_path().'/vendor/autoload.php';

use App\Campaign;
use App\MailEditor;
use App\Segment;
use Illuminate\Http\Request;
use Mailgun\Mailgun;
use App\Http\Controllers\EmailTemplateController;
use Carbon\Carbon;


class MailgunController extends Controller
{

    private function setting($param){
     if ($param=='key'){
        return env('MAILGUN_SECRET');
     } else{
         return env('MAILGUN_DOMAIN');
     }
    }
    public function sendmail(){
        $campaign_id=128;
        $domain=$this->setting('domain');
        $key=$this->setting('key');
        $mg=Mailgun::create($key);
        $campaign=Campaign::find($campaign_id);
        $template=$campaign->template;
        $contacts=$campaign->contact;
        foreach ($contacts as $contact){
            $data=[
                'firstname'=>$contact->fname ,
                'lastname'=>$contact->lname ,
                'title'=>$contact->salutation,
                'hotelname'=>'RRP',
                'gmname'=>'Mr X',
            ];
            if (!empty($contact->email)){
                $mg->messages()->send($domain,
                    [
                        'from' => 'Admin <it.sysdev@rcoid.com>',
                        'to' => $contact->fname . ' ' . $contact->lname . '<danabala72@gmail.com>',
                        'subject' => $template[0]->subject,
                        'html' => $template[0]->parse($data),
                        'o:tracking-opens' => true,
                        'o:tracking-clicks' => true,
                        'o:tag' => [$campaign->id, $contact->email]
                    ]);
            }
        }
    }


    public function getLogs(){
        $domain=$this->setting('domain');
        $dt=Carbon::now()->timezone('UTC')->format('D, d M Y 00:00:00 -0000');;
        $mg=Mailgun::create($this->setting('key'));
        $q=[
        ];
        $res=$mg->events()->get($domain,$q);
        return $res->getItems();
    }
//    public function getSuppression($type){
//        $mg=Mailgun::create($this->setting('key'));
//
//        if ($type=='unsubscribes'){
//           $res= $mg->suppressions()->unsubscribes()->index($this->setting('domain'));
//            return $res->getItems();
//        }elseif($type=='bounces'){
//            $res=$mg->suppressions()->bounces()->index($this->setting('domain'));
//            return $res->getItems();
//        }else{
//            $res=$mg->suppressions()->complaints()->index($this->setting('domain'));
//            return $res->getItems();
//        }
//    }
//    public function unsub(){
//        return $this->getSuppression('unsubscribes');
//    }

    public function unsub(){
        $mg=Mailgun::create($this->setting('key'));
        $res=$mg->events()->get($this->setting('domain'),['event'=>'unsubscribed']);
        dd($res);
    }


}

