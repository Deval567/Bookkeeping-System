<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
$title = "Transactions";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/transactions.class.php";
require_once "../models/transactionrulelines.class.php";

$transactionModel = new Transaction($conn, null, null, null, null, null, null, null);
$transactionRuleLines = new TransactionRuleLines($conn, null, null, null, null, null);


$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;


$search = $_GET['search'] ?? '';
$filterRuleName = $_GET['rule_id'] ?? '';
$filterUsername = $_GET['username'] ?? '';
$filterDateFrom = $_GET['date_from'] ?? '';
$filterDateTo = $_GET['date_to'] ?? '';

$usernames = $transactionModel->getTransactionsGroupedByUser();
$all_rule_names = $transactionModel->getTransactionsGroupByRuleName();
$transactions = $transactionModel->getPaginatedTransactions($page, $search, $filterRuleName, $filterUsername, $filterDateFrom, $filterDateTo);
$total_pages = $transactionModel->getTotalTransactionPages($search, $filterRuleName, $filterUsername, $filterDateFrom, $filterDateTo);
$all_lines = $transactionRuleLines->getRuleIdRulelinesGroupedByCategory();


$queryParams = '&' . http_build_query([
    'search' => $search,
    'rule_id' => $filterRuleName,
    'username' => $filterUsername,
    'date_from' => $filterDateFrom,
    'date_to' => $filterDateTo
]);


?>

