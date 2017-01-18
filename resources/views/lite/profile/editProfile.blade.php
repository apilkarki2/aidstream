@extends('lite.base.sidebar')

@section('title', trans('lite/title.edit_profile'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.edit_profile')</div>
            </div>
            <div class="panel-body">
                {!! form_start($form) !!}
                <div>
                    Personal Information
                </div>
                {!! form_until($form, 'picture') !!}
                <div>
                    Organisation Information
                </div>
                {!! form_rest($form) !!}
                {!! form_end($form) !!}
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
