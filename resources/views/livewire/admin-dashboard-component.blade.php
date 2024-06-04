<div class="nk-content-body">
    <div class="nk-block-head nk-page-head">
        <div class="nk-block-head-between">
            <div class="nk-block-head-content">
                <h2 class="display-6">Logs</h2>
            </div>
            <div class="text-end">
                <input type="search"  class="form-control" placeholder="Search log" name="search" wire:model.live="search" id="search">
            </div>      
        </div>
    </div>
    <div class="nk-block">
        <div class="card shadown-none">
            <div class="card-body">
            <div class="row g-3 gx-gs">
            <div class="col-md-12" id="tableData">
                    <table id="data-table" class="table">
                        <thead>
                            <tr>
                                <th>Sr. </th>
                                <th>User</th>
                                <th>ASIN</th>
                                <th>Prompt</th>
                                <th>Product Image</th>
                                <th>Summary</th>
                                <th>Execution Time</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($logs as $key => $log)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$log->user->name}}</td>
                                    <td>{{$log->asin}}</td>
                                    <td>{{ Str::limit($log->prompt, 100) }}</td>
                                    <td>{{ Str::limit($log->image_match, 100) }}</td>
                                    <td>{{ Str::limit($log->summary, 100) }}</td>
                                    <td>{{ isset($log->execution_time) ? number_format($log->execution_time, 2, '.', ',') . ' sec' : '' }}</td>
                                    <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">Actions</button>
                                            <form class="dropdown-menu">
                                                <a class="dropdown-item" href="/admin/log/{{ $log->id }}">View</a>
                                                <a class="dropdown-item" href="/admin/log/delete/{{ $log->id }}">Delete</a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{$logs->links()}}
            </div>                            
            </div>
        </div>
    </div>
</div>
