<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Contoh template
Route::get('/example', function () {
    return view('example');
});


Route::get('testing',function(){
    $count=0;
    $u='';
    $users=['asasa','sasa','sasasa','asasw','sawe','sasadf','sadsc','sasasasa','sasasa','asasa','sasa','sasasa','asasw','sawe','sasadf','sadsc','sasasasa','sasasa'];
    foreach($users as $user){
        $count++;
        $u.=$user;
        if(($count%10)==0)
        {
           $u.='<br>';
            sleep(5);
        }
    }
    echo $u;

});

//Route::get('contacts/list',function(){
//
////setlocale(LC_MONETARY,"id_ID");
////    $b=[];
////    $a=[];
////    $c=[];
////
////    $contacts=\App\Contact::get();
////    foreach ($contacts as $contact){
////        array_push($b,$contact);
////        foreach ($contact->transaction as $trx){
////
////            array_push($c,$trx);
////
////        }
////        array_merge($b,$a);
////        array_merge($b,$c);
////
////    }
////    //dd($b[0]);
////	return view('contacts.list',['data'=>$b]);
//});

Route::group(['middleware'=>'auth'],function (){
    Route::get('home',function (){
        return redirect('/');
    });
    Route::get('reviews','ContactController@reviews');
    Route::post('campaign/getsegment','CampaignController@getSegment');
    Route::post('campaign/activate','CampaignController@activateCampaign')->name('campaign.activate');
    Route::get('contacts/f/male','ContactController@male');
    Route::get('contacts/f/female','ContactController@female');
    Route::get('contacts/f/inhouse','ContactController@inhouse');
    Route::get('contacts/f/country/{country}','ContactController@country');
    Route::get('contacts/f/created/{dateadded}','ContactController@dateadded');
    Route::get('contacts/f/status/{status}','ContactController@dstatus');
    Route::get('contacts/f/longest/{contact}','ContactController@longest');
    Route::get('contacts/f/spending/{spending}','ContactController@spending');
    Route::get('contacts/f/roomtype/{type}','ContactController@type');
    Route::get('contacts/f/ages/{type}','ContactController@ages');
    Route::get('contacts/f/source/{type}','ContactController@source');
    Route::get('/','ContactController@dashboard');
    Route::get('contacts/birthday',function (){
        return view('contacts.birthday');
    });
    Route::get('contacts/filter','ContactController@filter');
    Route::post('contacts/filter','ContactController@filterPost')->name('filter');
    Route::post('contacts/birthday/search','ContactController@search');
    Route::get('contacts/detail/{id}','ContactController@show');
    Route::post('contacts/update','ContactController@update')->name('contacts.update');
    Route::get('contacts/add','ContactController@create');
    Route::post('contacts/store','ContactController@store')->name('contacts.store');
    Route::get('contacts/delete/{id}','ContactController@destroy')->name('contacts.destroy');
    Route::get('contacts/incomplete','ContactController@incomplete');
    Route::post('contacts/incomplete/update','ContactController@updateStatus');
    Route::get('contacts/excluded/email','ContactController@excluded');
    Route::post('contacts/excluded/addemail','ContactController@addEmail');

    Route::post('deliverystatus','MailgunController@deliveryStatus')->name('deliverystatus');


    Route::get('email/template','Emailtemplate@template')->name('email.template');

//birthday config
    Route::get('email/config/birthday','Emailtemplate@birthdayConfig');
    Route::post('email/config/birthday/update','Emailtemplate@birthdayUpdate')->name('birthday.update');
    Route::post('email/config/template','Emailtemplate@birthdayTemplate')->name('email.birthdayTemplate');
    Route::post('email/config/birthday/activate','Emailtemplate@birthdayActivate');
//post stay config
    Route::get('email/config/poststay','Emailtemplate@postStayConfig');
    Route::post('email/config/poststay/update','Emailtemplate@postStayUpdate')->name('poststay.update');
    Route::post('email/config/template','Emailtemplate@poststayTemplate')->name('email.poststayTemplate');
    Route::post('email/config/poststay/activate','Emailtemplate@poststayActivate');
    Route::get('email/{id}/review','ContactController@review');
    Route::get('email/config/confirm','Emailtemplate@confirmConfig');
    Route::post('email/config/confirm/update','Emailtemplate@confirmUpdate')->name('confirm.update');
    Route::post('email/config/confirm','Emailtemplate@confirmTemplate')->name('email.confirmTemplate');
    Route::post('email/config/confirm/activate','Emailtemplate@confirmActivate');
    Route::get('email/delivery/status','MailgunController@delivery');
    Route::post('getClick','MailgunController@getclick')->name('getClick');
//We Miss You Letter
    Route::get('email/config/miss','Emailtemplate@missConfig');
    Route::post('email/config/miss/update','Emailtemplate@missUpdate')->name('miss.update');
    Route::post('email/config/miss','Emailtemplate@missTemplate')->name('email.missTemplate');
    Route::post('email/config/miss/activate','Emailtemplate@missActivate');
    Route::post('email/{id}/saverating','Emailtemplate@saveRating');

    Route::resource('email','Emailtemplate');
	Route::post('template/destroy','Emailtemplate@destroy');
	
    Route::post('campaign/template','EmailTemplateController@getTemplate')->name('campaign.template');
    Route::post('campaign/recepient','CampaignController@getRecepient')->name('campaign.recepient');
    Route::post('campaign/{id}/recepient','CampaignController@getRecepient');
    Route::post('campaign/gettype','CampaignController@getType');
    Route::post('campaign/{id}/gettype','CampaignController@getType');

    //Route::resource('campaign','CampaignController');
    route::get('campaign/list','CampaignController@index');
    Route::delete('campaign/{id}','CampaignController@destroy')->name('campaign.destroy');
    Route::get('campaign/create','CampaignController@create');
    Route::post('campaign/store','CampaignController@store')->name('campaign.store');
    Route::get('campaign','CampaignController@index');

    Route::get('mailsend/','EmailTemplateController@birthdaymail');

    //External contact
    Route::get('contacts/external','ExternalEmailController@index');
    Route::post('contacts/saveexternalcontact','ExternalEmailController@saveexternalcontact');
    Route::delete('contacts/external/destroy','ExternalEmailController@destroy');
    Route::post('loadcategory','ExternalEmailController@categorylist')->name('loadcategory');
    Route::post('delcategory','ExternalEmailController@delcategory')->name('delcategory');
//import contact
    Route::get('contacts/import','ContactController@import');
    Route::get('contacts/template/contact',function (){
        return response()->download(public_path().'/files/contacts-template.csv');
    });
    Route::post('contacts/upload/contact','ContactController@uploadContact');

//import stay
    Route::get('contacts/importstay','ContactController@importStay');
    Route::get('contacts/template/stay',function (){
        return response()->download(public_path().'/files/stays-template.csv');
    });
    Route::post('contacts/upload/stay','ContactController@uploadStay');


    Route::post('contacts/company/store','ContactController@store')->name('contacts.company.store');
    Route::get('contacts/stay/add/{id}','TransactionController@add');
    Route::get('contacts/stay/edit/{id}','TransactionController@edit');
    Route::post('contacts/stay/store','TransactionController@store')->name('stay.store');
    Route::post('contacts/stay/update','TransactionController@update')->name('stay.update');
    Route::get('contacts/stay/delete/{id}','TransactionController@delete')->name('stay.delete');

    Route::post('updateschedule','CampaignController@updateschedule')->name('updateschedule');
    Route::post('contacts/newcampaign','CampaignController@newCampaign');
    Route::post('email/saveclone','Emailtemplate@cloneTemplate');
    Route::post('email/sendtest','Emailtemplate@sendTest');
    Route::post('campaign/savesegment','CampaignController@saveSegment')->name('savesegment');
    Route::post('segments/updatesegment','SegmentController@update');
//segmen

    Route::post('segments/filtersegment','SegmentController@filterSegment')->name('filtersegment');
    Route::resource('segments','SegmentController');
    Route::get('preferences',function (){

        return view('preferences.index');
    });
    Route::post('savepreferences','PreferencesController@savePreferences');
    Route::post('getcountry','ContactController@getcountry')->name('getcountry');

});


