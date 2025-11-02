<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../configs/dbc.php';
require_once '../models/journalentry.class.php';

$month = $_GET['month'] ?? date('n');
$year = $_GET['year'] ?? date('Y');

$journal = new JournalEntries($conn, null, null, null, null, null, null);
$totals = $journal->getTotalsByMonth($month, $year);
$breakdown = $journal->getExpenseBreakdown($month, $year);
$recentTransactions = $journal->getRecentTransactions();

$_SESSION['dashboard_data'] = [
    'totals' => $totals,
    'breakdown' => $breakdown,
    'recentTransactions' => $recentTransactions,
    'month' => $month,
    'year' => $year
];

header("Location: ../pages/dashboard.php");
exit;
