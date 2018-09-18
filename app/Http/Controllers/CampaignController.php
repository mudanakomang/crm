<?php

namespace App\Http\Controllers;

use App\Campaign;
use App\Contact;
use App\Country;
use App\MailEditor;
use App\Schedule;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use DB;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request){
        dd($request->all());
    }
    public function index()
    {
        //
        $campaign=Campaign::all();

        return view('campaigns.index',['campaigns'=>$campaign]);
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
        return view('campaigns.manage',['action'=>$action,'model'=>$model,'option'=>[]]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        //
        $contacts=Contact::with('transaction')->when($request->country_id !=null,function ($q) use ($request){
            return $q->whereIn('country_id',$request->country_id);
        })->when($request->guest_status !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereIn('status',$request->guest_status);
            });
        })->when($request->spending_from ==null and $request->spending_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereBetween('revenue',[0,$request->spending_to]);
            });
        })->when($request->spending_from !=null and $request->spending_to ==null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->where('revenue','>=',$request->spending_from);
            });
        })->when($request->spending_from !=null and $request->spending_to !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereBetween('revenue',[$request->spending_from,$request->spending_to]);
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

        })->get();


       // $campaign=Campaign::find(3);

      //  $c=Contact::find(804);

        //$campaign->contact()->attach($c);
       // $campaign->contact()->updateExistingPivot($c,['status'=>'sent']);

        $campaign=new Campaign();
        $campaign->name=$request->name;
        $campaign->status='Draft';
        $campaign->type=$request->type;
        $campaign->country_id=serialize($request->country_id);
        $campaign->guest_status=serialize($request->guest_status);
        $campaign->spending_from=$request->spending_from;
        $campaign->spending_to=$request->spending_to;
        $campaign->stay_from=Carbon::parse($request->stay_from)->format('Y-m-d');
        $campaign->stay_to=Carbon::parse($request->stay_to)->format('Y-m-d');
        $campaign->total_stay_from=$request->total_stay_from;
        $campaign->total_stay_to=$request->total_stay_to;
        $campaign->total_night_from=$request->total_night_from;
        $campaign->total_night_to=$request->total_night_to;
        $campaign->gender=$request->gender[0];
        $campaign->template_id=$request->template;
        $campaign->save();

        foreach ($contacts as $contact) {
            $campaign->contact()->attach($contact);
            $campaign->contact()->updateExistingPivot($contact,['status'=>'queue']);
        }


        $campaign->template()->attach($request->template);
        $this->setSheduleFunc($campaign->id,$request->schedule);
        return redirect('campaign');

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
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //


        $contacts=Contact::with('transaction')->when($request->country_id !=null,function ($q) use ($request){
            return $q->whereIn('country_id',$request->country_id);
        })->when($request->guest_status !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereIn('status',$request->guest_status);
            });
        })->when($request->spending_from ==null and $request->spending_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereBetween('revenue',[0,$request->spending_to]);
            });
        })->when($request->spending_from !=null and $request->spending_to ==null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->where('revenue','>=',$request->spending_from);
            });
        })->when($request->spending_from !=null and $request->spending_to !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereBetween('revenue',[$request->spending_from,$request->spending_to]);
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

        })->get();
     // dd($request->all());
        $campaign=Campaign::find($id);
        $campaign->name=$request->name;
        $campaign->type=$request->type;
        $campaign->country_id=serialize($request->country_id);
        $campaign->guest_status=$request->guest_status;
        $campaign->spending_from=$request->spending_from;
        $campaign->spending_to=$request->spending_to;
        $campaign->stay_from=Carbon::parse($request->stay_from)->format('Y-m-d');
        $campaign->stay_to=Carbon::parse($request->stay_to)->format('Y-m-d');
        $campaign->total_stay_from=$request->total_stay_from;
        $campaign->total_stay_to=$request->total_stay_to;
        $campaign->total_night_from=$request->total_night_from;
        $campaign->total_night_to=$request->total_night_to;
        $campaign->gender=$request->gender;
        if ($request->template<>'') {
            $campaign->template_id = $request->template;
        }
        $campaign->save();
        $campaign->template()->sync($request->template);
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
       $campaign->delete();

       return redirect()->back();

    }

    public function getRecepient(Request $request){


        $contacts=Contact::with('transaction')->when($request->country_id !=null,function ($q) use ($request){
            return $q->whereIn('country_id',$request->country_id);
        })->when($request->guest_status !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereIn('status',$request->guest_status);
            });
        })->when($request->spending_from ==null and $request->spending_to !=null ,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
               return $q->whereBetween('revenue',[0,$request->spending_to]);
            });
        })->when($request->spending_from !=null and $request->spending_to ==null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->where('revenue','>=',$request->spending_from);
            });
        })->when($request->spending_from !=null and $request->spending_to !=null,function ($q) use ($request){
            return $q->whereHas('transaction',function ($q) use ($request){
                return $q->whereBetween('revenue',[$request->spending_from,$request->spending_to]);
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

        })->get();
        return response($contacts,200);
    }

    public function getType(Request $request){

        $template=MailEditor::where('type',$request->type)->pluck('name','id')->all();
        return response()->json($template);
    }
    public function activateCampaign(Request $request){

       $campaign=Campaign::find($request->id);
       if ($request->status=='on'){
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
    public function setSchedule(Request $request){
        $this->setSheduleFunc($request->id,$request->value);
    }
    public function getSegment(Request $request){
        $campaign=Campaign::find($request->id);
        $country=unserialize($campaign->country_id);
        $guestsatus=unserialize($campaign->guest_status);

        return response([$campaign,$country,$guestsatus],200);
    }
}
