<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Contact;
use App\ExternalContact;
use App\ExternalContactCategory;
use App\MailEditor;
use App\Schedule;
use App\Segment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $seg){
        dd($seg->all());
    }
    public function index()
    {
        //
        $campaign=Campaign::orderBy('created_at','desc')->get();
       // dd($campaign);
        return view('campaign.index',['campaigns'=>$campaign]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $action='create';
        $model=new Campaign();
        return view('campaign.manage',['action'=>$action,'model'=>$model,'option'=>[]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $seg
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
//        $contacts=Contact::with('transaction','profilesfolio')->when($seg->country_id !=null,function ($q) use ($seg){
//            return $q->whereIn('country_id',$seg->country_id);
//        })->when($seg->area!=null,function ($q) use ($seg){
//            return $q->whereIn('area',$seg->area);
//        })->when($seg->guest_status !=null,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->whereIn('status',$seg->guest_status);
//            });
//        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
//            });
//        })->when($seg->spending_from !=null and $seg->spending_to ==null,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->where('revenue','>=',str_replace('.','',$seg->spending_from));
//            });
//        })->when($seg->spending_from !=null and $seg->spending_to !=null,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
//            });
//        })->when($seg->gender !=null,function ($q) use ($seg) {
//            return $q->whereIn('gender', $seg->gender);
//        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
//            return $q->whereHas('transaction', function ($q) use ($seg) {
//                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
//            });
//        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
//            });
//        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
//            return $q->whereHas('transaction', function ($q) use ($seg) {
//                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
//                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
//            });
//        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
//            return $q->whereHas('transaction', function ($q) use ($seg) {
//                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
//            });
//        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
//            return $q->whereHas('transaction',function ($q) use ($seg){
//                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
//            });
//        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
//            return  $q->whereHas('transaction', function ($q) use ($seg) {
//                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
//            });
//        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
//            return $q->has('transaction','>=',$seg->total_stay_from);
//        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
//            return $q->has('transaction','<=',$seg->total_stay_to);
//        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
//            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);
//
//        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
//            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
//        })->when($seg->age_from!=null ,function($q) use ($seg){
//            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
//        })->when($seg->age_to!=null,function ($q) use ($seg){
//            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
//        })->when($seg->booking_source!=null,function ($q) use ($seg){
//            $q->whereHas('profilesfolio',function ($q) use ($seg){
//                $q->whereIn('source',$seg->booking_source);
//            });
//        })->when($seg->wedding_bday_from ==null and $seg->wedding_bday_to !=null , function ($q) use ($seg){
//            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
//        })->when($seg->wedding_bday_from !=null and $seg->wedding_bday_to == null , function ($q) use ($seg){
//            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')]);
//        })->when($seg->wedding_bday_from != null and $seg->wedding_bday_to !=null, function ($q) use ($seg){
//            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')])
//                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
//        })
//            ->get();

     //   dd($seg->all());
        if ($request->category=='' || $request->category==null){
            $type='internal';
            $segment=$request->segments;
        }else{
            $type='external';
            $segment=$request->category;
        }


       // $campaign=Campaign::find(3);

      //  $c=Contact::find(804);

        //$campaign->contact()->attach($c);
       // $campaign->contact()->updateExistingPivot($c,['status'=>'sent']);
        //dd($seg->template);
        $campaign=new Campaign();
        $campaign->name=$request->name;
        $campaign->status='Draft';
        $campaign->type=$type;
        $campaign->template_id=$request->template;
        $campaign->save();

        $seg=Segment::find($segment);
        $cat=ExternalContactCategory::find($segment);

        //dd(unserialize($seg->country_id));
        if($type=='internal') {
            $contacts = Contact::with('transaction', 'profilesfolio')->when(unserialize($seg->country_id)[0] != null, function ($q) use ($seg) {
                return $q->where('country_id',unserialize($seg->country_id)[0]);
        })->when(unserialize($seg->area)[0]!=null,function ($q) use ($seg){
            return $q->where('area',unserialize($seg->area)[0]);
        })->when(unserialize($seg->guest_status)[0] !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('status',unserialize($seg->guest_status)[0]);
            });
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from!=null and $seg->spending_to ==null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('revenue','>=',str_replace('.','',$seg->spending_from));
            });
        })->when($seg->spending_from!=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        })->when(unserialize($seg->gender)[0] !=null,function ($q) use ($seg) {
            return $q->whereIn('gender', unserialize($seg->gender)[0]);
        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from !=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to !=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        })->when(unserialize($seg->booking_source)[0]!=null,function ($q) use ($seg){
           return  $q->whereHas('profilesfolio',function ($q) use ($seg){
               return  $q->whereIn('source',unserialize($seg->booking_source));
            });
        })->when($seg->wedding_bday_from ==null and $seg->wedding_bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from !=null and $seg->wedding_bday_to == null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')]);
        })->when($seg->wedding_bday_from != null and $seg->wedding_bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->get();

            foreach ($contacts as $contact) {
                $campaign->contact()->attach($contact);
                $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
            }
            $campaign->segment()->attach($segment);
            $campaign->template()->attach($request->template);
            $this->setSheduleFunc($campaign->id,$request->schedule);
            return redirect('campaign');
        } else {
            $contacts=$cat->email;
            foreach ($contacts as $contact){
                $campaign->external()->attach($contact);
                $campaign->external()->updateExistingPivot($contact,['status'=>'queue']);
            }
            $campaign->externalSegment()->attach($segment);
            $campaign->template()->attach($request->template);
            $this->setSheduleFunc($campaign->id,$request->schedule);
            return redirect('campaign');

        }

