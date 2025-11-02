<?php
session_start();
$title = "Dashboard";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";

require_once "../configs/dbc.php";
require_once "../models/journalentries.class.php";

$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');

$journal = new JournalEntries($conn, null, null, null, null, null, null);
$totals = $journal->getTotalsByMonth($month, $year);
$breakdown = $journal->getExpenseBreakdown($month, $year);
$recentTransactions = $journal->getRecentTransactions();
?>

<main class="bg-gray-100 px-6 py-6">
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-2xl font-semibold"><?= $title ?></h2>
    <form id="filterForm" method="GET" class="flex gap-2">
      <select name="month" class="border rounded px-2 py-1" onchange="document.getElementById('filterForm').submit()">
        <?php for ($m = 1; $m <= 12; $m++): ?>
          <option value="<?= $m ?>" <?= $m == $month ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 1)) ?></option>
        <?php endfor; ?>
      </select>
      <select name="year" class="border rounded px-2 py-1" onchange="document.getElementById('filterForm').submit()">
        <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
          <option value="<?= $y ?>" <?= $y == $year ? 'selected' : '' ?>><?= $y ?></option>
        <?php endfor; ?>
      </select>
    </form>
  </div>

  <?php if (empty($totals) || (!$totals['total_revenue'] && !$totals['total_expenses'])): ?>
    <div class="bg-white p-6 rounded shadow text-center">
      <p class="text-gray-500 text-lg">No data available for <?= date('F', mktime(0, 0, 0, $month, 1)) ?> <?= $year ?>.</p>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Total Revenue</h2>
        <p class="text-2xl font-bold text-green-600"><?= number_format($totals['total_revenue'] ?? 0, 2) ?></p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Total Expenses</h2>
        <p class="text-2xl font-bold text-red-600"><?= number_format($totals['total_expenses'] ?? 0, 2) ?></p>
      </div>
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold">Net Profit</h2>
        <p class="text-2xl font-bold text-blue-600"><?= number_format(($totals['total_revenue'] ?? 0) - ($totals['total_expenses'] ?? 0), 2) ?></p>
      </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-6">
      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Expense Breakdown</h2>
        <canvas id="expenseChart" class="w-full h-64"></canvas>
      </div>

      <div class="bg-white p-4 rounded shadow">
        <h2 class="text-lg font-semibold mb-4">Recent Transactions</h2>
        <?php if (!empty($recentTransactions)): ?>
          <table class="w-full border-collapse">
            <thead>
              <tr class="bg-gray-100">
                <th class="border px-2 py-1 text-left">Date</th>
                <th class="border px-2 py-1 text-left">Reference #</th>
                <th class="border px-2 py-1 text-left">Description</th>
                <th class="border px-2 py-1 text-right">Total Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentTransactions as $txn): ?>
                <tr>
                  <td class="border px-2 py-1"><?= htmlspecialchars($txn['transaction_date']) ?></td>
                  <td class="border px-2 py-1"><?= htmlspecialchars($txn['reference_no']) ?></td>
                  <td class="border px-2 py-1"><?= htmlspecialchars($txn['description']) ?></td>
                  <td class="border px-2 py-1 text-right">₱<?= number_format($txn['total_amount'], 2) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-gray-500">No recent transactions found.</p>
        <?php endif; ?>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      const ctx = document.getElementById('expenseChart');
      const expenseData = {
        labels: <?= json_encode(array_column($breakdown, 'account_name')) ?>,
        datasets: [{
          data: <?= json_encode(array_column($breakdown, 'total')) ?>,
          backgroundColor: [
            '#ef4444', '#f97316', '#facc15', '#22c55e', '#3b82f6', '#8b5cf6', '#ec4899'
          ],
          borderWidth: 1
        }]
      };
      new Chart(ctx, {
        type: 'pie',
        data: expenseData,
        options: {
          plugins: {
            legend: {
              position: 'bottom'
            }
          }
        }
      });
    </script>
  <?php endif; ?>
</main>