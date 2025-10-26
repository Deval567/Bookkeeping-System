<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
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
            text-align: right;
        }

        table.statement th {
            background-color: #b91c1c;
            color: white;
            text-align: center;
            font-weight: bold;
        }

        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
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

// Process balances
$assets = [];
$liabilities = [];
$equities = [];
$totalAssets = 0;
$totalLiabilities = 0;
$totalEquity = 0;

foreach ($balances as $bal) {
    $balance = $bal['balance'];
    $type = strtoupper($bal['account_type']);
    
    if ($type == 'ASSET') {
        $assets[] = ['name' => $bal['account_name'], 'balance' => $balance];
        $totalAssets += $balance;
    } elseif ($type == 'LIABILITY') {
        $liabilities[] = ['name' => $bal['account_name'], 'balance' => $balance];
        $totalLiabilities += $balance;
    } elseif ($type == 'EQUITY') {
        $equities[] = ['name' => $bal['account_name'], 'balance' => $balance];
        $totalEquity += $balance;
    }
}

$maxRows = max(count($assets), count($liabilities), count($equities));
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
<h2>Balance Sheet</h2>
<p class="period">
    As of 
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

<?php if (empty($balances)): ?>
    <p class="empty-notice">No Balance Sheet Records Found</p>
<?php else: ?>
    <table class="statement">
        <thead>
            <tr>
                <th style="width: 33.33%;">Assets</th>
                <th style="width: 33.33%;">Liabilities</th>
                <th style="width: 33.33%;">Equity</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                    <td>
                        <?php if (isset($assets[$i])): ?>
                            <?= htmlspecialchars($assets[$i]['name']) ?>: <?= number_format($assets[$i]['balance'], 2) ?>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($liabilities[$i])): ?>
                            <?= htmlspecialchars($liabilities[$i]['name']) ?>: <?= number_format($liabilities[$i]['balance'], 2) ?>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (isset($equities[$i])): ?>
                            <?= htmlspecialchars($equities[$i]['name']) ?>: <?= number_format($equities[$i]['balance'], 2) ?>
                        <?php else: ?>
                            &nbsp;
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endfor; ?>

            <tr class="total-row">
                <td>Total Assets: <?= number_format(abs($totalAssets), 2) ?></td>
                <td colspan="2" style="text-align: center;">
                    Total Liabilities + Equity: <?= number_format(abs($totalLiabilities + $totalEquity), 2) ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>