<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            font-size: 26px;
            font-weight: bold;
            color: #333;
            margin-bottom: 20px;
            padding: 10px;
            border-bottom: 2px solid #007BFF;
        }
        .sub-header {
            text-align: center;
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:nth-child(odd) {
            background-color: #fff;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #777;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">Audit Report</div>
    <div class="sub-header">Detailed log of all actions performed on the file {{ $file_name }}</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>File ID</th>
                <th>User ID</th>
                <th>Action</th>
                <th>Details</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            @if(count($audit) > 0)
            @foreach ($audit as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->file_id }}</td>
                    <td>{{ $item->user_id }}</td>
                    <td>{{ $item->change_type }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->created_at }}</td>
                    <td>{{ $item->updated_at }}</td>
                </tr>
            @endforeach
            @else
                <tr>
                    <td colspan="7">No audit records found</td>
                </tr>
            @endif
        </tbody>
    </table>
    <div class="footer">
        Generated on {{ now()->toDayDateTimeString() }} | Audit Report System
    </div>
</body>
</html>
