@extends('lite.base.sidebar')

@section('title', trans('lite/title.transaction'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel__heading">
                <div class="panel__title">@lang('lite/title.transaction')</div>
            </div>
            <div class="panel-body">
                <div class="create-form">
                    <div class="col-md-9">
                        {!! form_start($form) !!}
                        @foreach($ids as $index => $id)
                            <input name="ids[]" type="hidden" value={{ $id }}>
                        @endforeach
                        {!! form_rest($form) !!}
                        {!! form_end($form) !!}
                        <input class="ids" type="hidden" data-ids=$ids>
                    </div>
                </div>
            </div>
            <div class="collection-container hidden"
                 data-prototype="{{ form_row($form->{strtolower($type)}->prototype()) }}"></div>
        </div>
    </div>
@stop
