<?php
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

$maxRows = max(count($operating), count($investing), count($financing));
$netCash = $cashFlow['NetCash'];
?>

<main class="bg-gray-100 px-6 py-4">
    <div class="mb-4 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-semibold"><?= $title ?></h2>
            <p class="text-gray-600">Summary of Cash Flow by Activities.</p>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
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
    <div class="bg-white rounded-lg shadow">
        <table class="min-w-full bg-white border border-gray-200">
            <thead>
                <tr class="bg-red-700 text-white">
                    <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Operating Activities</th>
                    <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Investing Activities</th>
                    <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Financing Activities</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < $maxRows; $i++): ?>
                    <tr class="hover:scale-[1.02] transition-transform duration-150 hover:bg-gray-100">
                        <td class="py-2 px-4 text-right font-semibold border-r border-gray-200">
                            <?php if (isset($operating[$i])): ?>
                                <?= htmlspecialchars($operating[$i]['name']) ?>: <?= number_format($operating[$i]['balance'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-4 text-right font-semibold border-r border-gray-200">
                            <?php if (isset($investing[$i])): ?>
                                <?= htmlspecialchars($investing[$i]['name']) ?>: <?= number_format($investing[$i]['balance'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td class="py-2 px-4 text-right font-semibold border-r border-gray-200">
                            <?php if (isset($financing[$i])): ?>
                                <?= htmlspecialchars($financing[$i]['name']) ?>: <?= number_format($financing[$i]['balance'], 2) ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endfor; ?>

                <tr class="bg-gray-50 font-bold">
                    <td class="py-2 px-4 border-t text-right border-gray-300">Total Operating = <?= number_format($totalOperating, 2) ?></td>
                    <td class="py-2 px-4 border-t text-right border-gray-300">Total Investing = <?= number_format($totalInvesting, 2) ?></td>
                    <td class="py-2 px-4 border-t text-right border-gray-300">Total Financing = <?= number_format($totalFinancing, 2) ?></td>
                </tr>

                <tr class="bg-gray-100 font-bold">
                    <td colspan="3" class="py-2 px-4 border-t text-center border-gray-300">
                        Net Cash = <?= number_format($netCash, 2) ?>
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
