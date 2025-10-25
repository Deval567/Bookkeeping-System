<?php
$title = "Trial Balance";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$entry = new JournalEntries($conn, null, null, null, null, null, null);
$search = $_GET['search'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$trialBalance = $entry->getAllTrialBalance($month, $year);
?>
<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold"><?= $title ?></h2>
            <p class="text-gray-600">Summary of account balances.</p>
        </div>
        <!-- Success and Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
                <div class="flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 shadow-lg">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                        <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-green-800">
                            <?php
                            if (is_array($_SESSION['success_message'])) {
                                echo implode(', ', $_SESSION['success_message']);
                            } else {
                                echo $_SESSION['success_message'];
                            }
                            ?>
                        </p>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        <?php
            unset($_SESSION['success_message']);
        endif;
        ?>


        <?php if (isset($_SESSION['trialbalance_errors'])): ?>
            <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
                <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <?php foreach ($_SESSION['trialbalance_errors'] as $error): ?>
                            <p class="text-sm font-medium text-red-800">
                                <?php echo ($error); ?>
                            </p>
                        <?php endforeach; ?>
                    </div>
                    <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        <?php
            unset($_SESSION['trialbalance_errors']);
        endif;
        ?>

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

    <!-- Trial Balance Table -->
    <?php if (empty($trialBalance)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Trial Balance Records are found.</p>
        </div>
    <?php else: ?>
        <div class="bg-white rounded-lg shadow">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-red-700 text-white">
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Account Name</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Account Type</th>
                        <th class="py-2 px-4 text-right text-sm font-medium border-r border-red-600">Debit</th>
                        <th class="py-2 px-4 text-right text-sm font-medium border-r border-red-600">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalDebit = 0;
                    $totalCredit = 0;
                    ?>

                    <?php foreach ($trialBalance as $row):
                        $totalDebit += $row['total_debit'];
                        $totalCredit += $row['total_credit'];
                    ?>
                        <tr class="hover:scale-[1.02]  transition-transform duration-150 hover:bg-gray-100">
                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                <?= htmlspecialchars($row['account_name']) ?>
                            </td>
                            <td class="py-2 px-4 text-center text-gray-900 font-medium border-r border-gray-200">
                                <?php
                                $acc_type = $row['account_type'];
                                $accTypeStyle = [
                                    'Asset' => 'bg-red-100 text-red-700',
                                    'Liability' => 'bg-blue-100 text-blue-700',
                                    'Equity' => 'bg-green-100 text-green-700',
                                    'Expense' => 'bg-yellow-100 text-yellow-700',
                                    'Revenue' => 'bg-purple-100 text-purple-700',
                                ];
                                $style = $accTypeStyle[$row['account_type']] ?? 'bg-gray-100 text-gray-700';
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $style ?>">
                                    <?= strtoupper($acc_type) ?>
                                </span>
                            </td>
                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200 text-right">
                                <?= number_format($row['total_debit'], 2) ?>
                            </td>
                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200 text-right">
                                <?= number_format($row['total_credit'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                    <!-- Totals Row -->
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="2" class="py-2 px-4 border-t border-gray-300">Total</td>
                        <td class="py-2 px-4 text-right border-t border-gray-300"><?= number_format($totalDebit, 2) ?></td>
                        <td class="py-2 px-4 text-right border-t border-gray-300"><?= number_format($totalCredit, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="flex justify-end my-4 gap-4">
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
                            <h3 id="download-dialog-title" class="text-lg font-semibold text-gray-900">Download Trial Balance</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Do you want to download a PDF of Trial Balance for
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
