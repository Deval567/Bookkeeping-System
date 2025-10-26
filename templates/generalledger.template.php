<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>General Ledger</title>
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

        .account-header {
            background-color: #f3f4f6;
            padding: 10px;
            margin-top: 20px;
            margin-bottom: 5px;
            border: 1px solid #333;
            border-radius: 4px;
        }

        .account-header h3 {
            margin: 0 0 3px 0;
            font-size: 14px;
            font-weight: bold;
        }

        .account-header .info {
            font-size: 11px;
            color: #666;
        }

        .account-header .balance {
            float: right;
            font-weight: bold;
            color: #b91c1c;
            font-size: 13px;
        }

        table.statement {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table.statement th,
        table.statement td {
            border: 1px solid #333;
            padding: 8px;
        }

        table.statement th {
            background-color: #b91c1c;
            color: white;
            font-weight: bold;
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .total-row {
            font-weight: bold;
            background-color: #f3f4f6;
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
<h2>General Ledger</h2>
<p class="period">
    For the period of 
    <?php 
    if (!empty($month) && !empty($year)) {
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj ? $dateObj->format('F') : $month;
        echo $monthName . ' ' . $year;
    } elseif (!empty($month)) {
        $dateObj = DateTime::createFromFormat('!m', $month);
        $monthName = $dateObj ? $dateObj->format('F') : $month;
        echo $monthName . ' (All Years)';
    } elseif (!empty($year)) {
        echo 'All Months ' . $year;
    } else {
        echo 'All Periods';
    }
    ?>
</p>

<?php if (empty($ledgers)): ?>
    <p class="empty-notice">No Ledger Entries Found</p>
<?php else: ?>
    <?php foreach ($ledgers as $ledger): ?>
        <?php
        $finalBalance = end($ledger['entries'])['balance'] ?? 0;
        reset($ledger['entries']);
        ?>
        
        <!-- Account Header -->
        <div class="account-header">
            <span class="balance">Balance: <?= number_format($finalBalance, 2) ?></span>
            <h3><?= htmlspecialchars($ledger['account_name']) ?></h3>
            <div class="info"><?= htmlspecialchars($ledger['account_type']) ?></div>
        </div>

        <table class="statement">
            <thead>
                <tr>
                    <th style="width: 12%;">Date</th>
                    <th style="width: 38%;">Particulars</th>
                    <th style="width: 16%;" class="text-right">Debit</th>
                    <th style="width: 16%;" class="text-right">Credit</th>
                    <th style="width: 18%;" class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ledger['entries'] as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['journal_date']) ?></td>
                        <td>
                            <?= htmlspecialchars($entry['transaction_type'] ?? 'General Entry') ?>
                            <?php if (!empty($entry['reference_no'] ?? '')): ?>
                                - <?= htmlspecialchars($entry['reference_no']) ?>
                            <?php endif; ?>
                            <br>
                            <small style="color: #666;"><?= htmlspecialchars($entry['description'] ?? '') ?></small>
                        </td>
                        <td class="text-right"><?= number_format($entry['debit'], 2) ?></td>
                        <td class="text-right"><?= number_format($entry['credit'], 2) ?></td>
                        <td class="text-right"><?= number_format($entry['balance'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <tr class="total-row">
                    <td colspan="2">Total for <?= htmlspecialchars($ledger['account_name']) ?></td>
                    <td class="text-right"><?= number_format(array_sum(array_column($ledger['entries'], 'debit')), 2) ?></td>
                    <td class="text-right"><?= number_format(array_sum(array_column($ledger['entries'], 'credit')), 2) ?></td>
                    <td class="text-right"><?= number_format($finalBalance, 2) ?></td>
                </tr>
            </tbody>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>