@extends('lite.base.sidebar')

@section('title', @trans('lite/title.activities'))

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper">
        @include('includes.response')
        <div class="element-panel-heading">
            <div class="col-md-9">

                <div class="element-panel-heading-info">
                    <div>
                        {{ $activity->title ? $activity->title[0]['narrative'] : trans('lite/global.no_title') }}
                    </div>
                    <span>{{ $activity->identifier['activity_identifier'] }}</span>
                    <span class="last-updated-date">@lang('lite/global.last_updated_on'): {{ changeTimeZone($activity['updated_at'], 'M d, Y H:i') }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="clearfix">
                    <a href="{{ route('lite.activity.edit', $activity->id) }}" class="edit-btn">@lang('lite/global.edit')</a>
                </div>
            </div>

        </div>
        <div class="col-xs-12 col-md-8 col-lg-8 element-content-wrapper fullwidth-wrapper">
            <div class="activity-status activity-status-{{ $statusLabel[$activityWorkflow] }}">
                <ol>
                    @foreach($statusLabel as $key => $value)
                        @if($key == $activityWorkflow)
                            <li class="active"><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span></li>
                        @else
                            <li><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span></li>
                        @endif
                    @endforeach
                </ol>
                @include('lite.activity.partials.workflow')
            </div>
        </div>
        @include('lite.activity.partials.activityList')
    </div>
@endsection
