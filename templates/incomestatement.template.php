<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .header td {
            border: none;
            padding: 0;
        }

        .header img {
            max-width: 80px;
            max-height: 80px;
        }

        .company-info h1 {
            margin: 0 0 5px 0;
            font-size: 18px;
            font-weight: bold;
        }

        .company-info p {
            margin: 0;
            font-size: 11px;
            color: #666;
        }

        h2 {
            text-align: center;
            margin: 15px 0 5px 0;
            font-size: 16px;
        }

        .period {
            text-align: center;
            margin: 0 0 20px 0;
            font-size: 11px;
            font-style: italic;
        }

        table.statement {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.statement th,
        table.statement td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        table.statement th {
            background-color: #b91c1c;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        table.statement td {
            text-align: right;
        }

        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .net-row {
            background-color: #e5e7eb;
            font-weight: bold;
            text-align: center !important;
        }

        .empty-notice {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>

<?php
// Get logo as base64
$logoPath = dirname(__DIR__) . '/images/logo.jpg';
$logoData = '';
if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
}

// Process entries
$revenues = [];
$expenses = [];
$totalRevenue = 0;
$totalExpenses = 0;

foreach ($entries as $entry) {
    $balance = $entry['balance'];

    if (strtoupper($entry['account_type']) === 'REVENUE') {
        $revenues[] = [
            'name' => $entry['account_name'],
            'balance' => $balance
        ];
        $totalRevenue += $balance;
    }

    if (strtoupper($entry['account_type']) === 'EXPENSE') {
        $balance = abs($balance);
        $expenses[] = [
            'name' => $entry['account_name'],
            'balance' => $balance
        ];
        $totalExpenses += $balance;
    }
}

$maxRows = max(count($revenues), count($expenses));
$netIncome = $totalRevenue - $totalExpenses;
?>

<!-- Header -->
<div class="header">
    <table>
        <tr>
            <td style="width: 90px;">
                <?php if ($logoData): ?>
                    <img src="data:image/jpeg;base64,<?= $logoData ?>" alt="Logo">
                <?php endif; ?>
            </td>
            <td>
                <div class="company-info">
                    <h1>JJ&C Stainless Steel Fabrication Services</h1>
                    <p>Gonzaga Ext., Barangay Taculing, Bacolod City, Philippines</p>
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Title -->
<h2>Income Statement</h2>
<p class="period">
    For the period ending 
    <?php 
    if (!empty($month) && !empty($year)) {
        echo date('F', mktime(0, 0, 0, intval($month), 1)) . ' ' . $year;
    } elseif (!empty($month)) {
        echo date('F', mktime(0, 0, 0, intval($month), 1)) . ' (All Years)';
    } elseif (!empty($year)) {
        echo 'All Months ' . $year;
    } else {
        echo 'All Periods';
    }
    ?>
</p>

<?php if (empty($entries)): ?>
    <p class="empty-notice">No Income Statement Records Found</p>
<?php else: ?>
    <table class="statement">
        <thead>
            <tr>
                <th style="width: 50%;">Revenue</th>
                <th style="width: 50%;">Expenses</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                    <td>
                        <?php if (isset($revenues[$i])): ?>
                            <?= htmlspecialchars($revenues[$i]['name']) ?>: <?= number_format($revenues[$i]['balance'], 2) ?>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($expenses[$i])): ?>
                            <?= htmlspecialchars($expenses[$i]['name']) ?>: <?= number_format($expenses[$i]['balance'], 2) ?>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>

            <tr class="total-row">
                <td>Total Revenue: <?= number_format($totalRevenue, 2) ?></td>
                <td>Total Expenses: <?= number_format($totalExpenses, 2) ?></td>
            </tr>

            <tr class="net-row">
                <td colspan="2">
                    <?php if ($netIncome >= 0): ?>
                        Net Income: <?= number_format($netIncome, 2) ?>
                    <?php else: ?>
                        Net Loss: <?= number_format(abs($netIncome), 2) ?>
                    <?php endif; ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>