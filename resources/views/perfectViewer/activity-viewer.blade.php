<!doctype html>
<html lang="en">

@inject('codeListHelper', 'App\Helpers\GetCodeName')

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/style.css')}}">
    <link rel="shortcut icon" type="image/png" sizes="16*16" href="{{asset('/images/favicon.png')}}"/>
    <link rel="stylesheet" href="{{asset('/css/bootstrap.min.css')}}">
    <link href="{{asset('/css/jquery.jscrollpane.css')}}" rel="stylesheet">
    <title>Activity Viewer</title>
</head>

<body>
<div class="wrapper">
    @include('includes.header')
    <section class="col-md-12 org-map-wrapper">
        <div class="width-940">
            <div class="organisation-info">
                <a href="#" class="organisation-logo">
                    <!--dynamic organisation logo-->
                    <img src="{{ $organization[0]['logo_url'] }}" alt="AbleChildAfrica" width="238" height="68">
                </a>
                <span class="organisation-name">
                    <a href="#" title="AbleChildAfrica">
                        <!--dynamic organisation name-->
                        {{ getVal($organization, [0, 'name'], '')}}
                    </a>
                </span>
                <address><i class="pull-left material-icons">room</i>Unit 3 Graphite Square, London, SE11 5EE</address>
                <a href="#" class="see-all-activities"><i class="pull-left material-icons">arrow_back</i>See all
                    Activities</a>
            </div>
        </div>
    </section>
    <section class="col-md-12 activity-main-wrapper">
        <div class="width-940">
            <div class="col-xs-12 activity-detail-wrapper">
                <div class="activity-detail-top-wrapper">
                    <h1>
                        <!--dynamic title-->
                        {{ getVal($activity, [0, 'published_data', 'title', 0, 'narrative'], '') }}
                    </h1>
                    <div class="activity-iati-info">
                        <div class="pull-left iati-identifier-wrapper">IATI Identifier:
                            <span class="iati-identifier">
                                        <!--dynamic iati identifier-->
                                {{ getVal($activity, [0, 'published_data', 'identifier', 'activity_identifier'], '') }}
                                    </span>
                        </div>
                        <div class="pull-right activity-publish-state">
                            @if(getVal($activity, [0, 'activity_in_registry'], false))
                                <span class="pull-left published-in-iati">
                                    <!--dynamic state-->
                                        Published in IATI
                                    </span>
                            @else
                                <span class="pull-left unpublished-in-iati">
                                    <!--dynamic state-->
                                        Not Published in IATI
                                    </span>
                            @endif
                            <img src="{{asset('images/ic-iati-logo.png')}}" alt="IATI" width="27" height="25">
                        </div>
                    </div>
                    <div class="activity-info activity-more-info">
                        <ul class="pull-left">
                            <li><i class="pull-left material-icons">date_range</i>
                                <span>
                                    <!--dynamic date-->
                                    @if($activity[0]['published_data']['activity_date'][0]['type'] == 2)
                                        {{getVal($activity, [0, 'published_data', 'activity_date', 0, 'date'], '')}}
                                    @elseif($activity[0]['published_data']['activity_date'][0]['type'] == 1)
                                        {{getVal($activity, [0, 'published_data', 'activity_date', 0, 'date'], '')}}
                                    @else
                                    @endif
                                    -
                                    @if($activity[0]['published_data']['activity_date'][0]['type'] == 4)
                                        {{getVal($activity, [0, 'published_data', 'activity_date', 0, 'date'], '')}}
                                    @elseif($activity[0]['published_data']['activity_date'][0]['type'] == 3)
                                        {{getVal($activity, [0, 'published_data', 'activity_date', 0, 'date'], '')}}
                                    @else
                                    @endif
                                </span>
                            </li>
                            <li>
                                <i class="pull-left material-icons">autorenew</i>
                                <span>
                                    <!--dynamic status-->
                                    {{ $codeListHelper->getCodeNameOnly('ActivityStatus', getVal($activity, [0, 'published_data', 'activity_status'], '')) }} <i>(Status)</i>
                                    </span>
                            </li>
                        </ul>
                        <ul class="pull-right links">
                            <li><a href="#"><i class="pull-left material-icons">mail</i>Contact</a></li>
                            <li><a href="#"><i class="pull-left material-icons">share</i>Share</a></li>
                        </ul>
                    </div>
                    <div class="activity-description">
                        <p>
                            <!--dynamic description-->
                            {{ getVal($activity, [0, 'published_data', 'description', 0, 'narrative'], '' )}}
                        </p>
                        <span class="show-more"><i class="material-icons">more_horiz</i></span>
                    </div>
                    <div class="activity-sectors">
                        <span class="pull-left">Sectors:</span>
                        <ul class="pull-left">
                            <!--dynamic sectors-->
                            @foreach(getVal($activity, [0, 'published_data', 'sector'], []) as $index => $sector)
                                <li>{{getVal($sector, ['narrative', 0, 'narrative'], '')}}<i class="pull-right material-icons">error</i>
                                    <div class="sector-more-info">
                                        <dl>
                                            <dt class="pull-left">Sector code:</dt>
                                            <dd class="pull-left">{{getVal($sector, ['sector_code'], '')}}
                                                - {{ $codeListHelper->getCodeNameOnly('Sector', getVal($sector, ['sector_code'], '')) }} </dd>
                                            <dt class="pull-left">Sector vocabulary</dt>
                                            <dd class="pull-left">{{getVal($sector, ['sector_vocabulary'], '')}}
                                                - {{ $codeListHelper->getCodeNameOnly('SectorVocabulary', getVal($sector, ['sector_vocabulary'], '')) }}</dd>
                                        </dl>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="activity-block participating-organisation-block">
                    <h2>Participating Organisations</h2>
                    <table>
                        <thead>
                        <tr>
                            <th>Organisation Name</th>
                            <th>Organisation Type</th>
                            <th>Organisation Role</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--dynamic value-->
                        @foreach(getVal($activity, [0, 'published_data', 'participating_organization'], '') as $index => $org)
                            <tr>
                                <td>{{ getVal($org, ['narrative', 0, 'narrative'], '') }}</td>
                                <td>{{ $codeListHelper->getCodeNameOnly('OrganisationType', getVal($org, ['organization_type'], '')) }}</td>
                                <td>{{ $codeListHelper->getCodeNameOnly('OrganisationRole', getVal($org, ['organization_role'], '')) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="activity-block transaction-block">
                    <h2>Transaction</h2>
                    <table>
                        <thead>
                        <tr>
                            <th width="30%">Transaction Value</th>
                            <th width="30%">Provider Receiver</th>
                            <th width="20%">Type</th>
                            <th width="20%">Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--dynamic value-->
                        @foreach(getVal($activity, [0, 'published_data', 'transactions'], []) as $index => $transaction)
                            <tr>
                                <td><span class="transaction-value">{{getVal($transaction, ['transaction', 'value', 0, 'amount'], '')}}</span><i>(Valued
                                        at {{getVal($transaction, ['transaction', 'value', 0, 'date'], '')}})</i>
                                </td>
                                <td><span class="provider"><i>circle</i>{{getVal($transaction, ['transaction', 'provider_organization', 0, 'narrative', 0, 'narrative'], '')}}</span><span
                                            class="receiver"><i>circle</i>{{getVal($transaction, ['transaction', 'receiver_organization', 0, 'narrative', 0, 'narrative'], '')}}</span>
                                </td>
                                <td class="type">
                                    <strong>{{ $codeListHelper->getCodeNameOnly('TransactionType', getVal($transaction, ['transaction', 'transaction_type', 0, 'transaction_type_code'], '')) }}</strong>
                                </td>
                                <td class="date"><i class="pull-left material-icons">date_range</i>{{getVal($transaction, ['transaction', 'transaction_date', 0, 'date'])}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="activity-block budget-block">
                    <h2>Budget</h2>
                    <div class="budget-content">
                        <div class="pull-left total-budget">
                            <strong>
                                <!--dynamic value-->
                                11,681.57
                            </strong>
                            <span class="currency">GBP</span>
                            <label>Total Budget</label>
                        </div>
                        <div class="pull-left budget-table">
                            <table>
                                <tbody>
                                <!--dynamic value-->
                                @foreach(getVal($activity, [0, 'published_data', 'budget'], []) as $index => $budget)
                                    <tr>
                                        <td><span class="transaction-value">{{getVal($budget, ['value', 0, 'amount'], '')}} {{getVal($budget, ['value', 0, 'currency'], '')}} GBP</span><i>(Valued at
                                                {{getVal($budget, ['value', 0, 'value_date'], '')}})</i></td>
                                        <td class="date"><i class="pull-left material-icons">date_range</i>
                                            {{getVal($budget, ['period_start', 0, 'date'], '')}}
                                            -
                                            {{getVal($budget, ['period_end', 0, 'date'], '')}}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="activity-other-info">
                <div class="pull-left updated-date"><i class="pull-left material-icons">access_time</i>Updated on
                    <span>
                        <!--dynamic date-->
                        {{getVal($activity, ['updated_at'], '')}}
                    </span>
                </div>
                <a href="#" class="view-xml-file">View XML file here</a>
            </div>
        </div>
    </section>
    <footer>Footer</footer>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('.activity-description .show-more').click(function () {
            $(this).siblings('p').animate({
                height: '100%'
            });
            $(this).hide();
        });
    });
</script>
</body>
</html>