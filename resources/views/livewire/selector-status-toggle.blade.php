<div>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Selector</th>
                <th>Type</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @if(count($selectors) > 0)
                @foreach ($selectors as $selector)
                <tr>
                    <td>{{ $selector->id }}</td>
                    <td>{{ $selector->name }}</td>
                    <td>{{ $selector->selector }}</td>
                    <td>{{ $selector->type }}</td>
                    <td><button wire:click="toggleStatus({{ $selector->id }})" class="btn btn-light {{$selector->status === 'enable' ? 'bg-success' : 'bg-danger'}}">
                            {{ $selector->status === 'enable' ? 'Enable' : 'Disable' }}
                        </button></td>
                    <td>
                        <a class=" btn btn-light" href="/admin/selectors/{{ $selector->id }}/edit">Edit</a>
                    </td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6">No records found...</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
