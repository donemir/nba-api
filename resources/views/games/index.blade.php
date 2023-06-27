<!DOCTYPE html>
<html>
<head>
    <title>NBA Games</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div id="results" class="container mt-5">

        <h1>Hi!</h1>
        <p>
            I tried this test using plain PHP, and it worked, but I didn't find it enjoyable. As a fun experiment, I attempted to achieve similar results in Laravel, and here we are. The API endpoints require at least one parameter, so I set the season to 2022.
            <br>
            Cheers,
            <br>
            Amir
        </p>
          
        <hr>

        <h1>Data Processing</h1>

        <form method="GET">
            <div class="row mb-3">
                <div class="col">
                    <label for="date" class="form-label">Filter by Date:</label>
                    <input type="date" name="date" id="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-auto">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="/" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Teams</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>Arena</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gamesPaginated as $game)
                    <tr>
                        <td>{{ $game['id'] }}</td>
                        <td>
                            <a href="?date={{ $game['date']['start'] }}">
                                {{ $game['date']['start'] }}
                            </a>
                        </td>
                        <td>{{ $game['teams']['visitors']['name'] }} vs {{ $game['teams']['home']['name'] }}</td>
                        <td>{{ $game['status']['long'] }}</td>
                        <td>{{ $game['scores']['visitors']['points'] }} - {{ $game['scores']['home']['points'] }}</td>
                        <td>{{ $game['arena']['name'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $gamesPaginated->links() }}
        </div>
    </div>
</body>
</html>
