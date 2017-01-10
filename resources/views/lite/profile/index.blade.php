@extends('lite.base.sidebar')

@section('title', trans('lite/title.profile'))

@inject('getCode', 'App\Helpers\GetCodeName')
@section('content')
    {{Session::get('message')}}

    <div class="col-xs-9 col-lg-9 content-wrapper activity-wrapper">
        @include('includes.response')
        <div id="xml-import-status-placeholder"></div>
        <div class="panel panel-default">
            <div class="panel-content-heading">
                <div>@lang('lite/title.profile')</div>
            </div>
            <div class="panel-body">
                <img src="{{url('images/avatar-full.png')}}" width="200" height="200"
                     alt="{{Auth::user()->name}}">
                <div class="profile-info">
                    <span class="profile-username">{{Auth::user()->username}}</span>
                    <span class="profile-user-email"><a
                                href="mailto:{{Auth::user()->email}}">{{Auth::user()->email}}</a></span>
                    <div><a href="{{route('lite.user.profile.edit')}}">
                            Edit Profile
                        </a> |
                        <a href="{{route('lite.user.username.edit')}}">
                            Change Username
                        </a> |
                        <a href="{{route('lite.user.password.edit')}}">
                            Change Password
                        </a>
                    </div>
                </div>
                <div class="organization-logo"><img
                            src="{{$organisation->logo ? url($organisation->logo_url) : url('images/no-logo.png')}}">
                </div>
                <div class="organization-detail">
                    <div class="organization-name">{{$organisation->name}}</div>
                    <ul>
                        @if($organisation->telephone)
                            <li class="telephone col-xs-6 col-md-4 col-lg-4">
                                <label>Telephone</label><span>{{$organisation->telephone}}</span></li>
                        @endif
                        @if($organisation->twitter)
                            <li class="twitter col-xs-6 col-md-4 col-lg-4"><label>Twitter</label><a
                                        href="http://www.twitter.com/{{ $organisation->twitter }}">{{$organisation->twitter}}</a></li>
                        @endif
                        @if($organisation->organization_url)
                            <li class="website col-xs-6 col-md-4 col-lg-4"><label>Website</label><a
                                        href="{{$organisation->organization_url}}" target="_blank">{{$organisation->organization_url}}</a></li>
                        @endif
                        @if($organisation->address)
                            <li class="address col-xs-6 col-md-4 col-lg-4">
                                <label>Address</label><span>{{$organisation->address}}</span></li>
                        @endif
                        @if($organisation->country)
                            <li class="country col-xs-6 col-md-4 col-lg-4">
                                <label>Country</label><span>{{$getCode->getOrganizationCodeName('Country', $organisation->country)}}</span>
                            </li>
                        @endif
                    </ul>
                    {{--<div class="disqus-wrapper"><span>Disqus Comments : </span>{{($organisation->disqus_comments == 1) ? 'Enabled' : 'Disabled'}}</div>--}}
                </div>
            </div>
        </div>
    </div>
@stop

@section('script')

@stop
