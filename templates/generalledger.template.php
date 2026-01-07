<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>General Ledger</title>
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

        .account-section {
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }

        .account-header {
            background-color: white;
            border-bottom: 2px solid #d1d5db;
            padding: 12px 15px;
        }

        .account-info h3 {
            margin: 0 0 3px 0;
            font-size: 14px;
            font-weight: bold;
            color: #111827;
        }

        .account-info p {
            margin: 0;
            font-size: 10px;
            color: #6b7280;
        }

        table.ledger-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.ledger-table th,
        table.ledger-table td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }

        table.ledger-table th {
            background-color: #b91c1c;
            color: white;
            font-weight: bold;
            text-align: left;
            font-size: 11px;
        }

        table.ledger-table th.text-right {
            text-align: right;
        }

        .entry-row {
            background-color: white;
        }

        .entry-date {
            color: #111827;
            font-weight: 500;
        }

        .entry-particulars .main {
            color: #111827;
            font-weight: 500;
        }

        .entry-particulars .reference {
            color: #6b7280;
        }

        .entry-particulars .description {
            color: #6b7280;
            font-size: 9px;
            display: block;
            margin-top: 2px;
        }

        .amount-cell {
            text-align: right;
            font-weight: 500;
            color: #111827;
        }

        .total-row {
            font-weight: bold;
            background-color: #f9fafb;
            border-top: 2px solid #d1d5db;
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
        <h2>GENERAL LEDGER</h2>
        <p>
            For the Period of
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
    </div>

    <?php if (empty($ledgers)): ?>
        <p class="empty-notice">No Ledger Entries Found</p>
    <?php else: ?>
        <?php foreach ($ledgers as $ledger): ?>

            <!-- Account Section -->
            <div class="account-section">
                <!-- Account Header -->
                <div class="account-header">
                    <div class="account-info">
                        <h3><?= htmlspecialchars($ledger['account_name']) ?></h3>
                        <p><?= htmlspecialchars($ledger['account_type']) ?></p>
                    </div>
                </div>

                <!-- Ledger Table -->
                <table class="ledger-table">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Date</th>
                            <th style="width: 50%;">Particulars</th>
                            <th style="width: 17.5%;" class="text-right">Debit</th>
                            <th style="width: 17.5%;" class="text-right">Credit</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ledger['entries'] as $entry): ?>
                            <tr class="entry-row">
                                <td class="entry-date">
                                    <?= date('Y-m-d', strtotime($entry['journal_date'])) ?>
                                </td>
                                <td class="entry-particulars">
                                    <span class="main">
                                        <?= htmlspecialchars($entry['transaction_type'] ?? 'General Entry') ?>
                                    </span>
                                    <?php if (!empty($entry['reference_no'] ?? '')): ?>
                                        <span class="reference"> - <?= htmlspecialchars($entry['reference_no']) ?></span>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['description'] ?? '')): ?>
                                        <span class="description"><?= htmlspecialchars($entry['description']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="amount-cell">
                                    <?= $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '0.00' ?>
                                </td>
                                <td class="amount-cell">
                                    <?= $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '0.00' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <!-- Total Row -->
                        <tr class="total-row">
                            <td colspan="2">
                                <strong>Total for <?= htmlspecialchars($ledger['account_name']) ?></strong>
                            </td>
                            <td class="amount-cell">
                                <strong><?= number_format(array_sum(array_column($ledger['entries'], 'debit')), 2) ?></strong>
                            </td>
                            <td class="amount-cell">
                                <strong><?= number_format(array_sum(array_column($ledger['entries'], 'credit')), 2) ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        <?php endforeach; ?>
    <?php endif; ?>

</body>

</html>