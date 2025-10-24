<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #D1D5DB;
            padding: 6px 8px;
            text-align: right;
        }
        th {
            background-color: #B91C1C;
            color: white;
            text-align: left;
        }
        td.text-left {
            text-align: left;
        }
        .totals {
            font-weight: bold;
            background-color: #F3F4F6;
        }
    </style>
</head>
<body>

<h2>Trial Balance</h2>
<p style="text-align:center;">
    <?php
    $label = "All Months & Years";
    if (!empty($month) && !empty($year)) {
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj ? $dateObj->format('F') : $month;
        $label = $monthName . ' ' . $year;
    } elseif (!empty($month)) {
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj ? $dateObj->format('F') : $month;
        $label = $monthName . ' (All Years)';
    } elseif (!empty($year)) {
        $label = 'All Months ' . $year;
    }
    echo $label;
    ?>
</p>

<table>
    <thead>
        <tr>
            <th class="text-left">Account Name</th>
            <th class="text-left">Account Type</th>
            <th>Debit</th>
            <th>Credit</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($trialBalance as $row):
            $totalDebit += $row['total_debit'];
            $totalCredit += $row['total_credit'];
        ?>
            <tr>
                <td class="text-left"><?= htmlspecialchars($row['account_name']) ?></td>
                <td class="text-left"><?= htmlspecialchars($row['account_type']) ?></td>
                <td><?= number_format($row['total_debit'], 2) ?></td>
                <td><?= number_format($row['total_credit'], 2) ?></td>
            </tr>
        <?php endforeach; ?>

        <tr class="totals">
            <td colspan="2">Total</td>
            <td><?= number_format($totalDebit, 2) ?></td>
            <td><?= number_format($totalCredit, 2) ?></td>
        </tr>
    </tbody>
</table>

</body>
</html>
