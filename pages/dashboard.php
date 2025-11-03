<?php
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
$title = "Dashboard";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$journal = new JournalEntries($conn, null, null, null, null, null, null);

// Get filter parameters
$filterMonth = $_GET['month'] ?? '';
$filterYear = $_GET['year'] ?? '';

// Get data for filtered statements (top 3 cards)
$balanceSheetData = $journal->getBalanceSheet($filterMonth, $filterYear);
$incomeStatementData = $journal->getIncomeStatement($filterMonth, $filterYear);
$cashFlowData = $journal->getCashFlow($filterMonth, $filterYear);
$transactions = $journal->getRecentTransactions();

// Process Balance Sheet Data
$totalAssets = 0;
$totalLiabilities = 0;
$totalEquity = 0;

foreach ($balanceSheetData as $bal) {
  $balance = floatval($bal['balance']);
  $type = strtoupper($bal['account_type']);

  if (isset($bal['is_net_income']) && $bal['is_net_income']) {
    $totalEquity += $balance;
  } elseif ($type == 'ASSET') {
    $totalAssets += $balance;
  } elseif ($type == 'LIABILITY') {
    $totalLiabilities += $balance;
  } elseif ($type == 'EQUITY') {
    $totalEquity += $balance;
  }
}

$hasBalanceSheetData = !empty($balanceSheetData);

// Process Income Statement Data
$totalRevenue = 0;
$totalExpenses = 0;

foreach ($incomeStatementData as $entry) {
  $balance = floatval($entry['balance']);
  if (strtoupper($entry['account_type']) === 'REVENUE') {
    $totalRevenue += $balance;
  } elseif (strtoupper($entry['account_type']) === 'EXPENSE') {
    $totalExpenses += abs($balance);
  }
}

$netIncome = $totalRevenue - $totalExpenses;
$hasIncomeStatementData = !empty($incomeStatementData);

// Process Cash Flow Data
$operatingCash = array_sum(array_column($cashFlowData['Operating'], 'balance'));
$investingCash = array_sum(array_column($cashFlowData['Investing'], 'balance'));
$financingCash = array_sum(array_column($cashFlowData['Financing'], 'balance'));
$netCashFlow = $cashFlowData['NetCash'];
$endingCash = $cashFlowData['EndingCash'];
$hasCashFlowData = !empty($cashFlowData['Operating']) || !empty($cashFlowData['Investing']) || !empty($cashFlowData['Financing']);

// Get monthly data for charts (last 12 months) - ALWAYS UNFILTERED
$monthlyRevenue = [];
$monthlyExpenses = [];
$monthlyNetIncome = [];
$monthLabels = [];

for ($i = 11; $i >= 0; $i--) {
  $month = date('m', strtotime("-$i months"));
  $year = date('Y', strtotime("-$i months"));
  $monthLabels[] = date('M Y', strtotime("-$i months"));

  $monthData = $journal->getIncomeStatement($month, $year);
  $revenue = 0;
  $expenses = 0;

  foreach ($monthData as $entry) {
    if (strtoupper($entry['account_type']) === 'REVENUE') {
      $revenue += floatval($entry['balance']);
    } elseif (strtoupper($entry['account_type']) === 'EXPENSE') {
      $expenses += abs(floatval($entry['balance']));
    }
  }

  $monthlyRevenue[] = $revenue;
  $monthlyExpenses[] = $expenses;
  $monthlyNetIncome[] = $revenue - $expenses;
}

// Get cash flow data for last 12 months - ALWAYS UNFILTERED
$monthlyCashFlow = [];
$cashFlowLabels = [];

for ($i = 11; $i >= 0; $i--) {
  $month = date('m', strtotime("-$i months"));
  $year = date('Y', strtotime("-$i months"));
  $cashFlowLabels[] = date('M Y', strtotime("-$i months"));

  $cashData = $journal->getCashFlow($month, $year);
  $monthlyCashFlow[] = $cashData['NetCash'];
}

function formatCurrency($amount)
{
  return number_format(abs($amount), 2);
}
?>

