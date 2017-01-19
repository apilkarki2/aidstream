<div class="activity-element-wrapper">
    @if ($activity->transaction)
        <a href="{{ route('lite.activity.transaction.edit', [$activity->id, 'IncomingFunds']) }}"
           class="edit-element">
            <span>Edit Incoming Funds</span>
        </a>
        <div>
        </div>
        <div class="activity-element-list">
            <div class="activity-element-label">
                Incoming Funds
            </div>
            <div class="activity-element-info">
                @foreach ($activity->transaction as $index => $transaction)
                    <li>
                        {{ getVal($transaction, ['value', 0, 'amount']) }} {{ getVal($transaction, ['value', 0, 'currency']) }} [{{ getVal($transaction, ['period_start', 0, 'date']) }}
                        - {{ getVal($transaction, ['period_end', 0, 'date']) }}]

                        <a data-href="{{ route('lite.activity.transaction.delete', [$activity->id, 'IncomingFunds'])}}" data-index="{{ $index }}"
                           class="delete-lite-transaction" data-toggle="modal" data-target="#delete-transaction-modal"> @lang('lite/global.delete') </a>
                    </li>
                @endforeach
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'IncomingFunds']) }}"
               class="add-more"><span>Add Incoming Funds</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">
                Incoming Funds
            </div>
            <a href="{{ route('lite.activity.transaction.create', [$activity->id, 'IncomingFunds']) }}"
               class="add-more"><span>Add Incoming Funds</span></a>
        </div>
    @endif
</div>
