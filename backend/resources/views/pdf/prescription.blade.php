<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Đơn thuốc</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .title { text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 20px; margin-top: 20px; }
        .info { margin-bottom: 20px; }
        p {
            margin: 0;
        }
        .header {
            display: flex;
            justify-content: space-between;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .instruction {
            font-style: italic;
        }
        .signature {
            margin-top: 50px;
        }
        .footer {
            text-align: right;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <p>{{ $facility->facility_name }}</p>
            <p>{{ $facility->phone }}</p>
            <p>{{ $facility->address }}</p>
        </div>
    </div>
    <div class="title">
        ĐƠN THUỐC
    </div>
    {!! DNS1D::getBarcodeHTML('PRX-' . $medicalRecord->id, 'C128', 2, 60) !!}
    <div class="info">
        <strong>Bệnh nhân:</strong> {{ $patient->name }}<br>
        <strong>Ngày sinh:</strong> {{ $patient->date_of_birth ?? '...' }}<br>
        <strong>Ngày khám:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y') }} <br>
        <strong>Chuẩn đoán:</strong> {{ $medicalRecord->diagnosis }}<br>
    </div>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên thuốc</th>
                <th>Đơn vị</th>
                <th>Só lượng</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($medicines as $index => $medicine)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $medicine->name }}
                        <p class="instruction">{{ $medicine->pivot->usage }}</p>
                    </td>
                    <td>{{ $medicine->unit }}</td>
                    <td>{{ $medicine->pivot->amount }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <strong>Bác sĩ điều trị</strong> <br>
        <p class="signature">{{ $doctor->name }}</p>
    </div>

</body>
</html>
