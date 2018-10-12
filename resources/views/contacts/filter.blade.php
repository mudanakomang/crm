@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                      <div class="panel">
                          <div class="panel-heading">
                              <h2>Filter Contacts</h2>
                          </div>
                          <div class="panel-body">
                              {{ Form::open() }}
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  {{ Form::label('name','Contact Name') }}
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-8 col-xs-12">
                                  <div class="form-group">
                                      <div class="form-line">
                                          {{ Form::text('name',null,['class'=>'form-control','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()']) }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  {{ Form::label('country_id','Country') }}
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                  <div class="form-group">
                                      <div class="form-line">
                                          {{ Form::select('country_id[]',\App\Country::pluck('country','iso3')->all(),null,['class'=>'form-control selectpicker country','multiple','onchange'=>'checkRecepient()','actionsBox'=>'true', 'data-live-search'=>'true']) }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  {{ Form::label('guest_status','Guest Status') }}
                              </div>
                              <div class="col-lg-4 col-md-4 col-sm-6 col-xs-6">
                                  <div class="form-group">
                                      <div class="form-line">
                                          {{ Form::select('guest_status[]',['I'=>'Inhouse','C'=>'Prestay','O'=>'Poststay','X'=>'Cancel'],null,['class'=>'form-control selectpicker guest','multiple','actionsBox'=>'true', 'data-live-search'=>'true','onchange'=>'checkRecepient()']) }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-4 col-xs-5 form-control-label">
                                  {{ Form::label('','Total Spending') }}
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                  <div class="form-group">
                                      <div class="form-line">
                                          {{ Form::text('spending_from',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_from','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'From']) }}
                                      </div>
                                  </div>
                              </div>
                              <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                                  <div class="form-group">
                                      <div class="form-line">
                                          {{ Form::text('spending_to',null,['class'=>'form-control', 'data-live-search'=>'true','id'=>'spending_to','onchange'=>'checkRecepient()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','placeholder'=>'To']) }}
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
                                          {{ Form::select('gender[]',['M'=>'Male','F'=>'Female'],null,['class'=>'form-control selectpicker','id'=>'gender','multiple','onchange'=>'checkRecepient()','onkeyup'=>'this.onchange()','onpaste'=>'this.onchange()','oninput'=>'this.onchange()','actionsBox'=>'true','data-live-search'=>'true']) }}
                                      </div>
                                  </div>
                              </div>

                          {{ Form::close() }}
                          </div>
                          <hr>
                          <div class="panel-body">
                              <table class="table table-bordered table-striped table-hover " >
                                  <thead class="bg-teal">
                                  <tr>
                                      <th><input type="checkbox" id="selectAll"></th>
                                      <th class="align-center">#</th>
                                      <th class="align-center">Full Name</th>
                                      <th class="align-center">Birthday</th>
                                      <th class="align-center">Country</th>
                                      <th class="align-center">Status</th>

                                      <th class="align-center">Total Stays</th>
                                      {{--<th class="align-center">Last Stay</th>--}}
                                      <th class="align-center">Total Spending (Rp.)</th>
                                      {{--<th class="align-center">Action</th>--}}
                                  </tr>
                                  </thead>
                                  <tbody class="table_body">

                                  </tbody>
                              </table>
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

        $('#selectAll').on('click',function () {
            if ($(this).prop('checked',true)){
                $(this).prop('checked')
                console.log('true')
            }else {
                console.log('false')
            }
        })

        $('#stay_from').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function(){
            checkRecepient()
        });
        $('#stay_to').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        }).on('dp.change',function() {
            checkRecepient();
        });
    </script>
        <script>
            function checkRecepient() {
                $('.table_body').empty()
                var nat=[];
                var guest=[];
                var gen=[];
                var name=$('#name').val()
                var stay_from=$('#stay_from').val()
                var stay_to=$('#stay_to').val()
                var spending_from=$('#spending_from').val()
                var spending_to=$('#spending_to').val()
                var total_stay_from=$('#total_stay_from').val()
                var total_stay_to=$('#total_stay_to').val()
                var total_night_from=$('#total_night_from').val()
                var total_night_to=$('#total_night_to').val()

                $('.country').children('option:selected').each(function () {
                    nat.push($(this).val())
                });
                $('.guest').children('option:selected').each(function () {
                    guest.push($(this).val())
                });
                $('#gender').children('option:selected').each(function () {
                    gen.push($(this).val())
                });

                $.ajax({
                    url:'filter',
                    type:'post',
                    data:{
                        _token:'{{ csrf_token() }}',
                        name:name,
                        country_id:nat,
                        gender:gen,
                        guest_status:guest,
                        stay_form:stay_from,
                        stay_to:stay_to,
                        spending_from:spending_from,
                        spending_to:spending_to,
                        total_stay_from:total_stay_from,
                        total_stay_to:total_stay_to,
                        total_night_from:total_night_from,
                        total_night_to:total_night_to,
                    },
                    success:function (data) {

                        $.each(data,function (i) {

                          var id =data[i].contactid;
                          var tr=$('#'+id+'')
                            if(data[i].birthday !=null){
                                var bd=moment(data[i].birthday).format('D MMMM YYYY')
                            }else {
                                bd='';
                            }

                            var trans=data[i].transaction
                                if(trans.length>0){
                                var rev=0;
                                     if(trans.length>=1){
                                        for(var j =0 ; j<=trans.length-1;j++){
                                            rev+=trans[j].revenue
                                        }
                                     }
                                }

                            if(trans.length>0){
                               var trx=trans[0].status
                               if (trx !='undefined') {
                                      var status = trx
                                }
                               }else {
                                    status='';
                               }
                            var el='<tr><td><input type="checkbox" name="check[]" /></td>'
                            el+='<td>'+(i+1)+'</td>'
                            el+='<td>'+data[i].fname+' '+data[i].lname+'</td>'
                            el+='<td>'+bd+'</td>'
                            el+='<td>'+data[i].country_id+'</td>'
                            el+='<td>'+status+'</td>'
                            el+='<td>'+trans.length+'</td>'
                            el+='<td>'+rev+'</td></tr>'
                            $('.table_body').append(el)
                        })

                    }
                })
            }
        </script>

    @endsection