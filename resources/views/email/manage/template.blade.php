@extends('layouts.app')
@section('main-content')
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>Template Editor</h2>
                        </div>
                        <div class="body">
                            @include('email.manage._manage')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endsection
@section('script')
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
        $(document).ready(function () {
            if ($('#radio_1').is(':checked')){
                $('#summernote').summernote('enable');
                $('.fileImport').hide();
                $('.editorInput').show();
                $('#templateForm').append('<input type="hidden" name="active" id="priority" value="1" />');
            }
            $('input:radio[name=group1]').change(function(){
               if($('#radio_1').is(':checked')){
                  $('#summernote').summernote('enable');
                  $('.fileImport').hide();
                  $('.editorInput').show();
                   $('#priority').remove();
                   $('#templateForm').append('<input type="hidden" name="active" id="priority" value="1" />');

               }else {
                   $('#summernote').summernote('disable');
                   $('.fileImport').show();
                   $('.editorInput').hide();
                   $('#priority').remove();
                   $('#templateForm').append('<input type="hidden" name="active" id="priority" value="2" />');
               }
            });
        })
    </script>
@endsection