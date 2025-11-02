<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Journal Entries</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding: 20px;
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
            margin-bottom: 15px;
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
        }

        .text-right {
            text-align: right;
        }

        .gray-bg {
            background-color: #f3f4f6;
            font-size: 11px;
            padding: 6px 8px !important;
        }

        .gray-bg strong {
            font-weight: bold;
            color: #111827;
        }

        .gray-bg .ref {
            font-weight: 600;
            color: #374151;
        }

        .gray-bg .desc {
            font-style: italic;
            color: #6b7280;
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

<!-- Statement Title -->
    <div class="statement-title">
        <h2>JOURNAL ENTRIES</h2>
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

<?php if (empty($entries)): ?>
    <p class="empty-notice">No Journal Entries Found</p>
<?php else: ?>
    <table class="statement">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 40%;">Particulars</th>
                <th style="width: 22.5%;" class="text-right">Debit</th>
                <th style="width: 22.5%;" class="text-right">Credit</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $entry): ?>
                <?php 
                $accounts = $entry['accounts'];
                $rowCount = count($accounts); 
                ?>
                
                <?php for ($i = 0; $i < $rowCount; $i++): ?>
                    <tr>
                        <?php if ($i === 0): ?>
                            <td rowspan="<?= $rowCount ?>">
                                <?= htmlspecialchars($entry['journal_date']) ?>
                            </td>
                        <?php endif; ?>
                        
                        <td><?= htmlspecialchars($accounts[$i]['account_name']) ?></td>
                        <td class="text-right"><?= number_format($accounts[$i]['debit'], 2) ?></td>
                        <td class="text-right"><?= number_format($accounts[$i]['credit'], 2) ?></td>
                    </tr>
                <?php endfor; ?>
                
                <tr class="gray-bg">
                    <td colspan="4">
                        <strong><?= htmlspecialchars($entry['transaction_name'] ?? 'General Entry') ?></strong>
                        <?php if (!empty($entry['reference_no'])): ?>
                            - Ref# <span class="ref"><?= htmlspecialchars($entry['reference_no']) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($entry['description'])): ?>
                            <br><span class="desc">(<?= htmlspecialchars($entry['description']) ?>)</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>