<main class="bg-gray-100 px-6 py-4">
  <!-- Header with Filters -->
  <div class="mb-6 flex justify-between items-center">
    <div>
      <h2 class="text-3xl font-bold text-gray-800">Dashboard</h2>
      <p class="text-gray-600">Financial Overview and Performance Metrics</p>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-4 rounded-lg shadow">
      <form method="GET" class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
        <div class="sm:w-48">
          <label class="block text-sm text-gray-700 mb-1 font-medium">Filter by Month</label>
          <select name="month" onchange="this.form.submit()"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition bg-white">
            <option value="">--All--</option>
            <?php for ($m = 1; $m <= 12; $m++): ?>
              <option value="<?= $m ?>" <?= ($filterMonth == $m) ? 'selected' : '' ?>>
                <?= date('F', mktime(0, 0, 0, $m, 1)) ?>
              </option>
            <?php endfor; ?>
          </select>
        </div>

        <div class="sm:w-48">
          <label class="block text-sm text-gray-700 mb-1 font-medium">Filter by Year</label>
          <select name="year" onchange="this.form.submit()"
            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none transition bg-white">
            <option value="">--All--</option>
            <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
              <option value="<?= $y ?>" <?= ($filterYear == $y) ? 'selected' : '' ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>

        <?php if ($filterMonth || $filterYear): ?>
          <div class="flex items-end">
            <a href="?" class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-md hover:bg-gray-300 transition duration-200">
              Clear
            </a>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </div>

  <!-- Filter Status Badge -->
  <?php if ($filterMonth || $filterYear): ?>
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
      <p class="text-sm text-blue-800">
        <strong>Filtered Data:</strong>
        Showing data for
        <span class="font-semibold">
          <?php
          if ($filterMonth && $filterYear) {
            echo date('F', mktime(0, 0, 0, $filterMonth, 1)) . ' ' . $filterYear;
          } elseif ($filterMonth) {
            echo date('F', mktime(0, 0, 0, $filterMonth, 1)) . ' (All Years)';
          } elseif ($filterYear) {
            echo 'All Months ' . $filterYear;
          }
          ?>
        </span>
      </p>
    </div>
  <?php endif; ?>

    <?php if (isset($_SESSION['dashboard_errors'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['dashboard_errors'] as $error): ?>
                        <p class="text-sm font-medium text-red-800 ">
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
        unset($_SESSION['dashboard_errors']);
    endif;
    ?>

  <!-- Top Row: 3 Summary Cards -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- Balance Sheet Summary Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <div class="bg-gradient-to-r from-red-600 to-red-700 p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-white text-lg font-bold">Balance Sheet</h3>
            <p class="text-red-100 text-sm">Financial Position</p>
          </div>
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700 w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="p-6">
        <?php if (!$hasBalanceSheetData): ?>
          <div class="py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            <p class="text-gray-500 font-semibold">No balance sheet data found</p>
            <p class="text-gray-400 text-sm mt-1">No records for the selected period</p>
          </div>
        <?php else: ?>
          <div class="space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Total Assets</span>
              <span class="text-xl font-bold text-gray-900"><?= formatCurrency($totalAssets) ?></span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Total Liabilities</span>
              <span class="text-xl font-bold text-gray-900"><?= formatCurrency($totalLiabilities) ?></span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Total Equity</span>
              <span class="text-xl font-bold text-gray-900"><?= formatCurrency($totalEquity) ?></span>
            </div>
            <div class="flex justify-between items-center pt-2">
              <span class="text-gray-700 font-bold">Balanced?</span>
              <?php
              $isBalanced = abs($totalAssets - ($totalLiabilities + $totalEquity)) < 0.01;
              ?>
              <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $isBalanced ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                <?= $isBalanced ? '✓ Balanced' : '✗ Not Balanced' ?>
              </span>
            </div>
          </div>
        <?php endif; ?>
        <a href="balancesheet.php<?= ($filterMonth || $filterYear) ? '?month=' . $filterMonth . '&year=' . $filterYear : '' ?>"
          class="mt-4 block text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition duration-200">
          View Details
        </a>
      </div>
    </div>

    <!-- Income Statement Summary Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-white text-lg font-bold">Income Statement</h3>
            <p class="text-blue-100 text-sm">Profitability</p>
          </div>
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7 text-blue-700 w-9 h-9">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </div>
        </div>
      </div>
      <div class="p-6">
        <?php if (!$hasIncomeStatementData): ?>
          <div class="py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <p class="text-gray-500 font-semibold">No income statement data found</p>
            <p class="text-gray-400 text-sm mt-1">No records for the selected period</p>
          </div>
        <?php else: ?>
          <div class="space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Total Revenue</span>
              <span class="text-xl font-bold text-green-600"><?= formatCurrency($totalRevenue) ?></span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Total Expenses</span>
              <span class="text-xl font-bold text-red-600"><?= formatCurrency($totalExpenses) ?></span>
            </div>
            <div class="flex justify-between items-center pt-2">
              <span class="text-gray-700 font-bold">Net Income</span>
              <span class="text-2xl font-bold <?= $netIncome >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $netIncome >= 0 ? '' : '(' ?><?= formatCurrency($netIncome) ?><?= $netIncome >= 0 ? '' : ')' ?>
              </span>
            </div>
            <?php if ($totalRevenue > 0): ?>
              <div class="bg-gray-50 p-3 rounded-lg">
                <div class="flex justify-between items-center">
                  <span class="text-sm text-gray-600">Profit Margin</span>
                  <span class="text-sm font-bold <?= $netIncome >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= number_format(($netIncome / $totalRevenue) * 100, 1) ?>%
                  </span>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
        <a href="incomestatement.php<?= ($filterMonth || $filterYear) ? '?month=' . $filterMonth . '&year=' . $filterYear : '' ?>"
          class="mt-4 block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-200">
          View Details
        </a>
      </div>
    </div>

    <!-- Cash Flow Summary Card -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <div class="bg-gradient-to-r from-green-600 to-green-700 p-4">
        <div class="flex items-center justify-between">
          <div>
            <h3 class="text-white text-lg font-bold">Cash Flow</h3>
            <p class="text-green-100 text-sm">Liquidity</p>
          </div>
          <div class="bg-white bg-opacity-20 p-3 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-700 w-8 h-8">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
            </svg>
          </div>
        </div>
      </div>
      <div class="p-6">
        <?php if (!$hasCashFlowData): ?>
          <div class="py-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <p class="text-gray-500 font-semibold">No cash flow data found</p>
            <p class="text-gray-400 text-sm mt-1">No records for the selected period</p>
          </div>
        <?php else: ?>
          <div class="space-y-4">
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Operating</span>
              <span class="text-lg font-bold <?= $operatingCash >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $operatingCash >= 0 ? '' : '(' ?><?= formatCurrency($operatingCash) ?><?= $operatingCash >= 0 ? '' : ')' ?>
              </span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Investing</span>
              <span class="text-lg font-bold <?= $investingCash >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $investingCash >= 0 ? '' : '(' ?><?= formatCurrency($investingCash) ?><?= $investingCash >= 0 ? '' : ')' ?>
              </span>
            </div>
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
              <span class="text-gray-600 font-medium">Financing</span>
              <span class="text-lg font-bold <?= $financingCash >= 0 ? 'text-green-600' : 'text-red-600' ?>">
                <?= $financingCash >= 0 ? '' : '(' ?><?= formatCurrency($financingCash) ?><?= $financingCash >= 0 ? '' : ')' ?>
              </span>
            </div>
            <div class="bg-green-50 p-3 rounded-lg">
              <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-gray-700">Ending Cash</span>
                <span class="text-xl font-bold text-green-700"><?= formatCurrency($endingCash) ?></span>
              </div>
            </div>
          </div>
        <?php endif; ?>
        <a href="cashflow.php<?= ($filterMonth || $filterYear) ? '?month=' . $filterMonth . '&year=' . $filterYear : '' ?>"
          class="mt-4 block text-center bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition duration-200">
          View Details
        </a>
      </div>
    </div>
  </div>

  <!-- Bottom Row: 2 Charts (Always show last 12 months - unfiltered) -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    <!-- Revenue vs Expenses Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="mb-4">
        <h3 class="text-xl font-bold text-gray-800">Revenue vs Expenses</h3>
        <p class="text-sm text-gray-600">Last 12 Months Trend</p>
      </div>
      <div style="height: 250px;">
        <canvas id="revenueExpensesChart"></canvas>
      </div>
    </div>

    <!-- Cash Flow Trend Chart -->
    <div class="bg-white rounded-lg shadow-lg p-6">
      <div class="mb-4">
        <h3 class="text-xl font-bold text-gray-800">Cash Flow Trend</h3>
        <p class="text-sm text-gray-600">Last 12 Months Net Cash Flow</p>
      </div>
      <div style="height: 250px;">
        <canvas id="cashFlowChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Recent Transactions Section -->
  <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-red-600 to-red-700 p-4">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-white text-lg font-bold">Recent Transactions</h3>
          <p class="text-white text-sm">Latest Transactions</p>
        </div>
        <div class="bg-white bg-opacity-20 p-3 rounded-full">
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700 h-8 w-8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
          </svg>
        </div>
      </div>
    </div>

    <?php if (empty($transactions)): ?>
      <div class="p-8 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <p class="text-gray-500 font-semibold text-lg">No Transactions Found</p>
        <p class="text-gray-400 text-sm mt-2">There are no transactions recorded yet.</p>
      </div>
    <?php else: ?>
      <div>
        <table class="min-w-full bg-white border border-gray-200">
          <thead>
            <tr class="bg-red-700 text-white">
              <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Date</th>
              <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Name</th>
              <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Reference Number #</th>
              <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Transaction Details</th>
              <th class="py-2 px-4 text-left text-sm font-medium border-r border-red-600">Total Amount</th>
              <th class="py-2 px-4 text-center text-sm font-medium border-r border-red-600">Encoded By</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($transactions as $transaction): ?>
              <tr class="hover:bg-gray-100 hover:scale-[1.02] transition-all duration-200">
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
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</main>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
  // Revenue vs Expenses Chart
  const revenueExpensesCtx = document.getElementById('revenueExpensesChart').getContext('2d');
  const revenueExpensesChart = new Chart(revenueExpensesCtx, {
    type: 'line',
    data: {
      labels: <?= json_encode($monthLabels) ?>,
      datasets: [{
          label: 'Revenue',
          data: <?= json_encode($monthlyRevenue) ?>,
          borderColor: 'rgb(34, 197, 94)',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          tension: 0.4,
          fill: true
        },
        {
          label: 'Expenses',
          data: <?= json_encode($monthlyExpenses) ?>,
          borderColor: 'rgb(239, 68, 68)',
          backgroundColor: 'rgba(239, 68, 68, 0.1)',
          tension: 0.4,
          fill: true
        },
        {
          label: 'Net Income',
          data: <?= json_encode($monthlyNetIncome) ?>,
          borderColor: 'rgb(59, 130, 246)',
          backgroundColor: 'rgba(59, 130, 246, 0.1)',
          tension: 0.4,
          fill: true
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let label = context.dataset.label || '';
              if (label) label += ': ';
              let value = context.parsed.y;
              return label + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
          }
        }
      }
    }
  });

  // Cash Flow Chart
  const cashFlowCtx = document.getElementById('cashFlowChart').getContext('2d');
  const cashFlowChart = new Chart(cashFlowCtx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($cashFlowLabels) ?>,
      datasets: [{
        label: 'Net Cash Flow',
        data: <?= json_encode($monthlyCashFlow) ?>,
        backgroundColor: <?= json_encode(array_map(function ($val) {
                            return $val >= 0 ? 'rgba(34, 197, 94, 0.8)' : 'rgba(239, 68, 68, 0.8)';
                          }, $monthlyCashFlow)) ?>,
        borderColor: <?= json_encode(array_map(function ($val) {
                        return $val >= 0 ? 'rgb(34, 197, 94)' : 'rgb(239, 68, 68)';
                      }, $monthlyCashFlow)) ?>,
        borderWidth: 2
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              let value = context.parsed.y;
              return 'Net Cash: ' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }
          }
        }
      }
    }
  });
</script>