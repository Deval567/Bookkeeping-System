<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cash Flow Statement</title>
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
            vertical-align: middle;
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

        .net-row {
            background-color: #e5e7eb;
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
$logoPath = dirname(__DIR__) . '/images/logo.jpg';
$logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';

$operating = $cashFlow['Operating'] ?? [];
$investing = $cashFlow['Investing'] ?? [];
$financing = $cashFlow['Financing'] ?? [];

$totalOperating = array_sum(array_column($operating, 'balance'));
$totalInvesting = array_sum(array_column($investing, 'balance'));
$totalFinancing = array_sum(array_column($financing, 'balance'));

$maxRows = max(count($operating), count($investing), count($financing));
$netCash = $cashFlow['NetCash'] ?? ($totalOperating + $totalInvesting + $totalFinancing);
?>

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

<h2>Cash Flow Statement</h2>
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

<?php if (empty($operating) && empty($investing) && empty($financing)): ?>
    <p class="empty-notice">No Cash Flow Records Found</p>
<?php else: ?>
    <table class="statement">
        <thead>
            <tr>
                <th>Operating Activities</th>
                <th>Investing Activities</th>
                <th>Financing Activities</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $maxRows; $i++): ?>
                <tr>
                    <td><?= isset($operating[$i]) ? htmlspecialchars($operating[$i]['name']) . ': ' . number_format($operating[$i]['balance'], 2) : '&nbsp;' ?></td>
                    <td><?= isset($investing[$i]) ? htmlspecialchars($investing[$i]['name']) . ': ' . number_format($investing[$i]['balance'], 2) : '&nbsp;' ?></td>
                    <td><?= isset($financing[$i]) ? htmlspecialchars($financing[$i]['name']) . ': ' . number_format($financing[$i]['balance'], 2) : '&nbsp;' ?></td>
                </tr>
            <?php endfor; ?>

            <tr class="total-row">
                <td>Total Operating: <?= number_format($totalOperating, 2) ?></td>
                <td>Total Investing: <?= number_format($totalInvesting, 2) ?></td>
                <td>Total Financing: <?= number_format($totalFinancing, 2) ?></td>
            </tr>

            <tr class="net-row">
                <td colspan="3" style="text-align:center;">
                    Net Cash Flow: <?= number_format($netCash, 2) ?>
                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>
