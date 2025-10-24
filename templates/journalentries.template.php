<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th,
        td {
            border: 1px solid black;
            padding: 6px;
        }

        th {
            background: #b91c1c;
            color: white;
        }

        /* Red Header */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .italic {
            font-style: italic;
        }

        .gray-bg {
            background: #f5f5f5;
        }
    </style>
    <title>Journal Entries Report</title>
</head>

<body>
<h2>Journal Entries</h2>
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

    <?php if (empty($entries)): ?>
        <p class="text-center italic">No Journal Entries Found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Particulars</th>
                    <th class="text-right">Debit</th>
                    <th class="text-right">Credit</th>
                </tr>
            </thead>

            <?php foreach ($entries as $entry): ?>
                <?php $accounts = $entry['accounts'];
                $rowCount = count($accounts); ?>

                <tbody>
                    <?php for ($i = 0; $i < $rowCount; $i++): ?>
                        <tr>
                            <?php if ($i === 0): ?>
                                <!-- Date Column (Rowspan for grouped accounts) -->
                                <td rowspan="<?= $rowCount ?>">
                                    <?= htmlspecialchars($entry['journal_date']) ?>
                                </td>
                            <?php endif; ?>

                            <td><?= htmlspecialchars($accounts[$i]['account_name']) ?></td>
                            <td class="text-right"><?= number_format($accounts[$i]['debit'], 2) ?></td>
                            <td class="text-right"><?= number_format($accounts[$i]['credit'], 2) ?></td>
                        </tr>
                    <?php endfor; ?>

                    <!-- Description Row -->
                    <tr class="gray-bg">
                        <td colspan="4" class="italic">
                            (<?= htmlspecialchars($entry['description']) ?>)
                        </td>
                    </tr>
                </tbody>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

</body>

</html>