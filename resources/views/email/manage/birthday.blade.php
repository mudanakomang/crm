@extends('layouts.app')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Birthday Configuration</h2>
                        </div>
                        <div class="body">
                            <div class="form-inline">
                                {{ Form::model($birthday,['route'=>['birthday.update',$birthday->id],'files'=>'true','id'=>'templateForm']) }}
                                <div class="form-group">

                                    <div class="col-md-12">
                                        <div class="pull-right">
                                            <div class="switch">
                                                <label>
                                                    Deactivate
                                                    <input type="checkbox" name="activate" id="myonoffswitch" >
                                                    <span class="lever"></span>
                                                    Activate
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-4">
                                            <div class="form-group">
                                                <label class="control-label">Number of days before birthday that the campaign is sent :</label>
                                                {{ Form::select('sendafter',[''=>'Day/s before']+range(0,31,1),$birthday->sendafter,['class'=>'form-control ']) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                    <div class="col-lg-4 col-md-4">
                                        <div id="templateSelection">
                                            <label class="control-label">Select Email Template</label>
                                            {{ Form::select('template',[''=>'Select Template']+\App\MailEditor::pluck('name','id')->all(),$birthday->template_id,['id'=>'templateChose','class'=>'form-control','onchange'=>'selectTemplate(this.value)']) }}
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="editorInput col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                            <div class="form-group">
                                                {{ Form::textarea('contents',$birthday->template->content,['class'=>'form-control','id'=>'summernote']) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row clearfix">
                                        <div class="col-lg-12 ">
                                            <div class="col-lg-6">
                                                <a class="btn bg-teal waves-effect btn-xs" href="{{ url('email/template') }}" title="Back"><i class="material-icons">arrow_back</i> Back</a>
                                                <button class="btn bg-teal waves-effect btn-xs" type="submit"> <i class="material-icons">save</i> Save</button>
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
@endsection
@section('script')
    <script>
        $(document).ready(function () {
            var status= '{{ $birthday->active }}';
            if (status==='n'){
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
                $.ajax({
                    url:'birthday/activate',
                    type:'post',
                    data:{
                        state:state,
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