<?php

namespace App\Http\Controllers;

use App\Segment;
use Illuminate\Http\Request;

class SegmentController extends Controller
{
    public function index (){
        return view('campaigns.segment');
    }
    public function create()
    {
        //
        $action='create';
        $model=new Segment();
        return view('campaigns.manage_segment',['action'=>$action,'model'=>$model,'option'=>[]]);
    }
    public function edit($id)
    {
        //
        $action='edit';
        $model=Segment::find($id);
        return view('campaigns.manage_segment',['action'=>$action,'model'=>$model]);
    }
}
