<!DOCTYPE html>
<html>
<head>
    <title>GRN Entries</title>
    <style>
        body { font-family: notosanssinhala; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; font-size: 12px; }
        th { background-color: #ccc; }
    </style>
</head>
<body>
    <h3>GRN Entries</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Supplier Code</th>
                <th>Item Code</th>
                <th>Item Name</th>
                <th>Packs</th>
                <th>Weight (kg)</th>
                <th>Per KG Price</th>
                <th>Txn Date</th>
                <th>GRN No</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td>{{ $entry['id'] }}</td>
                <td>{{ $entry['code'] }}</td>
                <td>{{ $entry['supplier_code'] }}</td>
                <td>{{ $entry['item_code'] }}</td>
                <td>{{ $entry['item_name'] }}</td>
                <td>{{ $entry['packs'] }}</td>
                <td>{{ $entry['weight'] }}</td>
                <td>{{ $entry['per_kg_price'] }}</td>
                <td>{{ $entry['txn_date'] }}</td>
                <td>{{ $entry['grn_no'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
