<?php
$title = "Income Statement";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$journal = new JournalEntries($conn, null, null, null, null, null, null);

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$entries = $journal->getIncomeStatement($month, $year);

// Separate Revenues and Expenses
$revenues = [];
$expenses = [];
$totalRevenue = 0;
$totalExpenses = 0;

foreach ($entries as $entry) {
    $balance = $entry['balance'];
    if (strtoupper($entry['account_type']) === 'REVENUE') {
        $revenues[] = ['name' => $entry['account_name'], 'balance' => $balance];
        $totalRevenue += $balance;
    } elseif (strtoupper($entry['account_type']) === 'EXPENSE') {
        // Make expenses positive
        $balance = abs($balance);
        $expenses[] = ['name' => $entry['account_name'], 'balance' => $balance];
        $totalExpenses += $balance;
    }
}

$maxRows = max(count($revenues), count($expenses));
$netIncome = $totalRevenue - $totalExpenses;
?>

<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold"><?= $title ?></h2>
            <p class="text-gray-600">Summary of Revenues and Expenses.</p>
        </div>

        <!-- Filter Form -->
        <div class="bg-white p-4 rounded shadow">
            <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Month -->
                <div class="sm:w-48">
                    <label class="block text-sm text-gray-700 mb-1">Filter by Month</label>
                    <select name="month" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                        <option value="">--All--</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Year -->
                <div class="sm:w-48">
                    <label class="block text-sm text-gray-700 mb-1">Filter by Year</label>
                    <select name="year" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                        <option value="">--All--</option>
                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Clear button -->
                <div class="flex gap-2 pt-5 sm:mt-0">
                    <?php if ($month || $year): ?>
                        <a href="?"
                            class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-200">
                            Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
<?php if (empty($entries)): ?>
    <div class="bg-white rounded-lg shadow p-8 text-center">
        <p class="text-gray-500 font-semibold">No Income Statement Records Found</p>
    </div>
<?php else: ?>
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-red-700 text-white">
                    <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Revenue</th>
                    <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Expenses</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < $maxRows; $i++): ?>
                    <tr class="hover:scale-[1.02] transition-transform duration-150 hover:bg-gray-100">
                        <td class="py-2 px-4 border-r text-right font-semibold border-gray-200">
                            <?php if (isset($revenues[$i])): ?>
                                <?= htmlspecialchars($revenues[$i]['name']) ?>: <?= number_format($revenues[$i]['balance'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-4 text-right font-semibold border-r border-gray-200">
                            <?php if (isset($expenses[$i])): ?>
                                <?= htmlspecialchars($expenses[$i]['name']) ?>: <?= number_format($expenses[$i]['balance'], 2) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endfor; ?>

                <tr class="bg-gray-50 font-bold">
                    <td class="py-2 px-4 border-t text-right border-gray-300">Total Revenue = <?= number_format($totalRevenue, 2) ?></td>
                    <td class="py-2 px-4 border-t text-right border-gray-300">Total Expenses = <?= number_format($totalExpenses, 2) ?></td>
                </tr>

                <tr class="bg-gray-100 font-bold">
                    <td colspan="2" class="py-2 px-4 border-t text-center border-gray-300">
                        <?php
                        if ($netIncome >= 0) {
                            echo "Net Income = " . number_format($netIncome, 2);
                        } else {
                            echo "Net Loss = " . number_format(abs($netIncome), 2);
                        }
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end my-4">
        <button
            command="show-modal"
            commandfor="download-dialog"
            class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transition duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <span>Download PDF</span>
        </button>
    </div>
<?php endif; ?>

</main>

<!-- Download Dialog -->
<el-dialog>
    <dialog id="download-dialog" aria-labelledby="download-dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-green-500 size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                            </svg>
                        </div>

                        <div class="text-left">
                            <h3 id="download-dialog-title" class="text-lg font-semibold text-gray-900">Download Income Statement</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Do you want to download a PDF of Income Statement for
                                <span class="font-semibold">
                                    <?php
                                    $label = "All Months & Years";
                                    if (!empty($month) && !empty($year)) {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " " . $year;
                                    } elseif (!empty($month)) {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " (all Years)";
                                    } elseif (!empty($year)) {
                                        $label = "all Months " . $year;
                                    }
                                    echo $label;
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form (Hidden Inputs) -->
                <form action="../controllers/trialbalance.controller.php" method="POST" target="_blank" class="px-6 pb-4">
                    <input type="hidden" name="action" value="download_pdf">
                    <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
                    <input type="hidden" name="year" value="<?= htmlspecialchars($year) ?>">

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Download PDF
                        </button>
                        <button type="button" command="close" commandfor="download-dialog"
                            class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>
            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>
