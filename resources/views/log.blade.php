<x-admin-layout>

    <div class="container">
        <h1 class="my-2">Logs</h1>

        <div class="text-end">
            <a href="{{route('dashboard')}}" class="btn btn-primary">Show All</a>
        </div>

        <table class="table table-hover">
            <tr>
                <td class="fw-bold">ASIN</td>
                <td>{{ $log->asin }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Summary</td>
                <td>{!!$log->summary !!}</td>
            </tr>
        </table>

    </div>

</x-admin-layout>