Route::get('list',function (){
   return view('contacts.list3');
})->name('list');
Route::post('contactslist','ContactController@contactslist')->name('contactslist');

Route::get('tt',function (){
  $mg=new \App\Http\Controllers\MailgunController();
  $logs=$mg->getLogs();
  foreach ($logs as $log){
      dd($log->getMessage()['headers']['subject']);
  }
});
Route::get('test',function (){
    $res=\App\EmailResponse::where('event','=','unsubscribed')->select('recepient')->get();
    $r=[];
    foreach ($res as $ss){
        array_push($r,$ss->recepient);
    }
    $campaign=\App\Campaign::find(155);
    dd($campaign->contact);
});
Route::get('contacts/list',function (){
   return view('contacts.list2');
});
Route::post('loadcontacts','ContactController@loadcontacts')->name('loadcontacts');
Route::get('test',function (){

    //dd(env('FTP_USERNAME'));
    $file=file_get_contents('images/user.jpg');
//    $ftp=new \FtpClient\FtpClient();
//    $ftp->connect(env('FTP_HOST'));
//
//    $ftp->login(env('FTP_USERNAME'),env('FTP_PASSWORD'));
//    $ftp->pasv(true);
//    $size=$ftp->size('/upload.jpg');
//    if ($size !=-1){
//        $ftp->remove('upload.jpg');
//        $ftp->putFromString('/upload.jpg',$file);
//    }else{
//        $ftp->putFromString('/upload.jpg',$file);
//    }
//
//
//   $ftp->close();
//    //$ftp->put('images/upload/user.jpg','images/user.jpg',FTP_BINARY);
//    $ftp->putFromString('images/upload/user.jpg',$file);
   \Illuminate\Support\Facades\Storage::disk('ftp')->put('user.jpg',$file);


});
Route::get('editor',function(){
   return view('editor.index');
});

Route::get('upload',function (){
   $file=file_get_contents('images/user.png');
   \Illuminate\Support\Facades\Storage::disk('ftp')->put('user.jpg',$file);

});

Route::get('test2',function (){
   $str='TEST ';
   $mg = new \App\Http\Controllers\MailgunController();
   return $mg->convertstring($str);
});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('ttt',function (){
    $extcontact=\App\ExternalContact::find(1);
    dd($extcontact->category);
});