//





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
        $action='edit';
        $model=Campaign::find($id);
        return view('campaigns.manage',['action'=>$action,'model'=>$model]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $seg
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $seg, $id)
    {
        //


        $contacts=Contact::with('transaction','profilesfolio')->when($seg->country_id !=null,function ($q) use ($seg){
            return $q->whereIn('country_id',$seg->country_id);
        })->when($seg->guest_status !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereIn('status',$seg->guest_status);
            });
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from !=null and $seg->spending_to ==null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('revenue','>=',str_replace('.','',$seg->spending_from));
            });
        })->when($seg->spending_from !=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->gender !=null,function ($q) use ($seg) {
            return $q->whereIn('gender', $seg->gender);
        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);

        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from!=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to!=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        })->when($seg->booking_source!=null,function ($q) use ($seg){
            $q->whereHas('profilesfolio',function ($q) use ($seg){
                $q->whereIn('source',$seg->booking_source);
            });
        })
            ->get();
     // dd($seg->all());
        $campaign=Campaign::find($id);
        $campaign->name=$seg->name;
        $campaign->type=$seg->type;
        $campaign->country_id=serialize($seg->country_id);
        $campaign->guest_status=$seg->guest_status;
        $campaign->spending_from=str_replace('.','',$seg->spending_from);
        $campaign->spending_to=str_replace('.','',$seg->spending_to);
        $campaign->stay_from=Carbon::parse($seg->stay_from)->format('Y-m-d');
        $campaign->stay_to=Carbon::parse($seg->stay_to)->format('Y-m-d');
        $campaign->total_stay_from=$seg->total_stay_from;
        $campaign->total_stay_to=$seg->total_stay_to;
        $campaign->total_night_from=$seg->total_night_from;
        $campaign->total_night_to=$seg->total_night_to;
        $campaign->gender=serialize($seg->gender);
        $campaign->age_from=$seg->age_from;
        $campaign->age_to=$seg->age_to;
        $campaign->booking_source=serialize($seg->booking);
        if ($seg->template<>'') {
            $campaign->template_id = $seg->template;
        }
        $campaign->save();
        $campaign->template()->sync($seg->template);
        $campaign->contact()->detach();
        foreach ($contacts as $contact) {
            $campaign->contact()->attach($contact);
            $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
        }
        return redirect('campaign');
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
       $campaign=Campaign::find($id);
       foreach ($campaign->contact as $key => $contact) {
           $campaign->contact()->detach($contact->id);
       }
       foreach ($campaign->segment as $key => $value) {
           $campaign->segment()->detach($value->id);
       }
       foreach ($campaign->external as $key => $value) {
           $campaign->external()->detach($value->id);
       }
       foreach ($campaign->externalSegment as $key => $value) {
           $campaign->externalSegment()->detach($value->id);
       }
       $campaign->delete();

       return redirect()->back();

    }

    public function getRecepient(Request $seg){
        $contacts=Contact::with('transaction','profilesfolio')->when($seg->country_id !=null,function ($q) use ($seg) {
            return $q->whereIn('country_id', $seg->country_id);
        })->when($seg->area!=null,function($q) use ($seg){
            return $q->whereIn('area',$seg->area);
        })->when($seg->guest_status !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereIn('status', $seg->guest_status);
            })->orderBy('created_at', 'desc');
        })->when($seg->spending_from ==null and $seg->spending_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[0,str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->spending_from !=null and $seg->spending_to ==null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('revenue', '>=', str_replace('.', '', $seg->spending_from));
            });
        })->when($seg->spending_from !=null and $seg->spending_to !=null,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereBetween('revenue',[str_replace('.','',$seg->spending_from),str_replace('.','',$seg->spending_to)]);
            });
        })->when($seg->gender !=null,function ($q) use ($seg) {
            return $q->whereIn('gender', $seg->gender);
        })->when($seg->stay_from == null and $seg->stay_to != null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to ==null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->where('checkin','>=',Carbon::parse($seg->stay_from)->format('Y-m-d'));
            });
        })->when($seg->stay_from !=null and $seg->stay_to !=null,function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->where('checkin', '>=', Carbon::parse($seg->stay_from)->format('Y-m-d'))
                    ->where('checkout', '<=', Carbon::parse($seg->stay_to)->format('Y-m-d'));
            });
        })->when($seg->bday_from ==null and $seg->bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($seg->bday_to)->format('m-d')]);
        })->when($seg->bday_from!=null and $seg->bday_to ==null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') = ?',[Carbon::parse($seg->bday_from)->format('m-d')]);
        })->when($seg->bday_from !=null and $seg->bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') >= ?',[Carbon::parse($seg->bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(birthday,\'%m-%d\') <= ?',[Carbon::parse($seg->bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from ==null and $seg->wedding_bday_to !=null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->wedding_bday_from !=null and $seg->wedding_bday_to == null , function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') = ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')]);
        })->when($seg->wedding_bday_from != null and $seg->wedding_bday_to !=null, function ($q) use ($seg){
            return $q->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') >= ?',[Carbon::parse($seg->wedding_bday_from)->format('m-d')])
                ->whereRaw('DATE_FORMAT(wedding_bday,\'%m-%d\') <= ?',[Carbon::parse($seg->wedding_bday_to)->format('m-d')]);
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to==null, function ($q) use ($seg) {
            return $q->whereHas('transaction', function ($q) use ($seg) {
                return $q->whereRaw('DATEDIFF(checkout,checkin) >= ' . $seg->total_night_from);
            });
        })->when($seg->total_night_from == null and $seg->total_night_to !=null ,function ($q) use ($seg){
            return $q->whereHas('transaction',function ($q) use ($seg){
                return $q->whereRaw('DATEDIFF(checkout,checkin) <='.$seg->total_night_to);
            });
        })->when($seg->total_night_from !=null and $seg->total_night_to !=null, function ($q) use ($seg) {
            return  $q->whereHas('transaction', function ($q) use ($seg) {
                return    $q->whereRaw('DATEDIFF(checkout,checkin) between ' . $seg->total_night_from . ' and ' . $seg->total_night_to . '');
            });
        })->when($seg->total_stay_from !=null and $seg->total_stay_to ==null ,function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from);
        })->when($seg->total_stay_from == null and $seg->total_stay_to !=null ,function ($q) use ($seg){
            return $q->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->total_stay_from !=null and $seg->total_stay_to !=null, function ($q) use ($seg){
            return $q->has('transaction','>=',$seg->total_stay_from)->has('transaction','<=',$seg->total_stay_to);
        })->when($seg->name !=null,function ($q) use ($seg){
            return $q->whereRaw('CONCAT(fname,lname) like \'%'.$seg->name.'%\'');
        })->when($seg->age_from!=null and $seg->age_to!=null ,function ($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(), INTERVAL \''.$seg->age_from.'\' YEAR) and birthday >= date_sub(now(),interval \''.$seg->age_to.'\' year)');
        })->when($seg->age_from!=null ,function($q) use ($seg){
            return $q->whereRaw('birthday <= date_sub(now(),INTERVAL \''.$seg->age_from.'\' YEAR)');
        })->when($seg->age_to!=null,function ($q) use ($seg){
            return $q->whereRaw('birthday >= date_sub(now(),INTERVAL \''.$seg->age_to.'\' YEAR)');
        })->when($seg->booking_source!=null,function ($q) use ($seg){
            $q->whereHas('profilesfolio',function ($q) use ($seg){
                $q->whereIn('source',$seg->booking_source);
            });
        })
            ->get();

        return response($contacts,200);
    }

    public function getType(Request $seg){

        $template=MailEditor::where('type',$seg->type)->pluck('name','id')->all();
        return response()->json($template);
    }
    public function activateCampaign(Request $seg){

       $campaign=Campaign::find($seg->id);
       if ($seg->status=='on'){
           $campaign->status='Active';
           $campaign->save();
       }else{
           $campaign->status='Inactive';
           $campaign->save();
       }

       return response($campaign,200);
    }

    public function setSheduleFunc($campaign_id,$date){
        $schedule=Schedule::updateOrCreate(
            ['campaign_id'=>$campaign_id],
            ['schedule'=>Carbon::parse($date)->format('Y-m-d H:i')]
        );
        $campaign=Campaign::find($campaign_id);
        $campaign->status='Scheduled';
        $campaign->save();
        return response(['status'=>'success','id'=>$schedule->id,'campstatus'=>$campaign->status,'schedule'=>$schedule->schedule,'campaignid'=>$campaign_id],200);
    }
    public function updateschedule(Request $seg){
    
        $this->setSheduleFunc($seg->id,$seg->value);
    }
    
    public function getSegment(Request $seg){

        $campaign=Segment::find($seg->id);
        if (!empty($campaign->country_id)){
           $country=unserialize($campaign->country_id);
        }else{
            $country='';
        }
        if (!empty($campaign->area)){
            $area=unserialize($campaign->area);
        }else{
            $area='';
        }
        if(!empty($campaign->guest_status)){
            $guestsatus=unserialize($campaign->guest_status);
        }else{
            $guestsatus='';
        }
        if(!empty($campaign->gender)){
            $gender=unserialize($campaign->gender);
        }else{
            $gender='';
        }
        if(!empty($campaign->booking_source)){
            $booking=unserialize($campaign->booking_source);
        }else{
            $booking='';
        }

        return response([$campaign,$country,$guestsatus,$gender,$booking,$area],200);
    }
    public function newCampaign(Request $seg){

        $rules=[
           'cname'=>'required',
          'schedule'=>'required',
        ];
        $messages=[
            'cname.required'=>'Campaign name Required',
           'schedule.required'=>'Schedule Required',
        ];

        $validator =Validator::make($seg->all(),$rules,$messages);
        if(!$validator->fails()){
            $campaign=new Campaign();
            $campaign->name=$seg->cname;
            $campaign->status='Draft';
            $campaign->type='Promo';
            $campaign->segment_id=$seg->segment_id;
            $campaign->template_id=$seg->template_id;
            $campaign->save();

            foreach ($seg->contacts as $cid){
                $contact=Contact::find($cid['value']);
                $campaign->contact()->attach($contact);
                $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
            }
            $campaign->template()->attach($seg->template_id);
            $this->setSheduleFunc($campaign->id,$seg->schedule);
            return response('success',200);
        } else{
            return response(['errors'=>$validator->errors()]);
        }

    }
    public function saveSegment(Request $seg){

        $rules=['name'=>'required'];
        $message=['name.required'=>'Segment Name Required'];
        $validator=Validator::make($seg->all(),$rules,$message);
        if(!$validator->fails()){
            $guest_status=serialize($seg->guest_status);
            $country_id=serialize($seg->country_id);
            $gender=serialize($seg->gender);
            $booking_source=serialize($seg->booking_source);
            $segment=new Segment();
            $segment->name=$seg->name;
            $segment->guest_status=$guest_status;
            $segment->country_id=$country_id;
            $segment->area=serialize($seg->area);
            $segment->gender=$gender;
            $segment->booking_source=$booking_source;
            if($seg->stay_from!=null){
                $segment->stay_from=Carbon::parse($seg->stay_from)->format('Y-m-d');
            } else
            {
                $segment->stay_from=null;
            }
            if($seg->stay_to!=null){
                $segment->stay_to=Carbon::parse($seg->stay_to)->format('Y-m-d');
            } else{
                $segment->stay_to=null;
            }
            if($seg->spending_from!=null){
                $segment->spending_from=str_replace('.','',$seg->spending_from);
            }else{
                $segment->spending_from=null;
            }
            if($seg->spending_to!=null){
                $segment->spending_to=str_replace('.','',$seg->spending_to);
            }else{
                $segment->spending_to=null;
            }

            $segment->total_stay_from=$seg->total_stay_from;
            $segment->total_stay_to=$seg->total_stay_to;
            $segment->total_night_from=$seg->total_night_from;
            $segment->total_night_to=$seg->total_night_to;
            $segment->age_from=$seg->age_from;
            $segment->age_to=$seg->age_to;
            if($seg->bday_from){
                $segment->bday_from=Carbon::parse($seg->bday_from)->format('Y-m-d');
            }else{
                $segment->bday_from=NULL;
            }
            if ($seg->bday_to){
                $segment->bday_to=Carbon::parse($seg->bday_to)->format('Y-m-d');
            }else{
                $segment->bday_to=NULL;
            }
            if($seg->wedding_bday_from){
                $segment->wedding_bday_from=Carbon::parse($seg->wedding_bday_from)->format('Y-m-d');
            }else{
                $segment->wedding_bday_from=NULL;
            }
            if($seg->wedding_bday_to){
                $segment->wedding_bday_to=Carbon::parse($seg->wedding_bday_to)->format('Y-m-d');
            }else{
                $segment->wedding_bday_to=NULL;
            }
            $segment->save();
            return response(['success'=>['id'=>$segment->id,'name'=>$seg->name]],200);
        }else{
            return response(['error'=>$validator->errors()],200);
        }



    }

}
