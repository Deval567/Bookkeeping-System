<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
$title = "Journal Entries";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";
require_once "../models/transactions.class.php";

$transactionModel = new Transaction($conn, null, null, null, null, null, null, null);
$entry = new JournalEntries($conn, null, null, null, null, null, null);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = $_GET['search'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';
$filterRuleName = $_GET['rule_id'] ?? '';

$all_rule_names = $transactionModel->getTransactionsGroupByRuleName();
$journalEntries = $entry->getPaginatedJournalEntries($page, $search, $month, $year, $filterRuleName);
$total_pages = $entry->getTotalJournalPages($search, $month, $year, $filterRuleName);

// Get selected rule name for display
$selectedRuleName = '';
if (!empty($filterRuleName)) {
    foreach ($all_rule_names as $rule) {
        if ($rule['rule_id'] == $filterRuleName) {
            $selectedRuleName = $rule['rule_name'];
            break;
        }
    }
}

$queryParams = '&search=' . urlencode($search) . '&month=' . urlencode($month) . '&year=' . urlencode($year)
    . '&rule_id=' . urlencode($filterRuleName); ?>

<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4">
        <h2 class="text-2xl font-semibold"><?= $title ?></h2>
        <p class="text-gray-600">Manage <?= $title ?> here.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 mb-4 rounded shadow">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Filter by Transaction Name</label>
                <select
                    name="rule_id"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">

                    <option value="">--All Transaction Names--</option>

                    <?php
                    $currentCategory = '';
                    foreach ($all_rule_names as $name):
                        if ($name['category'] !== $currentCategory):
                            $currentCategory = $name['category'];
                    ?>
                            <option disabled class="text-black-700 bg-gray-200 text-center cursor-default">[--<?= htmlspecialchars($currentCategory) ?> Category--]</option>
                        <?php endif; ?>

                        <option value="<?= $name['rule_id'] ?>"
                            <?= (isset($_GET['rule_id']) && $_GET['rule_id'] == $name['rule_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($name['rule_name']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

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

            <!-- Search -->
            <div class="flex-1">
                <label class="block text-sm text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search account, transaction, or description..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                </div>
            </div>

            <!-- Clear / Search buttons -->
            <div class="flex gap-2 pt-5 sm:mt-0">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Search
                </button>

                <?php if ($search || $month || $year || $filterRuleName): ?>
                    <a href="?"
                        class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Journal Entries Table -->
    <?php if (empty($journalEntries)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Journal Entries are found.</p>
        </div>
    <?php else: ?>
        <div class="rounded-lg shadow">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-red-700 text-white">
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Date</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Particulars</th>
                        <th class="py-2 px-4 text-right text-sm font-medium border-r border-red-600">Debit</th>
                        <th class="py-2 px-4 text-right text-sm font-medium">Credit</th>
                    </tr>
                </thead>

                <?php foreach ($journalEntries as $je): ?>
                    <tbody class="group hover:scale-[1.02] hover:bg-gray-100 transition-all duration-200">
                        <?php
                        $accounts = $je['accounts'] ?? [];
                        $maxLines = count($accounts);
                        ?>
                        <?php for ($i = 0; $i < $maxLines; $i++): ?>
                            <tr>
                                <?php if ($i === 0): ?>
                                    <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200" rowspan="<?= $maxLines ?>">
                                        <?= date('Y-m-d', strtotime($je['journal_date'] ?? date('Y-m-d'))) ?>
                                    </td>
                                <?php endif; ?>

                                <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                    <?= htmlspecialchars($accounts[$i]['account_name'] ?? '') ?>
                                </td>
                                <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200 text-right">
                                    <?= number_format($accounts[$i]['debit'] ?? 0, 2) ?>
                                </td>
                                <td class="py-2 px-4 text-gray-900 font-medium text-right">
                                    <?= number_format($accounts[$i]['credit'] ?? 0, 2) ?>
                                </td>
                            </tr>
                        <?php endfor; ?>
                        <tr class="bg-gray-50">
                            <td colspan="4" class="py-1 px-4 text-gray-600 text-sm">
                                <strong><?= htmlspecialchars($je['transaction_name'] ?? 'General Entry') ?></strong>
                                <?php if (!empty($je['reference_no'])): ?>
                                    - Ref# <span class="font-medium"><?= htmlspecialchars($je['reference_no']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($je['description'])): ?>
                                    <br><span class="italic text-gray-500">(<?= htmlspecialchars($je['description']) ?>)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>
        <?php if ($role == 'Admin'): ?>
            <div class="flex justify-end my-4">
                <button
                    command="show-modal"
                    commandfor="download-dialog"
                    class="flex items-center space-x-2 bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-green-200 size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Download PDF</span>
                </button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Pagination -->
    <div class="flex justify-center my-4 space-x-2 pb-4">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 . $queryParams ?>"
                class="inline-flex items-center gap-1 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                <span>Prev</span>
            </a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i . $queryParams ?>"
                class="px-3 py-1 rounded <?= $i == $page ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?= $page + 1 . $queryParams ?>"
                class="inline-flex items-center gap-1 px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">
                <span>Next</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        <?php endif; ?>
    </div>
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
                            <h3 id="download-dialog-title" class="text-lg font-semibold text-gray-900">Download Journal Entries</h3>

                            <p class="mt-2 text-sm text-gray-600">
                                Do you want to download a PDF of Journal Entries for
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
                                    
                                    // Add rule name if selected
                                    if (!empty($selectedRuleName)) {
                                        $label .= " - " . htmlspecialchars($selectedRuleName);
                                    }
                                    
                                    echo $label;
                                    ?>
                                </span>?
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="../controllers/journalentries.controller.php" method="POST" target="_blank" class="px-6 pb-4">
                    <input type="hidden" name="action" value="download_pdf">
                    <input type="hidden" name="month" value="<?= htmlspecialchars($month) ?>">
                    <input type="hidden" name="year" value="<?= htmlspecialchars($year) ?>">
                    <input type="hidden" name="rule_id" value="<?= htmlspecialchars($filterRuleName) ?>">

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