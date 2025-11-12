<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You don't have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}

if ($_SESSION['role'] !== 'Admin') {
    $_SESSION['dashboard_errors'] = ["Access denied. You dont have access to this page."];
    header("Location: ../pages/dashboard.php");
    exit();
}
$title = "Transaction Rule Lines";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/transactionrulelines.class.php";
require_once "../models/transactionrules.class.php";
require_once "../models/chartofacc.class.php";

$chartofAcc = new ChartofAccounts($conn, null, null, null, null, null);
$transactionRules = new transactionRules($conn, null, null, null, null);
$transactionRuleLines = new TransactionRuleLines($conn, null, null, null, null);

$page   = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$rule_id = $_GET['rule_id'] ?? '';

$queryParams = "&search=" . urlencode($search) . "&entry_type=" . "&rule_id=" . urlencode($rule_id);
$accounts = $chartofAcc->getAllChart();
$all_lines = $transactionRuleLines->getRuleIdRulelinesGroupedByCategory();
$rules = $transactionRules->getAllRules();
$total_pages = $transactionRuleLines->getTotalPages($search,  $rule_id);
$rule_lines  = $transactionRuleLines->getPaginatedRuleLines($page, $search,  $rule_id);

// Get rule lines grouped by rule_id for DISPLAY (filtered)
$grouped_lines = [];
foreach ($rule_lines as $line) {
    $grouped_lines[$line['rule_id']][] = $line;
}

