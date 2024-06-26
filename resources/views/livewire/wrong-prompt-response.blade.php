<div class="nk-content-body">
    <div class="nk-block-head nk-page-head">
        <div class="nk-block-head-between">
            <div class="nk-block-head-content">
                <h2 class="display-6">Wrong Prompt Response</h2>
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
                                <th>Log ID</th>
                                <th>ASIN</th>
                                <th>Product ID</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($alldata as $key => $data)
                                <tr>
                                    <td>{{$key + 1}}</td>
                                    <td>{{$data->user->name}}</td>
                                    <td>{{$data->log_id}}</td>
                                    <td>{{$data->asin}}</td>
                                    <td>{{$data->product_id}}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false">Actions</button>
                                            <form class="dropdown-menu">
                                                <a class="dropdown-item" href="/admin/log/{{ $data->log_id }}">View</a>
                                                <a class="dropdown-item" href="/admin/wrong-prompt-resp/delete/{{ $data->id }}">Delete</a>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{$alldata->links()}}
            </div>                            
            </div>
        </div>
    </div>
</div>
