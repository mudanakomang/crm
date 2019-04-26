<?php

namespace App\Http\Controllers;

use App\Birthday;
use App\ConfirmEmail;
use App\Contact;
use App\EmailReview;
use App\MailEditor;
use App\PostStay;
use App\MissYou;
use FtpClient\FtpClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use SebastianBergmann\Comparator\FactoryTest;

class Emailtemplate extends Controller
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $action='create';
        $template=new MailEditor();
        return view('email.manage.template',['action'=>$action,'template'=>$template]);
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
      //  dd($request->all());
        if ($request->file('file') != null && $request->active==2){
            $detail=file_get_contents($request->file('file'));
        }else{
            $detail=$this->upload($request);
        }

        $templ=new MailEditor();
        $str=str_replace(' ','_',$request->name);

        $templ->content=$detail;
        $templ->name=$str;
        $templ->type=$request->type;
        $templ->subject=$request->subject;
        $templ->save();
       // $file_handle=fopen(base_path().'/resources/views/email/templates/'.$request->get('name').'.blade.php','w+');

       // fwrite($file_handle,$detail);
       // fclose($file_handle);
        $template=MailEditor::all();

        return view('email.manage.list',['template'=>$template]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $temp=MailEditor::find($id);
        dd($temp);
        $name=$temp->name;
        return view('email.templates.'.$name);
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
        $templ=MailEditor::find($id);
//dd($templ);
        return view('email.manage.template',['action'=>$action,'template'=>$templ]);

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
      
        if ($request->file('file') != null && $request->active==2){
            $detail=file_get_contents($request->file('file'));
        }else{
            $detail=$this->upload($request);
        }

        $templ=MailEditor::find($id);
        $str=str_replace(' ','_',$request->name);
        $templ->content=$detail;
        $templ->name=$str;
        $templ->type=$request->type;
        $templ->subject=$request->subject;
        $templ->save();
       // $file_handle=fopen(base_path().'/resources/views/email/templates/'.$request->get('name').'.blade.php','w+');

      //  fwrite($file_handle,$detail);
      //  fclose($file_handle);

        return redirect('email/template');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $template=MailEditor::find($request->id);
        $campaign=$template->campaign;
        $poststay=$template->poststay;
        $birthday=$template->birthday;
        $miss=$template->miss;
//        if (!empty($poststay)){
//            return response('empty');
//        }else{
//            return response('not empty');
//        }
        if($campaign->isEmpty() && empty($poststay) && empty($birthday) && empty($miss)){
            $dom = new \DomDocument();
            libxml_use_internal_errors(true);
            $dom->loadHtml($template->content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
            $images = $dom->getElementsByTagName('img');
            foreach($images as $k => $img) {
                $name = $img->getAttribute('src');
                Storage::disk('ftp')->delete($name);
            }

            $template->delete();
            return response(['status'=>'success']);
        }else{
            return response(['status'=>'error']);
        }
    }



    public function upload(Request $request){    
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($request->contents, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');
        $s=[];
        foreach($images as $k => $img) {
            $data = $img->getAttribute('src');
            $name=$img->getAttribute('data-filename');
            if (!empty(explode(';', $data)[1])) {

                list($type, $data) = explode(';', $data);

                list(, $data) = explode(',', $data);            

                $data = base64_decode($data);

                if($request->type=='Birthday'){
                    $pth='birthday';
                }elseif ($request->type=='Poststay'){
                    $pth='poststay';
                }elseif ($request->type=='Miss You'){
                    $pth='wemissu';
                }elseif ($request->type=='Promo') {
                    $pth = 'campaign';
                }elseif ($request->type=='Prestay'){
                    $pth='prestay';
                }else{
                    $pth='other';
                }
                $base='crm.ramaresidencepadma.com/themes/ramaresidencepadma/assets/mail-template';                
                $image_name = $base.'/'.$pth.'/'.$name;
                if (Storage::disk('ftp')->exists($image_name)){
                   Storage::disk('ftp')->delete($image_name);
                    Storage::disk('ftp')->put($image_name,$data);
                }else{
                    Storage::disk('ftp')->put($image_name,$data);
                }
                $img->removeAttribute('src');
                $img->setAttribute('src', 'http://'.$image_name);
                $img->setAttribute('url','');
                $img->setAttribute('target','_blank');

            }
        }

        return $detail = $dom->saveHTML();

    }
    public function templateNew(){
        $action='create';
        $template=new MailEditor();
        return view('email.manage.template',['action'=>$action,'template'=>$template]);
    }
    public function template(){
        $templ=MailEditor::all();
        return view('email.manage.list',['template'=>$templ]);
    }

    public function thankyoumail(){
        $contacts=DB::select(DB::raw('select contacts.*, transactions.checkout from contacts, transactions, profilesfolio where profilesfolio.folio_master = transactions.resv_id and DATE_FORMAT(checkout,\'%Y-%m-%d\') = \''.\Carbon\Carbon::now()->subdays(2)->format('Y-m-d').'\'limit 3'));
        $template=MailEditor::where('name','thankyou_letter ')->first();
        foreach ($contacts as $contact){
            $this->emailsend($contact,$template,$template->subject.''.$contact->salutation.''.$contact->fname);
        }
    }

    //POST STAY CONFIGURATION

    public function confirmConfig(){
        $confirm=ConfirmEmail::find(1);

        return view('email.manage.confirm',['confirm'=>$confirm]);
    }
    public function confirmActivate(Request $request){
        $confirm=ConfirmEmail::find(1);
        if($request->state=='on'){
            $confirm->update(['active'=>'Y']);
            return response(['active'=>true],200);
        }else{
            $confirm->update(['active'=>'N']);
            return response(['active'=>false],200);
        }
    }

    public function postStayConfig(){
        $poststay=PostStay::first();
        return view('email.manage.poststay',['poststay'=>$poststay]);
    }

    public function poststayTemplate(Request $request){
        $templ=MailEditor::find($request->id);
        return response($templ,200);
    }
    public function postStayUpdate(Request $request){
        $poststay=PostStay::find(1);
        $poststay->sendafter=$request->sendafter+1;
        $poststay->template_id=$request->template;
        $poststay->save();
        return redirect()->back();
    }

    public function confirmUpdate(Request $request){
        $confirm=ConfirmEmail::find(1);
        $confirm->sendafter=$request->sendafter;
        $confirm->template_id=$request->template;
        $confirm->save();
        return redirect()->back();
    }
    public function poststayActivate(Request $request){
    $poststay=PostStay::find(1);
    if ($request->state=='on'){
        $poststay->update(['active'=>'y']);
        return response(['active'=>true],200);
    }else{
        $poststay->update(['active'=>'n']);
        return response(['active'=>false],200);
    }

    }

    //BIRTHDAY CONFIG

    public function birthdayConfig(){
        $birthday=Birthday::find(1);
       return view('email.manage.birthday',['birthday'=>$birthday]);
    }

    public function birthdayTemplate(Request $request){
        $templ=MailEditor::find($request->id);
        return response($templ,200);
    }
    public function birthdayUpdate(Request $request){
        $birthday=Birthday::find(1);
        $birthday->sendafter=$request->sendafter;
        $birthday->template_id=$request->template;
        $birthday->save();
        return redirect()->back();
    }
    public function birthdayActivate(Request $request){
        $birthday=Birthday::find(1);
        if ($request->state=='on'){
            $birthday->update(['active'=>'y']);
            return response(['active'=>true],200);
        }else{
            $birthday->update(['active'=>'n']);
            return response(['active'=>false],200);
        }

    }
    public function cloneTemplate(Request $request){
       $old=MailEditor::find($request->tid);
       $rules=[
           'name'=>'required',
       ];
       $message=['name.required'=>'Template Name is Required'];
       $validator=Validator::make($request->all(),$rules,$message);
       if(!$validator->fails()) {
           $new = $old->replicate();
           $new->name = $request->name;
           $new->save();
           return response('success', 200);
       }else{

          return response(['errors'=>$validator->errors()]);
        }

    }
    //We Miss You
    public function missConfig(){
        $miss = MissYou::find(1);
        return view('email.manage.miss',['miss'=>$miss]);
    }

    public function missTemplate(Request $request){
        $templ=MailEditor::find($request->id);
        return response($templ,200);
    }
    public function missUpdate(Request $request){
    
        $miss=MissYou::find(1);
        $miss->sendafter=$request->sendafter;
        $miss->template_id=$request->template;
        $miss->save();
        return redirect()->back();
    }
    public function missActivate(Request $request){
        $miss=MissYou::find(1);
        if ($request->state=='on'){
            $miss->update(['active'=>'y']);
            return response(['active'=>true],200);
        }else{
            $miss->update(['active'=>'n']);
            return response(['active'=>false],200);
        }

    }
    public function saveRating(Request $request,$id){
        $review=EmailReview::where('contact_id','=',$id)->first();
        $contact=Contact::find($id);
        if($review==null){
            $ids=$request->id;
            $r=new EmailReview();
            $r->contact_id=$id;
            $r->fname=$contact->fname;
            $r->lname=$contact->lname;
            $r->$ids=$request->val;
            $r->save();
            return response('success');
        }else{
            $ids=$request->id;
            $review->contact_id=$id;
            $review->fname=$contact->fname;
            $review->lname=$contact->lname;
            $review->$ids=$request->val;
            $review->save();
            return response('success');
        }
    }
    public function sendTest(Request $request){

        $email=new EmailTemplateController();
        $rules=[
            'email'=>'required',
        ];
        $message=['email.required'=>'Email Address is Required'];
        $validator=Validator::make($request->all(),$rules,$message);
        if(!$validator->fails()){
            $email->testmail($request);
            return response('success', 200);
        }else{
            return response(['errors'=>$validator->errors()]);
        }

    }
    public function blast(){
        return view('contacts.blast');
    }



}
