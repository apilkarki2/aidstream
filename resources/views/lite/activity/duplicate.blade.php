@extends('lite.base.sidebar')

@section('title', @trans('lite/title.duplicate'))

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="element-panel-heading">
            <div class="panel panel-default panel-create">
                <div class="panel-content-heading panel-title-heading">
                    <div>{{ trans('global.duplicate_activity') }}</div>
                </div>
                <div class="panel-body">
                    <div class="create-activity-form">
                        <div class="panel panel-default">
                            <div class="panel-body">
                                {!! form($form) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
