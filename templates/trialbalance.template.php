<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Trial Balance</title>
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

        table.trial-balance {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.trial-balance th,
        table.trial-balance td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }

        table.trial-balance th {
            background-color: #b91c1c;
            color: white;
            font-weight: bold;
            text-align: left;
        }

        table.trial-balance th.text-right {
            text-align: right;
        }

        .account-row:hover {
            background-color: #f9fafb;
        }

        .account-name {
            color: #111827;
            font-weight: 500;
        }

        .account-type {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-asset {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-liability {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .badge-equity {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-expense {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-revenue {
            background-color: #e9d5ff;
            color: #6b21a8;
        }

        .amount-cell {
            text-align: right;
            font-weight: 500;
            color: #111827;
        }

        .total-row {
            background-color: #f3f4f6;
            font-weight: bold;
            border-top: 2px solid #9ca3af;
        }

        .warning-row {
            background-color: #fef2f2;
            border: 2px solid #fca5a5;
        }

        .warning-row td {
            color: #991b1b;
            font-weight: bold;
        }

        .empty-notice {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #9ca3af;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
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

    function displayAmount($amt)
    {
        return $amt > 0 ? number_format($amt, 2) : '—';
    }

    $totalDebit = 0;
    $totalCredit = 0;

    foreach ($trialBalance as $row) {
        $totalDebit += $row['display_debit'];
        $totalCredit += $row['display_credit'];
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
        <h2>TRIAL BALANCE</h2>
        <p>
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
    </div>

    <?php if (empty($trialBalance)): ?>
        <p class="empty-notice">No Trial Balance Records Found</p>
    <?php else: ?>
        <table class="trial-balance">
            <thead>
                <tr>
                    <th style="width: 40%;">Account Name</th>
                    <th style="width: 20%;">Account Type</th>
                    <th class="text-right" style="width: 20%;">Debit</th>
                    <th class="text-right" style="width: 20%;">Credit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($trialBalance as $row): ?>
                    <tr class="account-row">
                        <td class="account-name"><?= htmlspecialchars($row['account_name']) ?></td>
                        <td class="account-type">
                            <?php
                            $type = $row['account_type'];
                            $badgeClass = [
                                'Asset' => 'badge-asset',
                                'Liability' => 'badge-liability',
                                'Equity' => 'badge-equity',
                                'Expense' => 'badge-expense',
                                'Revenue' => 'badge-revenue',
                            ];
                            $class = $badgeClass[$type] ?? 'badge-asset';
                            ?>
                            <span class="badge <?= $class ?>">
                                <?= strtoupper($type) ?>
                            </span>
                        </td>
                        <td class="amount-cell"><?= displayAmount($row['display_debit']) ?></td>
                        <td class="amount-cell"><?= displayAmount($row['display_credit']) ?></td>
                    </tr>
                <?php endforeach; ?>

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="2"><strong>TOTAL</strong></td>
                    <td class="amount-cell"><strong><?= number_format($totalDebit, 2) ?></strong></td>
                    <td class="amount-cell"><strong><?= number_format($totalCredit, 2) ?></strong></td>
                </tr>

                <!-- Balance Check -->
                <?php
                $difference = abs($totalDebit - $totalCredit);
                if ($difference > 0.01):
                ?>
                    <tr class="warning-row">
                        <td colspan="2">
                            ⚠️ Trial Balance is OUT OF BALANCE
                        </td>
                        <td colspan="2" class="amount-cell">
                            Difference: <?= number_format($difference, 2) ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php endif; ?>

</body>

</html>