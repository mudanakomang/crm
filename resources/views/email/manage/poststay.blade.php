@extends('layouts.master')
@section('content')
    <div class="right_col" role="main">
        <section class="content">
            <div class="container-fluid">
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel tile">
                            <div class="x_title">
                                <h2>Post Stay Configuration</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    <li><a class="close-link"><i class="fa fa-close"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <div class="form-inline">
                                    {{ Form::model($poststay,['route'=>['poststay.update',$poststay->id],'files'=>'true','id'=>'templateForm']) }}
                                    <div class="form-group">


                                            <div class="pull-right">


                                                <label>
                                                    Deactivate  <input type="checkbox" name="activate" class="js-switch"  id="myonoffswitch"  />  Activate
                                                </label>

                                            </div>
                                            <div class="col-lg-12 col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">Send after checkout (days):</label>
                                                    {{ Form::select('sendafter',[''=>'Day/s after']+range(1,7,1),$poststay->sendafter-1,['class'=>'form-control selectpicker']) }}
                                                </div>
                                            </div>
                                                <div class="col-lg-6 col-md-6">
                                                    <div id="templateSelection">
                                                        <label class="control-label">Select Email Template</label>
                                                        {{ Form::select('template',[''=>'Select Template']+\App\MailEditor::pluck('name','id')->all(),$poststay->template_id,['id'=>'templateChose','class'=>'selectpicker form-control','onchange'=>'selectTemplate(this.value)']) }}
                                                    </div>
                                                </div>

                                    </div>


                                   <br>
                                    <div class="form-group">
                                        <div class="editorInput col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                <div class="form-group">
                                                    {{ Form::textarea('contents',$poststay->template->content,['class'=>'form-control','id'=>'summernote']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row clearfix">
                                            <div class="col-lg-12 ">
                                                <div class="col-lg-6">
                                                    <a class="btn bg-sm btn-success" href="{{ url('email/template') }}" title="Back"><i class="fa fa-arrow-circle-o-left"></i> Back</a>
                                                    <button class="btn btn-sm btn-success" type="submit"> <i class="fa fa-save"></i> Save</button>
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
        var mySwitch = new Switchery($('#myonoffswitch')[0], {
            size:"small",
            color: '#0D74E9'
        });
        //Checks the switch
        var status='{{ $poststay->active }}';
        if(status==='y'){
            setSwitchery(mySwitch, true);
        } else {
            setSwitchery(mySwitch, false);
        }

        $('#myonoffswitch').on('click',function () {
            if (mySwitch.isChecked()){
                var state='on';
            }else {
                state='off';
            }
            $.ajax({
                url:'poststay/activate',
                type:'post',
                data:{
                    state:state,
                    _token:'{{ csrf_token() }}',
                },
                success:function (data) {
                    if(data['active']==true){
                        setSwitchery(mySwitch, true);
                    }else {
                        setSwitchery(mySwitch, false);
                    }
                }

            })
        })
        //Unchecks the switch

    </script>

    <script>
        $('#summernote').summernote({
            height:350,
            popover: {
                image: [
                    ['custom', ['imageAttributes']],
                    ['imagesize', ['imageSize100', 'imageSize50', 'imageSize25']],
                    ['float', ['floatLeft', 'floatRight', 'floatNone']],
                    ['remove', ['removeMedia']]
                ],

            },
            imageAttributes:{
                icon:'<i class="note-icon-pencil"/>',
                removeEmpty:false, // true = remove attributes | false = leave empty if present
                disableUpload: false // true = don't display Upload Options | Display Upload Options
            },
        });

    </script>
    <script>

        function selectTemplate(id) {
            $.ajax({
                url:'template',
                type:'post',
                data:{
                    id:id,
                    _token:'{{ csrf_token() }}'
                },
                success:function (data) {
                    $('#summernote').summernote('code', '');
                    $('#summernote').summernote('editor.pasteHTML',data['content']);
                }
            })
        }
    </script>
@endsection