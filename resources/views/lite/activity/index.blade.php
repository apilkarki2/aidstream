@extends('lite.base.sidebar')

@section('title', trans('lite/title.activities'))

@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.activities')</div>
            </div>
            <div class="panel-body">
                @if(count($activities) > 0)
                    <table class="table table-striped" id="data-table">
                        <thead>
                        <tr>
                            <th width="45%">@lang('lite/global.activity_title')</th>
                            <th class="default-sort">@lang('lite/global.last_updated')</th>
                            <th class="status">@lang('lite/global.status')</th>
                            <th class="no-sort" style="width:100px!important">@lang('lite/global.actions')</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $status_label = ['draft', 'completed', 'verified', 'published'];
                        ?>
                        @foreach($activities as $key=>$activity)
                            <tr class="clickable-row" data-href="{{ route('lite.activity.show', [$activity->id]) }}">
                                {{--<td>{{ $key + 1 }}</td>--}}
                                <td class="activity_title">
                                    {{ $activity->title ? $activity->title[0]['narrative'] : 'No Title' }}
                                    <i class="{{ $activity->isImportedFromXml() ? 'imported-from-xml' : '' }}">icon</i>
                                    <span>{{ $activity->identifier['activity_identifier'] }}</span>
                                </td>
                                <td class="updated-date">{{ changeTimeZone($activity->updated_at) }}</td>
                                <td>
                                    {{-- Activity Status Label Stuff here --}}
                                    {{--<span class="{{ $status_label[$activity->activity_workflow] }}">{{ $status_label[$activity->activity_workflow] }}</span>--}}
                                    {{--@if($activity->activity_workflow == 3)--}}
                                    {{--<div class="popup-link-content">--}}
                                    {{--<a href="#" title="{{ucfirst($activityPublishedStats[$activity->id])}}" class="{{ucfirst($activityPublishedStats[$activity->id])}}">{{ucfirst($activityPublishedStats[$activity->id])}}</a>--}}
                                    {{--<div class="link-content-message">--}}
                                    {{--{!!$messages[$activity->id]!!}--}}
                                    {{--</div>--}}
                                    {{--</div>--}}
                                    {{--@endif--}}
                                    {{-- Activity Status Label Stuff here --}}
                                </td>
                                <td>
                                    <a href="{{ route('lite.activity.show', [$activity->id]) }}" class="view"></a>
                                    <a href="{{ route('lite.activity.edit', [$activity->id]) }}" class="edit"></a>
                                    {{--Use Delete Form to delete--}}
                                    {{--<a href="{{ url(sprintf('/lite/activity/%s/delete', $activity->id)) }}" class="delete">Delete</a>--}}
                                    <a href="{{route('lite.activity.delete',$activity->id)}}" class="delete">@lang('lite/global.delete')</a>
                                    {{--Use Delete Form--}}
                                    <a href="{{ route('lite.activity.duplicate', [$activity->id]) }}" class="duplicate"></a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center no-data no-activity-data">
                        <p>@lang('lite/global.not_added',['type' => trans('global.activity')]))</p>
                        <a href="{{route('lite.activity.create') }}" class="btn btn-primary">@lang('lite/global.add_an_activity')</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
