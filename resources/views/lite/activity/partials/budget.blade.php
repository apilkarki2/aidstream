<div class="activity-element-wrapper">
    @if ($activity->budget)
        <a href="{{ route('lite.activity.budget.edit', $activity->id) }}"
           class="edit-element">
            <span>Edit Budget</span>
        </a>
        <div>
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                Budget
            </div>
            <div class="activity-element-info">
                @foreach ($activity->budget as $index => $budget)
                    <li>
                        {{ getVal($budget, ['value', 0, 'amount']) }} {{ getVal($budget, ['value', 0, 'currency']) }} [{{ getVal($budget, ['period_start', 0, 'date']) }}
                        - {{ getVal($budget, ['period_end', 0, 'date']) }}]

                        <a data-href="{{ route('lite.activity.budget.delete', $activity->id)}}" data-index="{{ $index }}"
                           class="delete-lite-budget" data-toggle="modal" data-target="#delete-budget-modal"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>Add Budget</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                Budget
            </div>
            <a href="{{ route('lite.activity.budget.create', $activity->id) }}"
               class="add-more"><span>Add Budget</span></a>
        </div>
    @endif
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="delete-budget-modal">
    <div class="modal-dialog modal-lg" role="document">     
        <div class="modal-content">         
            <div class="modal-header">             
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">@lang('lite/settings.confirm_upgrade')</h4>
            </div>
            <form action="" method="POST" id="delete-budget-form">
                {{ csrf_field() }}
                <input id="index" type="hidden" value="" name="index">
                <div class="modal-body">
                    <p>@lang('lite/global.confirm_delete')</p>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="submit-delete-budget" class="btn btn-primary">@lang('lite/settings.yes')</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('lite/settings.no')</button>
                        
                </div>
            </form>
               
        </div>
    </div>
</div>
@section('script')
    <script>
        $('.delete-lite-budget').on('click', function(){
            var form = $('#delete-budget-form');

            form.attr('action', $(this).attr('data-href'));
            form.children('input#index').attr('value', $(this).attr('data-index'));
        });
    </script>
@stop
