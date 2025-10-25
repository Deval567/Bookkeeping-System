<?php
$title = "Balance Sheet";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$journal = new JournalEntries($conn, null, null, null, null, null, null);

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$balances = $journal->getBalanceSheet($month, $year);
?>

<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold"><?= $title ?></h2>
            <p class="text-gray-600">Summary of account balances.</p>
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

    <!-- Balance Sheet Table -->
    <?php if (empty($balances)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Balance Sheet Records Found</p>
        </div>
    <?php else: ?>
        <?php
        // Separate accounts by type
        $assets = [];
        $liabilities = [];
        $equities = [];

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;

        foreach ($balances as $bal) {
            $balance = $bal['balance']; // keep actual sign
            $type = strtoupper($bal['account_type']);
            if ($type == 'ASSET') {
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

        $maxRows = max(count($assets), count($liabilities), count($equities));
        ?>

        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 bg-gray-50">
                <h2 class="text-2xl font-semibold text-gray-800">Balance Sheet</h2>
                <p class="text-gray-500 mt-1 text-sm">
                    As of
                    <?= !empty($month) ? date('F', mktime(0, 0, 0, intval($month), 1)) : "all months" ?>
                    <?= !empty($year) ? htmlspecialchars($year) : "all years" ?>
                </p>
            </div>

            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-red-700 text-white">
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Assets</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Liabilities</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Equity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < $maxRows; $i++): ?>
                        <tr class="hover:scale-[1.02] transition-transform duration-150 hover:bg-gray-100">
                            <!-- Asset Column -->
                            <td class="py-2 px-4 border-r font-semibold border-gray-200">
                                <?php if (isset($assets[$i])): ?>
                                    <?= htmlspecialchars($assets[$i]['name']) ?>: <?= number_format($assets[$i]['balance'], 2) ?>
                                <?php endif; ?>
                            </td>

                            <!-- Liability Column -->
                            <td class="py-2 px-4 border-r font-semibold border-gray-200">
                                <?php if (isset($liabilities[$i])): ?>
                                    <?= htmlspecialchars($liabilities[$i]['name']) ?>: <?= number_format($liabilities[$i]['balance'], 2) ?>
                                <?php endif; ?>
                            </td>

                            <!-- Equity Column -->
                            <td class="py-2 px-4 font-semibold border-r border-gray-200">
                                <?php if (isset($equities[$i])): ?>
                                    <?= htmlspecialchars($equities[$i]['name']) ?>: <?= number_format($equities[$i]['balance'], 2) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endfor; ?>

                    <!-- Totals Row -->
                    <tr class="bg-gray-50 font-bold">
                        <td class="py-2 px-4 border-t text-right border-gray-300">Total Assets = <?= number_format(abs($totalAssets), 2) ?></td>
                        <td colspan="2" class="py-2 px-4 border-t text-center border-gray-300">Total Liabilities + Equity = <?= number_format(abs($totalLiabilities + $totalEquity), 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Download Button -->
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

<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>

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
                            <h3 id="download-dialog-title" class="text-lg font-semibold text-gray-900">Download Balance Sheet</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Do you want to download a PDF of Balance Sheet for
                                <span class="font-semibold">
                                    <?php
                                    $label = "All Months & Years"; // Default value

                                    // Both month and a specific year
                                    if (!empty($month) && !empty($year) && $year !== 'all') {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " " . $year;

                                        // Month selected but year is empty or "all"
                                    } elseif (!empty($month) && (empty($year) || $year === 'all')) {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " (All Years)";

                                        // Year selected but month is empty
                                    } elseif (empty($month) && !empty($year) && $year !== 'all') {
                                        $label = "All Months " . $year;
                                    }

                                    echo $label;
                                    ?>
                                </span>
                            </p>

                        </div>
                    </div>
                </div>

                <!-- Form (Only Hidden Inputs) -->
                <form action="../controllers/generalledger.controller.php" method="POST" target="_blank" class="px-6 pb-4">
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