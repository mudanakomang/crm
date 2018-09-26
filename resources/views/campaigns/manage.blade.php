@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile ">
                            <div class="x_title">
                                <h2>Create Campaign</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content" >
                                @include('campaigns._form')
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
        function setSwitchery(switchElement, checkedBool) {
            if((checkedBool && !switchElement.isChecked()) || (!checkedBool && switchElement.isChecked())) {
                switchElement.setPosition(true);
                switchElement.handleOnchange(true);
            }
        }
        var mySwitch = new Switchery($('#segment')[0], {
            size:"small",
            color: '#0D74E9'
        });

    </script>
    <script>
        $(document).ready(function(){
           $('.selectsegment').hide();
           $('#campaignForm').parsley();
            $("#saveBtn").on('click', function(event) {
                // validate form with parsley.
                $('#campaignForm').parsley().validate();

                // if this form is valid
                if (!$('#campaignForm').parsley().isValid()) {
                    // show alert message
                    swal('Error','Some fields contain error','warning');
                } else {
                    $('#campaignForm').submit();
                }

                // prevent default so the form doesn't submit. We can return true and
                // the form will be submited or proceed with a ajax request.
                event.preventDefault();
            });
        });
    </script>
    <script>
        $('#stay_from').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        });
        $('#stay_to').datetimepicker({
            format: 'DD MMMM YYYY',
            showClear:true,
        });
        $('#schedule').datetimepicker({
            minDate:new Date(),

        });
