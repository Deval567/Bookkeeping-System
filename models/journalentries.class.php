<?php

class JournalEntries
{
    private $conn;
    private $id;
    private $transaction_id;
    private $account_id;
    private $entry_type;
    private $amount;
    private $description;
    private $date;
    public $limit = 10;

    public function __construct($conn, $transaction_id, $account_id, $entry_type, $amount, $description, $date)
    {
        $this->conn = $conn;
        $this->transaction_id = $transaction_id;
        $this->account_id = $account_id;
        $this->entry_type = $entry_type;
        $this->amount = $amount;
        $this->date = $date;
        $this->description = $description;
    }
    public function getTotalJournalEntries($search = '', $month = '', $year = '')
    {
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(je.description LIKE '%$search%' OR coa.account_name LIKE '%$search%')";
        }

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sql = "
        SELECT COUNT(DISTINCT je.transaction_id) AS total
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
    ";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedJournalEntries($page = 1, $search = '', $month = '', $year = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * $this->limit;
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(je.description LIKE '%$search%' OR coa.account_name LIKE '%$search%')";
        }

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sqlIds = "
        SELECT DISTINCT je.transaction_id
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
        ORDER BY je.date DESC
        LIMIT {$this->limit} OFFSET {$offset}
    ";
        $resultIds = mysqli_query($this->conn, $sqlIds);
        $transactionIds = mysqli_fetch_all($resultIds, MYSQLI_ASSOC);
        if (empty($transactionIds)) return [];

        $ids = implode(',', array_column($transactionIds, 'transaction_id'));


        $sql = "
        SELECT 
            je.transaction_id,
            je.date AS journal_date,
            je.description,
            coa.account_name,
            je.debit,
            je.credit
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        WHERE je.transaction_id IN ($ids)
        ORDER BY je.date DESC
    ";
        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $transactions = [];
        foreach ($rows as $row) {
            $tid = $row['transaction_id'];
            if (!isset($transactions[$tid])) {
                $transactions[$tid] = [
                    'transaction_id' => $tid,
                    'journal_date' => $row['journal_date'],
                    'description' => $row['description'],
                    'accounts' => []
                ];
            }
            $transactions[$tid]['accounts'][] = [
                'account_name' => $row['account_name'],
                'debit' => $row['debit'],
                'credit' => $row['credit']
            ];
        }

        return array_values($transactions);
    }

    public function getTotalJournalPages($search = '', $month = '', $year = '')
    {
        return ceil($this->getTotalJournalEntries($search, $month, $year) / $this->limit);
    }
    public function getAllJournalEntries($month = '', $year = '')
    {
        $conditions = [];

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sqlIds = "
        SELECT DISTINCT je.transaction_id
        FROM journal_entries AS je
        $filterQuery
        ORDER BY je.date ASC
    ";
        $resultIds = mysqli_query($this->conn, $sqlIds);
        $transactionIds = mysqli_fetch_all($resultIds, MYSQLI_ASSOC);

        if (empty($transactionIds)) return [];

        $ids = implode(',', array_column($transactionIds, 'transaction_id'));

        $sql = "
        SELECT 
            je.transaction_id,
            je.date AS journal_date,
            je.description,
            coa.account_name,
            je.debit,
            je.credit
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        WHERE je.transaction_id IN ($ids)
        ORDER BY je.date ASC, je.transaction_id ASC
    ";
        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Group by transaction
        $transactions = [];
        foreach ($rows as $row) {
            $tid = $row['transaction_id'];
            if (!isset($transactions[$tid])) {
                $transactions[$tid] = [
                    'transaction_id' => $tid,
                    'journal_date' => $row['journal_date'],
                    'description' => $row['description'],
                    'accounts' => []
                ];
            }
            $transactions[$tid]['accounts'][] = [
                'account_name' => $row['account_name'],
                'debit' => $row['debit'],
                'credit' => $row['credit']
            ];
        }

        return array_values($transactions);
    }


    public function createJournalEntry($transaction_id, $account_id, $entry_type, $amount, $description, $date)
    {
        $debit = $entry_type === 'debit' ? $amount : 0;
        $credit = $entry_type === 'credit' ? $amount : 0;

        $sql = "INSERT INTO journal_entries (transaction_id, account_id, debit, credit,description,date)
            VALUES (?, ?, ?, ?,?,?)";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiddss", $transaction_id, $account_id, $debit, $credit, $description, $date);
        return mysqli_stmt_execute($stmt);
    }
    public function deleteJournalEntriesByTransactionId($transaction_id)
    {
        $sql = "DELETE FROM journal_entries WHERE transaction_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $transaction_id);
        return mysqli_stmt_execute($stmt);
    }
    public function updateJournalEntry($id, $account_id, $entry_type, $amount, $description, $date)
    {
        $debit = $entry_type === 'debit' ? $amount : 0;
        $credit = $entry_type === 'credit' ? $amount : 0;

        $sql = "UPDATE journal_entries 
            SET debit = ?, credit = ?, description = ?, date = ?
            WHERE transaction_id = ? AND account_id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ddssii", $debit, $credit, $description, $date, $id, $account_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
    // General Ledger

    public function getTotalLedgerAccounts($search = '', $month = '', $year = '')
    {
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(coa.account_name LIKE '%$search%' OR coa.description LIKE '%$search%')";
        }

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sql = "
        SELECT COUNT(DISTINCT je.account_id) AS total
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
    ";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedGeneralLedger($page = 1, $search = '', $month = '', $year = '')
    {
        $page = max(1, (int)$page);
        $offset = ($page - 1) * 5;
        $conditions = [];

        if ($search !== '') {
            $search = mysqli_real_escape_string($this->conn, $search);
            $conditions[] = "(coa.account_name LIKE '%$search%' OR coa.description LIKE '%$search%')";
        }

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sqlIds = "
        SELECT DISTINCT je.account_id
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
        ORDER BY coa.account_type, coa.account_name
        LIMIT 5 OFFSET {$offset}
    ";

        $resultIds = mysqli_query($this->conn, $sqlIds);
        $accountIds = mysqli_fetch_all($resultIds, MYSQLI_ASSOC);

        if (empty($accountIds)) return [];

        $ids = implode(',', array_column($accountIds, 'account_id'));

        $entryConditions = ["je.account_id IN ($ids)"];

        if ($search !== '') {
            $entryConditions[] = "(coa.account_name LIKE '%$search%' OR coa.description LIKE '%$search%')";
        }

        if ($month !== '') {
            $entryConditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $entryConditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $entryFilter = "WHERE " . implode(' AND ', $entryConditions);

        $sql = "
        SELECT 
            coa.id as account_id,
            coa.account_name,
            coa.account_type,
            je.date,
            je.transaction_id,
            t.reference_no,
            tr.rule_name as transaction_type,
            COALESCE(je.description, t.description) as description,
            je.debit,
            je.credit
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        JOIN transactions AS t ON je.transaction_id = t.id
        LEFT JOIN transaction_rules AS tr ON t.rule_id = tr.id
        $entryFilter
        ORDER BY coa.account_type, coa.account_name, je.date, je.transaction_id
    ";

        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $ledgers = [];
        $currentAccountId = null;
        $runningBalance = 0;

        foreach ($rows as $row) {
            $accountId = $row['account_id'];

            if ($currentAccountId !== $accountId) {
                $currentAccountId = $accountId;
                $runningBalance = 0;

                $ledgers[$accountId] = [
                    'account_id'   => $accountId,
                    'account_name' => $row['account_name'],
                    'account_type' => $row['account_type'],
                    'entries'      => []
                ];
            }

            $runningBalance += ($row['debit'] - $row['credit']);

            $ledgers[$accountId]['entries'][] = [
                'date'           => $row['date'],
                'transaction_id' => $row['transaction_id'],
                'reference_no'   => $row['reference_no'],
                'transaction_type' => $row['transaction_type'],
                'description'    => $row['description'],
                'debit'          => $row['debit'],
                'credit'         => $row['credit'],
                'balance'        => $runningBalance
            ];
        }

        return array_values($ledgers);
    }

    public function getTotalLedgerPages($search = '', $month = '', $year = '')
    {
        return ceil($this->getTotalLedgerAccounts($search, $month, $year) / 5);
    }
    public function getAllGeneralLedger($month = '', $year = '')
    {
        $conditions = [];

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = " . intval($month);
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = " . intval($year);
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sqlIds = "
        SELECT DISTINCT je.account_id
        FROM journal_entries AS je
        $filterQuery
        ORDER BY je.account_id ASC
    ";
        $resultIds = mysqli_query($this->conn, $sqlIds);
        $accountIds = mysqli_fetch_all($resultIds, MYSQLI_ASSOC);

        if (empty($accountIds)) return [];

        $ids = implode(',', array_column($accountIds, 'account_id'));

        $sql = "
        SELECT 
            coa.id AS account_id,
            coa.account_name,
            coa.account_type,
            je.transaction_id,
            je.date AS journal_date,
            COALESCE(je.description, t.description) AS description,
            je.debit,
            je.credit,
            t.reference_no,
            tr.rule_name AS transaction_type
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
        LEFT JOIN transaction_rules AS tr ON t.rule_id = tr.id
        WHERE je.account_id IN ($ids)
        " . ($filterQuery ? " AND " . implode(' AND ', $conditions) : "") . "
        ORDER BY coa.account_name ASC, je.date ASC, je.transaction_id ASC
    ";

        $result = mysqli_query($this->conn, $sql);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $ledgers = [];
        $runningBalances = [];

        foreach ($rows as $row) {
            $accountId = $row['account_id'];

            if (!isset($ledgers[$accountId])) {
                $ledgers[$accountId] = [
                    'account_id' => $accountId,
                    'account_name' => $row['account_name'],
                    'account_type' => $row['account_type'],
                    'entries' => []
                ];
                $runningBalances[$accountId] = 0;
            }

            $runningBalances[$accountId] += ($row['debit'] - $row['credit']);

            $ledgers[$accountId]['entries'][] = [
                'transaction_id' => $row['transaction_id'],
                'journal_date' => $row['journal_date'],
                'description' => $row['description'] ?? '',
                'debit' => $row['debit'],
                'credit' => $row['credit'],
                'balance' => $runningBalances[$accountId],
                'reference_no' => $row['reference_no'] ?? '',
                'transaction_type' => $row['transaction_type'] ?? 'General Entry'
            ];
        }

        return array_values($ledgers);
    }

    // Trial Balance
    

public function getAllTrialBalance($month = '', $year = '')
{
    $conditions = [];
    if ($month !== '') $conditions[] = "MONTH(je.date) = " . intval($month);
    if ($year !== '') $conditions[] = "YEAR(je.date) = " . intval($year);

    $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

    $sql = "
        SELECT 
            coa.id AS account_id,
            coa.account_name,
            coa.account_type,
            SUM(je.debit) AS total_debit,
            SUM(je.credit) AS total_credit,
            (SUM(je.debit) - SUM(je.credit)) AS balance
        FROM journal_entries AS je
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
        GROUP BY coa.id, coa.account_name, coa.account_type
        ORDER BY coa.account_type, coa.account_name
    ";

    $result = mysqli_query($this->conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

}
