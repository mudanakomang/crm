@extends('layouts.app')
@section('main-content')
    <section class="content">
        <div class="row clearfix">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                    <div class="header">
                        <h2>Add Stay</h2>
                    </div>
                    <div class="body">
                        @if($action=='add')
                        {{ Form::model($model,['route'=>['stay.store'],'id'=>'stayForm']) }}
                        <div class="row clearfix">
                            {{ Form::hidden('contact_id',$contact_id) }}
                            @else
                                {{ Form::model($model,['route'=>['stay.update'],'id'=>'stayForm']) }}
                                <div class="row clearfix">
                                    {{ Form::hidden('id',$model->id) }}
                                @endif
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('resv_id','Reservation Number') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('resv_id',$model->resv_num,['class'=>'form-control','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('room','Room Number') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('room',$model->room,['class'=>'form-control','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                 {{ Form::label('room_type','Room Type') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                      <div class="form-line">
                                                {{ Form::text('room_type',$model->room_type,['class'=>'form-control']) }}
                                      </div>
                                 </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('checkin','Check in') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('checkin',$model->checkin=='' ? '': \Carbon\Carbon::parse($model->checkin)->format('d M Y'),['class'=>'datepicker form-control','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('checkout','Check out') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('checkout',$model->checkout=='' ? '': \Carbon\Carbon::parse($model->checkout)->format('d M Y'),['class'=>'datepicker form-control','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('revenue','Revenue') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                <div class="form-group">
                                    <div class="form-line">
                                        {{ Form::text('revenue',$model->revene,['class'=>'form-control','required']) }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                {{ Form::label('status','Status') }}
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                    <div class="form-group">
                                          <div class="form-line">
                                               {{ Form::select('status',[''=>'Select Status','Checked Out'=>'Checked Out','Inhouse'=>'Inhouse','Confirm'=>'Confirm','Cancelled'=>'Cancelled'],$model->status,['class'=>'form-control','required']) }}
                                            </div>
                                    </div>
                             </div>
                            <div class="row clearfix">
                                <div class="col-lg-12 ">
                                    <div class="col-lg-6">
                                        <button class="btn bg-teal waves-effect btn-lg" type="submit">Save</button>
                                    </div>

                                </div>
                            </div>


                        </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection
@section('script')
        <script>
            $('#stayForm').validate();
        </script>
    @endsection