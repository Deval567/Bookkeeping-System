<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Income Statement</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
            padding: 20px;
        }

        .header {
            margin-bottom: 20px;
            border-bottom: 3px solid #b91c1c;
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
            color: #b91c1c;
        }

        .company-info p {
            margin: 0;
            font-size: 10px;
            color: #666;
        }

        .statement-title {
            text-align: center;
            margin: 20px 0 15px 0;
            background-color: #b91c1c;
            color: white;
            padding: 15px;
            border-radius: 5px;
        }

        .statement-title h2 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: bold;
        }

        .statement-title p {
            margin: 0;
            font-size: 11px;
        }

        table.income-statement {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.income-statement td {
            border: 1px solid #e5e7eb;
            padding: 8px 12px;
        }

        .section-header {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 12px;
            color: #374151;
            border-top: 2px solid #9ca3af;
        }

        .account-line td {
            background-color: white;
        }

        .account-line:hover td {
            background-color: #f9fafb;
        }

        .account-name {
            text-align: left;
            padding-left: 20px;
            color: #4b5563;
        }

        .account-amount {
            text-align: right;
            padding-right: 20px;
            color: #111827;
        }

        .total-line {
            background-color: #fef2f2;
            font-weight: bold;
            border-top: 2px solid #9ca3af;
        }

        .total-line td {
            color: #111827;
        }

        .net-income-line {
            background-color: #fee2e2;
            font-weight: bold;
            border-top: 3px solid #991b1b;
            border-bottom: 3px solid #991b1b;
        }

        .net-income-positive {
            color: #15803d;
        }

        .net-income-negative {
            color: #dc2626;
        }

        .empty-notice {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #9ca3af;
            background-color: #f9fafb;
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

// Process income statement data
$revenues = [];
$expenses = [];
$totalRevenue = 0;
$totalExpenses = 0;

foreach ($entries as $entry) {
    $balance = floatval($entry['balance']);
    if (strtoupper($entry['account_type']) === 'REVENUE') {
        $revenues[] = ['name' => $entry['account_name'], 'balance' => $balance];
        $totalRevenue += $balance;
    } elseif (strtoupper($entry['account_type']) === 'EXPENSE') {
        $balance = abs($balance);
        $expenses[] = ['name' => $entry['account_name'], 'balance' => $balance];
        $totalExpenses += $balance;
    }
}

$netIncome = $totalRevenue - $totalExpenses;

function displayAmount($amt) {
    return $amt < 0 ? '(' . number_format(abs($amt), 2) . ')' : number_format($amt, 2);
}
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

<!-- Statement Title -->
<div class="statement-title">
    <h2>INCOME STATEMENT</h2>
    <p>
        For the Period Ended:
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
</div>

<?php if (empty($entries)): ?>
    <p class="empty-notice">No Income Statement Records Found</p>
<?php else: ?>
    <!-- Income Statement Table -->
    <table class="income-statement">
        <tbody>
            <!-- REVENUE SECTION -->
            <tr class="section-header">
                <td colspan="2">REVENUE</td>
            </tr>

            <?php if (empty($revenues)): ?>
                <tr>
                    <td colspan="2" class="empty-notice">No Revenue Records Found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($revenues as $rev): ?>
                    <tr class="account-line">
                        <td class="account-name"><?= htmlspecialchars($rev['name']) ?></td>
                        <td class="account-amount"><?= displayAmount($rev['balance']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="total-line">
                <td class="account-name">Total Revenue</td>
                <td class="account-amount"><?= displayAmount($totalRevenue) ?></td>
            </tr>

            <!-- EXPENSES SECTION -->
            <tr class="section-header">
                <td colspan="2">EXPENSES</td>
            </tr>

            <?php if (empty($expenses)): ?>
                <tr>
                    <td colspan="2" class="empty-notice">No Expense Records Found</td>
                </tr>
            <?php else: ?>
                <?php foreach ($expenses as $exp): ?>
                    <tr class="account-line">
                        <td class="account-name"><?= htmlspecialchars($exp['name']) ?></td>
                        <td class="account-amount"><?= displayAmount($exp['balance']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="total-line">
                <td class="account-name">Total Expenses</td>
                <td class="account-amount"><?= displayAmount($totalExpenses) ?></td>
            </tr>

            <!-- NET INCOME/LOSS -->
            <tr class="net-income-line">
                <td class="account-name" style="font-size: 13px;">
                    <?= $netIncome >= 0 ? 'NET INCOME' : 'NET LOSS' ?>
                </td>
                <td class="account-amount <?= $netIncome >= 0 ? 'net-income-positive' : 'net-income-negative' ?>" style="font-size: 13px;">
                    <?= displayAmount(abs($netIncome)) ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>