<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cash Flow Statement</title>
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

        .section-box {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 15px;
            background-color: #f9fafb;
        }

        .section-header {
            padding: 10px 15px;
            font-size: 12px;
            font-weight: bold;
            border-bottom: 2px solid;
        }

        .section-header.operating {
            background-color: #fef2f2;
            color: #991b1b;
            border-bottom-color: #fecaca;
        }

        .section-header.investing {
            background-color: #fff7ed;
            color: #9a3412;
            border-bottom-color: #fed7aa;
        }

        .section-header.financing {
            background-color: #fffbeb;
            color: #92400e;
            border-bottom-color: #fde68a;
        }

        .activity-item {
            padding: 8px 15px;
            background-color: white;
            border-bottom: 1px solid #f3f4f6;
        }

        .activity-item table {
            width: 100%;
            border: none;
        }

        .activity-item td {
            border: none;
            padding: 0;
        }

        .activity-name {
            text-align: left;
            color: #4b5563;
        }

        .activity-amount {
            text-align: right;
            font-weight: bold;
            color: #111827;
        }

        .total-line {
            padding: 10px 15px;
            font-weight: bold;
            border-top: 2px solid;
        }

        .total-line.operating {
            background-color: #fee2e2;
            border-top-color: #fca5a5;
        }

        .total-line.investing {
            background-color: #ffedd5;
            border-top-color: #fdba74;
        }

        .total-line.financing {
            background-color: #fef3c7;
            border-top-color: #fcd34d;
        }

        .summary-box {
            margin-top: 20px;
            border-top: 3px solid #9ca3af;
            padding-top: 15px;
        }

        .summary-item {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
        }

        .summary-item.net-cash {
            background-color: #f3f4f6;
            font-weight: bold;
            font-size: 12px;
        }

        .summary-item.beginning-cash {
            background-color: white;
            border: 1px solid #e5e7eb;
        }

        .summary-item.ending-cash {
            background-color: #b91c1c;
            color: white;
            font-weight: bold;
            font-size: 13px;
        }

        .positive-amount {
            color: #15803d;
        }

        .negative-amount {
            color: #dc2626;
        }

        .reconciliation {
            padding: 10px 15px;
            margin-top: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 10px;
        }

        .reconciliation.success {
            background-color: #d1fae5;
            border: 2px solid #10b981;
            color: #065f46;
        }

        .reconciliation.error {
            background-color: #fef3c7;
            border: 2px solid #f59e0b;
            color: #92400e;
        }

        .empty-notice {
            text-align: center;
            padding: 10px;
            font-style: italic;
            color: #9ca3af;
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
        return $amt < 0 ? '(' . number_format(abs($amt), 2) . ')' : number_format($amt, 2);
    }

    $operating = $cashFlow['Operating'];
    $investing = $cashFlow['Investing'];
    $financing = $cashFlow['Financing'];

    $totalOperating = array_sum(array_column($operating, 'balance'));
    $totalInvesting = array_sum(array_column($investing, 'balance'));
    $totalFinancing = array_sum(array_column($financing, 'balance'));

    $netCash = $cashFlow['NetCash'];
    $beginningCash = $cashFlow['BeginningCash'];
    $endingCash = $cashFlow['EndingCash'];
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
        <h2>CASH FLOW STATEMENT</h2>
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

    <?php if (empty($operating) && empty($investing) && empty($financing)): ?>
        <p class="empty-notice" style="padding: 20px; background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 6px;">
            No Cash Flow Records Found
        </p>
    <?php else: ?>
        <!-- Operating Activities -->
        <div class="section-box">
            <div class="section-header operating">CASH FLOWS FROM OPERATING ACTIVITIES</div>
            <?php if (empty($operating)): ?>
                <div class="empty-notice">No operating activities</div>
            <?php else: ?>
                <?php foreach ($operating as $item): ?>
                    <div class="activity-item">
                        <table>
                            <tr>
                                <td class="activity-name"><?= htmlspecialchars($item['account_name']) ?></td>
                                <td class="activity-amount"><?= displayAmount($item['balance']) ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="total-line operating">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left; color: #991b1b;">
                            Net Cash <?= $totalOperating >= 0 ? 'Provided by' : 'Used in' ?> Operating Activities
                        </td>
                        <td style="border: none; text-align: right;" class="<?= $totalOperating >= 0 ? 'positive-amount' : 'negative-amount' ?>">
                            <?= displayAmount($totalOperating) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Investing Activities -->
        <div class="section-box">
            <div class="section-header investing">CASH FLOWS FROM INVESTING ACTIVITIES</div>
            <?php if (empty($investing)): ?>
                <div class="empty-notice">No investing activities</div>
            <?php else: ?>
                <?php foreach ($investing as $item): ?>
                    <div class="activity-item">
                        <table>
                            <tr>
                                <td class="activity-name"><?= htmlspecialchars($item['account_name']) ?></td>
                                <td class="activity-amount"><?= displayAmount($item['balance']) ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="total-line investing">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left; color: #9a3412;">
                            Net Cash <?= $totalInvesting >= 0 ? 'Provided by' : 'Used in' ?> Investing Activities
                        </td>
                        <td style="border: none; text-align: right;" class="<?= $totalInvesting >= 0 ? 'positive-amount' : 'negative-amount' ?>">
                            <?= displayAmount($totalInvesting) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Financing Activities -->
        <div class="section-box">
            <div class="section-header financing">CASH FLOWS FROM FINANCING ACTIVITIES</div>
            <?php if (empty($financing)): ?>
                <div class="empty-notice">No financing activities</div>
            <?php else: ?>
                <?php foreach ($financing as $item): ?>
                    <div class="activity-item">
                        <table>
                            <tr>
                                <td class="activity-name"><?= htmlspecialchars($item['account_name']) ?></td>
                                <td class="activity-amount"><?= displayAmount($item['balance']) ?></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="total-line financing">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left; color: #92400e;">
                            Net Cash <?= $totalFinancing >= 0 ? 'Provided by' : 'Used in' ?> Financing Activities
                        </td>
                        <td style="border: none; text-align: right;" class="<?= $totalFinancing >= 0 ? 'positive-amount' : 'negative-amount' ?>">
                            <?= displayAmount($totalFinancing) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-box">
            <div class="summary-item net-cash">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left;">Net Increase/(Decrease) in Cash</td>
                        <td style="border: none; text-align: right;" class="<?= $netCash >= 0 ? 'positive-amount' : 'negative-amount' ?>">
                            <?= displayAmount($netCash) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="summary-item beginning-cash">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left; color: #4b5563;">Cash at Beginning of Period</td>
                        <td style="border: none; text-align: right; font-weight: bold;">
                            <?= displayAmount($beginningCash) ?>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="summary-item ending-cash">
                <table style="width: 100%; border: none;">
                    <tr>
                        <td style="border: none; text-align: left; color: white;">Cash at End of Period</td>
                        <td style="border: none; text-align: right; color: white;">
                            <?= displayAmount($endingCash) ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Reconciliation Check -->
        <?php
        $calculatedEnding = $beginningCash + $netCash;
        $difference = abs($endingCash - $calculatedEnding);
        $isReconciled = $difference < 0.01;
        ?>
        <div class="reconciliation <?= $isReconciled ? 'success' : 'error' ?>">
            <?php if ($isReconciled): ?>
                <strong>✓ Cash Flow Statement reconciles successfully</strong><br>
                Ending cash (<?= displayAmount($endingCash) ?>) matches calculations
            <?php else: ?>
                <strong>⚠️ Warning: Cash reconciliation error!</strong><br>
                Difference: <?= displayAmount($difference) ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</body>

</html>