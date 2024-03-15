@extends('admin.layout')

@section('content')
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
                                    <table id="data-table" class="table">
                                        <thead>
                                            <tr>
                                                <th>Sr. </th>
                                                <th>User</th>
                                                <th>ASIN</th>
                                                <th>Prompt</th>
                                                <th>Summary</th>
                                                <th>Created At</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to fetch and append data
        function fetchData() {
            $.ajax({
                url: '/admin/fetch-logs',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#data-table tbody').empty();
                    if (response.length > 0) {
                        response.forEach(function(log, index) {
                            var row = `<tr>
                                <td>${index + 1}</td>
                                <td>${log.user.name}</td>
                                <td>${log.asin}</td>
                                <td>${log.prompt.length > 100 ? log.prompt.substring(0, 100) + '...' : log.prompt}</td>
                                <td>${log.summary.length > 100 ? log.summary.substring(0, 100) + '...' : log.summary}</td>
                                <td>${moment(log.created_at).format('YYYY-MM-DD HH:mm:ss')}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                            aria-expanded="false">Actions</button>
                                        <form class="dropdown-menu">
                                            <a class="dropdown-item" href="/admin/log/${log.id}">View</a>
                                            <a class="dropdown-item" href="/admin/log/delete/${log.id}">Delete</a>
                                        </form>
                                    </div>
                                </td>
                            </tr>`;
                            $('#data-table tbody').append(row);
                        });
                    } else {
                        var row = `<tr>
                            <td colspan="7">No records found...</td>
                        </tr>`;
                        $('#data-table tbody').append(row);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        fetchData();
        setInterval(fetchData, 5000);
    });
</script>
@endsection