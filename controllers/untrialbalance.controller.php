<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../pages/dashboard.php");
    exit;
}
if (!isset($_SESSION['username']) || !isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    $_SESSION['login_errors'] = ["You dont have access to that page. Please log in first."];
    header("Location: ../index.php");
    exit();
}
if ($_SESSION['role'] !== 'Admin') {
    $_SESSION['dashboard_errors'] = ["Access denied. You dont have access to this page."];
    header("Location: ../pages/dashboard.php"); 
    exit();
}

require_once '../configs/dbc.php';
require_once '../models/journalentries.class.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$journal = new JournalEntries($conn, null, null, null, null, null, null);

$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';
$account_ids = $_POST['account_ids'];
$balances = $_POST['balances'];
$action = $_POST['action'];

$_SESSION['trialbalance_month'] = [];

$trialBalance = $journal->getAllTrialBalance($month, $year);

switch ($action) {
    case "download_pdf":
        ob_start();
        include('../templates/untrialbalance.template.php');
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Unadjusted Trial_Balance';
        if (!empty($month) && !empty($year)) {
            $dateObj = DateTime::createFromFormat('!m', $month);
            $monthName = $dateObj ? $dateObj->format('F') : $month;
            $filename .= '(' . $monthName . ' ' . $year . ')';
        }
        $filename .= '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo $dompdf->output();
        break;

}
