<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Sheet</title>
    <link rel="icon" href="https://www.davaocity.gov.ph/wp-content/uploads/2020/06/cropped-davao-city-logo-32x32.png" sizes="32x32">
</head>
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        line-height: 1.6;
        font-weight: bold;
    }

    input {
        border: none;
        width: 80px;
        font-size: 12px;
        font-weight: bold;
        text-align: center;
    }

    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 12px;
    }

    p {
        margin-top: 0;
        margin-bottom: 0;
    }

    @media print {
        .content {
            margin-top: 2cm;
        }

        .page-break {
            page-break-before: always;
        }
    }

    .container {
        height: 10in;
        margin: 0 auto;
        font-size: 12px;
    }

    .header {
        text-align: center;
        padding: 20px 0;
    }

    .header h1 {
        margin: 0;
    }

    .footer {
        text-align: center;
        margin-top: 20px;
    }

    .footer p {
        margin: 0;
    }

    .border {
        border: 1px solid black;
    }

    .bb {
        border-bottom: 1px solid black;
    }

    .center {
        text-align: center;
    }

    .date {
        padding-right: 20px;
    }

    .align-left {
        text-align: left;
    }
</style>

<body>
    @foreach ($weekdays as $weekday)
    <div class="container">
        @php $employeeCount = count($employees); @endphp
        @for ($i = 0; $i < $employeeCount; $i+=7)
        <div class="header">
            <p>Republic of the Philippines</p>
            <p>City of Davao</p>
            <p>OFFICE OF THE SANGGUNIANG PANLUNGSOD</p>
            <p>DAILY ATTENDANCE LOG SHEET</p>
            <p>Office of Vice Mayor Atty. J. Melchor B. Quitain Jr</p>
        </div>
        <div class="content">
            <table>
                <tr>
                    <th>Date: {{ \Carbon\Carbon::parse($weekday)->isValid() ? \Carbon\Carbon::parse($weekday)->format('Y-m-d') : 'Invalid Date' }}</th>
                    <th></th>
                    <th></th>
                    <th>Morning</th>
                    <th></th>
                    <th>Afternoon</th>
                </tr>
                <tr>
                    <th class="border" style="width: 100px;">SEQ.</th>
                    <th class="border">EMP. NO.</th>
                    <th class="border" style="width: 150px;">EMPLOYEE'S NAME</th>
                    <th class="border">IN/OUT<br>SIGNATURE</th>
                    <th class="border" style="width: 10px;">REMARKS</th>
                    <th class="border">IN/OUT<br>SIGNATURE</th>
                    <th class="border" style="width: 10px;">REMARKS</th>
                </tr>
                @for ($j = $i; $j < min($i + 7, $employeeCount); $j++)
                    @php
                        $employee = $employees[$j];
                        $isOnLeave = false;
                        $isWorkFromHome = false;
                        $leaveType = '';

                        if ($employee->leaves && !$employee->leaves->isEmpty()) {
                            $leaveDates = $employee->leaves->map(function ($leave) {
                                return $leave->only(['start_date', 'end_date', 'type']);
                            });

                            foreach ($leaveDates as $index => $date) {
                                $startDate = \Carbon\Carbon::parse($date['start_date']);
                                $endDate = \Carbon\Carbon::parse($date['end_date']);
                                if (\Carbon\Carbon::parse($weekday)->between($startDate, $endDate)) {
                                    $isOnLeave = true;
                                    $leaveType = strtoupper($date['type']);
                                    break;
                                }
                            }
                        }
//
                        if ($employee->workFromHomes && !$employee->workFromHomes->isEmpty()) {
                            $workFromHomeDates = $employee->workFromHomes->map(function ($workFromHome) {
                                return $workFromHome->only(['start_date', 'end_date']);
                            });

                            foreach ($workFromHomeDates as $index => $date) {
                                $startDate = \Carbon\Carbon::parse($date['start_date']);
                                $endDate = \Carbon\Carbon::parse($date['end_date']);
                                if (\Carbon\Carbon::parse($weekday)->between($startDate, $endDate)) {
                                    $isWorkFromHome = true;
                                    break;
                                }
                            }
                        }
                    @endphp
                    <tr class="border">
                        <td class="center border">{{ $j + 1 }}</td>
                        <td class="center border">{{ $employee->employee_id }}</td>
                        <td class="center border">{{ $employee->full_name }}</td>
                        <td>
                            <table>
                                <tr class="bb">
                                    @if($isOnLeave || $isWorkFromHome)
                                        <td>IN:</td>
                                    @else
                                        <td>IN:{{ rand(7, 7) }}:{{ str_pad(rand(30, 58), 2, '0', STR_PAD_LEFT) }} AM</td>
                                    @endif
                                </tr>
                                <tr class="bb">
                                    <td>SIG:</td>
                                </tr>
                                <tr class="bb">
                                    @if($isOnLeave || $isWorkFromHome)
                                        <td>IN:</td>
                                    @else
                                        <td>Out:{{ rand(12, 12) }}:{{ str_pad(rand(5, 10), 2, '0', STR_PAD_LEFT) }} PM</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>SIG:</td>
                                </tr>
                            </table>
                        </td>
                        <td class="center border">
                            @if($isOnLeave)
                                Leave ({{ $leaveType }})
                            @elseif($isWorkFromHome)
                                Work From Home
                            @endif
                        </td>
                        <td>
                            <table>
                                <tr class="bb">
                                    @if($isOnLeave || $isWorkFromHome)
                                        <td>IN:</td>
                                    @else
                                        <td>IN: {{ rand(12, 12) }}:{{ str_pad(rand(40, 50), 2, '0', STR_PAD_LEFT) }} PM</td>
                                    @endif
                                </tr>
                                <tr class="bb">
                                    <td>SIG:</td>
                                </tr>
                                <tr class="bb">
                                    @if($isOnLeave || $isWorkFromHome)
                                        <td>Out:</td>
                                    @else
                                        <td>Out: {{ rand(5, 5) }}:{{ str_pad(rand(5, 15), 2, '0', STR_PAD_LEFT) }} PM</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>SIG:</td>
                                </tr>
                            </table>
                        </td>
                        <td class="center border">
                            @if($isOnLeave)
                                Leave ({{ $leaveType }})
                            @elseif($isWorkFromHome)
                                Work From Home
                            @endif
                        </td>
                    </tr>
                @endfor
            </table>
        </div>
        <table>
            <tr>
                <th>
                    <p>Certified true &amp; correct:</p>
                    <br>
                    <p class="pdf-center">DIANA ANN W. QUITAIN</p>
                    <p class="pdf-center">Chief of Staff</p>
                </th>
                <th>
                    <p>Attested:</p>
                    <br>
                    <p class="pdf-center">ATTY. J. MELCHOR B. QUITAIN JR.</p>
                    <p class="pdf-center">City Vice Mayor</p>
                </th>
            </tr>
        </table>
        @if ($j < $employeeCount && (($employeeCount - $j) < 7 || $weekday !== end($weekdays)))
            <div class="page-break"></div>
        @endif
        @endfor
    </div>
    @endforeach
</body>

</html>
