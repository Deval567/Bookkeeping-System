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
            $conditions[] = "(je.description LIKE '%$search%' 
                        OR coa.account_name LIKE '%$search%' 
                        OR tr.rule_name LIKE '%$search%' 
                        OR t.reference_no LIKE '%$search%')";
        }

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        // Get transaction IDs with pagination
        $sqlIds = "
        SELECT DISTINCT je.transaction_id
        FROM journal_entries AS je
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
        LEFT JOIN transaction_rules AS tr ON t.rule_id = tr.id
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
        ORDER BY je.date DESC
        LIMIT {$this->limit} OFFSET {$offset}
    ";

        $resultIds = mysqli_query($this->conn, $sqlIds);
        $transactionIds = mysqli_fetch_all($resultIds, MYSQLI_ASSOC);

        if (empty($transactionIds)) return [];

        $ids = implode(',', array_column($transactionIds, 'transaction_id'));

        // Get full details for those transactions
        $sql = "
        SELECT 
            je.transaction_id,
            je.date AS journal_date,
            je.description,
            t.reference_no,
            tr.rule_name AS transaction_name,
            coa.account_name,
            je.debit,
            je.credit
        FROM journal_entries AS je
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
        LEFT JOIN transaction_rules AS tr ON t.rule_id = tr.id
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        WHERE je.transaction_id IN ($ids)
        ORDER BY je.date DESC, je.id ASC
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
                    'transaction_name' => $row['transaction_name'],
                    'reference_no' => $row['reference_no'],
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

        // Get transaction IDs with filters
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

        // Get full details including transaction name and reference number
        $sql = "
        SELECT 
            je.transaction_id,
            je.date AS journal_date,
            je.description,
            t.reference_no,
            tr.rule_name AS transaction_name,
            coa.account_name,
            je.debit,
            je.credit
        FROM journal_entries AS je
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
        LEFT JOIN transaction_rules AS tr ON t.rule_id = tr.id
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        WHERE je.transaction_id IN ($ids)
        ORDER BY je.date ASC, je.transaction_id ASC, je.id ASC
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
                    'transaction_name' => $row['transaction_name'],
                    'reference_no' => $row['reference_no'],
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

    //Balance Sheet
    public function getBalanceSheet($month = null, $year = null)
    {
        $where = [];

        if ($month) $where[] = "MONTH(j.date) = " . intval($month);
        if ($year)  $where[] = "YEAR(j.date) = " . intval($year);

        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
        SELECT 
            coa.account_name, 
            coa.account_type, 
            SUM(j.debit - j.credit) AS balance
        FROM journal_entries j
        JOIN chart_of_accounts coa ON j.account_id = coa.id
        $whereSql
        AND coa.account_type IN ('Asset','Liability','Equity')
        GROUP BY coa.id
        ORDER BY FIELD(coa.account_type, 'Asset','Liability','Equity'), coa.account_name
    ";

        $result = $this->conn->query($sql);
        $balances = [];

        if ($result && $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $balances[] = $row;
            }
        }

        return $balances;
    }
    //Income Statemnet
    public function getIncomeStatement($month = null, $year = null)
    {
        $where = [];

        if ($month) $where[] = "MONTH(j.date) = " . intval($month);
        if ($year)  $where[] = "YEAR(j.date) = " . intval($year);

        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
        SELECT 
            coa.account_name, 
            coa.account_type, 
            SUM(j.credit - j.debit) AS balance
        FROM journal_entries j
        JOIN chart_of_accounts coa ON j.account_id = coa.id
        $whereSql
        AND coa.account_type IN ('Revenue','Expense')
        GROUP BY coa.id
        ORDER BY FIELD(coa.account_type, 'Revenue','Expense'), coa.account_name
    ";

        $result = $this->conn->query($sql);
        $balances = [];

        if ($result && $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $balances[] = $row;
            }
        }

        return $balances;
    }
    //Cash Flow Statement
    public function getCashFlow($month = null, $year = null)
    {
        $where = [];

        if ($month) $where[] = "MONTH(j.date) = " . intval($month);
        if ($year)  $where[] = "YEAR(j.date) = " . intval($year);

        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        $sql = "
        SELECT 
            coa.account_name, 
            coa.cash_flow_category, 
            SUM(j.debit - j.credit) AS balance
        FROM journal_entries j
        JOIN chart_of_accounts coa ON j.account_id = coa.id
        $whereSql
        AND coa.cash_flow_category IS NOT NULL
        GROUP BY coa.id
        ORDER BY FIELD(coa.cash_flow_category, 'Operating', 'Investing', 'Financing'), coa.account_name
    ";

        $result = $this->conn->query($sql);
        $entries = [];

        if ($result && $result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                $entries[] = $row;
            }
        }

        $cashFlows = [
            'Operating' => [],
            'Investing' => [],
            'Financing' => [],
            'NetCash' => 0
        ];

        foreach ($entries as $entry) {
            $category = ucfirst(strtolower($entry['cash_flow_category']));
            $balance = $entry['balance'];

            if (in_array($category, ['Operating', 'Investing', 'Financing'])) {
                $cashFlows[$category][] = [
                    'name' => $entry['account_name'],
                    'balance' => $balance
                ];
                $cashFlows['NetCash'] += $balance;
            }
        }

        return $cashFlows;
    }
}
