<x-admin-layout>

    <div class="nk-content">
        <div class="container-xl">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-page-head">
                        <div class="nk-block-head-between">
                            <div class="nk-block-head-content">
                                <h2 class="display-6">Logs</h2>
                            </div>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="card shadown-none">
                            <div class="card-body">
                                <div class="row g-3 gx-gs">
                                    <div class="col-md-12">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Sr. </th>
                                                    <th>ASIN</th>
                                                    <th>Summary</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($logs) > 0)
                                                @foreach($logs as $key => $log)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $log->asin }}</td>
                                                    <td>{{ strlen($log->summary) > 100 ? substr($log->summary, 0, 100) .
                                                        '...' : $log->summary }}</td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-light dropdown-toggle" type="button"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                            </button>
                                                            <form class="dropdown-menu">
                                                                <a class="dropdown-item"
                                                                    href="/log/{{ $log->id }}">View</a>
                                                                <a class="dropdown-item"
                                                                    href="/log/delete/{{ $log->id }}">Delete</a>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @else
                                                <tr>
                                                    <td colspan="5">No records found...</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>