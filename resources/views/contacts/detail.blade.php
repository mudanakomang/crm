@extends('layouts.app')
@section('main-content')
	<section class="content">

		<div class="row clearfix">
			<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
				<div class="info-box bg-teal hover-zoom-effect">
					<div class="icon">
						<i class="material-icons">local_hotel</i>
					</div>
					<div class="content">
						<div class="text">
							STAYS/NIGHTS
						</div>
						<div class="number">

                            @if(empty($data->transaction) )
                               0
                                @else
							{{ count($data->transaction) }} /
							@if(!$data->transaction->isEmpty())

								@php
									$sum=0;
									foreach($data->transaction as $night){
									$total= \Carbon\Carbon::parse($night->checkout)->diffInDays(\Carbon\Carbon::parse($night->checkin));
									$sum+=$total;
									}
								@endphp
								{{  $sum }}
							@else
								0
							@endif
                                @endif

					</div>
				</div>
			</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<div class="info-box bg-teal hover-zoom-effect">
				<div class="icon">
					<i class="material-icons">attach_money</i>
				</div>
				<div class="content">
					<div class="text">
						TOTAL SPENDING
					</div>
					<div class="text">
						@if(!$data->transaction->isEmpty())
							@php
								$sum=0;
								foreach($data->transaction as $spending){
									$total=$spending->revenue;
									$sum+=$total;
								}
							@endphp
							{{ "Rp " . number_format($sum,0,',','.') }}
						@else
							0
						@endif

				</div>
			</div>
		</div>
		</div>
		<div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
			<div class="info-box bg-teal hover-zoom-effect">
				<div class="icon">
					<i class="material-icons">bubble_chart</i>
				</div>
				<div class="content">
					<div class="text">
						AVG SPENT / STAY
					</div>
					<div class="text">
						@if(!$data->transaction->isEmpty())
							@php
								$sum=0;
                                    foreach($data->transaction as $spending){
                                        $total=$spending->revenue;
                                        $sum+=$total;
                                    }
							@endphp
							{{"Rp " . number_format($sum/count($data->transaction),0,',','.') }}
						@else
							0
						@endif
				</div>
			</div>
		</div>
		</div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <div class="info-box bg-teal hover-zoom-effect">
                    <div class="icon">
                        <i class="material-icons">cake</i>
                    </div>
                    <div class="content">
                        <div class="text">
                            @if(!$data->birthday==NULL)
                           BIRTHDAY  {{ \Carbon\Carbon::parse($data->birthday)->format('d M') }}
                                @else
                                BIRTHDAY
                            @endif
                        </div>
                        <div class="text">
                            @php
                                $datenow=\Carbon\Carbon::now('Asia/Makassar')->day;
                                $datebday=\Carbon\Carbon::parse($data->birthday)->day;
                                 $monthnow=\Carbon\Carbon::now('Asia/Makassar')->month;
                                  $monthbday=\Carbon\Carbon::parse($data->birthday)->month;
                               $bday=\Carbon\Carbon::create(\Carbon\Carbon::now()->year,$monthbday,$datebday);
                               $now=\Carbon\Carbon::create(\Carbon\Carbon::now()->year,$monthnow,$datenow);

                            if ($bday>$now){
                                $day=$bday->diffInDays($now);
                                echo 'Birthday in '. $day .' day/s';
                            }elseif($bday<$now){


                                 $y=\Carbon\Carbon::now('Asia/Makassar')->addYear()->year;
                                $next=\Carbon\Carbon::create($y,$monthbday,$datebday);
                                $day=$next->diffInDays(\Carbon\Carbon::now('Asia/Makassar'));
                                  echo 'Birthday in '. $day .' day/s';
                            } elseif($data->birthday==NULL){
                                echo '';
                            } else{
                                echo 'Today';
                            }

                            @endphp

                        </div>


                    </div>
                </div>
            </div>

		</div>
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="body">
                        <div class="row clearfix">
                            <div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
                                <div class="panel-group full-body" id="accordion_18" role="tablist" aria-multiselectable="true">
                                    <div class="panel">
                                        <div class="panel-heading" role="tab" id="headingOne_18">
                                            <h4 class="panel-title ">
                                                <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseOne_18" aria-expanded="true" aria-controls="collapseOne_18">
                                                    <i class="material-icons">perm_contact_calendar</i> PERSONAL INFORMATION
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseOne_18" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_18">
                                            <div class="panel-body">
                                                <ul class="nav nav-tabs" role="tablist">
                                                    <li role="presentation" class="active">
                                                        <a href="#profile_info" data-toggle="tab">
                                                            <i class="material-icons">face</i> PROFILE
                                                        </a>
                                                    </li>
                                                    <li role="presentation">
                                                        <a href="#messages_with_icon_title" data-toggle="tab">
                                                            <i class="material-icons">hotel</i> STAY
                                                        </a>
                                                    </li>
                                                    <li role="presentation">
                                                        <a href="#settings_with_icon_title" data-toggle="tab">
                                                            <i class="material-icons">email</i> CAMPAIGNS
                                                        </a>
                                                    </li>
                                                </ul>

                                                <!-- Tab panes -->
                                                <div class="tab-content">
                                                    <div role="tabpanel" class="tab-pane fade in active" id="profile_info">
                                                        <div class="row clearfix">
                                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                                <div class="card">
                                                                    <div class="body">
                                                                        @if(!empty($action))
                                                                            {{ Form::model($data,['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm1']) }}
                                                                        @else
                                                                        {{ Form::model($data,['route'=>['contacts.update','id'=>$data->contactid],'class'=>'form-horizontal','id'=>'contactForm1']) }}

                                                                        @endif
                                                                        {{ Form::hidden('company_name',$data->companyname->isEmpty() ? NULL: $data->companyname[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_address',$data->companyaddress->isEmpty() ? NULL :$data->companyaddress[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_phone',$data->companyphone->isEmpty() ? NULL : $data->companyphone[0]->pivot->value )}}
                                                                        {{ Form::hidden('company_email',$data->companyemail->isEmpty() ? NULL:$data->companyemail[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_status',$data->companystatus->isEmpty() ? NULL : $data->companystatus[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_type',$data->companytype->isEmpty() ? NULL :$data->companytype[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_area',$data->companyarea->isEmpty() ? NULL:$data->companyarea[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_nationality',$data->companynationality->isEmpty() ? NULL : $data->companynationality[0]->pivot->value) }}
                                                                        {{ Form::hidden('company_fax',$data->companyfax->isEmpty() ? NULL : $data->companyfax[0]->pivot->value) }}



                                                                            <div class="row clearfix">
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('fname','First Name') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('fname',$data->fname,['class'=>'form-control','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('lname','Last Name') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('lname',$data->lname=='' ? '':$data->lname,['class'=>'form-control']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('salutation','Salutation') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::select('salutation',[''=>'Select Salutation','Mr'=>'Mr','Mrs'=>'Mrs','Miss'=>'Miss'],$data->salutation,['class'=>'form-control','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('marital_status','Marital Status') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::select('marital_status',[''=>'Select Marital Status','Divorced'=>'Divorced','Married'=>'Married','Single'=>'Single','Widowed'=>'Widowed'],$data->marital_status,['class'=>'form-control']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('gender','Gender') }}
                                                                            </div>
                                                                            <div class="col-lg-4  col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::select('gender',[''=>'Select Gender','Male'=>'Male','Female'=>'Female'],$data->gender,['class'=>'form-control','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('birthday','Birthday') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('birthday',$data->birthday==NULL ? '': \Carbon\Carbon::parse($data->birthday)->format('d M Y'),['class'=>'datepicker form-control','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('address1','Address Line 1') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('address1',$data->address1->isEmpty() ? '': $data->address1[0]->pivot->value,['class'=>'form-control']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('address2','Address Line 2') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('address2',$data->address2->isEmpty() ? '': $data->address2[0]->pivot->value,['class'=>'form-control']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('country_id','Nationality') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::select('country_id',[''=>'Select Nationality']+\App\Country::pluck('country','id')->all(),$data->country==NULL ? '' : $data->country->id,['class'=>'form-control', 'data-live-search'=>'true','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('email','Email') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::email('email',$data->email,['class'=>'form-control','required']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('mobile','Mobile') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('mobile',$data->mobile->isEmpty() ? '': $data->mobile[0]->pivot->value,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                                {{ Form::label('idnumber','ID Number') }}
                                                                            </div>
                                                                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                                <div class="form-group">
                                                                                    <div class="form-line">
                                                                                        {{ Form::text('idnumber',$data->idnumber=='' ? '': $data->idnumber,['class'=>'form-control']) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        <div class="row clearfix">
                                                                            <div class="col-md-12 text-left">
                                                                                <button class="btn bg-teal waves-effect btn-lg updateContact" type="submit" id="updateContact1">Save</button>
                                                                            </div>
                                                                        </div>
                                                                        {{ Form::close() }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div role="tabpanel" class="tab-pane fade" id="messages_with_icon_title">
                                                        <div class="row clearfix">
                                                            <div class="col-lg-12">
                                                                <div class="col-lg-6 col-md-6  ">
                                                                    <a href="{{ url('contacts/stay/add/').'/'.$data->id }}" class="btn btn-sm btn-primary waves-effect">
                                                                        <i class="material-icons">add</i>
                                                                        <span>Add Stay</span>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <table class="table table-bordered table-striped table-hover dataTable js-basic-example">
                                                            <thead>
                                                            <tr class="bg-teal">
                                                                <th class="align-center">Reservation Number</th>
                                                                <th class="align-center">Check in</th>
                                                                <th class="align-center">Check Out</th>
                                                                <th class="align-center">No. Of Night</th>
                                                                <th class="align-center">Room Number</th>
                                                                <th class="align-center">Room Type</th>
                                                                <th class="align-center">Total Revenue (Rp)</th>
                                                                <th class="align-center">Status</th>
                                                                <th class="align-center">Action</th>
                                                            </tr>
                                                            </thead>

                                                            <tbody>
                                                            @foreach($data->transaction as $trx)
                                                                @if($trx->status != 'C')
                                                                <tr class="align-center">
                                                                    <td>{{$trx->resv_id}}</td>
                                                                    <td>{{\Carbon\Carbon::parse($trx->checkin)->format('d M Y')}}</td>
                                                                    <td>{{\Carbon\Carbon::parse($trx->checkout)->format('d M Y')}}</td>
                                                                    <td>{{\Carbon\Carbon::parse($trx->checkin)->diffInDays(\Carbon\Carbon::parse($trx->checkout))}}</td>
                                                                    <td>{{$trx->room}}</td>
                                                                    <td>{{$trx->roomType->room_name}}</td>
                                                                    <td>{{number_format($trx->revenue,0,',','.')}}</td>
                                                                    <td>
                                                                        @if($trx->status=='O')
                                                                            Checked Out
                                                                         @elseif($trx->status=='I')
                                                                        Inhouse
                                                                            @elseif($trx->status=='X')
                                                                        Cancel
                                                                            @else
                                                                        Confirm
                                                                            @endif
                                                                    </td>
                                                                    <td>
                                                                        <a href="{{ url('contacts/stay/edit').'/'.$trx->id }}" title="Edit Stay"> <i class="material-icons">mode_edit</i></a>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            @endforeach

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div role="tabpanel" class="tab-pane fade" id="settings_with_icon_title">
                                                        <b>Settings Content</b>
                                                        <p>
                                                            Kosong
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel ">
                                        <div class="panel-heading" role="tab" id="headingTwo_18">
                                            <h4 class="panel-title">
                                                <a class="collapsed bg-teal" role="button" data-toggle="collapse" data-parent="#accordion_18" href="#collapseTwo_18" aria-expanded="false"
                                                   aria-controls="collapseTwo_18">
                                                    <i class="material-icons">domain</i> COMPANY INFORMATION
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="collapseTwo_18" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_18">
                                            <div class="panel-body">
                                                <div class="row clearfix">
                                                    <div class="card">
                                                        <div class="body">
                                                            @if(!empty($action))
                                                                {{ Form::model($data,['route'=>['contacts.store'],'class'=>'form-horizontal','id'=>'contactForm2']) }}
                                                            @else
                                                                {{ Form::model($data,['route'=>['contacts.update','id'=>$data->contactid],'class'=>'form-horizontal','id'=>'contactForm2']) }}
                                                            @endif

                                                                {{ Form::hidden('fname',$data->fname )}}
                                                                {{ Form::hidden('lname',$data->lname )}}
                                                                {{ Form::hidden('email',$data->email )}}
                                                                {{ Form::hidden('salutation',$data->salutation )}}
                                                                {{ Form::hidden('salutation',$data->salutation )}}
                                                                {{ Form::hidden('gender',$data->gender )}}
                                                                {{ Form::hidden('birthday',$data->birthday )}}
                                                                {{ Form::hidden('country_id',$data->country==null ? '':$data->country->id )}}
                                                                {{ Form::hidden('address1',$data->address1->isEmpty() ? '' :$data->address1[0]->pivot->value) }}
                                                                {{ Form::hidden('address2',$data->address2->isEmpty() ? '' :$data->address2[0]->pivot->value) }}

                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_name','Name') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                        {{ Form::text('company_name',$data->companyname->isEmpty() ? '': $data->companyname[0]->pivot->value,['class'=>'form-control', 'data-live-search'=>'true']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_address','Address') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::text('company_address',$data->companyaddress->isEmpty() ? '':$data->companyaddress[0]->pivot->value,['class'=>'form-control']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_phone','Phone') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::text('company_phone',$data->companyphone->isEmpty() ? '': $data->companyphone[0]->pivot->value,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_fax','Fax') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::text('company_fax',$data->companyfax->isEmpty() ? '': $data->companyfax[0]->pivot->value,['class'=>'form-control','number=number','minlength=6','maxlength=15']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_email','Email') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::email('company_email',$data->companyemail->isEmpty()  ? '': $data->companyemail[0]->pivot->value,['class'=>'form-control']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_type','Type') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::select('company_type',[''=>'Select Type','Compliment'=>'Compliment','Corporate'=>'Corporate','Direct'=>'Direct','Goverment'=>'Goverment','Group'=>'Group','OTA'=>'OTA','Timeshare'=>'Timeshare', 'Travel Agent'=>'Travel Agent','Whosaler'=>'Wholesaler'],$data->companytype->isEmpty() ? '' : $data->companytype[0]->pivot->value,['class'=>'form-control']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_area','Area') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::text('company_area',$data->companyarea->isEmpty() ? '': $data->companyarea[0]->pivot->value,['class'=>'form-control']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_nationality','Nationality') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::select('company_nationality',[''=>'Select Nationality']+\App\Country::pluck('country','id')->all(),$data->companynationality->isEmpty() ? '' : $data->companynationality[0]->pivot->value,['class'=>'form-control', 'data-live-search'=>'true']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                                                    {{ Form::label('company_status','Status') }}
                                                                </div>
                                                                <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                                                    <div class="form-group">
                                                                        <div class="form-line">
                                                                            {{ Form::select('company_status',[''=>'Select Status','Prospect'=>'Prospect','Active'=>'Active','In Active'=>'In Active'],$data->companystatus->isEmpty() ? '' : $data->companystatus[0]->pivot->value,['class'=>'form-control']) }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="row clearfix">
                                                                <div class="col-md-12 text-left">
                                                                    <button class="btn bg-teal waves-effect btn-lg updateContact" id="updateContact2"  type="submit" >Save</button>
                                                                </div>
                                                            </div>
                                                        {{ Form::close() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</section>
@endsection
@section('script')
    <script>
        if (window.location.pathname=='/contacts/add'){
            $('#updateContact2').attr('disabled','disabled');
        }
    </script>
    <script>
       $('#contactForm1').validate();
       $('#contactForm2').validate();
    </script>
    <script>
        $('button[id^=updateContact]').click(function (event) {
            event.preventDefault();
            if(this.id=='updateContact1'){
                var form1=$('#contactForm1');
                form1.submit();
                var error=$('.error:visible').length;
                if (error>0){
                    $('#updateContact2').attr('disabled','disabled');
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else {
                    $('#updateContact2').removeAttr('disabled');
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            } else {
                var form2=$('#contactForm2');
                form2.submit();

                var  error=$('.error:visible').length;
                if (error>0){
                    sweetAlert('Warning!', 'Error Found ','warning',7000);
                }else
                {
                    sweetAlert('Congratulation!', 'Data updated','success',7000);
                }
            }

            //var form = $('#contactForm');
            //form.submit();
           // var error=$('.error:visible').length;
           // if (error>0){
            //    $('#updateContact2').attr('disabled','disabled');
            //    sweetAlert('Warning!', 'Error Found ','warning',7000);
            //}else {
              //  $('#updateContact2').removeAttr('disabled');
            //    sweetAlert('Congratulation!', 'Data updated','success',7000);
           // }
        });
    </script>
@endsection


