<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            padding: 10px 0;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
        }

        .account-header h3 {
            margin: 0;
            font-size: 14px;
        }

        .account-header .balance {
            font-weight: bold;
            font-size: 14px;
            color: #b91c1c;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
        }

        th {
            background: #b91c1c;
            color: #fff;
            font-weight: bold;
        }

        td {
            background: #fff;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .gray-bg {
            background: #f5f5f5;
            font-style: italic;
        }

        .total-row {
            font-weight: bold;
            background: #f0f0f0;
        }
    </style>
    <title>General Ledger Report</title>
</head>

<body>
    <h2>General Ledger</h2>
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

    <?php if (empty($ledgers)): ?>
        <p class="text-center italic">No Ledger Entries Found.</p>
    <?php else: ?>
        <?php foreach ($ledgers as $ledger): ?>
            <?php
            $finalBalance = end($ledger['entries'])['balance'] ?? 0;
            reset($ledger['entries']);
            ?>
            <div class="account-header">
                <div>
                    <h3><?= htmlspecialchars($ledger['account_name']) ?></h3>
                    <div><?= htmlspecialchars($ledger['account_type']) ?></div>
                </div>
                <div class="balance">Current Balance: <?= number_format($finalBalance, 2) ?></div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Particulars</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ledger['entries'] as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['journal_date']) ?></td>
                            <td>
                                <?= htmlspecialchars($entry['transaction_type'] ?? 'General Entry') ?>
                                <?= !empty($entry['reference_no'] ?? '') ? ' - ' . htmlspecialchars($entry['reference_no']) : '' ?><br>
                                <small><?= htmlspecialchars($entry['description'] ?? '') ?></small>
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