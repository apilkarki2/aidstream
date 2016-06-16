<div class="activity-element-wrapper">
    <div class="title">
        Transactions
    </div>
    @if(count($disbursement) > 0)
        <div class="activity-element-label">
            Disbursement
        </div>
        <a href="{{url(sprintf('project/%s/transaction/%s/edit', $project->id, 3))}}"
           class="edit-element">
            <span>Edit Disbursement</span>
        </a>
        <div>
            {!! Form::open(['method' => 'POST', 'route' => ['transaction.destroy', $project->id, 3]]) !!}
            {!! Form::submit('Delete', ['class' => 'pull-left delete-transaction']) !!}
            {!! Form::close() !!}
        </div>
        @foreach($disbursement as $data)
            <div class="activity-element-info">
                <li>{{ number_format($data['value'][0]['amount']) }} {{ $data['value'][0]['currency'] }}, {{$data['transaction_date'][0]['date']}}</li>
                <div class="toggle-btn">
                    <span class="show-more-info">Show more info</span>
                    <span class="hide-more-info hidden">Hide more info</span>
                </div>
                <div class="more-info hidden">
                    <div class="element-info">
                        <div class="activity-element-label">
                            Internal Ref
                        </div>
                        <div class="activity-element-info">
                            {{$data['reference']}}
                        </div>
                    </div>
                    <div class="element-info">
                        <div class="activity-element-label">
                            Transaction Value
                        </div>
                        <div class="activity-element-info">
                            {{ number_format($data['value'][0]['amount']) }} {{ getVal($data, ['value', 0, 'currency']) }}
                        </div>
                    </div>
                    <div class="element-info">
                        <div class="activity-element-label">
                            Transaction Date
                        </div>
                        <div class="activity-element-info">
                            {{$data['transaction_date'][0]['date']}}
                        </div>
                    </div>
                    <div class="element-info">
                        <div class="activity-element-label">
                            Description
                        </div>
                        <div class="activity-element-info">
                            {{$data['description'][0]['narrative'][0]['narrative']}}
                        </div>
                    </div>
                    <div class="element-info">
                        <div class="activity-element-label">
                            Receiver Organization
                        </div>
                        <div class="activity-element-info">
                            {{$data['provider_organization'][0]['narrative'][0]['narrative']}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="activity-element-list">
            <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,3)) }}" class="add-more"><span>Add Disbursement</span></a>
        </div>
    @else
        <div class="activity-element-list">
            <div class="title">Disbursement</div>
            <a href="{{ url(sprintf('project/%s/transaction/%s/create', $project->id,3)) }}" class="add-more"><span>Add Disbursement</span></a>
        </div>
    @endif
</div>
