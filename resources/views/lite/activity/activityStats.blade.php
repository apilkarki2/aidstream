<div class="panel__status text-center">
    <div class="col-sm-4">
        <h2>@lang('lite/activityDashboard.total_activities')</h2>
        <span class="count">
                {{count($activities)}}
                </span>
        <div class="published-num">
            <span>@lang('lite/activityDashboard.no_of_activities_published_to_iati'):</span> <a
                    href="#">{{$noOfPublishedActivities}}</a>
        </div>
    </div>
    <div class="col-sm-4">
        <h2>@lang('lite/activityDashboard.activities_by_status')</h2>
        <div class="stats"></div>
    </div>
    <div class="col-sm-4">
        <h2>@lang('lite/activityDashboard.total_budget')</h2>
        <span class="count" id="budgetTotal"><small>$</small>0<small>m</small></span>
        <div class="highest-budget">@lang('lite/activityDashboard.highest_budget_in_activity'): <span
                    id="maxBudget">$0</span></div>
    </div>
</div>