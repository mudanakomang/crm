<?php

namespace App\Http\Controllers;

use App\Configuration;
use Illuminate\Http\Request;

class PreferencesController extends Controller
{
    //
    public function savePreferences(Request $request){

        $prf=Configuration::find(1);
        $prf->hotel_name=$request->hotel_name;
        $prf->app_title=$request->app_title;
        $prf->gm_name=$request->gm_name;
        $prf->mailgun_domain=$request->mailgun_domain;
        $prf->mailgun_password=$request->mailgun_password;
        $prf->mailgun_apikey=$request->mailgun_apikey;
        $prf->sender_email=$request->sender_email;
        $prf->sender_name=$request->sender_name;

        $prf->save();
        return response('success',200);
    }
}
