<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

require_once '../configs/dbc.php';
require_once '../models/journalentries.class.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

$journal = new JournalEntries($conn, null, null, null, null, null, null);

$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';
$action = $_POST['action'];

$trialBalance = $journal->getAllTrialBalance($month, $year);

switch ($action) {
    case "download_pdf":
        ob_start();
        include('../templates/trialbalance.template.php');
        $html = ob_get_clean();

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'Trial_Balance';
        if (!empty($month) && !empty($year)) {
            $dateObj = DateTime::createFromFormat('!m', $month);
            $monthName = $dateObj ? $dateObj->format('F') : $month;
            $filename .= '(' . $monthName . ' ' . $year . ')';
        }
        $filename .= '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $filename . '"');
        echo $dompdf->output();
        break;
    case "close_account":
}
