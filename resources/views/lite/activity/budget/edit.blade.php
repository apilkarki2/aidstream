@extends('lite.base.sidebar')

@section('title', trans('lite/title.budget'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel__heading">
                <div class="panel__title">@lang('lite/title.budget')</div>
            </div>
            <div class="panel__body">
                <div class="create-form">
                    <div class="col-md-9">
                        {!! form($form) !!}
                    </div>
                </div>
            </div>
            <div class="collection-container hidden" data-prototype="{{ form_row($form->budget->prototype()) }}"></div>
        </div>
    </div>
@stop
