<?php
$title = "Transaction Rules";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/transactionrules.class.php";
require_once "../models/chartofacc.class.php";

$chartofacc = new ChartofAccounts($conn, null, null, null, null);
$transactionRules = new transactionRules($conn, null, null, null, null, null, null);

$page   = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$debitAccountFilter = $_GET['debit_account_id'] ?? '';
$creditAccountFilter = $_GET['credit_account_id'] ?? '';

$queryParams = "&search=" . urlencode($search) . "&filter=" . urlencode($categoryFilter)
    . "&debit_account_id=" . urlencode($debitAccountFilter) . "&credit_account_id=" . urlencode($creditAccountFilter);

$charts = $chartofacc->getAllChart();
$transactions  = $transactionRules->getPaginatedRules($page, $search,  $categoryFilter, $debitAccountFilter, $creditAccountFilter);
$total_pages = $transactionRules->getTotalPages($search, $categoryFilter, $debitAccountFilter, $creditAccountFilter);
?>

<main class="g-gray-100 px-6 py-2">
    <div>
        <h2 class="text-2xl font-semibold"><?php echo $title ?></h2>
        <p class=" text-gray-600">Manage Transaction Rules here.</p>
    </div>

    <div class="flex justify-end mb-2">
        <button
            command="show-modal"
            commandfor="dialog"
            id="openModalBtn"
            class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transform transition duration-200 hover:-translate-y-0.5 hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            <span>Add New Transaction Rule</span>
        </button>
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
                        <?php echo ($_SESSION['success_message']); ?>
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

    <?php if (isset($_SESSION['rules_error'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['rules_error'] as $error): ?>
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
        unset($_SESSION['rules_error']);
    endif;
    ?>


    <!-- Search and Filter Form -->
    <div class="bg-white  p-4 mb-4">
        <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
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
                        placeholder="Search rule name, category or description..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                </div>
            </div>



            <!-- Filter Dropdown -->
            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Sort by Category</label>
                <select
                    name="category"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">--Category--</option>
                    <option value="General" <?= ($_GET['category'] ?? '') === 'General' ? 'selected' : '' ?>>General</option>
                    <option value="Invoice" <?= ($_GET['category'] ?? '') === 'Invoice' ? 'selected' : '' ?>>Invoice</option>
                    <option value="Payment" <?= ($_GET['category'] ?? '') === 'Payment' ? 'selected' : '' ?>>Payment</option>
                    <option value="Purchase" <?= ($_GET['category'] ?? '') === 'Purchase' ? 'selected' : '' ?>>Purchase</option>
                </select>
            </div>

            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Sort by Debit Account</label>
                <select
                    name="debit_account_id"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">--Debit Accounts--</option>
                    <?php foreach ($charts as $chart): ?>
                        <option value="<?= $chart['id'] ?>" <?= ($_GET['debit_account_id'] ?? '') === $chart['id'] ? 'selected' : '' ?>>
                            <?= $chart['account_name']; ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="sm:w-48">
                <label class="block text-sm text-gray-700 mb-1">Sort by Credit Account</label>
                <select
                    name="credit_account_id"
                    onchange="this.form.submit()"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                    <option value="">--Credit Accounts--</option>
                    <?php foreach ($charts as $chart): ?>
                        <option value="<?= $chart['id'] ?>" <?= ($_GET['credit_account_id'] ?? '') === $chart['id'] ? 'selected' : '' ?>>
                            <?= $chart['account_name']; ?>
                        </option>
                    <?php endforeach; ?>

                </select>
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

    <!-- Chart of Account Table -->
    <div class=" rounded-lg shadow">
        <table class="min-w-full bg-white">
            <thead>
                <tr class="bg-red-700 text-white">
                    <th class="py-2 px-4 text-left text-sm font-medium">Transaction Rule Name</th>
                    <th class="py-2 px-4 text-left text-sm font-medium">Category</th>
                    <th class="py-2 px-4 text-left text-sm font-medium">Debit Account</th>
                    <th class="py-2 px-4 text-center text-sm font-medium">Credit Account</th>
                    <th class="py-2 px-4 text-center text-sm font-medium">Description</th>
                    <th class="py-2 px-4 text-center text-sm font-medium">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
                <?php foreach ($transactions as $transaction): ?>
                    <tr class="hover:bg-gray-100 transition-all duration-200 hover:scale-[1.02] ">
                        <td class="py-2 px-4 text-gray-900 font-medium">
                            <?= ($transaction['rule_name']); ?>
                        </td>
                        <td class="py-2 px-4 text-gray-600 ">
                            <?= ($transaction['category']); ?>
                        </td>
                        <td class="py-2 px-4 text-gray-600">
                            <?= ($transaction['debit_account_name']); ?>
                        </td>
                        <td class="py-2 px-4 text-gray-600">
                            <?= ($transaction['credit_account_name']); ?>
                        </td>

                        <td class="py-2 px-4 text-gray-600">
                            <?= ($transaction['description']); ?>
                        </td>
                        <td class="py-2 px-4">
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

<!-- Add New Transaction Rule Modal -->
<el-dialog>
    <dialog id="dialog" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="dialog-title" class="text-lg font-semibold text-gray-900 pt-2">Add New Account</h3>
                        </div>
                    </div>
                </div>

                <!-- Add Transaction Form -->
                <form action="../controllers/chartofacc.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                    <input type="hidden" name="action" value="add_account">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Transaction Rule Name</label>
                        <input type="text" name="rule_names" placeholder="Enter a Transaction Rule Name"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" />
                    </div>


                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Category</label>
                        <select name="category"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            <option value="General">--Choose a Category--</option>
                            <option value="General">General</option>
                            <option value="Invoice">Invoice</option>
                            <option value="Payment">Payment</option>
                            <option value="Purchase">Purchase</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">-- Choose a Debit Account --</label>
                        <select
                            name="debit_account_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">--Debit Accounts--</option>
                            <?php foreach ($charts as $chart): ?>
                                <option value="<?= $chart['id'] ?>" <?= ($_GET['debit_account_id'] ?? '') === $chart['id'] ? 'selected' : '' ?>>
                                    <?= $chart['account_name']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">-- Choose a Credit Account --</label>
                        <select
                            name="credit_account_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                            <option value="">--Credit Accounts--</option>
                            <?php foreach ($charts as $chart): ?>
                                <option value="<?= $chart['id'] ?>" <?= ($_GET['credit_account_id'] ?? '') === $chart['id'] ? 'selected' : '' ?>>
                                    <?= $chart['account_name']; ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Description</label>
                        <textarea name="description" placeholder="Enter a Description for the Account"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none"></textarea>
                    </div>

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

<!-- Edit Transaction Rulee Modal -->
<?php foreach ($transactions as $transaction): ?>
    <el-dialog>
        <dialog id="edit-dialog-<?= $transaction['id'] ?>" aria-labelledby="edit-dialog-title-<?= $transaction['id'] ?>" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
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
                                <h3 id="edit-dialog-title-<?= $transaction['id'] ?>" class="text-lg font-semibold text-gray-900">Edit Transaction Rule</h3>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Account Form -->
                    <form action="../controllers/chartofacc.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                        <input type="hidden" name="id" value="<?= ($transaction['id']) ?>">
                        <input type="hidden" name="action" value="update_account">

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Transaction Rule Name</label>
                            <input type="text" name="acc_name" value="<?= ($transaction['rule_name']) ?>" placeholder="Enter an Account Name"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" required />
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Category</label>
                            <select name="category"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                                <option value="General" <?= $transaction['category'] == 'General' ? 'selected' : '' ?>>General</option>
                                <option value="Invoice" <?= $transaction['category'] == 'Invoice' ? 'selected' : '' ?>>Invoice</option>
                                <option value="Payment" <?= $transaction['category'] == 'Payment' ? 'selected' : '' ?>>Payment</option>
                                <option value="Purchase" <?= $transaction['category'] == 'Purchase' ? 'selected' : '' ?>>Purchase</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">-- Choose a Debit Account --</label>
                            <select
                                name="debit_account_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">--Debit Accounts--</option>
                                <?php foreach ($charts as $chart): ?>
                                    <option value="<?= $chart['id'] ?>" <?= $transaction['debit_account_id'] == $chart['id'] ? 'selected' : '' ?>>
                                        <?= $chart['account_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">-- Choose a Credit Account --</label>
                            <select
                                name="debit_account_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                <option value="">--Debit Accounts--</option>
                                <?php foreach ($charts as $chart): ?>
                                    <option value="<?= $chart['id'] ?>" <?= $transaction['credit_account_id'] == $chart['id'] ? 'selected' : '' ?>>
                                        <?= $chart['account_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700 mb-1">Description</label>
                            <textarea name="description" placeholder="Enter a Description for the Account"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none"><?= ($transaction['description']) ?></textarea>
                        </div>

                        <!-- Buttons -->
                        <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                            <button type="submit"
                                class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                                Update
                            </button>
                            <button type="button" command="close" commandfor="edit-dialog-<?= $transaction['id'] ?>"
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
                                <h3 id="delete-dialog-title-<?= $transaction['id'] ?>" class="text-lg font-semibold text-gray-900">Delete Account</h3>
                                <p class="mt-2 text-sm text-gray-600">
                                    Are you sure you want to delete <span class="font-semibold"><?= ($transaction['rule_name']) ?></span>? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Form -->
                    <form action="../controllers/chartofacc.controller.php" method="POST" class="px-6 pb-4">
                        <input type="hidden" name="action" value="delte_account">
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