<main class="bg-gray-100 px-6 py-4">
    <div>
        <h2 class="text-2xl font-semibold"><?php echo $title ?></h2>
        <p class=" text-gray-600">Manage <?php echo $title ?> here.</p>
    </div>

    <div class="flex justify-end mb-2">
        <button
            command="show-modal"
            commandfor="dialog"
            id="openModalBtn"
            class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transform transition duration-200 hover:-translate-y-0.5 hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
            </svg>

            <span>Add New Transaction</span>
        </button>
    </div>

    <?php
    // Determine success message position
    $successBottom = isset($_SESSION['success_message']) ? 'bottom-20' : 'bottom-4';
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
                    <?php foreach ($_SESSION['success_message'] as $message): ?>
                        <p class="text-sm font-medium text-green-800"><?= $message; ?></p>
                    <?php endforeach; ?>
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
    <?php if (isset($_SESSION['transaction_errors'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['transaction_errors'] as $error): ?>
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
        <?php unset($_SESSION['transaction_errors']); ?>
    <?php endif; ?>

    <!-- Search and Filter Form -->
    <div class="bg-white p-4 mb-4 rounded shadow">
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
            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Filter by Encoder</label>
                <select
                    name="username"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">--All Users--</option>
                    <?php foreach ($usernames as $user): ?>
                        <option value="<?= $user['user_id'] ?>" <?= (isset($_GET['username']) && $_GET['username'] == $user['user_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($user['username']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex gap-2">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Date From</label>
                    <input
                        type="date"
                        onchange="this.form.submit()"
                        name="date_from"
                        value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Date To</label>
                    <input
                        onchange="this.form.submit()"
                        type="date"
                        name="date_to"
                        value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>"
                        class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
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
                        placeholder="Search transaction name, reference number#, or transaction details ..."
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

                <?php if (!empty($_GET['search']) || !empty($_GET['filter'])): ?>
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
    <!-- Transactions Table -->
    <?php if (empty($transactions)): ?>
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <p class="text-gray-500 font-semibold">No Transactions are found.</p>
        </div>
    <?php else: ?>
        <div class=" rounded-lg shadow">
            <table class="min-w-full bg-white border border-gray-200">
                <thead>
                    <tr class="bg-red-700 text-white">
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Date</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Name</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Reference Number #</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Details</th>
                        <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Total Amount</th>
                        <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Encoded By</th>
                        <th class="py-2 px-4 text-center text-sm font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($transactions as $transaction): ?>
                        <tr class="hover:bg-gray-100 transition-all duration-200 hover:scale-[1.02]">
                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                <?= date('Y-m-d', strtotime($transaction['transaction_date'])); ?>
                            </td>

                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                <?= htmlspecialchars($transaction['rule_name']); ?>
                            </td>

                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                <?= htmlspecialchars($transaction['reference_no']); ?>
                            </td>
                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200">
                                <?= htmlspecialchars($transaction['description']); ?>
                            </td>

                            <td class="py-2 px-4 text-gray-900 font-medium border-r border-gray-200 text-right">
                                <?= number_format($transaction['total_amount'], 2); ?>
                            </td>
                            <td class="py-2 px-4 text-center border-r border-gray-200">
                                <?= htmlspecialchars($transaction['username']); ?>
                            </td>

                            <td class="py-2 px-4 border-gray-200">
                                <div class="flex items-center justify-center space-x-2">
                                    <button command="show-modal" commandfor="edit-dialog-<?= $transaction['id'] ?>" class="p-1.5 text-blue-600 hover:bg-blue-600 hover:text-white rounded transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>
                                    </button>

                                    <button command="show-modal" commandfor="delete-dialog-<?= $transaction['id'] ?>" class="p-1.5 text-red-600 hover:bg-red-600 hover:text-white rounded transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        </div>


        <!-- Pagination Links !-->
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
<!-- Add New Transaction Modal -->
<el-dialog>
    <dialog id="dialog" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity
            data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out
            data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all
                data-enter:duration-300 data-leave:duration-200 data-enter:ease-out data-leave:ease-in">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="dialog-title" class="text-lg font-semibold text-gray-900 pt-2">Add Transaction</h3>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <form action="../controllers/transactions.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                    <input type="hidden" name="action" value="add_transaction">

                    <!-- 4-column grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Transaction Name -->
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Transaction Name</label>
                            <select id="rule_id" name="rule_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">--All Transaction Names--</option>
                                <?php
                                $currentCategory = '';
                                foreach ($all_lines as $line):
                                    if ($line['category'] !== $currentCategory):
                                        $currentCategory = $line['category'];
                                ?>
                                        <option disabled class="text-gray-400 cursor-default">[---<?= htmlspecialchars($currentCategory) ?> Category---]</option>
                                    <?php endif; ?>
                                    <option value="<?= $line['rule_id'] ?>"><?= htmlspecialchars($line['rule_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Transaction Date -->
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Transaction Date</label>
                            <input type="date" name="transaction_date" required class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                        </div>

                        <!-- Reference Number -->
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Reference Number #</label>
                            <input type="text" placeholder="Enter Reference Number" required name="reference_no" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                        </div>

                        <!-- Total Amount -->
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Total Amount</label>
                            <input type="number" name="total_amount" required placeholder="0.00" min="0" step="0.01" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                        </div>
                    </div>

                    <!-- Transaction Details -->
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Transaction Details</label>
                        <textarea name="description" placeholder="Add a description for the transaction" required rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none"></textarea>
                    </div>

                    <!-- Rule Lines Display -->
                    <div class="mt-4">
                        
                        <div id="rule-lines-display">

                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit" class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save</button>
                        <button type="button" command="close" commandfor="dialog" class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">Cancel</button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>


<?php foreach ($transactions as $transaction): ?>
    <el-dialog>
        <dialog id="edit-dialog-<?= $transaction['id'] ?>" aria-labelledby="edit-dialog-title-<?= $transaction['id'] ?>" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
            <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity
            data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out
            data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

            <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
                <el-dialog-panel class="relative w-full max-w-6xl transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all
                data-enter:duration-300 data-leave:duration-200 data-enter:ease-out data-leave:ease-in">

                    <!-- Header -->
                    <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <div class="text-left">
                                <h3 id="edit-dialog-title-<?= $transaction['id'] ?>" class="text-lg font-semibold text-gray-900 pt-2">Edit Transaction</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Transaction Form -->
                    <form action="../controllers/transactions.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                        <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                        <input type="hidden" name="action" value="update_transaction">

                        <!-- 4-column grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Transaction Name -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Transaction Name</label>
                                <select id="rule_id_<?= $transaction['id'] ?>" name="rule_id" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                    <option value="">--All Transaction Names--</option>
                                    <?php
                                    $currentCategory = '';
                                    foreach ($all_lines as $line):
                                        if ($line['category'] !== $currentCategory):
                                            $currentCategory = $line['category'];
                                    ?>
                                            <option disabled class="text-gray-400 cursor-default">[--<?= htmlspecialchars($currentCategory) ?> Category--]</option>
                                        <?php endif; ?>
                                        <option value="<?= $line['rule_id'] ?>"
                                            <?= (isset($transaction['rule_id']) && $transaction['rule_id'] == $line['rule_id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($line['rule_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Transaction Date -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Transaction Date</label>
                                <input type="date" name="transaction_date" value="<?= $transaction['transaction_date'] ?>" required class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            </div>

                            <!-- Reference Number -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Reference Number #</label>
                                <input type="text" name="reference_no" required value="<?= $transaction['reference_no'] ?>" placeholder="Enter Reference Number" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            </div>

                            <!-- Total Amount -->
                            <div>
                                <label class="block text-sm text-gray-700 mb-1">Total Amount</label>
                                <input type="number" name="total_amount" value="<?= $transaction['total_amount'] ?>" required min="0" step="0.01" placeholder="0.00" class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            </div>
                        </div>

                        <!-- Transaction Details -->
                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Transaction Details</label>
                            <textarea name="description" required placeholder="Add a description for the transaction" rows="3"  class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none"><?= $transaction['description'] ?></textarea>
                        </div>

                        <!-- Rule Lines Display -->
                        <div class="mt-4">
                            <div id="rule-lines-display-<?= $transaction['id'] ?>"></div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                            <button type="submit" class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Update</button>
                            <button type="button" command="close" commandfor="edit-dialog-<?= $transaction['id'] ?>" class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">Cancel</button>
                        </div>
                    </form>

                </el-dialog-panel>
            </div>
        </dialog>
    </el-dialog>
<?php endforeach; ?>

<!-- Delete Account Modal -->
<?php foreach ($transactions as $transaction): ?>
    <el-dialog>
        <dialog id="delete-dialog-<?= $transaction['id'] ?>" aria-labelledby="delete-dialog-title-<?= $transaction['id'] ?>" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
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
                                <h3 id="delete-dialog-title-<?= $transaction['id'] ?>" class="text-lg font-semibold text-gray-900">Delete Transaction</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Are you sure you want to delete <span class="font-semibold"><?= ($transaction['rule_name']) ?></span> reference # <span class="font-semibold"><?= ($transaction['reference_no']) ?></span> with a total amount of <span class="font-semibold"><?= ($transaction['total_amount']) ?></span> as of <span class="font-semibold"><?= ($transaction['transaction_date']) ?></span> ? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Form -->
                    <form action="../controllers/transactions.controller.php" method="POST" class="px-6 pb-4">
                        <input type="hidden" name="action" value="delete_transaction">
                        <input type="hidden" name="id" value="<?= $transaction['id'] ?>">
                        <input type="hidden" name="page" value="<?= $page ?>">

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                Delete
                            </button>
                            <button type="button" command="close" commandfor="delete-dialog-<?= $transaction['id'] ?>"
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
    document.addEventListener('DOMContentLoaded', () => {

        function fetchRuleLines(ruleId, display, transactionId = null) {
            display.innerHTML = '<p class="text-gray-500 text-sm">Loading...</p>';

            if (!ruleId) {
                display.innerHTML = '';
                return;
            }

            const formData = new FormData();
            formData.append('rule_id', ruleId);

            if (transactionId) {
                formData.append('transaction_id', transactionId);
            }

            fetch('../ajax/getrulelines.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        display.innerHTML = `<p class="text-red-500 text-sm">Error: ${data.error}</p>`;
                        return;
                    }

                    if (!data.length) {
                        display.innerHTML = '<p class="text-gray-500 text-sm">No lines found</p>';
                        return;
                    }

                    display.innerHTML = '';

                    data.forEach(line => {
                        const accountId = line.account_id ?? '';
                        const accountName = line.account_name ?? '';
                        const entryType = line.entry_type ?? '';
                        const amount = line.amount ?? '';

                        const lineHTML = `
                        <h3 class="text-md font-semibold text-gray-900 mb-2">Journal Entries</h3>
                    <div class="flex gap-4 mb-2 items-end pb-2 border-b border-gray-200">
                        <!-- Account Name -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm text-gray-700 mb-1">Account Name</label>
                            <input type="text" value="${accountName}" disabled readonly
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-900">
                            <input type="hidden" name="account_ids[]" value="${accountId}">
                        </div>

                        <!-- Entry Type -->
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm text-gray-700 mb-1">Entry Type</label>
                            <input type="hidden" name="entry_types[]" value="${entryType}">
                            <input type="text" value="${entryType}" readonly disabled
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-900 capitalize">
                        </div>

                        <!-- Amount -->
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-sm text-gray-700 mb-1">Amount</label>
                            <input type="number" placeholder="0.00" required min="0" name="amounts[]" step="0.01"
                                value="${amount}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                        </div>
                    </div>`;

                        display.innerHTML += lineHTML;
                    });
                })
                .catch(err => {
                    console.error('Error:', err);
                    display.innerHTML = '<p class="text-red-500 text-sm">Error fetching data</p>';
                });
        }

        // Handle ADD modal
        const addRuleSelect = document.getElementById('rule_id');
        const addDisplay = document.getElementById('rule-lines-display');

        if (addRuleSelect && addDisplay) {
            addRuleSelect.addEventListener('change', () => {
                fetchRuleLines(addRuleSelect.value, addDisplay);
            });
        }

        // Handle EDIT modals (multiple)
        document.querySelectorAll('select[id^="rule_id_"]').forEach(select => {
            const transactionId = select.id.replace('rule_id_', '');
            const display = document.getElementById('rule-lines-display-' + transactionId);

            if (display) {
                // Trigger on change
                select.addEventListener('change', () => {
                    fetchRuleLines(select.value, display, transactionId);
                });

                // Load on page load if rule is already selected (for edit modal)
                if (select.value) {
                    fetchRuleLines(select.value, display, transactionId);
                }
            }
        });
    });
</script>