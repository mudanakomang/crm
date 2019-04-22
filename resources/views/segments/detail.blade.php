@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile" style="height:700px; !important;">
                            <div class="x_title">
                                <h2>Segment Details</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>

                            </div>
                            <div class="x_content" >

                                <div class="row clearfix">
                                    <div class="formsegment">
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('segmentname','Segment Name') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('segmentname',$segment->name,['class'=>'form-control  segmentname','required' ]) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('country_id','Country') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::select('country_id[]',\App\Country::pluck('country','iso2')->all(),unserialize($segment->country_id),['class'=>'form-control selectpicker  country_id','multiple','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('area','Area/Origin') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::select('area[]',\App\Contact::groupBy('area')->pluck('area','area')->all(),unserialize($segment->area),['class'=>'form-control selectpicker  area','multiple','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('guest_status','Guest Status') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::select('guest_status[]',['I'=>'Inhouse','C'=>'Prestay','O'=>'Poststay','X'=>'Cancel','G'=>'Guaranteed'],unserialize($segment->guest_status),['class'=>'form-control selectpicker guest','multiple','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Total Spending') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('spending_from',$segment->spending_from,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_from','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('spending_to',$segment->spending_to,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_to','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Stay Duration') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('stay_from',$segment->stay_from !=null ? \Carbon\Carbon::parse($segment->stay_from)->format('d F Y'):null ,['class'=>'datepicker form-control','id'=>'stay_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('stay_to', $segment->stay_to != null ? \Carbon\Carbon::parse($segment->stay_to)->format('d F Y'):null,['class'=>'datepicker form-control', 'id'=>'stay_to','data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Total Stays') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('total_stay_from',$segment->total_stay_from,['class'=>'form-control','id'=>'total_stay_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('total_stay_to',$segment->total_stay_to,['class'=>'form-control', 'id'=>'total_stay_to','data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Total Nights') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('total_night_from',$segment->total_night_from,['class'=>'form-control','id'=>'total_night_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('total_night_to',$segment->total_night_to,['class'=>'form-control','id'=>'total_night_to', 'data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('gender','Gender') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::select('gender[]',['M'=>'Male','F'=>'Female'],unserialize($segment->gender),['class'=>'form-control selectpicker','id'=>'gender','multiple','actionsBox'=>'true','data-live-search'=>'true']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Age') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('age_from',$segment->age_from,['class'=>'form-control','id'=>'age_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('age_to',$segment->age_to,['class'=>'form-control', 'id'=>'age_to','data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Birthday') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('bday_from',$segment->bday_from==null ? '' : \Carbon\Carbon::parse($segment->bday_from)->format('M d'),['class'=>'form-control','id'=>'bday_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('bday_to',$segment->bday_to==null ? '' : \Carbon\Carbon::parse($segment->bday_to)->format('M d'),['class'=>'form-control', 'id'=>'bday_to','data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Wedding Birthday') }}
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('wedding_bday_from',$segment->wedding_bday_from==null ? '' : \Carbon\Carbon::parse($segment->wedding_bday_from)->format('M d'),['class'=>'form-control','id'=>'wedding_bday_from', 'data-live-search'=>'true','placeholder'=>'From']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::text('wedding_bday_to',$segment->wedding_bday_to==null ? '' : \Carbon\Carbon::parse($segment->wedding_bday_to)->format('M d'),['class'=>'form-control', 'id'=>'wedding_bday_to','data-live-search'=>'true','placeholder'=>'To']) }}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                            {{ Form::label('','Booking Source') }}
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                            <div class="form-group">
                                                <div class="form-line">
                                                    {{ Form::select('booking_source[]',\App\ProfileFolio::groupBy('source')->pluck('source','source')->all(),unserialize($segment->booking_source),['class'=>'form-control selectpicker','id'=>'booking_source','multiple','actionsBox'=>'true','data-live-search'=>'true']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <button class="btn btn-success" id="saveSegment"><i class="fa fa-save"></i> Save Segment</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('script')
    <script>
     $(window).on('load',function () {

         if('{{ $segment->spending_from!=NULL }}') {
             $('#spending_from').maskMoney({thousands: '.', decimal: ',', precision: 0});
             $('#spending_from').maskMoney('mask', $('#spending_from').val());
         }
         if('{{ $segment->spending_to!=NULL }}') {
             $('#spending_to').maskMoney({thousands: '.', decimal: ',', precision: 0});
             $('#spending_to').maskMoney('mask', $('#spending_to').val());
         }

     })
        $('#saveSegment').on('click',function (e) {
            e.preventDefault()
            var sel=[];
            $('.country_id').children('option:selected').each(function () {
                sel.push($(this).val())
            });
            var ar=[];
            $('.area').children('option:selected').each(function () {
                ar.push($(this).val())
            })
            var guest=[];
            $('.guest').children('option:selected').each(function () {
                guest.push($(this).val())
            })
            var booking=[]
            $('#booking_source').children('option:selected').each(function () {
                booking.push($(this).val())
            })
            var gen=[]
            $('#gender').children('option:selected').each(function () {
                gen.push($(this).val())
            })
            $('#gender bs-select-all').on('click',function () {
                gen.length=0;
            })
            $('.country_id bs-select-all').on('click',function () {
                sel.length=0;
            })
            $('.area bs-select-all').on('click',function () {
                ar.length=0;
            })
            $('.guest bs-select-all').on('click',function () {
                guest.length=0;
            })
            var data={
                id:'{{ $segment->id }}',
                _token:'{{ csrf_token() }}',
                name:$('#segmentname').val(),
                guest_status:guest,
                stay_from:$('#stay_from').val(),
                stay_to:$('#stay_to').val(),
                total_night_from:$('#total_night_from').val(),
                total_night_to:$('#total_night_to').val(),
                age_from:$('#age_from').val(),
                age_to:$('#age_to').val(),
                country_id:sel,
                area:ar,
                spending_from:$('#spending_from').val(),
                spending_to:$('#spending_to').val(),
                total_stay_from:$('#total_stay_from').val(),
                total_stay_to:$('#total_stay_to').val(),
                gender:gen,
                bday_from:$('#bday_from').val(),
                bday_to:$('#bday_to').val(),
                wedding_bday_from:$('#wedding_bday_from').val(),
                wedding_bday_to:$('#wedding_bday_to').val(),
                booking_source:booking,
            }
            console.log(data)
            $.ajax({
                url:'updatesegment',
                type:'POST',
                data:data,
                success:function (data) {
                    if(data['error']){
                        swal('Error',data['error']['name'][0],'warning');
                    }else {

                        swal('Success','Segment Saved','success')
//                        setSwitchery(mySwitch, true);
//                        $('#segments').append('<option selected="selected" value="' + data['success']['id'] + '">' + data['success']['name'] + '</option>')
//                        $('#segments').trigger('change', true);
//                        $('#segments').selectpicker('refresh')

                    }
                }
            })
        })

     $('#stay_from').datetimepicker({
         format: 'DD MMMM YYYY',
         showClear:true,
     }).on('dp.change',function(){
         
     });
     $('#stay_to').datetimepicker({
         format: 'DD MMMM YYYY',
         showClear:true,
     }).on('dp.change',function() {
         ;
     });
     $('#bday_from').datetimepicker({
         format: 'DD MMMM',
         showClear:true,
         viewMode:'months',
     }).on('dp.change',function(){
         
     });
     $('#bday_to').datetimepicker({
         format: 'DD MMMM',
         showClear:true,
         viewMode:'months',
     }).on('dp.change',function() {
         ;
     });
     $('#wedding_bday_from').datetimepicker({
         format: 'DD MMMM',
         showClear:true,
         viewMode:'months',
     }).on('dp.change',function(){
         
     });
     $('#wedding_bday_to').datetimepicker({
         format: 'DD MMMM ',
         showClear:true,
         viewMode:'months',
     }).on('dp.change',function() {
         ;
     });
    </script>
@endsection