<?php

namespace App\Http\Controllers;

use App\Segment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SegmentController extends Controller
{
    public function index (){
        return view('segments.index');
    }
    public function create()
    {
        //
        $action='create';
        $model=new Segment();
        return view('segments.manage',['action'=>$action,'model'=>$model,'option'=>[]]);
    }
    public function edit($id)
    {
        //
        $action='edit';
        $model=Segment::find($id);
        return view('segments.manage',['action'=>$action,'model'=>$model]);
    }
    public function destroy($id){
        $segment=Segment::find($id);
        $segment->delete();
        return redirect()->back();
    }

    public function filterSegment(Request $request){
        dd($request->all());
    }
    public function store(Request $request){
     //   dd($request->all());
        $rules=['segmentname'=>'required'];
        $message=['segmentname.required'=>'Segment Name Required'];
        $validator=Validator::make($request->all(),$rules,$message);
        if(!$validator->fails()) {
            $guest_status = serialize($request->guest_status);
            $country_id = serialize($request->country_id);
            $gender = serialize($request->gender);
            $booking_source = serialize($request->booking_source);
            $segment = new Segment();
            $segment->name = $request->segmentname;
            $segment->guest_status = $guest_status;
            $segment->country_id = $country_id;
            $segment->gender = $gender;
            $segment->booking_source = $booking_source;
            if ($request->stay_from != null) {
                $segment->stay_from = Carbon::parse($request->stay_from)->format('Y-m-d');
            } else {
                $segment->stay_from = null;
            }
            if ($request->stay_to != null) {
                $segment->stay_to = Carbon::parse($request->stay_to)->format('Y-m-d');
            } else {
                $segment->stay_to = null;
            }
            if ($request->spending_from != null) {
                $segment->spending_from = str_replace('.', '', $request->spending_from);
            } else {
                $segment->spending_from = null;
            }
            if ($request->spending_to != null) {
                $segment->spending_to = str_replace('.', '', $request->spending_to);
            } else {
                $segment->spending_to = null;
            }

            $segment->total_stay_from = $request->total_stay_from;
            $segment->total_stay_to = $request->total_stay_to;
            $segment->total_night_from = $request->total_night_from;
            $segment->total_night_to = $request->total_night_to;
            $segment->age_from = $request->age_from;
            $segment->age_to = $request->age_to;
            $segment->save();

            return response('success',200);
        }else{
            return response(['errors'=>$validator->errors()],200);
        }
    }
}
