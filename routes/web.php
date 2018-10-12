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

Route::get('contacts/list',function(){
setlocale(LC_MONETARY,"id_ID");
    $b=[];
    $a=[];
    $c=[];

    $contacts=\App\Contact::get();
    foreach ($contacts as $contact){
        array_push($b,$contact);       
        foreach ($contact->transaction as $trx){

            array_push($c,$trx);

        }
        array_merge($b,$a);
        array_merge($b,$c);

    }

	return view('contacts.list',['data'=>$b]);
});
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
Route::get('/','ContactController@dashboard');
Route::get('contacts/birthday',function (){
   return view('contacts.birthday');
});
Route::get('contacts/filter','ContactController@filter');
Route::post('contacts/filter','ContactController@filterPost');
Route::post('contacts/birthday/search','ContactController@search');
Route::get('contacts/detail/{id}','ContactController@show');
Route::post('contacts/update','ContactController@update')->name('contacts.update');
Route::get('contacts/add','ContactController@create');
Route::post('contacts/store','ContactController@store')->name('contacts.store');
Route::get('contacts/delete/{id}','ContactController@destroy')->name('contacts.destroy');



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
Route::post('email/config/poststay/activate','Emailtemplate@poststayActivate')
;
Route::get('email/config/confirm','Emailtemplate@confirmConfig');
Route::post('email/config/confirm/update','Emailtemplate@confirmUpdate')->name('confirm.update');
Route::post('email/config/confirm','Emailtemplate@confirmTemplate')->name('email.confirmTemplate');
Route::post('email/config/confirm/activate','Emailtemplate@confirmActivate');

Route::resource('email','Emailtemplate');


Route::post('campaign/template','EmailTemplateController@getTemplate')->name('campaign.template');
Route::post('campaign/recepient','CampaignController@getRecepient')->name('campaign.recepient');
Route::post('campaign/{id}/recepient','CampaignController@getRecepient');
Route::post('campaign/gettype','CampaignController@getType');
Route::post('campaign/{id}/gettype','CampaignController@getType');
Route::resource('campaign','CampaignController');


Route::get('mailsend/','EmailTemplateController@birthdaymail');



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

Route::post('setschedule','CampaignController@setSchedule');


//segmen
Route::get('segment','SegmentController@index')->name('segment');
Route::get('tt',function (){
$campaign=\App\Campaign::first();
dd(unserialize($campaign->country_id)[0]);
});



