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
                        <div class="text-end">
                            <input type="search" class="form-control" placeholder="Search log" name="search"
                                id="search">
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
<script src="{{ asset('assets/dashboard/js/pagination.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var fetchIntervalId;
        var lastInteractionTime = new Date();
        var currentPage = 1; 

        function fetchData(searchQuery = '') {
            try {
                currentPage = $('#tableData').pagination('getCurrentPageNum');
            } catch {}
            $.ajax({
                url: '/admin/fetch-logs',
                type: 'GET',
                data: {search: searchQuery, currentPage: currentPage},
                dataType: 'json',
                success: function(response) {
                    // console.log(response);
                    if (response.logs && response.logs.length > 0) {
                        $('#tableData').pagination({
                            dataSource: response.logs,
                            pageSize: 10,
                            pageNumber: response.currentPage, 
                            callback: function(data, pagination) {
                                clearInterval(fetchIntervalId);
                                $('#data-table tbody').empty();
                                data.forEach(function(log, index) {
                                    var row = `<tr>
                                        <td>${index + 1}</td>
                                        <td>${log.user.name}</td>
                                        <td>${log.asin}</td>
                                        <td>${log.prompt.length > 100 ? log.prompt.substring(0, 100) + '...' : log.prompt}</td>
                                        <td>${log.image_match.length > 100 ? log.image_match.substring(0, 100) + '...' : log.image_match}</td>
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
                                lastInteractionTime = new Date();
                            }
                        });
                    } else {
                        $('#data-table tbody').html(`<tr><td colspan="8">No records found...</td></tr>`);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        }

        function checkInactivity() {
            var currentTime = new Date();
            if ((currentTime - lastInteractionTime) > 2000) {
                startFetchInterval();
            }
        }

        function startFetchInterval() {
            clearInterval(fetchIntervalId);
            fetchIntervalId = setInterval(function() {
                fetchData();
            }, 1000);
        }

        $('#search').on('input', function() {
            var searchQuery = $(this).val();
            clearInterval(fetchIntervalId);
            if (searchQuery === '') {
                startFetchInterval();
            } else {
                fetchData(searchQuery);
            }
        });

        if ($('#search').val() === '') {
            startFetchInterval();
        } else {
            fetchData($('#search').val());
        }

        setInterval(checkInactivity, 3000); 

        $('#tableData').on('pageChange', function(event, pageNumber) {
            currentPage = pageNumber;
            console.log(pageNumber);
        });
    });




</script>
@endsection
