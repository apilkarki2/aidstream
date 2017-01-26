@extends('lite.base.sidebar')

@section('title', @trans('lite/title.activities'))

@inject('getCode', 'App\Helpers\GetCodeName')

@section('content')
    <div class="col-xs-9 col-md-9 col-lg-9 content-wrapper activity__detail__wrapper">
        @include('includes.response')
        <div class="panel__heading">
            <div class="panel__heading__info pull-left">
                <div class="panel__title">
                    @lang('lite/global.activity_detail')
                </div>
                <a href="#" class="back-to-activities-list">@lang('lite/global.back_to_activities_list')</a>
                {{--<span>{{ $activity->identi{ $activity->identifier['activity_identifier'] }}</span>--}}
            </div>
            <a href="{{ route('lite.activity.edit', $activity->id) }}"
               class="edit-activity pull-right">@lang('lite/global.edit_activity')</a>
        </div>
        <div class="panel__body">
            <div class="col-xs-12 col-sm-9 panel__activity__detail">
                <h1 class="activity__title">
                    {{ $activity->title ? $activity->title[0]['narrative'] : trans('lite/global.no_title') }}
                </h1>
                <div class="activity-iati-info">
                    <div class="pull-left iati-identifier-wrapper">IATI Identifier:
                        <span class="iati-identifier">{{ $activity->identifier['activity_identifier'] }}</span>
                    </div>
                    <div class="pull-right activity-publish-state">
                        <span class="pull-left published-in-iati">@lang('lite/global.published_in_iati')</span>
                        <img src="{{asset('images/ic-iati-logo.png')}}" alt="IATI" width="27" height="25">
                    </div>
                </div>
                <div class="activity-info activity-more-info">
                    <ul class="pull-left">
                        <li>
                            <i class="pull-left material-icons">date_range</i>
                            <span>Apr 01, 2012</span>
                            <span> - Mar 31, 2015 </span>
                        </li>
                        <li>
                            <i class="pull-left material-icons">autorenew</i>
                            <span>Implementation<i>(Status)</i></span>
                        </li>
                    </ul>
                </div>
                {{--<div class="activity-status activity-status-{{ $statusLabel[$activityWorkflow] }}">--}}
                {{--<ol>--}}
                {{--@foreach($statusLabel as $key => $value)--}}
                {{--@if($key == $activityWorkflow)--}}
                {{--<li class="active"><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span>--}}
                {{--</li>--}}
                {{--@else--}}
                {{--<li><span>{{ trans(sprintf('lite/global.%s',strtolower($value)))}}</span></li>--}}
                {{--@endif--}}
                {{--@endforeach--}}
                {{--</ol>--}}
                {{--@include('lite.activity.partials.workflow')--}}
                {{--</div>--}}
                @include('lite.activity.partials.activityList')
            </div>
        </div>
    </div>
@endsection
