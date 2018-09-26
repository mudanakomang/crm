 @if($action=='create')
       {{ Form::model($model,['route'=>'campaign.store','files'=>'true','id'=>'campaignForm','class'=>'data-parsley-validate']) }}
  @else
       {{ Form::model($model,['route'=>['campaign.update',$model->id],'files'=>'true','id'=>'campaignForm','class'=>'data-parsley-validate']) }}
       {{ method_field('PUT') }}
  @endif

<div class="col-xs-12 ol-sm-12 col-md-12 col-lg-12">
    <div class="panel-group" id="accordion_11" role="tablist" aria-multiselectable="true">
        <div class="panel panel-col-teal">
            <div class="panel-heading" role="tab" id="headingOne_11">
                <h4 class="panel-title">
                    <a role="button" data-toggle="collapse" data-parent="#accordion_11" href="#collapseOne_11" aria-expanded="true" aria-controls="collapseOne_11">
                        <i class="fa fa-chevron-down"></i> CAMPAIGN DETAIL
                    </a>
                </h4>
            </div>
            <div id="collapseOne_11" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_11">
                <div class="panel-body">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            {{ Form::label('name','Campaign Name') }}
                        </div>
                        <div class="col-lg-10 col-md-12 col-sm-12 col-xs-12">
                            <div class="form-group form-float">
                                <div class="form-line">
                                    {{ Form::text('name',$model->name,['class'=>'form-control data-parsley-trigger="change"','required']) }}
                                </div>
                                <div class="help-info"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">


                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            {{ Form::label('type','Template Type') }}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::select('type',[''=>'Select Type','Poststay'=>'Poststay','Birthday'=>'Birthday','Checkin Reminder'=>'Checkin Reminder','Promo'=>'Promo','Special Event'=>'Special Event'],$model->type,['class'=>'form-control ','onchange'=>'getType(this.value)', 'data-live-search'=>'true','required','id'=>'type']) }}
                                </div>
                                <div class="help-info"></div>
                            </div>
                        </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('template','Template',['class'=>'text-right']) }}
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <div class="form-line ">
                                {{ Form::select('template',[''=>'Select Template'],$model->template,['class'=>'form-control selectpicker','id'=>'template','onchange=selectTemplate(this.value)', 'data-live-search'=>'true','required']) }}
                            </div>
                            <div class="help-info"></div>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-col-teal">
            <div class="panel-heading" role="tab" id="headingTwo_11">
                <h4 class="panel-title">
                    <a class="collapsed " role="button" data-toggle="collapse" data-parent="#accordion_11" href="#collapseTwo_11" aria-expanded="false"
                       aria-controls="collapseTwo_11">
                        <i class="fa fa-chevron-down"></i> SEGMENTS
                    </a>

                </h4>
            </div>
            <div id="collapseTwo_11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo_11">
                <div class="panel-body">
                    <h5 class="recepient"></h5>

                    <div class="">
                        <label>   New Segment
                            <input type="checkbox" class="js-switch" name="getsegment" id="segment"   onchange="selectSegment()"/>  Use Existing Segment
                        </label>
                    </div>


                    <div class="selectsegment" >
                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                            {{ Form::label('segments','Segment') }}
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <div class="form-line">
                                    {{ Form::select('segments',[''=>'Select from campaign']+\App\Campaign::pluck('name','id')->all(),null,['class'=>'form-control selectpicker segments']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="formsegment">
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('country_id','Country') }}
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::select('country_id[]',\App\Country::pluck('country','iso3')->all(),$model->country_id,['class'=>'form-control selectpicker country','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('guest_status','Guest Status') }}
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::select('guest_status[]',['I'=>'Inhouse','C'=>'Prestay','O'=>'Poststay','X'=>'Cancel'],$model->guest_status,['class'=>'form-control selectpicker guest','multiple','actionsBox'=>'true', 'data-live-search'=>'true','onchange'=>'checkRecepient()']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('','Total Spending') }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('spending_from',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'From']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('spending_to',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'To']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('','Stay Duration') }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('stay_from',null,['class'=>'datepicker form-control','id'=>'stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('stay_to',null,['class'=>'datepicker form-control', 'id'=>'stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('','Total Stays') }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('total_stay_from',null,['class'=>'form-control','id'=>'total_stay_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('total_stay_to',null,['class'=>'form-control', 'id'=>'total_stay_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','data-live-search'=>'true','placeholder'=>'To']) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('','Total Nights') }}
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('total_night_from',null,['class'=>'form-control','id'=>'total_night_from','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'From']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::text('total_night_to',null,['class'=>'form-control','id'=>'total_night_to','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()', 'data-live-search'=>'true','placeholder'=>'To']) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                        {{ Form::label('gender','Gender') }}
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                        <div class="form-group">
                            <div class="form-line">
                                {{ Form::select('gender[]',['M'=>'Male','F'=>'Female'],$model->gender,['class'=>'form-control selectpicker','id'=>'gender','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','actionsBox'=>'true','data-live-search'=>'true']) }}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="panel-body">
                    <h5>Schedule</h5>
                   <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="datetimepicker form-control" name="schedule" id="schedule" required placeholder="Set Schedule">
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-col-teal">
            <div class="panel-heading" role="tab" id="headingThree_11">
                <h4 class="panel-title">
                    <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion_11" href="#collapseThree_11" aria-expanded="false"
                       aria-controls="collapseThree_11">
                        <i class="fa fa-chevron-down"></i>  SUMMARY
                    </a>
                </h4>
            </div>
            <div id="collapseThree_11" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree_11">
                <div class="panel-body">
                    <div class="row clearfix">
                            <div class="col-lg-6 col-md-6">
                               <ul >
                                        <li class="list-inline-item">
                                            <p class=" text-sm-center" id="namecopy"></p>
                                        </li>
                                        <li class="list-inline-item">
                                            <p class="text-sm-center" id="tempnamecopy"></p>
                                        </li>
                                        <li class="list-inline-item">
                                            <p class=" text-sm-center" id="countrycopy"></p>
                                        </li>
                                       <li class="list-inline-item">
                                           <p class=" text-sm-center" id="spendingrange"></p>
                                       </li>
                                        <li class="list-inline-item">
                                            <p class="text-sm-center" id="totalstay"></p>
                                        </li>
                                       <li class="list-inline-item">
                                           <p class="text-sm-center" id="gendercopy"></p>
                                       </li>
                                        <li class="list-inline-item">
                                           <p class="text-sm-center" id="statuscopy"></p>
                                        </li>
                                    </ul>


                            </div>
                    </div>

                    <div class="card preview">
                            <textarea class="template_preview " id="summernote"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-lg-12 ">
        <div class="col-lg-6">
            <a class="btn btn-success " href="{{ url('campaign') }}" title="Back"><i class="fa fa-arrow-circle-o-left"></i> Back</a>
            <button class="btn  btn-success "  id="saveBtn"> <i class="fa fa-save"></i> Save</button>
        </div>

    </div>
</div>



{{ Form::close() }}

