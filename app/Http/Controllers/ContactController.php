<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\CheckContact;
use App\Contact;
use App\Country;

use App\EmailReview;
use App\ExcludedEmail;
use App\ProfileFolio;
use App\RoomType;
use App\Transaction;
use Carbon\Carbon;

use Illuminate\Http\Request;
use DB;
use League\Csv\Reader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function ages($type){

        $tr=Contact::with('transaction')->when($type=='low',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 < 30 ');
        })->when($type=='mid',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 between 30 and 60 ');
        })->when($type=='high',function ($q){
            return $q->whereRaw('DATEDIFF(DATE_FORMAT(now(),\'%y-%m-%d\'),birthday) /365 > 60 ');
        })->get();
        return view('contacts.list',['data'=>$tr]);

    }
    public function source($type){
        $contacts=Contact::whereHas('profilesfolio',function ($q) use ($type){
            return $q->where('source','=',$type);
        })->get();
        return view('contacts.list',['data'=>$contacts]);
    }
    public function reviews(){
        $ta=json_decode(file_get_contents('tripadvisor.json'));
        //dd($ta);
       // $ta_reviews=collect($ta->reviews);
	$ta_reviews=\App\Reviews::where('source','=','tripadvisor')->orderBy('created_at','desc')->take(15)->get();
	//dd($ta_reviews);
        $currentPageTa = LengthAwarePaginator::resolveCurrentPage('ta');
        $currentPageB=LengthAwarePaginator::resolveCurrentPage('bo');
        $currentPageH=LengthAwarePaginator::resolveCurrentPage('ht');
        $perPage = 15;
        $currentResultsTa = $ta_reviews->slice(($currentPageTa - 1) * $perPage, $perPage)->take(10);

        $filebooking=file_get_contents('booking.json');
        $databooking=json_decode($filebooking,true);
        $booking_review=collect($databooking["reviews"]["reviewlist"]);

        $resultsta = new LengthAwarePaginator($currentResultsTa, $ta_reviews->count(), $perPage);
        $currentResultsB = $booking_review->slice(($currentPageB - 1) * $perPage, $perPage)->take(10);
        $resultsbooking=new LengthAwarePaginator($currentResultsB,$booking_review->count(),$perPage);

        $ht=json_decode(file_get_contents('hotels.json'));
        $ht_reviews=collect($ht->reviews);
        $currentResultsH=$ht_reviews->slice(($currentPageH-1)* $perPage,$perPage)->take(10);
        $resutlsHotel=new LengthAwarePaginator($currentResultsH,$ht_reviews->count(),$perPage);
        $poststay=DB::select(DB::raw('select sum(cleanliness)/count(*) as cleanliness,sum(comfort)/count(*) as comfort ,sum(location)/count(*) as location,sum(facilities)/count(*) as facilities,sum(staff)/count(*) as staff,sum(vfm)/count(*) as vfm,sum(wifi)/count(*) as wifi, (cleanliness+comfort+location+facilities+staff+vfm+wifi)*2/7 as total from email_reviews '));
        $poststaydata=EmailReview::all();
        return view('review.index',['tripadvisor'=>$ta,'ta_reviews'=>$ta_reviews,'booking'=>$databooking,'booking_reviews'=>$resultsbooking,'hotels'=>$ht,'hotelreview'=>$resutlsHotel,'poststay'=>$poststay,'poststaydata'=>$poststaydata]);
    }
    public function dashboard(){

        $total=0;
        $contact=Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[\Carbon\Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[\Carbon\Carbon::now()->addDays(7)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->limit(10)->get();
        $contacts=DB::select(DB::raw('select country as label, count(*) as value from contacts left join countries on contacts.country_id=countries.iso3 left join contact_transaction on contact_transaction.contact_id=contacts.contactid left join transactions on transactions.id=contact_transaction.transaction_id where transactions.checkin between DATE_FORMAT(DATE_SUB(now(),INTERVAL 90 day),\'%Y-%m-%d\') and DATE_FORMAT(Now(),\'%Y-%m-%d\')  group by label order by value asc'));
        $country=json_encode($contacts);

        foreach ($contacts as $value){
            $total=$total+$value->value;
        }
        $added=DB::select(DB::raw('select DATE_FORMAT(created_at,\'%Y %M\') as created,count(*) as count from contacts where created_at between DATE_SUB(now(),INTERVAL 90 day) and now() group by DATE_FORMAT(created_at,\'%Y %m\')'));
        $data=[];
        foreach ($added as $item) {
            $tmp=['x'=>$item->created,'y'=>$item->count];
            array_push($data,$tmp);
        }

        $data=json_encode($data);

        $status=DB::select(DB::raw('select foliostatus as status,count(*) as count from profilesfolio p inner join contacts c on c.contactid=p.profileid group by foliostatus'));
        $datastatus=[];
        foreach ($status as $item){
            if($item->status=='I'){
                $st='Inhouse';
            }elseif ($item->status=='O'){
                $st='Checked Out';
            } elseif ($item->status=='C'){
                $st='Confirm';
            } elseif($item->status='G'){
		$st='Guaranteed';
	    }else{
                $st='Cancel';
            }
            $tmp=['x'=>$st,'y'=>$item->count];
            array_push($datastatus,$tmp);
        }
        $datastatus=json_encode($datastatus);
       // dd($datastatus);
       // $spending=Transaction::orderBy('revenue','desc')->take(10)->get();
        $spending=DB::select(DB::raw('select fname,lname,revenue from transactions a left join contact_transaction b on b.transaction_id=a.id left join contacts c on b.contact_id=c.contactid where contact_id is not NULL and a.checkin BETWEEN  DATE_FORMAT(DATE_SUB(now(),INTERVAL 90 day),\'%Y-%m-%d\') and DATE_FORMAT(Now(),\'%Y-%m-%d\') order by revenue desc limit 10'));
        $dataspending=[];
      foreach ($spending as $sp){
          $temspend=['x'=>$sp->fname.' '.$sp->lname,'y'=>$sp->revenue];
          array_push($dataspending,$temspend);
       }
       $dataspending=json_encode($dataspending);

        $stays=DB::select(DB::raw('select contactid,count(contactid) stays ,fname,lname, sum(revenue) revenue from contacts ,contact_transaction,transactions
where contactid is not NULL and transactions.checkin BETWEEN  DATE_FORMAT(DATE_SUB(now(),INTERVAL 90 day),\'%Y-%m-%d\') and DATE_FORMAT(Now(),\'%Y-%m-%d\') and contacts.contactid=contact_transaction.contact_id and contact_transaction.transaction_id=transactions.id and status=\'O\' group by contactid order  by stays desc,revenue desc limit 10'));
        $datastays=[];
        foreach ($stays as $stay){
            $tempstay=['x'=>$stay->fname.' '.$stay->lname,'y'=>$stay->stays];
            array_push($datastays,$tempstay);
        }

        $datatrx=[];
        $date=\Carbon\Carbon::now();


        $trx=DB::select(DB::raw('select fname,lname,datediff(checkout,checkin) as hari ,sum(revenue) as rev from transactions a left join contact_transaction b on b.transaction_id=a.id left join contacts c on c.contactid=b.contact_id where contact_id is not null and a.checkin BETWEEN DATE_FORMAT(DATE_SUB(now(),INTERVAL 90 day),\'%Y-%m-%d\') and DATE_FORMAT(Now(),\'%Y-%m-%d\') group by fname order by hari desc, rev desc limit 10 '));

        foreach ($trx as $tr){
            $tmp=['x'=>$tr->fname .' '.$tr->lname,'y'=>$tr->hari ];
            array_push($datatrx,$tmp);
        }
        $datatrx=json_encode($datatrx);



        $contacts_age=DB::select(DB::raw('select  sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) <30,1,0)) as low, sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) >=30  and floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365)<=60,1,0)) as mid,sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) >=60,1,0)) as high from contacts where created_at BETWEEN (DATE_SUB(now(),INTERVAL 90 day)) and (Now())'));
        $data_age=[];
        array_push($data_age,['label'=>'Under 30','value'=>$contacts_age[0]->low,'type'=>'low']);
        array_push($data_age,['label'=>'Between 30 and 60','value'=>$contacts_age[0]->mid,'type'=>'mid']);
        array_push($data_age,['label'=>'Higher than 60','value'=>$contacts_age[0]->high,'type'=>'high']);
        $data_age=json_encode($data_age);
        $tages=$contacts_age[0]->low+$contacts_age[0]->mid+$contacts_age[0]->high;

        $room_type=DB::select(DB::raw('select room_name as label, count(*)  as value from transactions,room_type where room_type is not null and room_type.room_code=transactions.room_type and checkin between DATE_FORMAT(DATE_SUB(now(),INTERVAL 90 day),\'%Y-%m-%d\') and DATE_FORMAT(Now(),\'%Y-%m-%d\') group by room_type order by value ASC'));
        $data_room_type=[];
        $troom=0;
        foreach ($room_type as $item) {
            $tmp=['label'=>$item->label,'value'=>$item->value];
            array_push($data_room_type,$tmp);
            $troom+=$item->value;
        }
        $data_room_type=json_encode($data_room_type);
        $reviews=json_decode(file_get_contents('tripadvisor.json'));
        $filebooking=file_get_contents('booking.json');
        $databooking=json_decode($filebooking,true);
        //dd($databooking["reviews"]["total"]);
        $bookingsource=[];
	 $tbookingsource=0;
        $booking=DB::select(DB::raw('select count(*) as count ,source from contacts left join contact_transaction on contact_transaction.contact_id=contacts.contactid LEFT JOIN transactions on contact_transaction.transaction_id=transactions.id LEFT JOIN profilesfolio on contactid=profileid  where contacts.created_at between DATE_SUB(now(),INTERVAL 90 day) and Now() group by source order by count ASC'));
        foreach ($booking as $b){
        	if($b->source!=null){ 
		   array_push($bookingsource,['label'=>$b->source,'value'=>$b->count]);
		}
		$tbookingsource+=$b->count;
        }
        $datastays=json_encode($datastays);

        $databookingsource=json_encode($bookingsource);
        $temailcount=0;
        $dataemail=[];
      //  $emails=DB::select(DB::raw('select count(*) as count, event from mailgun_logs where tags not in (\'Testing\') and DATE_FORMAT(now(),\'%Y-%m\')=DATE_FORMAT(timestamp,\'%Y-%m\') group by event'));
        $emails=DB::select(DB::raw('select event,count(*) as count from (select event from mailgun_logs  where timestamp in (select max(timestamp) from mailgun_logs group by message_id,recipient)  and event<>\'Testing\' and DATE_FORMAT(now(),\'%Y-%m\')=DATE_FORMAT(timestamp,\'%Y-%m\') group by recipient,message_id ) a group by event'));
        foreach ($emails as $email){
            array_push($dataemail,['label'=>ucfirst($email->event),'value'=>$email->count]);
            $temailcount+=$email->count;
        }

        $dataemailreport=json_encode($dataemail);
        return view('main.index',['data'=>$contact,'datastay'=>$datastays,'country'=>$country,'total'=>$total,'monthcount'=>$data,'countstatus'=>$datastatus,'spending'=>$dataspending,'longstay'=>$datatrx,'data_age'=>$data_age,'tages'=>$tages,'room_type'=>$data_room_type,'troom'=>$troom,'reviews'=>$reviews,'booking_com'=>$databooking,'databookingsource'=>$databookingsource,'tbookingsource'=>$tbookingsource,'dataemailreport'=>$dataemailreport,'temailcount'=>$temailcount]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $contact=new Contact();
        $act='add';
        return view('contacts.detail',['data'=>$contact,'action'=>$act]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function longest($contact){
        $contact=DB::select(DB::raw('select * from contacts where CONCAT(fname,\' \',lname)=\''.$contact.'\''));
        return $this->show($contact[0]->contactid);
    }

    public function spending($name)
    {
        $contact=DB::select(DB::raw('select * from contacts where CONCAT(fname,\' \',lname)=\''.$name.'\''));
        return $this->show($contact[0]->contactid);

    }
    public function type($ty){
        $type=RoomType::where('room_name',$ty)->first();

       $tr=Transaction::where('room_type',$type->room_code)->get();
       $b=[];
       $a=[];
       $c=[];
       foreach ($tr as $item){
          foreach ($item->contact as $contact){
              array_push($b,$contact);
              foreach ($contact->transaction as $trx){

                  array_push($c,$trx);

              }
              array_merge($b,$a);
              array_merge($b,$c);
          }
       }

        return view('contacts.list',['data'=>$b]);
    }
public function loadcontacts(Request $request){
    $contacts=Contact::with(['transaction'=>function($qq){
	      	return $qq->sum('revenue');
	    }])->whereHas('transaction',function ($q){
       	 return $q->whereNotNull('status')->where('revenue','>=',0);
	    })->withCount('campaign')->withCount('transaction')->get();
		return response($contacts);

}
public function contactslist(Request $request){
    $contacts=Contact::with(['transaction'=>function($qq){
        return $qq->sum('revenue');
    }])->whereHas('transaction',function ($q){
        return $q->where('status','<>',NULL)->where('revenue','>=',0);
    })->withCount('campaign')->withCount('transaction')
        ->get();

    $columns = array(
        0 =>'contactid',
        1 =>'fname',
        2 =>'lname',
        3 =>'birthday',
        4 =>'wedding_bday',
        5 =>'country_id',
        6 =>'area',
        8 =>'campaign_count',
        9 =>'transaction_count',
        11=>'revenue'
    );
    $totalData = $contacts->count();
    $totalFiltered = $totalData;
    $limit = $request->input('length');
    $start = $request->input('start');
    $order = $columns[$request->input('order.0.column')];
    $dir = $request->input('order.0.dir');
    if (empty($request->input('search.value'))){
        $contactslist=Contact::with(['transaction'=>function($qq){
            return $qq->sum('revenue');
        }])->whereHas('transaction',function ($q){
            return $q->where('status','<>',NULL)->where('revenue','>=',0);
        })->withCount('campaign')->withCount('transaction')
            ->offset($start)->limit($limit)->orderBy($order,$dir)
            ->get();
    }else{
        $search = $request->input('search.value');
        $contactslist=Contact::with(['transaction'=>function($qq){
            return $qq->sum('revenue');
        }])->whereHas('transaction',function ($q){
            return $q->where('status','<>',NULL)->where('revenue','>=',0);
        })->withCount('campaign')->withCount('transaction')
            ->select(DB::raw("sum(revenue) as revenue"))
            ->where('contactid','LIKE',"%{$search}%")
            ->orWhere('fname','LIKE',"%{$search}%")
            ->orWhere('lname','LIKE',"%{$search}%")
            ->orWhere('birthday','LIKE',"%{$search}%")
            ->orWhere('wedding_bday','LIKE',"%{$search}%")
            ->orWhereHas('country',function ($q) use ($search){
                return $q->where('country','LIKE',"%{$search}%");
            })->orWhereHas('transaction',function ($q) use ($search){
                return $q->where('status','LIKE',"%{$search}%");
            })
            ->orWhere('area','LIKE',"%{$search}%")
            ->orWhereHas('transaction',function ($q) use ($search){
                return $q->where('checkin','LIKE',"%{$search}%");
            })
            ->offset($start)
            ->limit($limit)
            ->orderBy($order,$dir)
            ->get();

        $totalFiltered=Contact::with(['transaction'=>function($qq){
            return $qq->sum('revenue');
        }])->whereHas('transaction',function ($q){
            return $q->where('status','<>',NULL)->where('revenue','>=',0);
        })->withCount('campaign')->withCount('transaction')
            ->select(DB::raw("sum(revenue) as revenue"))
            ->where('contactid','LIKE',"%{$search}%")
            ->orWhere('fname','LIKE',"%{$search}%")
            ->orWhere('lname','LIKE',"%{$search}%")
            ->orWhere('birthday','LIKE',"%{$search}%")
            ->orWhere('wedding_bday','LIKE',"%{$search}%")
            ->orWhereHas('country',function ($q) use ($search){
                return $q->where('country','LIKE',"%{$search}%");
            })->orWhereHas('transaction',function ($q) use ($search){
                return $q->where('status','LIKE',"%{$search}%");
             })
            ->orWhere('area','LIKE',"%{$search}%")
            ->orWhereHas('transaction',function ($q) use ($search){
                return $q->where('checkin','LIKE',"%{$search}%");
            })
            ->count();
    }
    $data=array();
    if (!empty($contactslist)){
        foreach ($contactslist as $key=>$list){
            $nestedData['contactid']=$key+1;
            $nestedData['fname']=$list->fname;
            $nestedData['lname']=$list->lname;
            $nestedData['birthday']=$list->birthday;
            $nestedData['wedding_bday']=$list->wedding_bday;
            $nestedData['country_id']=$list->country_id;
            $nestedData['area']=$list->area;
            $nestedData['status']=$list->transaction[0]->status;
            $nestedData['campaign']=count($list->campaign);
            $nestedData['stay']=count($list->transaction);
            $nestedData['checkin']=$list->transaction[0]->checkin;
            $nestedData['revenue']=$list->transaction;
            $data[]=$nestedData;
        }
    }
    $json_data=array(
        "draw"=>intval($request->input('draw')),
        "recordsTotal"=>intval($totalData),
        "recordsFiltered"=>intval($totalFiltered),
        "data"=>$data
    );
//    echo json_encode($json_data);

    return response($json_data);
}
    public function store(Request $request)
    {
        //dd($request->all());
        $contact=new Contact();
        $contact->fname=$request->fname;
        $contact->lname=$request->lname;
        $contact->ccid=2;
        $contact->salutation=$request->salutation;
        $contact->marital_status=$request->marital_status;
        $contact->gender=$request->gender;
        $contact->birthday=Carbon::parse($request->birthday)->format('Y-m-d');
        $contact->country_id=$request->country_id;
        $contact->email=$request->email;
        $contact->save();
        $attr=Attribute::where('attr_name','=','address1')->first();
        $contact->address1()->attach($attr->id,['value'=>$request->address1]);
        $attr=Attribute::where('attr_name','=','address2')->first();
        $contact->address2()->attach($attr->id,['value'=>$request->address2]);



        return redirect('contacts/detail/'.$contact->id);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //


//    $contacts=\App\Contact::with(['transaction'=>function($q){
//        return $q->orderBy('checkin','asc');
//    }])->where('contactid','=',$id)->first();
//    $contacts=\App\Contact::where('contactid','=',$id)->with(['transaction'=>function($q){
//        return $q->orderBy('created_at','desc');
//    }])->get();
   $contacts=Contact::where('contactid','=',$id)->with(['transaction'=>function($q){
       $q->orderBy('created_at','desc');
   }])->get();
  // dd($contacts);
	//$foliostatus=DB::table('profilesfolio')->where('profileid','=',$contacts->contactid)->first()->foliostatus;
	//dd($contacts->transaction);
	return view('contacts.detail',['data'=>$contacts]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    public  function getcountry(Request $request){
        $country=Country::where('iso3','=',$request->code)->first()['country'];
        return response($country);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        //
       // dd($request->all());

        //if($contact->lastname->isEmpty()){
         // $attr=Attribute::where('attr_name','=','Last Name')->first();
            //$contact->firstname()->attach($attr->id,['value'=>$request->lname]);
       // } else{
        //  $contact->lastname[0]->pivot->value=$request->lname;
       //   $contact->lastname[0]->pivot->save();

       // }

        $contact=\App\Contact::where('contactid','=',$request->id)->first();
       $contact->fname=$request->fname;
       $contact->lname=$request->lname;
       $contact->salutation=$request->salutation;
       $contact->marital_status=$request->marital_status;
       $contact->gender=$request->gender;
       if($request->birthday){
           $contact->birthday=\Carbon\Carbon::parse($request->birthday)->format('Y-m-d');
       }else{
           $contact->birthday=NULL;
       }

       if($request->wedding_bday){
           $contact->wedding_bday=\Carbon\Carbon::parse($request->wedding_bday)->format('Y-m-d');
       } else{
           $contact->wedding_bday=NULL;
       }

       $contact->country_id=$request->country_id;
       $contact->area=$request->area;
       $contact->email=$request->email;
       $contact->idnumber=$request->idnumber;
       $contact->save();


       if(!$contact->companyname->isEmpty() && $request->company_name != NULL) {
           $contact->companyname[0]->pivot->value=$request->company_name;
           $contact->companyname[0]->pivot->save();
       }elseif($request->company_name != NULL){
           $attr = Attribute::where('attr_name', '=', 'company_name')->first();
           $contact->companyname()->attach($attr->id, ['value' => $request->company_name]);
       }else{
           $attr = Attribute::where('attr_name', '=', 'company_name')->first();
           $contact->companyname()->detach($attr->id);
       }
       if (!$contact->companyphone->isEmpty() && $request->company_phone !=NULL){
           $contact->companyphone[0]->pivot->value = $request->company_phone;
           $contact->companyname[0]->pivot->save();
       }elseif($request->company_phone !=NULL) {
           $attr = Attribute::where('attr_name', '=', 'company_phone')->first();
           $contact->companyphone()->attach($attr->id, ['value' => $request->company_phone]);
       }else{
           $attr=Attribute::where('attr_name','=','company_phone')->first();
           $contact->companyphone()->detach($attr->id);
       }
        if (!$contact->companyemail->isEmpty() && $request->company_email !=NULL){
            $contact->companyemail[0]->pivot->value = $request->company_email;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_email !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_email')->first();
            $contact->companyemail()->attach($attr->id, ['value' => $request->company_email]);
        }else{
            $attr=Attribute::where('attr_name','=','company_email')->first();
            $contact->companyemail()->detach($attr->id);
        }
        if (!$contact->companyarea->isEmpty() && $request->company_area !=NULL){
            $contact->companyarea[0]->pivot->value = $request->company_area;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_area !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_area')->first();
            $contact->companyarea()->attach($attr->id, ['value' => $request->company_area]);
        }else{
            $attr=Attribute::where('attr_name','=','company_area')->first();
            $contact->companyarea()->detach($attr->id);
        }
        if (!$contact->companystatus->isEmpty() && $request->company_status !=NULL){
            $contact->companystatus[0]->pivot->value = $request->company_status;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_status !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_status')->first();
            $contact->companystatus()->attach($attr->id, ['value' => $request->company_status]);
        }else{
            $attr=Attribute::where('attr_name','=','company_status')->first();
            $contact->companystatus()->detach($attr->id);
        }
        if (!$contact->companyaddress->isEmpty() && $request->company_address !=NULL){
            $contact->companyaddress[0]->pivot->value = $request->company_address;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_address !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_address')->first();
            $contact->companyaddress()->attach($attr->id, ['value' => $request->company_address]);
        }else{
            $attr=Attribute::where('attr_name','=','company_address')->first();
            $contact->companyaddress()->detach($attr->id);
        }
        if (!$contact->companyfax->isEmpty() && $request->company_fax !=NULL){
            $contact->companyfax[0]->pivot->value = $request->company_fax;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_fax !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_fax')->first();
            $contact->companyfax()->attach($attr->id, ['value' => $request->company_fax]);
        }else{
            $attr=Attribute::where('attr_name','=','company_fax')->first();
            $contact->companyfax()->detach($attr->id);
        }
        if (!$contact->companytype->isEmpty() && $request->company_type !=NULL){
            $contact->companytype[0]->pivot->value = $request->company_type;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_type !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_type')->first();
            $contact->companytype()->attach($attr->id, ['value' => $request->company_type]);
        }else{
            $attr=Attribute::where('attr_name','=','company_type')->first();
            $contact->companytype()->detach($attr->id);
        }
        if (!$contact->companynationality->isEmpty() && $request->company_nationality !=NULL){
            $contact->companynationality[0]->pivot->value = $request->company_nationality;
            $contact->companyname[0]->pivot->save();
        }elseif($request->company_nationality !=NULL) {
            $attr = Attribute::where('attr_name', '=', 'company_nationality')->first();
            $contact->companynationality()->attach($attr->id, ['value' => $request->company_nationality]);
        }else{
            $attr=Attribute::where('attr_name','=','company_nationality')->first();
            $contact->companynationality()->detach($attr->id);
        }

        if($contact->address1->isEmpty() and $request->address1 !='') {
           $attr = Attribute::where('attr_name', '=', 'address1')->first();
            $contact->address1()->attach($attr->id, ['value' => $request->address1]);
        }elseif ($request->address1==''){
            $attr = Attribute::where('attr_name', '=', 'address1')->first();
            $contact->address1()->detach($attr->id);
        } else {
            $contact->address1[0]->pivot->value=$request->address1;
            $contact->address1[0]->pivot->save();
        }

        if($contact->address2->isEmpty() and $request->address2 !=''){
            $attr = Attribute::where('attr_name', '=', 'address2')->first();
            $contact->address2()->attach($attr->id, ['value' => $request->address2]);
        }elseif($request->address2==''){
            $attr = Attribute::where('attr_name', '=', 'address2')->first();
            $contact->address2()->detach($attr->id);
        } else {
            $contact->address2[0]->pivot->value=$request->address2;
            $contact->address2[0]->pivot->save();
        }
        if($contact->mobile->isEmpty() and $request->mobile !='') {
            $attr = Attribute::where('attr_name', '=', 'mobile')->first();
            $contact->mobile()->attach($attr->id, ['value' => $request->mobile]);
        }elseif($request->mobile==''){
            $attr = Attribute::where('attr_name', '=', 'mobile')->first();
            $contact->mobile()->detach($attr->id);
        } else {
            $contact->mobile[0]->pivot->value=$request->mobile;
            $contact->mobile[0]->pivot->save();
        }
        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $contact=\App\Contact::find($id);
        foreach ($contact->attribute as $item) {
            $attr=Attribute::find($item->id);
            $contact->attribute()->detach($attr->id);
        }
        $contact->delete();
        return redirect()->back();

    }
    public  function  search(Request $request){
        $days=$request->days;
        $contact=\App\Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[\Carbon\Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[\Carbon\Carbon::now()->addDays($days)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->get();
        return view('contacts.birthday',['contacts'=>$contact,'days'=>$days]);
    }

    public function import(){
        return view('contacts.import');
    }
    public function importStay(){
        return view('contacts.import_stay');
    }
    public function uploadContact(Request $request){

        $file=$request->file('file');
        $newName='contact.csv';
        $path='files/uploads/';
        $file->move($path,$newName);
        $created=0;
        $update=0;
        $created_data=[];
        $updated_data=[];
        $reader=Reader::createFromPath($path.'/'.$newName,'r');

        foreach ($reader as $key=>$row){

            if ($key>=2) {

                $country = Country::where('iso2', '=', $row[9])->value('id');
                $contacts = Contact::updateOrCreate([
                    'email' => $row[6],
                    'idnumber' => $row[0],
                ], [

                    'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                    'fname' => $row[2],
                    'lname' => $row[3],
                    'ccid' => $row[1],
                    'salutation' => $row[4],
                    'gender' => $row[5],
                    'country_id' => $country,
                    'marital_status'=>$row[8],

                ]);
                if ($contacts->wasRecentlyCreated) {
                    $created += 1;
                    array_push($created_data, [
                        'email' => $row[6],
                        'idnumber' => $row[0],
                        'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                        'fname' => $row[2],
                        'lname' => $row[3],
                        'ccid' => $row[1],
                        'salutation' => $row[4],
                        'gender' => $row[5],
                        'country_id' => $country,
                        'marital_status'=>$row[8],

                    ]);

                } else {
                    $update += 1;
                    array_push($updated_data, [
                        'email' => $row[6],
                        'idnumber' => $row[0],
                        'birthday' => Carbon::createFromFormat('Y-m-d',$row[7])->format('Y-m-d'),
                        'fname' => $row[2],
                        'lname' => $row[3],
                        'ccid' => $row[1],
                        'salutation' => $row[4],
                        'gender' => $row[5],
                        'country_id' => $country,
                        'marital_status'=>$row[8],

                    ]);
                }
            }
         }

        return view('contacts.import',['create'=>$created,'update'=>$update,'created_data'=>$created_data,'updated_data'=>$updated_data]);
    }
    public function country($c){
        $cid=Country::where('country','=',$c)->value('iso2');
        setlocale(LC_MONETARY,"id_ID");
        $b=[];
        $a=[];
        $c=[];

        $contacts=\App\Contact::where('country_id','=',$cid)->get();

        foreach ($contacts as $contact){
            array_push($b,$contact);
            foreach ($contact->transaction as $trx){

                array_push($c,$trx);

            }
            array_merge($b,$a);
            array_merge($b,$c);

        }
        return view('contacts.list',['data'=>$b]);
    }
    public function dateadded($date){
        $dt=Carbon::parse($date);
        setlocale(LC_MONETARY,"id_ID");
        $b=[];
        $a=[];
        $c=[];

       $contacts=Contact::whereMonth('created_at','=',$dt->month)
           ->whereYear('created_at','=',$dt->year)
           ->get();

        foreach ($contacts as $contact){
            array_push($b,$contact);
            foreach ($contact->transaction as $trx){

                array_push($c,$trx);

            }
            array_merge($b,$a);
            array_merge($b,$c);

        }
        return view('contacts.list',['data'=>$b]);
    }
    public function dstatus($stat)
    {

        setlocale(LC_MONETARY, "id_ID");
        if($stat=='Inhouse') {
            $contacts = Contact::whereHas('transaction', function ($q) {
                //return $q->where('status','=', 'I');
		return $q->where('status','=','I')->groupBy('id')->havingRaw('sum(revenue)>=0');
            })->get();
        }
        elseif ($stat=='Confirm'){
            $contacts = Contact::whereHas('profilesfolio', function ($q) {
               return $q->where('foliostatus', '=', 'C');
           })->get();
        }
       // dd($contacts[2]->profilesfolio);
            return view('contacts.list', ['data' => $contacts]);
        }

    public function male(){

        setlocale(LC_MONETARY, "id_ID");
        $b = [];
        $a = [];
        $c = [];

        $contacts=Contact::where('gender','=','M')->get();

        foreach ($contacts as $contact) {

            array_push($b, $contact);
            foreach ($contact->transaction as $trx) {
                array_push($c,$trx);

            }
            array_merge($b, $a);
            array_merge($b, $c);

        }
        return view('contacts.list', ['data' => $b]);
    }
    public function female(){

        setlocale(LC_MONETARY, "id_ID");
        $b = [];
        $a = [];
        $c = [];

        $contacts=Contact::where('gender','=','F')->get();

        foreach ($contacts as $contact) {

            array_push($b, $contact);
            foreach ($contact->transaction as $trx) {
                array_push($c,$trx);

            }
            array_merge($b, $a);
            array_merge($b, $c);

        }
        return view('contacts.list', ['data' => $b]);
    }
    public function uploadStay(Request $request){

       // dd($request->all());
        $file=$request->file('file');
        $newName='stay.csv';
        $path='files/uploads';
        $file->move($path,$newName);
        $created=0;
        $update=0;
        $created_data=[];
        $updated_data=[];

        $reader=Reader::createFromPath($path.'/'.$newName,'r');
        foreach ($reader as $key=>$row){
            if ($key>=2) {

                //dd(Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'));

                $stays=Transaction::updateOrCreate([
                    'resv_id'=>$row[1]
                ],[
                    'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                    'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                    'room'=>$row[4],
                    'room_type'=>$row[5],
                    'revenue'=>(float)$row[6],
                    'status'=>$row[7]
                ]);
                $profiles=ProfileFolio::updateOrCreate([
                    'profileid'=>$row[0]
                ],[
                   'folio_master'=>$row[1],
                    'folio'=>$row[1],
                    'foliostatus'=>$row[7],

                ]);

                if ($stays->wasRecentlyCreated){
                    $stays->contact()->attach($row[0]);
                    $created += 1;
                    array_push($created_data,[
                        'resv_id'=>$row[1],
                        'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                        'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                        'room'=>$row[4],
                        'room_type'=>$row[5],
                        'revenue'=>(float)$row[6],
                        'status'=>$row[7]
                    ]);
                } else{
                    $stays->contact()->sync($row[0]);
                    $update+=1;
                    array_push($updated_data,[
                        'resv_id'=>$row[1],
                        'checkin'=>Carbon::createFromFormat('Y-m-d',$row[2])->format('Y-m-d'),
                        'checkout'=>Carbon::createFromFormat('Y-m-d',$row[3])->format('Y-m-d'),
                        'room'=>$row[4],
                        'room_type'=>$row[5],
                        'revenue'=>(float)$row[6],
                        'status'=>$row[7]
                    ]);
                }

            }
        }

        return view('contacts.import_stay',['create'=>$created,'update'=>$update,'created_data'=>$created_data,'updated_data'=>$updated_data]);
    }
    public function filter(){
        $retval=Contact::with('transaction','profilesfolio')->get();

        return view('contacts.filter',['data'=>$retval]);
    }

    public function filterPost(Request $request){

        $contacts=Contact::with('transaction','profilesfolio')->when($request->country_id !=null,function ($q) use ($request){
            return $q->whereIn('country_id',$request->country_id);
        })->when($request->area !=null,function($q) use ($request){
            return $q->whereIn('area',$request->area);
        })->when($request->guest_status !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereIn('status',$request->guest_status);
            })->orderBy('created_at','desc');
        })->when($request->spending_from ==null and $request->spending_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
             return $q->havingRaw('SUM(revenue) between ? and ?',[0,str_replace('.','',$request->spending_to)]);
                //return $q->whereBetween($rev,[0,str_replace('.','',$request->spending_to)]);
                //return $q->whereBetween(DB::raw('sum(revenue)'),[0,str_replace('.','',$request->spending_to)]);
            });
        })->when($request->spending_from !=null and $request->spending_to ==null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                //return $q->where('revenue','>=',str_replace('.','',$request->spending_from));
                return $q->havingRaw('SUM(revenue) >= ?',[str_replace('.','',$request->spending_from)]);
            });
        })->when($request->spending_from !=null and $request->spending_to !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                //return $q->whereBetween('revenue',[str_replace('.','',$request->spending_from),str_replace('.','',$request->spending_to)]);
                return $q->havingRaw('SUM(revenue) between ? and ?',[str_replace('.','',$request->spending_from),str_replace('.','',$request->spending_to)]);
            });
        })->when($request->gender !=null,function ($q) use ($request) {
            return $q->whereIn('gender', $request->gender);
        })->when($request->stay_from == null and $request->stay_to != null,function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->where('checkout', '<=', Carbon::parse($request->stay_to)->format('Y-m-d'));
            });
        })->when($request->stay_from !=null and $request->stay_to ==null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->where('checkin','>=',Carbon::parse($request->stay_from)->format('Y-m-d'));
            });
        })->when($request->stay_from !=null and $request->stay_to !=null,function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->where('checkin', '>=', Carbon::parse($request->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($request->stay_to)->format('Y-m-d'));
            });
        })->when($request->bday_from ==null and $request->bday_to !=null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($request->bday_to)->format('m-d')]);
        })->when($request->bday_from!=null and $request->bday_to ==null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($request->bday_from)->format('m-d')]);
        })->when($request->bday_from !=null and $request->bday_to !=null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') >= ?',[Carbon::parse($request->bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') <= ?',[Carbon::parse($request->bday_to)->format('m-d')]);
        })->when($request->wedding_bday_from ==null and $request->wedding_bday_to !=null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($request->wedding_bday_to)->format('m-d')]);
        })->when($request->wedding_bday_from !=null and $request->wedding_bday_to == null , function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($request->wedding_bday_from)->format('m-d')]);
        })->when($request->wedding_bday_from != null and $request->wedding_bday_to !=null, function ($q) use ($request){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($request->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($request->wedding_bday_to)->format('m-d')]);
        })->when($request->total_night_from !=null and $request->total_night_to==null, function ($q) use ($request) {
            return $q->whereHas('transaction', function ($q) use ($request) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $request->total_night_from);
            });
        })->when($request->total_night_from == null and $request->total_night_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$request->total_night_to);
            });
        })->when($request->total_night_from !=null and $request->total_night_to !=null, function ($q) use ($request) {
            return  $q->whereHas('transaction', function ($q) use ($request) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $request->total_night_from . ' and ' . $request->total_night_to . '');
            });
        })->when($request->total_stay_from !=null and $request->total_stay_to ==null ,function ($q) use ($request){
            return $q->has('transaction','>=',$request->total_stay_from);
        })->when($request->total_stay_from == null and $request->total_stay_to !=null ,function ($q) use ($request){
            return $q->has('transaction','<=',$request->total_stay_to);
        })->when($request->total_stay_from !=null and $request->total_stay_to !=null, function ($q) use ($request){
            return $q->has('transaction','>=',$request->total_stay_from)->has('transaction','<=',$request->total_stay_to);
        })->when($request->name !=null,function ($q) use ($request){
            return $q->whereRaw('CONCAT(fname,lname) like \'%'.$request->name.'%\'');
        })->when($request->age_from!=null and $request->age_to!=null ,function ($q) use ($request){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$request->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$request->age_to.'\' year)');
        })->when($request->age_from!=null ,function($q) use ($request){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$request->age_from.'\' YEAR)');
        })->when($request->age_to!=null,function ($q) use ($request){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$request->age_to.'\' YEAR)');
        })->when($request->booking_source!=null,function ($q) use ($request){
            $q->whereHas('profilesfolio',function ($q) use ($request){
                $q->whereIn('source',$request->booking_source);
            });
        })
            ->get();


        return response($contacts,200);
    }
    public function review($id){
        $contacts=Contact::find($id);
        $contacts=$contacts->toJson();
        $review=EmailReview::where('contact_id','=',$id)->get();
        $review=$review->toJson();
        return view('review.feedback',['contacts'=>$contacts,'review'=>$review]);
    }
    public function incomplete(){
        $incomplete=CheckContact::where('checked','=','N')->orWhere('checked','=',NULL)->get();
        return view('contacts.incomplete',['incompletes'=>$incomplete]);
    }
    public function updateStatus(Request $request){
      $contact=CheckContact::where('folio','=',$request->id)->first();
      //dd($contact);
      $contact->checked='Y';
      $contact->update();
      return response('success',200);
    }
    public function excluded(){
       $excs=ExcludedEmail::all();
       return view('contacts.excluded',['data'=>$excs]);
    }
    public  function addEmail(Request $request){
        $rules=[
            'email'=>'unique:excluded_emails'
        ];
        $message=['email.unique'=>'Email/Domain exists in dataset'];
        $validator=Validator::make($request->all(),$rules,$message);
        if(!$validator->fails()){
            $exc=new ExcludedEmail();
            $exc->email=$request->email;
            $exc->save();
            return response('success');
        } else{
            return response(['errors'=>$validator->errors()]);
        }

    }
}
