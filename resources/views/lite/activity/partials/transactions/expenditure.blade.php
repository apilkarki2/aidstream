<div class="activity__detail">
    @if ($expenditure)
        <a href="{{ route('lite.activity.transaction.edit', [$activity->id, 4]) }}"
           class="edit-element">
            <span>@lang('lite/elementForm.edit_expenditure')</span>
        </a>
        <div>
        </div>
        <div class="activity__element__list">
            <div class="activity-element-label">
                @lang('lite/title.expenditure')
            </div>
            <div class="activity__element--info">
                @foreach ($expenditure as $index => $transaction)
                    <li>
                        {{ getVal($transaction, ['transaction', 'value', 0, 'amount']) }}
                        @if(getVal($transaction, ['transaction', 'value', 0, 'currency']))
                            {{ getVal($transaction, ['transaction', 'value', 0, 'currency']) }}
                        @else
                            {{ $defaultCurrency }}
                        @endif
                        @if(getVal($transaction, ['transaction', 'value', 0, 'date']))
                            [{{ getVal($transaction, ['transaction', 'value', 0, 'date']) }}]
                        @endif
                        <a data-href="{{ route('lite.activity.transaction.delete', $activity->id)}}" data-index="{{ getVal($transaction, ['id'], '') }}"
                           class="delete-lite-resource" data-toggle="modal" data-target="#delete-modal" data-message="@lang('lite/global.confirm_delete')"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 4]) }}"
               class="add-more"><span>@lang('lite/elementForm.add_expenditure')</span></a>
        </div>
    @else
        <div class="activity__element__list">
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 4]) }}"
               class="add-more"><span>@lang('lite/elementForm.add_expenditure')</span></a>
        </div>
    @endif
</div>
