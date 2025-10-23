<?php
$title = "Journal Entries";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$entry = new JournalEntries($conn, null, null, null, null, null, null);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = $_GET['search'] ?? '';
$month = $_GET['month'] ?? '';
$year = $_GET['year'] ?? '';

$journalEntries = $entry->getPaginatedJournalEntries($page, $search, $month, $year);
$total_pages = $entry->getTotalJournalPages($search, $month, $year);

$queryParams = '&search=' . urlencode($search) . '&month=' . urlencode($month) . '&year=' . urlencode($year);
?>

<main class="g-gray-100 px-6 py-4">
    <div class="mb-4">
        <h2 class="text-2xl font-semibold"><?= $title ?></h2>
        <p class="text-gray-600">Manage <?= $title ?> here.</p>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 mb-4 rounded shadow">
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

            <!-- Search -->
            <div class="flex-1">
                <label class="block text-sm text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search account or description..."
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

                <?php if ($search || $month || $year): ?>
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

            <?php if (empty($journalEntries)): ?>
                <tbody>
                    <tr>
                        <td colspan="4" class="py-4 px-4 text-center text-gray-500 font-semibold border">
                            No journal entries found.
                        </td>
                    </tr>
                </tbody>
            <?php else: ?>
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
                            <td colspan="4" class="py-1 px-4 text-gray-600 italic">
                                (<?= htmlspecialchars($je['description'] ?? '') ?>)
                            </td>
                        </tr>
                    </tbody>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>



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