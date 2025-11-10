<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}

$title = "Cash Flow Statement";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$journal = new JournalEntries($conn, null, null, null, null, null, null);

$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$cashFlow = $journal->getCashFlow($month, $year);

// Separate by category
$operating = $cashFlow['Operating'];
$investing = $cashFlow['Investing'];
$financing = $cashFlow['Financing'];

$totalOperating = array_sum(array_column($operating, 'balance'));
$totalInvesting = array_sum(array_column($investing, 'balance'));
$totalFinancing = array_sum(array_column($financing, 'balance'));

$netCash = $cashFlow['NetCash'];
$beginningCash = $cashFlow['BeginningCash'];
$endingCash = $cashFlow['EndingCash'];

function displayAmount($amt)
{
    return $amt < 0 ? '(' . number_format(abs($amt), 2) . ')' : number_format($amt, 2);
}
?>

<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800"><?= $title ?></h2>
            <p class="text-gray-600">Summary of Cash Flow by Activities.</p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <div class="sm:w-48">
                    <label class="block text-sm text-gray-700 mb-1 font-medium">Filter by Month</label>
                    <select name="month" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md outline-none transition bg-white">
                        <option value="">--All--</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="sm:w-48">
                    <label class="block text-sm text-gray-700 mb-1 font-medium">Filter by Year</label>
                    <select name="year" onchange="this.form.submit()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md outline-none transition bg-white">
                        <option value="">--All--</option>
                        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                            <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

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

    <?php if (empty($operating) && empty($investing) && empty($financing)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Cash Flow Records Found</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="p-6 bg-gradient-to-r from-red-600 to-red-700">
                <h2 class="text-3xl font-bold text-white">Cash Flow Statement</h2>
                <p class="text-red-100 mt-1 text-sm">
                    For the Period Ended:
                    <span class="font-semibold">
                        <?= !empty($month) ? date('F', mktime(0, 0, 0, intval($month), 1)) : "All Months" ?>
                        <?= !empty($year) ? htmlspecialchars($year) : "All Years" ?>
                    </span>
                </p>
            </div>

            <div class="p-6 space-y-6">
                <!-- Operating Activities -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-red-50 px-4 py-3 border-b border-red-200">
                        <h3 class="text-lg font-bold text-red-900 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            CASH FLOWS FROM OPERATING ACTIVITIES
                        </h3>
                    </div>
                    <div class="bg-white">
                        <?php if (empty($operating)): ?>
                            <div class="py-4 px-4 text-gray-500 italic text-center">No operating activities</div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-100">
                                <?php foreach ($operating as $item): ?>
                                    <div class="flex justify-between py-3 px-4 hover:bg-gray-50 hover:scale-[1.02] transition">
                                        <span class="text-gray-700"><?= htmlspecialchars($item['account_name']) ?></span>
                                        <span class="font-semibold text-gray-900 tabular-nums"><?= displayAmount($item['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between py-3 px-4 bg-red-100 font-bold border-t-2 border-red-300">
                            <span class="text-red-900">Net Cash <?= $totalOperating >= 0 ? 'Provided by' : 'Used in' ?> Operating Activities</span>
                            <span class="<?= $totalOperating >= 0 ? 'text-green-700' : 'text-red-700' ?> tabular-nums">
                                <?= displayAmount($totalOperating) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Investing Activities -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-orange-50 px-4 py-3 border-b border-orange-200">
                        <h3 class="text-lg font-bold text-orange-900 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            CASH FLOWS FROM INVESTING ACTIVITIES
                        </h3>
                    </div>
                    <div class="bg-white">
                        <?php if (empty($investing)): ?>
                            <div class="py-4 px-4 text-gray-500 italic text-center">No investing activities</div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-100">
                                <?php foreach ($investing as $item): ?>
                                    <div class="flex justify-between py-3 px-4 hover:bg-gray-50 hover:scale-[1.02] transition">
                                        <span class="text-gray-700"><?= htmlspecialchars($item['account_name']) ?></span>
                                        <span class="font-semibold text-gray-900 tabular-nums"><?= displayAmount($item['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between py-3 px-4 bg-orange-100 font-bold border-t-2 border-orange-300">
                            <span class="text-orange-900">Net Cash <?= $totalInvesting >= 0 ? 'Provided by' : 'Used in' ?> Investing Activities</span>
                            <span class="<?= $totalInvesting >= 0 ? 'text-green-700' : 'text-red-700' ?> tabular-nums">
                                <?= displayAmount($totalInvesting) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Financing Activities -->
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-amber-50 px-4 py-3 border-b border-amber-200">
                        <h3 class="text-lg font-bold text-amber-900 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            CASH FLOWS FROM FINANCING ACTIVITIES
                        </h3>
                    </div>
                    <div class="bg-white">
                        <?php if (empty($financing)): ?>
                            <div class="py-4 px-4 text-gray-500 italic text-center">No financing activities</div>
                        <?php else: ?>
                            <div class="divide-y divide-gray-100">
                                <?php foreach ($financing as $item): ?>
                                    <div class="flex justify-between py-3 px-4 hover:bg-gray-50 hover:scale-[1.02] transition">
                                        <span class="text-gray-700"><?= htmlspecialchars($item['account_name']) ?></span>
                                        <span class="font-semibold text-gray-900 tabular-nums"><?= displayAmount($item['balance']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <div class="flex justify-between py-3 px-4 bg-amber-100 font-bold border-t-2 border-amber-300">
                            <span class="text-amber-900">Net Cash <?= $totalFinancing >= 0 ? 'Provided by' : 'Used in' ?> Financing Activities</span>
                            <span class="<?= $totalFinancing >= 0 ? 'text-green-700' : 'text-red-700' ?> tabular-nums">
                                <?= displayAmount($totalFinancing) ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Summary Section -->
                <div class="border-t-4 border-gray-300 pt-6 space-y-3">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-gray-800">Net Increase/(Decrease) in Cash</span>
                            <span class="text-xl font-bold <?= $netCash >= 0 ? 'text-green-600' : 'text-red-600' ?> tabular-nums">
                                <?= displayAmount($netCash) ?>
                            </span>
                        </div>
                    </div>

                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-700 font-medium">Cash at Beginning of Period</span>
                            <span class="font-semibold text-gray-900 tabular-nums"><?= displayAmount($beginningCash) ?></span>
                        </div>
                    </div>

                    <div class="bg-gradient-to-r from-red-600 to-red-700 rounded-lg p-5 shadow-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-white font-bold text-lg">Cash at End of Period</span>
                            <span class="text-white font-bold text-2xl tabular-nums"><?= displayAmount($endingCash) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Reconciliation Check -->
                <?php
                $calculatedEnding = $beginningCash + $netCash;
                $difference = abs($endingCash - $calculatedEnding);
                if ($difference > 0.01): ?>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-yellow-800">⚠️ Warning: Cash reconciliation error!</p>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Difference: <?= displayAmount($difference) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-semibold text-green-800">✓ Cash Flow Statement reconciles successfully</p>
                                <p class="text-sm text-green-700 mt-1">
                                    Ending cash (<?= displayAmount($endingCash) ?>) matches calculations
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
                    class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2.5 rounded-lg shadow-md transition duration-200">
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
                            <h3 id="download-dialog-title" class="text-lg font-semibold text-gray-900">Download Cash Flow Statement</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Do you want to download a PDF of Cash Flow Statement for
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

                <form action="../controllers/cashflow.controller.php" method="POST" target="_blank" class="px-6 pb-4">
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