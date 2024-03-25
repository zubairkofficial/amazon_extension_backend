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
<script src="https://pagination.js.org/dist/2.6.0/pagination.min.js"></script>
<script>
    $(document).ready(function() {
        // Function to fetch and append data
       function fetchData(searchQuery = '') {
            $.ajax({
            url: '/admin/fetch-logs',
            type: 'GET',
            data: {search: searchQuery},
            dataType: 'json',
            success: function(response) {
            // Assuming `response` is the array of logs
                if (response && response.length > 0) {
                    // Initialize or refresh the pagination plugin
                    $('#tableData').pagination({
                        dataSource: response,
                        pageSize: 10,
                        showPageNumbers: false,
                        showNavigator: true,
                        callback: function(data, pagination) {
                            // Clear existing table rows
                            $('#data-table tbody').empty();

                            // Append new rows based on the current page's data
                            data.forEach(function(log, index) {
                                var row = `<tr>
                                    <td>${index + 1}</td>
                                    <td>${log.user.name}</td>
                                    <td>${log.asin}</td>
                                    <td>${log.prompt.length > 100 ? log.prompt.substring(0, 100) + '...' : log.prompt}</td>
                                    <td>${log.image_match.length >
                                        100 ? log.image_match.substring(0, 100) + '...' : log.image_match}</td>
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
                        }
                    });
                } else {
                    // Show "No records found" if no data is returned
                    $('#data-table tbody').html(`<tr>
                        <td colspan="8">No records found...</td>
                    </tr>`);
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    }

        // Add event listener for the search input field
        $('#search').on('input', function() {
        var searchQuery = $(this).val();

        if (searchQuery === '') {
        // If input is cleared, restart the interval
        startFetchInterval();
        } else {
        // If there is a search query, stop the interval and fetch data based on the query
        clearInterval(fetchIntervalId);
        fetchData(searchQuery);
        }
        });

        // Initialize
        if ($('#search').val() === '') {
            startFetchInterval(); // Start the interval if the search field is empty when the page loads
        } else {
            fetchData($('#search').val()); // Otherwise, fetch data based on the current value
        }
        var fetchIntervalId; // Variable to store the interval ID

        // Function to start the data fetch interval
        function startFetchInterval() {
            // Clear existing interval to prevent duplicates
            clearInterval(fetchIntervalId);

            // Set a new interval
            fetchIntervalId = setInterval(function() {
                fetchData(); // Call fetchData without parameters to get all data
            }, 5000);
        }
    });
</script>
@endsection
