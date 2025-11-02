<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Balance Sheet</title>
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

        .two-column {
            width: 100%;
            border-collapse: collapse;
        }

        .two-column td {
            vertical-align: top;
            padding: 0 10px;
            width: 50%;
        }

        .section-box {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #f9fafb;
        }

        .section-header {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid;
        }

        .section-header.assets {
            color: #991b1b;
            border-bottom-color: #991b1b;
        }

        .section-header.liabilities {
            color: #1e40af;
            border-bottom-color: #1e40af;
        }

        .section-header.equity {
            color: #065f46;
            border-bottom-color: #065f46;
        }

        .account-item {
            padding: 6px 10px;
            margin: 3px 0;
            background-color: white;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }

        .account-item table {
            width: 100%;
            border: none;
        }

        .account-item td {
            border: none;
            padding: 0;
        }

        .account-item .name {
            text-align: left;
            color: #374151;
        }

        .account-item .amount {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }

        .net-income {
            background-color: #d1fae5 !important;
            border: 1px solid #34d399 !important;
            font-weight: bold;
        }

        .net-loss {
            background-color: #fee2e2 !important;
            border: 1px solid #f87171 !important;
            font-weight: bold;
        }

        .total-row {
            background-color: #374151;
            color: white;
            padding: 8px 10px;
            border-radius: 4px;
            margin-top: 10px;
            font-weight: bold;
        }

        .total-row table {
            width: 100%;
            border: none;
        }

        .total-row td {
            border: none;
            padding: 0;
        }

        .total-row.assets {
            background-color: #991b1b;
        }

        .total-row.liabilities {
            background-color: #1e40af;
        }

        .total-row.equity {
            background-color: #065f46;
        }

        .grand-total {
            background-color: #6b21a8;
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            margin-top: 15px;
            font-weight: bold;
            font-size: 13px;
        }

        .balance-check {
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 6px;
            text-align: center;
        }

        .balance-check.balanced {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
        }

        .balance-check.unbalanced {
            background-color: #fee2e2;
            border: 2px solid #ef4444;
            color: #991b1b;
        }

        .empty-notice {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
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

    // Process balances
    $assets = [];
    $liabilities = [];
    $equities = [];
    $netIncome = 0;
    $totalAssets = 0;
    $totalLiabilities = 0;
    $totalEquity = 0;

    foreach ($balances as $bal) {
        $balance = floatval($bal['balance']);
        $type = strtoupper($bal['account_type']);

        // Check if this is the Net Income entry
        if (isset($bal['is_net_income']) && $bal['is_net_income']) {
            $netIncome = $balance;
            $totalEquity += $balance;
        } elseif ($type == 'ASSET') {
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

    function displayAmount($amt)
    {
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
        <h2>BALANCE SHEET</h2>
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

    <?php if (empty($balances)): ?>
        <p class="empty-notice">No Balance Sheet Records Found</p>
    <?php else: ?>
        <!-- Two Column Layout -->
        <table class="two-column">
            <tr>
                <!-- LEFT COLUMN: ASSETS -->
                <td>
                    <div class="section-box">
                        <div class="section-header assets">ASSETS</div>

                        <?php if (empty($assets)): ?>
                            <div style="text-align: center; color: #9ca3af; font-style: italic; padding: 10px;">
                                No assets
                            </div>
                        <?php else: ?>
                            <?php foreach ($assets as $asset): ?>
                                <div class="account-item">
                                    <table>
                                        <tr>
                                            <td class="name"><?= htmlspecialchars($asset['name']) ?></td>
                                            <td class="amount"><?= displayAmount($asset['balance']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="total-row assets">
                            <table>
                                <tr>
                                    <td style="text-align: left;">Total Assets</td>
                                    <td style="text-align: right;"><?= displayAmount($totalAssets) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>

                <!-- RIGHT COLUMN: LIABILITIES + EQUITY -->
                <td>
                    <!-- LIABILITIES Section -->
                    <div class="section-box">
                        <div class="section-header liabilities">LIABILITIES</div>

                        <?php if (empty($liabilities)): ?>
                            <div style="text-align: center; color: #9ca3af; font-style: italic; padding: 10px;">
                                No liabilities
                            </div>
                        <?php else: ?>
                            <?php foreach ($liabilities as $liability): ?>
                                <div class="account-item">
                                    <table>
                                        <tr>
                                            <td class="name"><?= htmlspecialchars($liability['name']) ?></td>
                                            <td class="amount"><?= displayAmount($liability['balance']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <div class="total-row liabilities">
                            <table>
                                <tr>
                                    <td style="text-align: left;">Total Liabilities</td>
                                    <td style="text-align: right;"><?= displayAmount($totalLiabilities) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- EQUITY Section -->
                    <div class="section-box">
                        <div class="section-header equity">EQUITY</div>

                        <?php if (empty($equities) && abs($netIncome) < 0.01): ?>
                            <div style="text-align: center; color: #9ca3af; font-style: italic; padding: 10px;">
                                No equity accounts
                            </div>
                        <?php else: ?>
                            <?php foreach ($equities as $equity): ?>
                                <div class="account-item">
                                    <table>
                                        <tr>
                                            <td class="name"><?= htmlspecialchars($equity['name']) ?></td>
                                            <td class="amount"><?= displayAmount($equity['balance']) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endforeach; ?>

                            <!-- Net Income or Net Loss -->
                            <?php if (abs($netIncome) > 0.01): ?>
                                <div class="account-item <?= $netIncome >= 0 ? 'net-income' : 'net-loss' ?>">
                                    <table>
                                        <tr>
                                            <td class="name">
                                                <?= $netIncome >= 0 ? 'Net Income' : 'Net Loss' ?>
                                            </td>
                                            <td class="amount"><?= displayAmount($netIncome) ?></td>
                                        </tr>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <div class="total-row equity">
                            <table>
                                <tr>
                                    <td style="text-align: left;">Total Equity</td>
                                    <td style="text-align: right;"><?= displayAmount($totalEquity) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Grand Total -->
                    <div class="grand-total">
                        <table>
                            <tr>
                                <td style="text-align: left;">Total Liabilities + Equity</td>
                                <td style="text-align: right;"><?= displayAmount($totalLiabilities + $totalEquity) ?></td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Balance Check -->
        <?php
        $difference = abs($totalAssets - ($totalLiabilities + $totalEquity));
        $isBalanced = $difference < 0.01;
        ?>
        <div class="balance-check <?= $isBalanced ? 'balanced' : 'unbalanced' ?>">
            <?php if ($isBalanced): ?>
                <strong>✓ Balance Sheet is balanced</strong><br>
                Assets (<?= displayAmount($totalAssets) ?>) = Liabilities (<?= displayAmount($totalLiabilities) ?>) + Equity (<?= displayAmount($totalEquity) ?>)
            <?php else: ?>
                <strong>⚠️ Warning: Balance Sheet does not balance!</strong><br>
                Assets: <?= displayAmount($totalAssets) ?> |
                Liabilities + Equity: <?= displayAmount($totalLiabilities + $totalEquity) ?> |
                Difference: <?= displayAmount($difference) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>

</html>