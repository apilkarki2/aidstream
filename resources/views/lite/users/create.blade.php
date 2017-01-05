@extends('lite.base.sidebar')

@section('title', trans('lite/title.add_user'))

@section('content')
    {{Session::get('message')}}
    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.add_user')</div>
            </div>
            <div class="panel-body">
                <div class="create-form create-project-form edit-form">
                    <span class="hidden" id="user-identifier" data-id="{{ $organizationIdentifier }}"></span>
                    {!! form($form) !!}
                </div>
            </div>
        </div>
    </div>
@stop
@section('script')
    <script src="{{url('js/chunk.js')}}"></script>
    <script>
        Chunk.usernameGenerator();
    </script>
@stop
