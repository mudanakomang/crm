<?php

namespace App\Http\Controllers;

use App\Birthday;
use App\ConfirmEmail;
use App\MailEditor;
use App\PostStay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

        if ($request->file('file') != null && $request->active==2){
            $detail=file_get_contents($request->file('file'));
        }else{
            $detail=$this->upload($request);
        }

        $templ=new MailEditor();
        $str=str_replace(' ','_',$request->name);

        $templ->content=$detail;
        $templ->name=$str;
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
    public function destroy($id)
    {
        //

        $template=MailEditor::find($id);

        $template->delete();
      //  unlink(base_path().'/resources/views/email/templates/'.$template->name.'.blade.php');
        $tmpl=MailEditor::all();
        return view('email.manage.list',['template'=>$tmpl]);
    }
    public function upload(Request $request){
        $dom = new \DomDocument();
        libxml_use_internal_errors(true);
        $dom->loadHtml($request->contents, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $images = $dom->getElementsByTagName('img');

        foreach($images as $k => $img) {
            $data = $img->getAttribute('src');

            if (!empty(explode(';', $data)[1])) {
                list($type, $data) = explode(';', $data);

                list(, $data) = explode(',', $data);

                $data = base64_decode($data);

                $image_name = "images/upload/" . time() . $k . '.png';

                $path = asset('/'.$image_name) ;
                $publicpath=public_path().'/'.$image_name;
                file_put_contents($publicpath, $data);

                $img->removeAttribute('src');

                $img->setAttribute('src', $path);
                $img->setAttribute('url',$path);
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
//    public function confirmActivate(Request $request){
//        $confirm=ConfirmEmail::find(1);
//        if ($request->state=='on'){
//            $confirm->update(['active'=>'y']);
//            return response(['active'=>true],200);
//        }else{
//            $confirm->update(['active'=>'n']);
//            return response(['active'=>false],200);
//        }
//    }

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
        $poststay=PostStay::find(1);
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

}
