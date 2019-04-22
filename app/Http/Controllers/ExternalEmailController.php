<?php

namespace App\Http\Controllers;

use App\ExternalContact;
use App\ExternalContactCategory;
use App\ExternalEmail;
use App\ExternalEmailCampaign;
use App\ExternalEmailCategory;
use Illuminate\Http\Request;
use Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class ExternalEmailController extends Controller
{
    //
    public function index(){
        return view('contacts.external');
    }
    public  function saveexternalcontact(Request $request){
//        dd($request->all());
        if ($request->getcategory==='on') {
            $rules = [
                'file' => 'required|mimes:xls,xlsx',
                'pick_category'=>'required',

            ];
            $message=[
                'file.required'=>'File is required',
                'file.mimes'=>'Invalid extension',
                'pick_category.required'=>'At least one category needed'
            ];
        }else
        {
            $rules = [
                'file' => 'required|mimes:xls,xlsx',
                'new_category.*'=>'required'
            ];
            $message=[
                'file.required'=>'File is required',
                'file.mimes'=>'Invalid extension',
                'new_category.*.required'=>'Category is required'

            ];
        }

      //  dd($message);
        $val=Validator::make($request->all(),$rules,$message);
        if (!$val->fails()){
            $path=$request->file('file');
            if ($path->getClientOriginalExtension()=='xls'){
                $reader=new Xls();
            }else {
                $reader = new Xlsx();
            }
            $spreadsheet=$reader->load($path)->getActiveSheet()->toArray();
            for ($j=1;$j<=count($spreadsheet)-1;$j++){
                $emails[]=$spreadsheet[$j];
            }

            if ($request->getcategory!=='on'){

                $categories=$request->new_category;
               // dd($categories);
                foreach ($categories as $category){
                    $cat=ExternalContactCategory::updateOrCreate(['category'=>$category],['category'=>$category]);
                    foreach ($emails as $list){
                        if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$list[0])) {
                            $external=ExternalContact::updateOrCreate(['email'=>$list[0]],
                                ['fname'=>$list[1],'lname'=>$list[2]]);
                            $external->category()->sync($cat->id);
                            $data[]=$external;
                        }

                    }
                }
            } else{
              // ExternalEmailCategory::updateOrCreate(['id'=>$request->pick_category],['category'=>$request->pick_category]);
               $categories=$request->pick_category;
                foreach ($emails as $list){
                    if(preg_match("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$^",$list[0])) {
                        $external=ExternalContact::updateOrCreate(['email'=>$list[0]],['fname'=>$list[1],'lname'=>$list[2]]);
                        foreach ($categories as $category){
                            $external->category()->sync($category);
                        }
                        $data[]=$external;
                    }

                }
            }

            return response(['success'=>$data],200);
        }else{
            //return back()->withErrors($val->messages())->withInput();
            return response(['errors'=>$val->messages()]);
        }


    }
    public function categorylist(Request $request){
        $category=ExternalContactCategory::with('email')->get();
        return response($category);
    }
    public function destroy(Request $request){
        $campaigns=ExternalEmailCampaign::find($request->id);
        foreach ($campaigns->email as $email){
           $email->category()->detach();
        }
        $campaigns->email()->detach();
        $campaigns->delete();

        return response('success',200);
    }

    public function delcategory(Request $request){
        $contact=ExternalContactCategory::find($request->id);
        $contact->email()->detach();
        $contact->delete();
        return response('success',200);
    }
}
