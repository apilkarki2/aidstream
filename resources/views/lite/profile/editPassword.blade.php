@extends('lite.base.sidebar')

@section('title', trans('lite/title.change_password'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.change_password')</div>
            </div>
            <div class="panel-body">
                {!! form($form) !!}
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