// Get COMPLETE rule lines for EDITING (unfiltered) - fetch all lines for each visible rule
$complete_lines = [];
foreach (array_keys($grouped_lines) as $rule_id_key) {
    $complete_lines[$rule_id_key] = $transactionRuleLines->getRuleLinesByRuleId($rule_id_key);
}
?>
<main class="bg-gray-100 px-6 py-4">
    <div class="mb-2">
        <h2 class="text-2xl font-semibold"><?php echo $title ?></h2>
        <p class="text-gray-600">Manage <?php echo $title ?> here.</p>
    </div>

    <div class="flex justify-end mb-6">
        <button
            command="show-modal"
            commandfor="dialog"
            id="openModalBtn"
            class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transform transition duration-200 hover:-translate-y-0.5 hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
            </svg>
            <span>Add New Rule Line</span>
        </button>
    </div>

    <?php
    $successBottom = isset($_SESSION['transactionrulelines_errors']) ? 'bottom-20' : 'bottom-4';
    ?>

    <!-- Success Message -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="fixed <?= $successBottom ?> left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800"><?= $_SESSION['success_message']; ?></p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Error Message -->
    <?php if (isset($_SESSION['transactionrulelines_errors'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['transactionrulelines_errors'] as $error): ?>
                        <p class="text-sm font-medium text-red-800"><?= $error; ?></p>
                    <?php endforeach; ?>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['transactionrulelines_errors']); ?>
    <?php endif; ?>

    <!-- Search and Filter Form -->
    <div class="bg-white p-4 mb-6 rounded-lg shadow">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <!-- Filter Dropdown -->
            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Filter by Transaction Name</label>
                <select
                    name="rule_id"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">--All Transaction Names--</option>
                    <?php
                    $currentCategory = '';
                    foreach ($all_lines as $line):
                        if ($line['category'] !== $currentCategory):
                            $currentCategory = $line['category'];
                    ?>
                            <option disabled class="bg-gray-300 text-center text-gray-700 cursor-default">
                                [--<?= htmlspecialchars($currentCategory) ?> Category--]
                            </option>
                        <?php endif; ?>
                        <option value="<?= $line['rule_id'] ?>" <?= (isset($_GET['rule_id']) && $_GET['rule_id'] == $line['rule_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($line['rule_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Search Input -->
            <div class="flex-1">
                <label class="block text-sm text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input
                        type="text"
                        name="search"
                        value="<?= $_GET['search'] ?? '' ?>"
                        placeholder="Search for transaction name ..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2 mt-5">
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    Search
                </button>

                <?php if (!empty($_GET['search']) || !empty($_GET['entry_type']) || !empty($_GET['rule_id'])): ?>
                    <a
                        href="?"
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

    <!-- Accordion Layout -->
    <?php if (empty($grouped_lines)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Transaction Rule Lines are found.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4 mb-8">
            <?php foreach ($grouped_lines as $rule_id => $lines):
                $first_line = $lines[0];
            ?>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Accordion Header -->
                    <div class="flex items-center px-6 py-4 bg-white hover:bg-gray-50 transition-colors duration-300 border-b border-gray-200">
                        <button
                            type="button"
                            class="accordion-toggle flex items-center gap-4 flex-1"
                            data-target="accordion-content-<?= $rule_id ?>">
                            <svg class="accordion-icon w-5 h-5 text-gray-600 transition-transform duration-500 ease-in-out flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                            <div class="text-left">
                                <h3 class="font-semibold text-gray-900 text-base"><?= htmlspecialchars($first_line['rule_name']); ?></h3>
                                <p class="text-xs text-gray-500 mt-0.5"><?= count($lines) ?> line<?= count($lines) > 1 ? 's' : '' ?></p>
                            </div>
                        </button>

                        <div class="flex items-center gap-2 ml-4">
                            <button
                                type="button"
                                command="show-modal"
                                commandfor="edit-dialog-<?= $rule_id ?>"
                                title="Edit"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-300 border border-transparent hover:border-blue-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </button>

                            <button
                                type="button"
                                command="show-modal"
                                commandfor="delete-dialog-<?= $rule_id ?>"
                                title="Delete"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-300 border border-transparent hover:border-red-200">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Accordion Content -->
                    <div id="accordion-content-<?= $rule_id ?>" class="accordion-content max-h-0 overflow-hidden transition-all duration-500 ease-in-out">
                        <div class="px-6 py-4">
                            <table class="min-w-full">
                                <thead class="bg-red-700 text-white">
                                    <tr class="border-b border-gray-200">
                                        <th class="py-3 px-4 text-left text-sm font-semibold text-white">Account Name</th>
                                        <th class="py-3 px-4 text-center text-sm font-semibold text-white">Entry Type</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($lines as $line): ?>
                                        <tr class="hover:bg-gray-50 hover:scale-[1.02] transition-all duration-200">
                                            <td class="py-3 px-4 text-gray-900">
                                                <?= htmlspecialchars($line['account_name']); ?>
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                <?php
                                                $entry_type = $line['entry_type'];
                                                $entryTypeStyle = [
                                                    'debit' => 'bg-green-100 text-green-700',
                                                    'credit' => 'bg-red-100 text-red-700',
                                                ];
                                                $style = $entryTypeStyle[$line['entry_type']] ?? 'bg-gray-100 text-gray-700';
                                                ?>
                                                <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $style ?>">
                                                    <?= strtoupper($entry_type) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Pagination Links -->
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

<!-- Add New Rule Line Modal -->
<el-dialog>
    <dialog id="dialog" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="dialog-title" class="text-lg font-semibold text-gray-900 pt-2">Add Transaction Rule Line</h3>
                        </div>
                    </div>
                </div>

                <form action="../controllers/transactionrulelines.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                    <input type="hidden" name="action" value="add_rule_lines">

                    <!-- Transaction Name -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Transaction Name</label>
                        <select name="rule_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            <option value="">Select a Transaction Name</option>
                            <?php
                            $currentCategory = '';
                            foreach ($rules as $rule):
                                if ($rule['category'] !== $currentCategory):
                                    $currentCategory = $rule['category'];
                            ?>
                                    <option disabled class="bg-gray-300 text-center text-gray-700 cursor-default">
                                        [--<?= htmlspecialchars($currentCategory) ?> Category--]
                                    </option>
                                <?php endif; ?>
                                <option value="<?= $rule['id']; ?>">
                                    <?= htmlspecialchars($rule['rule_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="line-entries" class="space-y-2">
                        <div class="line-entry flex gap-4 items-center">
                            <div class="flex-1">
                                <label class="block text-sm text-gray-700 mb-1">Account Name</label>
                                <select name="account_id[]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">--Select an Account--</option>
                                    <?php
                                    $currentCategory = '';
                                    foreach ($accounts as $account):
                                        if (!isset($account['account_type'], $account['id'], $account['account_name'])) continue;

                                        if ($account['account_type'] !== $currentCategory):
                                            $currentCategory = $account['account_type'];
                                    ?>
                                            <option disabled class="bg-gray-200 text-gray-700 text-center cursor-default">
                                                [-- <?= htmlspecialchars($currentCategory) ?> Accounts --]
                                            </option>
                                        <?php endif; ?>
                                        <option value="<?= $account['id']; ?>">
                                            <?= htmlspecialchars($account['account_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="w-40">
                                <label class="block text-sm text-gray-700 mb-1">Entry Type</label>
                                <select name="entry_type[]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                    <option value="">Select Entry Type</option>
                                    <option value="debit">Debit</option>
                                    <option value="credit">Credit</option>
                                </select>
                            </div>

                            <button type="button"
                                class="remove-line mt-7 p-1 text-red-500 hover:text-red-700 transition">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Add line button -->
                    <button type="button" id="add-line" class="text-blue-600 font-semibold">+ Add Line</button>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save
                        </button>
                        <button type="button" command="close" commandfor="dialog"
                            class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

    <!-- Edit Rule Lines Modal -->
    <?php foreach ($grouped_lines as $rule_id => $lines):
        $first_line = $lines[0];
    ?>
        <el-dialog>
            <dialog id="edit-dialog-<?= $rule_id ?>" aria-labelledby="edit-dialog-title-<?= $rule_id ?>" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
                <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

                <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                    <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                        <!-- Header -->
                        <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex items-start space-x-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <h3 id="edit-dialog-title-<?= $rule_id ?>" class="text-lg font-semibold text-gray-900 pt-2">Edit Transaction Rule Lines</h3>
                                </div>
                            </div>
                        </div>

                        <form action="../controllers/transactionrulelines.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                            <input type="hidden" name="action" value="update_rule_lines">
                            <input type="hidden" name="original_rule_id" value="<?= $rule_id ?>">

                            <!-- Transaction Name -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Transaction Name</label>
                                <select name="rule_id"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                                    <option value="">Select a Transaction Name</option>
                                    <?php
                                    $currentCategory = '';
                                    foreach ($rules as $rule):
                                        if ($rule['category'] !== $currentCategory):
                                            $currentCategory = $rule['category'];
                                    ?>
                                            <option disabled class="bg-gray-300 text-center text-gray-700 cursor-default">
                                                [--<?= htmlspecialchars($currentCategory) ?> Category--]
                                            </option>
                                        <?php endif; ?>
                                        <option value="<?= $rule['id']; ?>" <?= $rule['id'] == $rule_id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($rule['rule_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div id="edit-line-entries-<?= $rule_id ?>" class="space-y-2">
                                <?php foreach ($lines as $line): ?>
                                    <div class="line-entry flex gap-4 items-center">
                                        <input type="hidden" name="line_ids[]" value="<?= $line['id'] ?>">

                                        <div class="flex-1">
                                            <label class="block text-sm text-gray-700 mb-1">Account Name</label>
                                            <select name="account_id[]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                                <option value="">--Select an Account--</option>
                                                <?php
                                                $currentType = '';
                                                foreach ($accounts as $account):
                                                    if (!isset($account['account_type'], $account['id'], $account['account_name'])) continue;

                                                    if ($account['account_type'] !== $currentType):
                                                        $currentType = $account['account_type'];
                                                ?>
                                                        <option disabled class="bg-gray-200 text-gray-700 text-center cursor-default">
                                                            [-- <?= htmlspecialchars($currentType) ?> Accounts --]
                                                        </option>
                                                    <?php endif; ?>
                                                    <option value="<?= $account['id']; ?>" <?= $account['id'] == $line['account_id'] ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($account['account_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="w-40">
                                            <label class="block text-sm text-gray-700 mb-1">Entry Type</label>
                                            <select name="entry_type[]" class="w-full border border-gray-300 rounded-md px-3 py-2">
                                                <option value="">Select Entry Type</option>
                                                <option value="debit" <?= $line['entry_type'] == 'debit' ? 'selected' : '' ?>>Debit</option>
                                                <option value="credit" <?= $line['entry_type'] == 'credit' ? 'selected' : '' ?>>Credit</option>
                                            </select>
                                        </div>

                                        <button type="button"
                                            class="remove-line mt-7 p-1 text-red-500 hover:text-red-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="2"
                                                stroke="currentColor"
                                                class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Add line button -->
                            <button type="button" class="add-edit-line text-blue-600 font-semibold" data-target="edit-line-entries-<?= $rule_id ?>">+ Add Line</button>

                            <!-- Buttons -->
                            <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                                <button type="submit"
                                    class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                    Update
                                </button>
                                <button type="button" command="close" commandfor="edit-dialog-<?= $rule_id ?>"
                                    class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                                    Cancel
                                </button>
                            </div>
                        </form>

                    </el-dialog-panel>
                </div>
            </dialog>
        </el-dialog>
    <?php endforeach; ?>

<!-- Delete Rule Lines Modal -->
<?php foreach ($grouped_lines as $rule_id => $lines):
    $first_line = $lines[0];
    $line_ids = array_column($lines, 'id');
?>
    <el-dialog>
        <dialog id="delete-dialog-<?= $rule_id ?>" aria-labelledby="delete-dialog-title-<?= $rule_id ?>" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

            <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                    <!-- Header -->
                    <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 id="delete-dialog-title-<?= $rule_id ?>" class="text-lg font-semibold text-gray-900">Delete Transaction Rule Lines</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Are you sure you want to delete all lines for <span class="font-semibold"><?= htmlspecialchars($first_line['rule_name']) ?></span>?
                                    This will delete <span class="font-semibold"><?= count($lines) ?> line(s)</span>. This action cannot be undone.
                                </p>
                                <div class="mt-3 text-sm text-gray-700 space-y-1">
                                    <?php foreach ($lines as $line): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 rounded text-xs <?= $line['entry_type'] == 'debit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                                <?= strtoupper($line['entry_type']) ?>
                                            </span>
                                            <span><?= htmlspecialchars($line['account_name']) ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Form -->
                    <form action="../controllers/transactionrulelines.controller.php" method="POST" class="px-6 pb-4">
                        <input type="hidden" name="action" value="delete_rule_lines">
                        <?php foreach ($line_ids as $line_id): ?>
                            <input type="hidden" name="ids[]" value="<?= $line_id ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="rule_id" value="<?= $rule_id ?>">
                        <input type="hidden" name="page" value="<?= $page ?>">

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Delete All
                            </button>
                            <button type="button" command="close" commandfor="delete-dialog-<?= $rule_id ?>"
                                class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                                Cancel
                            </button>
                        </div>
                    </form>

                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
<?php endforeach; ?>


<script>
    // Accordion functionality with smooth animation
    document.querySelectorAll('.accordion-toggle').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const content = document.getElementById(targetId);
            const icon = this.querySelector('.accordion-icon');

            // Check if currently open
            const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

            if (isOpen) {
                // Close accordion
                content.style.maxHeight = '0px';
                content.style.paddingTop = '0px';
                content.style.paddingBottom = '0px';
                content.style.borderTopWidth = '0px';
                icon.style.transform = 'rotate(0deg)';
            } else {
                // Open accordion
                content.style.maxHeight = content.scrollHeight + 'px';
                content.style.paddingTop = '';
                content.style.paddingBottom = '';
                content.style.borderTopWidth = '';
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });

    // Add line functionality for new rule lines
    document.getElementById('add-line').addEventListener('click', function() {
        const container = document.getElementById('line-entries');
        const newLine = container.firstElementChild.cloneNode(true);
        newLine.querySelectorAll('select').forEach(select => select.value = '');
        container.appendChild(newLine);
    });

    // Add line functionality for edit modals
    document.querySelectorAll('.add-edit-line').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            const container = document.getElementById(targetId);
            const newLine = container.firstElementChild.cloneNode(true);

            // Clear the hidden ID field for new lines
            const hiddenId = newLine.querySelector('input[name="line_ids[]"]');
            if (hiddenId) hiddenId.value = '';

            // Clear all select values
            newLine.querySelectorAll('select').forEach(select => select.value = '');
            container.appendChild(newLine);
        });
    });

    // Remove line functionality
    document.addEventListener('click', function(e) {
        const removeBtn = e.target.closest('.remove-line');
        if (removeBtn) {
            const container = removeBtn.closest('[id^="line-entries"], [id^="edit-line-entries"]');
            const lines = container.querySelectorAll('.line-entry');
            if (lines.length > 1) removeBtn.closest('.line-entry').remove();
        }
    });
</script>
</body>

</html>