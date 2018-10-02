<?php

namespace App\Http\Controllers;

use App\Attribute;
use App\Contact;
use App\Country;
use App\ProfileFolio;
use App\RoomType;
use App\Transaction;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use DB;
use League\Csv\Reader;
use PHPUnit\Framework\Constraint\Count;
use function Sodium\add;

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
    public function dashboard(){
        $total=0;
        $contact=Contact::whereRaw('DATE_FORMAT(birthday,"%m-%d") >= ?',[\Carbon\Carbon::now()->format('m-d')])
            ->whereRaw('DATE_FORMAT(birthday,"%m-%d") <= ?',[\Carbon\Carbon::now()->addDays(7)->format('m-d')])
            ->orderBy(DB::raw('ABS( DATEDIFF( birthday, NOW() ) )'),'asc')->limit(10)->get();
        $contacts=DB::select(DB::raw('select country as label, count(*) as value from contacts left join countries on contacts.country_id=countries.iso3 group by label '));
        $country=json_encode($contacts);
        foreach ($contacts as $value){
            $total=$total+$value->value;
        }
        $added=DB::select(DB::raw('select DATE_FORMAT(created_at,\'%Y %M\') as created,count(*) as count from contacts group by created order by created_at asc'));
        $data=[];
        foreach ($added as $item) {
            $tmp=['x'=>$item->created,'y'=>$item->count];
            array_push($data,$tmp);
        }

        $data=json_encode($data);

        $status=DB::select(DB::raw('select status , count(*) as count from transactions group by status'));
        $datastatus=[];
        foreach ($status as $item){
            if($item->status=='I'){
                $st='Inhouse';
            }elseif ($item->status=='O'){
                $st='Checked Out';
            } elseif ($item->status=='C'){
                $st='Confirm';
            } else{
                $st='Cancel';
            }
            $tmp=['x'=>$st,'y'=>$item->count];
            array_push($datastatus,$tmp);
        }
        $datastatus=json_encode($datastatus);
       // $spending=Transaction::orderBy('revenue','desc')->take(10)->get();
        $spending=DB::select(DB::raw('select fname,lname,revenue from transactions a left join contact_transaction b on b.transaction_id=a.id left join contacts c on b.contact_id=c.contactid where contact_id is not NULL order by revenue desc limit 10'));
        $dataspending=[];
      foreach ($spending as $sp){
          $temspend=['x'=>$sp->fname.' '.$sp->lname,'y'=>$sp->revenue];
          array_push($dataspending,$temspend);
       }
       $dataspending=json_encode($dataspending);
        $datatrx=[];
        $trx=DB::select(DB::raw('select fname,lname,datediff(checkout,checkin) as hari from transactions a 
left join contact_transaction b on b.transaction_id=a.id
left join contacts c on c.contactid=b.contact_id
where contact_id is not null
order by hari desc limit 10 '));

        foreach ($trx as $tr){
            $tmp=['x'=>$tr->fname .' '.$tr->lname,'y'=>$tr->hari ];
            array_push($datatrx,$tmp);
        }
        $datatrx=json_encode($datatrx);

        $contacts_age=DB::select(DB::raw('select  sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) <30,1,0)) as low, sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) >=30  and floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365)<=60,1,0)) as mid,sum(if(floor(datediff(DATE_FORMAT(now(),\'%Y-%m-%d\'),birthday)/365) >=60,1,0)) as high from contacts'));
        $data_age=[];
        array_push($data_age,['label'=>'Under 30','value'=>$contacts_age[0]->low]);
        array_push($data_age,['label'=>'Between 30 and 60','value'=>$contacts_age[0]->mid]);
        array_push($data_age,['label'=>'Higher than 60','value'=>$contacts_age[0]->high]);
        $data_age=json_encode($data_age);
        $tages=$contacts_age[0]->low+$contacts_age[0]->mid+$contacts_age[0]->high;

        $room_type=DB::select(DB::raw('select room_name as label, count(*)  as value from transactions,room_type where room_type is not null and room_type.room_code=transactions.room_type group by room_type'));
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

        return view('main.index',['data'=>$contact,'country'=>$country,'total'=>$total,'monthcount'=>$data,'countstatus'=>$datastatus,'spending'=>$dataspending,'longstay'=>$datatrx,'data_age'=>$data_age,'tages'=>$tages,'room_type'=>$data_room_type,'troom'=>$troom,'reviews'=>$reviews,'booking_com'=>$databooking]);
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

    $contacts=\App\Contact::where('contactid','=',$id)->first();
	$foliostatus=DB::table('profilesfolio')->where('profileid','=',$contacts->contactid)->first()->foliostatus;
	//dd($contacts->transaction);
	return view('contacts.detail',['data'=>$contacts,'foliostatus'=>$foliostatus]);
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
       $contact->birthday=\Carbon\Carbon::parse($request->birthday)->format('Y-m-d');
       $contact->country_id=$request->country_id;
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
        $cid=Country::where('country','=',$c)->value('iso3');
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
        switch ($stat) {
            case "Inhouse":
                $status = 'I';
                break;
            case "Checked Out":
                $status = 'O';
                break;
            case "Confirm":
                $status = 'C';
                break;
            default:
                $status = 'X';
        }


        setlocale(LC_MONETARY, "id_ID");
//        $b = [];
//        $a = [];
//        $c = [];
        if($status=='I') {
            $contacts = Contact::whereHas('transaction', function ($q) use ($status) {
                return $q->where('status', '=', $status)->whereRaw('date_format(now(),\'%Y-%m-%d\') between checkin and checkout');
            })->get();
        }
        elseif ($status=='C'){
            $contacts = Contact::whereHas('transaction', function ($q) use ($status) {
                return $q->where('status', '=', $status)->whereRaw('date_format(now(),\'%Y-%m-%d\') < checkin ');
            })->get();
        }

//
//            foreach ($contacts as $contact) {
//
//                    array_push($b, $contact);
//                    foreach ($contact->transaction as $trx) {
//
//                            array_push($c, $trx);
//
//
//                    }
//
//                    array_merge($b, $a);
//                    array_merge($b, $c);
//
//            }

            return view('contacts.list', ['data' => $contacts]);
        }



      //  foreach ($contacts as $contact){
        //    if(!empty($contact)) {
          //      array_push($b, $contact);
            //    foreach ($contact->transaction as $trx) {

                  //  array_push($c, $trx);

                //}
              //  array_merge($b, $a);
            //    array_merge($b, $c);
          //  }
        //}
      //  return view('contacts.list',['data'=>$b]);
    //}
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
}
