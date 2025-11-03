<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
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
            <p class="text-gray-600">Assets = Liabilities + Equity</p>
        </div>

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

    <?php if (empty($balances)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Balance Sheet Records Found</p>
        </div>
    <?php else: ?>
        <?php
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

        <div class="bg-white rounded-lg shadow">
            <!-- Header -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-red-600 to-red-700">
                <h2 class="text-3xl font-bold text-white">Balance Sheet</h2>
                <p class="text-red-100 mt-1">
                    As of
                    <?= !empty($month) ? date('F', mktime(0, 0, 0, intval($month), 1)) : "All Months" ?>
                    <?= !empty($year) ? htmlspecialchars($year) : "All Years" ?>
                </p>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">

                <!-- LEFT SIDE: ASSETS -->
                <div class="space-y-4">
                    <div class="bg-red-50 rounded-lg p-4 border-l-4 border-red-700">
                        <h3 class="text-xl font-bold text-red-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            ASSETS
                        </h3>

                        <?php if (empty($assets)): ?>
                            <div class="py-3 px-4 text-gray-500 italic text-center">No assets</div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($assets as $asset): ?>
                                    <div class="flex justify-between py-2 px-4 bg-white rounded hover:bg-red-100 transition-colors">
                                        <span class="text-gray-800"><?= htmlspecialchars($asset['name']) ?></span>
                                        <span class="font-semibold text-gray-900"><?= displayAmount($asset['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between py-3 px-4 bg-red-700 text-white rounded font-bold text-lg mt-4">
                            <span>Total Assets</span>
                            <span><?= displayAmount($totalAssets) ?></span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDE: LIABILITIES + EQUITY -->
                <div class="space-y-4">

                    <!-- LIABILITIES Section -->
                    <div class="bg-blue-50 rounded-lg p-4 border-l-4 border-blue-700">
                        <h3 class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            LIABILITIES
                        </h3>

                        <?php if (empty($liabilities)): ?>
                            <div class="py-2 px-4 text-gray-500 italic text-center">No liabilities</div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($liabilities as $liability): ?>
                                    <div class="flex justify-between py-2 px-4 bg-white rounded hover:bg-blue-100 transition-colors">
                                        <span class="text-gray-800"><?= htmlspecialchars($liability['name']) ?></span>
                                        <span class="font-semibold text-gray-900"><?= displayAmount($liability['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between py-2 px-4 bg-blue-700 text-white rounded font-bold mt-3">
                            <span>Total Liabilities</span>
                            <span><?= displayAmount($totalLiabilities) ?></span>
                        </div>
                    </div>

                    <!-- EQUITY Section -->
                    <div class="bg-green-50 rounded-lg p-4 border-l-4 border-green-700">
                        <h3 class="text-xl font-bold text-green-900 mb-4 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            EQUITY
                        </h3>

                        <?php if (empty($equities) && abs($netIncome) < 0.01): ?>
                            <div class="py-2 px-4 text-gray-500 italic text-center">No equity accounts</div>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($equities as $equity): ?>
                                    <div class="flex justify-between py-2 px-4 bg-white rounded hover:bg-green-100 transition-colors">
                                        <span class="text-gray-800"><?= htmlspecialchars($equity['name']) ?></span>
                                        <span class="font-semibold text-gray-900"><?= displayAmount($equity['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>

                                <!-- Net Income or Net Loss Line -->
                                <?php if (abs($netIncome) > 0.01): ?>
                                    <?php if ($netIncome >= 0): ?>
                                        <!-- Net Income (Profit) -->
                                        <div class="flex justify-between py-2 px-4 bg-green-200 rounded font-semibold border border-green-400">
                                            <span class="text-green-900 flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                </svg>
                                                Net Income
                                            </span>
                                            <span class="text-green-900"><?= displayAmount($netIncome) ?></span>
                                        </div>
                                    <?php else: ?>
                                        <!-- Net Loss -->
                                        <div class="flex justify-between py-2 px-4 bg-red-200 rounded font-semibold border border-red-400">
                                            <span class="text-red-900 flex items-center">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                                                </svg>
                                                Net Loss
                                            </span>
                                            <span class="text-red-900"><?= displayAmount($netIncome) ?></span>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between py-2 px-4 bg-green-700 text-white rounded font-bold mt-3">
                            <span>Total Equity</span>
                            <span><?= displayAmount($totalEquity) ?></span>
                        </div>
                    </div>

                    <!-- Total Liabilities + Equity -->
                    <div class="flex justify-between py-4 px-4 bg-gradient-to-r from-purple-700 to-purple-800 text-white rounded-lg font-bold text-lg shadow-lg">
                        <span>Total Liabilities + Equity</span>
                        <span><?= displayAmount($totalLiabilities + $totalEquity) ?></span>
                    </div>
                </div>
            </div>

            <!-- Balance Check -->
            <div class="px-6 pb-6">
                <?php
                $difference = abs($totalAssets - ($totalLiabilities + $totalEquity));
                if ($difference > 0.01): ?>
                    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <p class="text-red-800 font-bold">⚠️ Warning: Balance Sheet does not balance!</p>
                                <div class="text-red-700 text-sm mt-2 space-y-1">
                                    <p>Assets: <span class="font-semibold"><?= displayAmount($totalAssets) ?></span></p>
                                    <p>Liabilities + Equity: <span class="font-semibold"><?= displayAmount($totalLiabilities + $totalEquity) ?></span></p>
                                    <p>Difference: <span class="font-semibold"><?= displayAmount($difference) ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="text-green-800 font-bold">✓ Balance Sheet is balanced</p>
                                <p class="text-green-700 text-sm mt-1">
                                    Assets (<?= displayAmount($totalAssets) ?>) = Liabilities (<?= displayAmount($totalLiabilities) ?>) + Equity (<?= displayAmount($totalEquity) ?>)
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($role == 'Admin'): ?>

            <div class="flex justify-end my-4">
                <button
                    command="show-modal"
                    commandfor="download-dialog"
                    class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-md shadow-md transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Download PDF</span>
                </button>
            </div>
        <?php endif; ?>
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
                                    $label = "All Months & Years";
                                    if (!empty($month) && !empty($year)) {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " " . $year;
                                    } elseif (!empty($month)) {
                                        $label = date("F", mktime(0, 0, 0, $month, 1)) . " (All Years)";
                                    } elseif (!empty($year)) {
                                        $label = "All Months " . $year;
                                    }
                                    echo $label;
                                    ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <form action="../controllers/balancesheet.controller.php" method="POST" target="_blank" class="px-6 pb-4">
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