//        $(document).ready(function () {
////            $('#campaignForm').validate({ignore: []});
//            $('#saveBtn').on('click',function (e) {
//                e.preventDefault();
//                var count = $('#campaignForm').valid();
//                if(count){
//                    $('#campaignForm').submit();
//                }else {
//                    swal("Error", "Some fields contain errors!", "warning")
//                }
//            })
//        })





    </script>


        <script>
            $('.selectpicker').selectpicker({
                actionsBox:true,
                selectOnTab:true,
            });
            $(document).ready(function () {
                $('.preview').hide();
                checkRecepient();
               var data= {!!  json_encode($model) !!};
              if(data != null || undefined){
                  var id_=data['template'][0]['id'];
                  var name_=data['template'][0]['name'];
                 $('#template').append('<option value="'+id_+'" selected="selected">'+name_+'</option>');
              }
            });
            function checkRecepient(){

                var url='{{ route('campaign.recepient') }}';
                var sel=[];
                $('.country').children('option:selected').each(function () {
                    sel.push($(this).val())
                });
                var guest=[];
                $('.guest').children('option:selected').each(function () {
                    guest.push($(this).val())
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
                $('.guest bs-select-all').on('click',function () {
                    guest.length=0;
                })
                $.ajax({
                    url:url,
                    data:{
                        country_id:sel,
                        _token:'{{ csrf_token() }}',
                        guest_status:guest,
                        spending_from:$('#spending_from').val(),
                        spending_to:$('#spending_to').val(),
                        stay_from:$('#stay_from').val(),
                        stay_to:$('#stay_to').val(),
                        total_stay_from:$('#total_stay_from').val(),
                        total_stay_to:$('#total_stay_to').val(),
                        total_night_from:$('#total_night_from').val(),
                        total_night_to:$('#total_night_to').val(),
                        gender:gen,
                    },
                    type:'POST',
                    success:function (data) {
                       var num=data.length;
                        $('.recepient').empty();
                       if (num >0 ){

                           $('.recepient').append( num + ' contacts match this criteria');
                       } else {
                           $('.recepient').append( 'No contacts match this criteria');
                       }
                    }

                });
            }
        </script>
    <script>
        function selectTemplate(id) {
            var url='{{ route('campaign.template') }}';
            console.log(url);
            $.ajax({
                url:url,
                type:'post',
                data:{
                    id:id,
                    _token:'{{ csrf_token() }}'
                },
                success:function (data) {
                    $('.preview').show();
                    $('#summernote').summernote({
                        toolbar: [

                        ],
                        height:400,
                    });
                   $('#summernote').summernote('code', '');
                 $('#summernote').summernote('editor.pasteHTML',data['content']);
                }
            })
        }
    </script>
    <script>
        $(document).ready(function () {
            var status= '{{ $model->status }}';
            if (status==='Inactive'){
                $('#myonoffswitch').prop('checked',false);
            }else {
                $('#myonoffswitch').prop('checked',true);
            }
            $('#myonoffswitch').on('change',function () {
                if ($('#myonoffswitch').is(':checked')){
                    var state='on';
                }else {
                    state='off';
                }
                var id='{{ $model->id }}';
                $.ajax({
                    url:"{{ route('campaign.activate')}}",
                    type:'post',
                    data:{
                        id:id,
                        status:state,
                        _token:'{{ csrf_token() }}',
                    },
                    success:function (data) {
                        if(data['active']==true){
                            $('#myonoffswitch').prop('checked',true);
                        }else {
                            $('#myonoffswitch').prop('checked',false);
                        }
                    }

                })
            })
        })
    </script>
    <script>
        function getType(val) {
            var value=val;
            $.ajax({
                url:'gettype',
                type:'post',
                data:{
                    _token:'{{ csrf_token() }}',
                    type:value
                },

                success:function (data) {

                       $('#template').empty();
                       $('#template').append('<option value="">Select Template</option>');
                       $.each(data, function (i, v) {
                           $('#template').append('<option value=' + i + ' >' + v + '</option>').selectpicker('refresh')
                       })
                }
            })
            console.log(value);
        }
    </script>
    <script>

        $('#headingThree_11').on('click',function () {
            $('#namecopy').empty().append("Campaign Name: "+$('#name').val());
            $('#tempnamecopy').empty().append($('#template option:selected').val() !=='' ? 'Template Name: '+$('#template option:selected').text():'Template Name: -');
            $('#countrycopy').empty();
            var txt='';
            $('.country option:selected').each(function () {
                txt+=$(this).text()+','
            });
            txt=txt.substr(0,txt.length-1);
            $('#countrycopy').append('Selected Country: '+txt);
            if($('#spending_from').val()==='' && $('#spending_to').val()===''){
                $('#spendingrange').empty().append('Spending : -');
            }else if($('#spending_from').val()!=='' && $('#spending_to').val()===''){
                $('#spendingrange').empty().append('Spending : More than / equal Rp. '+ $('#spending_from').val());
            }else if ($('#spending_from').val()==='' && $('#spending_to').val()!==''){
                $('#spendingrange').empty().append('Spending : Less than / equal Rp. '+ $('#spending_to').val());
            }else {
                $('#spendingrange').empty().append('Spending : Between Rp. '+ $('#spending_from').val()+' and Rp. '+ $('#spending_to').val());
            }
            if($('#total_stay_from').val()!=='' && $('#total_stay_to').val()!==''){
                $('#totalstay').empty().append('Total Stay '+$('#total_stay_from').val()+' day/s to '+$('#total_stay_to').val()+ ' day/s')
            }else if($('#total_stay_from').val()!=='' && $('#total_stay_to').val()===''){
                $('#totalstay').empty().append('Total Stay >= '+$('#total_stay_from').val()+' day/s')
            }else if($('#total_stay_from').val()==='' && $('#total_stay_to').val()!==''){
                $('#totalstay').empty().append('Total Stay <= '+$('#total_stay_to').val()+' day/s')
            }else {
                $('#totalstay').empty().append('Total Stay : -')
            }
            if($('#gender option:selected').val() !== 'M' && $('#gender option:selected').val() !== 'F' ){
                $('#gendercopy').empty().append('Gender : All');
            } else {
                $('#gendercopy').empty();
                var gen='';
                $('#gender option:selected').each(function () {
                    gen+=$(this).text()+',';
                })
                gen=gen.substr(0,gen.length-1);
                $('#gendercopy').append('Gender : '+gen);
            }
            $('#statuscopy').empty();
            var stt='';
            $('.guest option:selected').each(function () {
                stt+=$(this).text()+',';
            })
            stt=stt.substr(0,stt.length-1);
            if(stt===''){
                $('#statuscopy').append('Guest Status : -');
            }else {
                $('#statuscopy').append('Guest Status : '+stt);
            }
        });
    </script>
    <script>
        function selectSegment(){

             var $this=$('#segment');
             if($this.is(':checked')){
                 setSwitchery(mySwitch, true);
                 $('.formsegment').hide();
                 $('.selectsegment').show();
             }else {
                 setSwitchery(mySwitch, false);
                 $('.formsegment').show();
                 $('.selectsegment').hide();
             }
        }
       $('#segments').on('change',function () {
           $('.formsegment').show();

           var id_=$('#segments option:selected').val();

           $.ajax({
              url:'getsegment',
              type:'POST',
              data:{
                  id:id_,
                  _token:'{{ csrf_token() }}',
              } ,
               success:function (data) {
                   var options=data[1];
                   $('.country').selectpicker();
                   $('.country').selectpicker('val', '');
                   $('.country').selectpicker('val', options);

                   var gueststatus=data[2];
                   $('.guest').selectpicker();
                   $('.guest').selectpicker('val','');
                   $('.guest').selectpicker('val',gueststatus);

                   var campaign=data[0];
                   $('#spending_from').val('');
                   $('#spending_from').val(campaign['spending_from']);
                   $('#spending_to').val('');
                   $('#spending_to').val(campaign['spending_to']);
                   $('#stay_from').val('');
                   $('#stay_from').val(moment(campaign['stay_from']).format('D MMMM YYYY'));
                   $('#stay_to').val('');
                   $('#stay_to').val(moment(campaign['stay_to']).format('D MMMM YYYY'));
                   $('#total_stay_from').val('');
                   $('#total_stay_from').val(campaign['total_stay_from']);
                   $('#total_stay_to').val('');
                   $('#total_stay_to').val(campaign['total_stay_to']);
                   $('#total_night_from').val('')
                   $('#total_night_from').val(campaign['total_night_from']);
                   $('#total_night_to').val('');
                   $('#total_night_to').val(campaign['total_night_to']);
                   var gender =campaign['gender'];
                   if (gender==='M'){
                        $('#gender').selectpicker();
                       $('#gender').selectpicker('val','')
                        $('#gender').selectpicker('val','M')
                   } else {
                       $('#gender').selectpicker();
                       $('#gender').selectpicker('val','')
                        $('#gender').selectpicker('val','F')
                   }
                   checkRecepient();
                   }



           });
       })
    </script>
    <script>

        if($('#collapseOne_10').hasClass('error')){
            console.log('Error');
        }
       $('#collapseOne_10').find('.error').each(function () {
            console.log('.error').text();
        })
    </script>



    @endsection