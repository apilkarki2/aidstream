@extends('lite.base.sidebar')

@section('title', 'Activities')

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>Activities</div>
            </div>
            <div class="panel-body">
                <div class="create-form create-project-form edit-form">
                    {!! form($form) !!}
                </div>
                <div class="collection-container hidden"
                     data-prototype="{{ form_row($form->funding_organisations->prototype()) }}">
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
