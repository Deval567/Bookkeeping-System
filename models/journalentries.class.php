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
    public $limit = 5;

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
    public function getTotalJournalEntries($search = '', $month = '', $year = '', $rule_id = '')
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

        if ($rule_id !== '') {
            $conditions[] = "t.rule_id = '" . intval($rule_id) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sql = "
        SELECT COUNT(DISTINCT je.transaction_id) AS total
        FROM journal_entries AS je
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
        JOIN chart_of_accounts AS coa ON je.account_id = coa.id
        $filterQuery
    ";

        $result = mysqli_query($this->conn, $sql);
        $row = mysqli_fetch_assoc($result);

        return (int)$row['total'];
    }

    public function getPaginatedJournalEntries($page = 1, $search = '', $month = '', $year = '', $rule_id = '')
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

        if ($rule_id !== '') {
            $conditions[] = "t.rule_id = '" . intval($rule_id) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

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

    public function getTotalJournalPages($search = '', $month = '', $year = '', $rule_id = '')
    {
        return ceil($this->getTotalJournalEntries($search, $month, $year, $rule_id) / $this->limit);
    }
    public function getAllJournalEntries($month = '', $year = '', $rule_id = '')
    {
        $conditions = [];

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = '" . intval($month) . "'";
        }

        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = '" . intval($year) . "'";
        }

        if ($rule_id !== '') {
            $conditions[] = "t.rule_id = '" . intval($rule_id) . "'";
        }

        $filterQuery = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

        $sqlIds = "
        SELECT DISTINCT je.transaction_id
        FROM journal_entries AS je
        LEFT JOIN transactions AS t ON je.transaction_id = t.id
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
        $conditions = ["je.id IS NOT NULL"];

        if ($month !== '') {
            $conditions[] = "MONTH(je.date) = " . intval($month);
        }
        if ($year !== '') {
            $conditions[] = "YEAR(je.date) = " . intval($year);
        }

        $filterQuery = "WHERE " . implode(' AND ', $conditions);

        $sql = "
        SELECT 
            coa.id AS account_id,
            coa.account_name,
            coa.account_type,
            SUM(COALESCE(je.debit, 0)) AS total_debit,
            SUM(COALESCE(je.credit, 0)) AS total_credit
        FROM chart_of_accounts AS coa
        INNER JOIN journal_entries AS je ON je.account_id = coa.id
        $filterQuery
        GROUP BY coa.id, coa.account_name, coa.account_type
        ORDER BY 
            FIELD(coa.account_type, 'Asset', 'Liability', 'Equity', 'Revenue', 'Expense'),
            coa.account_name
    ";

        $result = mysqli_query($this->conn, $sql);
        $trialBalance = [];

        while ($row = mysqli_fetch_assoc($result)) {
            $debit = $row['total_debit'];
            $credit = $row['total_credit'];
            $type = $row['account_type'];

            $isDebitNormal = in_array($type, ['Asset', 'Expense']);

            if ($isDebitNormal) {
                $netBalance = $debit - $credit;
                $row['display_debit'] = $netBalance > 0 ? $netBalance : 0;
                $row['display_credit'] = $netBalance < 0 ? abs($netBalance) : 0;
            } else {
                $netBalance = $credit - $debit;
                $row['display_credit'] = $netBalance > 0 ? $netBalance : 0;
                $row['display_debit'] = $netBalance < 0 ? abs($netBalance) : 0;
            }

            $trialBalance[] = $row;
        }

        return $trialBalance;
    }
    // Balance Sheet
    public function getBalanceSheet($month = null, $year = null)
    {
        $where = [];
        if ($month) $where[] = "MONTH(je.date) = " . intval($month);
        if ($year)  $where[] = "YEAR(je.date) = " . intval($year);
        $whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

        // Get Asset, Liability, and Equity balances
        $sql = "
        SELECT 
            coa.id,
            coa.account_name, 
            coa.account_type,
            SUM(COALESCE(je.debit, 0)) AS total_debit,
            SUM(COALESCE(je.credit, 0)) AS total_credit,
            CASE 
                WHEN coa.account_type = 'Asset' THEN 
                    SUM(COALESCE(je.debit, 0)) - SUM(COALESCE(je.credit, 0))
                WHEN coa.account_type IN ('Liability', 'Equity') THEN 
                    SUM(COALESCE(je.credit, 0)) - SUM(COALESCE(je.debit, 0))
                ELSE 0
            END AS balance
        FROM chart_of_accounts coa
        INNER JOIN journal_entries je ON je.account_id = coa.id
        $whereSql
        " . ($where ? "AND" : "WHERE") . " coa.account_type IN ('Asset','Liability','Equity')
        GROUP BY coa.id, coa.account_name, coa.account_type
        HAVING ABS(balance) > 0.01
        ORDER BY 
            FIELD(coa.account_type, 'Asset','Liability','Equity'), 
            coa.account_name
    ";

        $result = $this->conn->query($sql);
        $balances = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $row['balance'] = floatval($row['balance']);
                $balances[] = $row;
            }
        }

        // Calculate Net Income or Net Loss (Revenue - Expenses)
        $netIncomeSql = "
        SELECT 
            SUM(CASE WHEN coa.account_type = 'Revenue' THEN je.credit - je.debit ELSE 0 END) as total_revenue,
            SUM(CASE WHEN coa.account_type = 'Expense' THEN je.debit - je.credit ELSE 0 END) as total_expenses
        FROM journal_entries je
        INNER JOIN chart_of_accounts coa ON je.account_id = coa.id
        $whereSql
        " . ($where ? "AND" : "WHERE") . " coa.account_type IN ('Revenue', 'Expense')
    ";

        $netIncomeResult = $this->conn->query($netIncomeSql);
        $netIncomeRow = $netIncomeResult->fetch_assoc();

        $totalRevenue = floatval($netIncomeRow['total_revenue'] ?? 0);
        $totalExpenses = floatval($netIncomeRow['total_expenses'] ?? 0);
        $netIncome = $totalRevenue - $totalExpenses;

        // Add net income/loss to the balances array as a special entry
        if (abs($netIncome) > 0.01) {
            $balances[] = [
                'id' => 'net_income',
                'account_name' => $netIncome >= 0 ? 'Net Income' : 'Net Loss',
                'account_type' => 'Equity',
                'balance' => $netIncome,
                'is_net_income' => true,
                'is_loss' => $netIncome < 0
            ];
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
    // Cash Flow Statement 
    public function getCashFlow($month = null, $year = null)
    {
        $dateConditions = [];
        $filterApplied = false;

        if (!empty($month) && strtolower($month) !== 'all') {
            $dateConditions[] = "MONTH(t.transaction_date) = " . intval($month);
            $filterApplied = true;
        }
        if (!empty($year) && strtolower($year) !== 'all') {
            $dateConditions[] = "YEAR(t.transaction_date) = " . intval($year);
            $filterApplied = true;
        }

        $dateWhere = $dateConditions ? " AND " . implode(" AND ", $dateConditions) : "";

        $cashAccountSql = "
    SELECT id FROM chart_of_accounts 
    WHERE account_type = 'Asset'
    AND (
        account_name = 'Cash'
        OR account_name = 'Cash in Bank'
        OR account_name = 'Cash on Hand'
        OR (LOWER(account_name) LIKE '%cash%' AND LOWER(account_name) NOT LIKE '%petty%')
    )
    ORDER BY 
        CASE 
            WHEN account_name = 'Cash' THEN 1
            WHEN account_name = 'Cash in Bank' THEN 2
            WHEN account_name = 'Cash on Hand' THEN 3
            ELSE 4
        END
    LIMIT 1
";
        $cashAccountResult = $this->conn->query($cashAccountSql);
        if (!$cashAccountResult || $cashAccountResult->num_rows == 0) {
            return [
                'Operating' => [],
                'Investing' => [],
                'Financing' => [],
                'NetCash' => 0,
                'BeginningCash' => 0,
                'EndingCash' => 0
            ];
        }
        $cashAccountRow = $cashAccountResult->fetch_assoc();
        $cashAccountId = $cashAccountRow['id'];

        $beginningCash = 0;
        if ($filterApplied) {
            $beginningCashSql = "
        SELECT 
            COALESCE(SUM(j.debit), 0) - COALESCE(SUM(j.credit), 0) as beginning_balance
        FROM journal_entries j
        JOIN transactions t ON j.transaction_id = t.id
        WHERE j.account_id = $cashAccountId
    ";
            if ($month && strtolower($month) !== 'all' && $year && strtolower($year) !== 'all') {
                $beginningCashSql .= " AND t.transaction_date < '" . $year . "-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01'";
            } elseif ($year && strtolower($year) !== 'all') {
                $beginningCashSql .= " AND YEAR(t.transaction_date) < " . intval($year);
            }
            $beginningResult = $this->conn->query($beginningCashSql);
            if ($beginningResult) {
                $beginningRow = $beginningResult->fetch_assoc();
                $beginningCash = floatval($beginningRow['beginning_balance'] ?? 0);
            }
        }

        $cashTransactionsSql = "
    SELECT DISTINCT j.transaction_id 
    FROM journal_entries j
    JOIN transactions t ON j.transaction_id = t.id
    WHERE j.account_id = $cashAccountId
    $dateWhere
";
        $cashTransResult = $this->conn->query($cashTransactionsSql);
        $transactionIds = [];
        if ($cashTransResult && $cashTransResult->num_rows > 0) {
            while ($row = $cashTransResult->fetch_assoc()) {
                $transactionIds[] = intval($row['transaction_id']);
            }
        }
        if (empty($transactionIds)) {
            return [
                'Operating' => [],
                'Investing' => [],
                'Financing' => [],
                'NetCash' => 0,
                'BeginningCash' => $beginningCash,
                'EndingCash' => $beginningCash
            ];
        }

        $idsList = implode(',', $transactionIds);
        $sql = "
    SELECT 
        j.transaction_id,
        t.transaction_date,
        t.description as transaction_description,
        j.description as entry_description,
        j.account_id,
        j.debit,
        j.credit,
        coa.account_name,
        coa.account_type,
        coa.cash_flow_category
    FROM journal_entries j
    JOIN transactions t ON j.transaction_id = t.id
    JOIN chart_of_accounts coa ON j.account_id = coa.id
    WHERE j.transaction_id IN ($idsList)
    ORDER BY t.transaction_date, j.transaction_id, j.account_id
";
        $result = $this->conn->query($sql);
        if (!$result) {
            return [
                'Operating' => [],
                'Investing' => [],
                'Financing' => [],
                'NetCash' => 0,
                'BeginningCash' => $beginningCash,
                'EndingCash' => $beginningCash
            ];
        }

        $transactions = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transactionId = $row['transaction_id'];
                if (!isset($transactions[$transactionId])) {
                    $transactions[$transactionId] = [
                        'date' => $row['transaction_date'],
                        'description' => $row['transaction_description'] ?: $row['entry_description'],
                        'accounts' => []
                    ];
                }
                $transactions[$transactionId]['accounts'][] = $row;
            }
        }

        $cashFlows = [
            'Operating' => [],
            'Investing' => [],
            'Financing' => [],
            'NetCash' => 0,
            'BeginningCash' => $beginningCash,
            'EndingCash' => 0
        ];

        foreach ($transactions as $transactionId => $transaction) {
            $cashAccount = null;
            $otherAccounts = [];
            foreach ($transaction['accounts'] as $account) {
                if ($account['account_id'] == $cashAccountId) {
                    $cashAccount = $account;
                } else {
                    $otherAccounts[] = $account;
                }
            }
            if (!$cashAccount || empty($otherAccounts)) continue;
            $otherAccount = $otherAccounts[0];
            $cashImpact = floatval($cashAccount['debit']) - floatval($cashAccount['credit']);
            if (abs($cashImpact) < 0.01) continue;
            $category = $otherAccount['cash_flow_category'];
            if (!$category || trim($category) == '') {
                $accountType = $otherAccount['account_type'];
                if (in_array($accountType, ['Revenue', 'Expense'])) $category = 'Operating';
                elseif ($accountType == 'Asset') $category = 'Investing';
                elseif (in_array($accountType, ['Liability', 'Equity'])) $category = 'Financing';
                else $category = 'Operating';
            }
            $category = ucfirst(strtolower(trim($category)));
            if (!in_array($category, ['Operating', 'Investing', 'Financing'])) $category = 'Operating';
            $accountName = $otherAccount['account_name'];

            $found = false;
            foreach ($cashFlows[$category] as &$existingItem) {
                if ($existingItem['account_name'] === $accountName) {
                    $existingItem['balance'] += $cashImpact;
                    $found = true;
                    break;
                }
            }
            unset($existingItem);

            if (!$found) {
                $cashFlows[$category][] = [
                    'account_name' => $accountName,
                    'balance' => $cashImpact
                ];
            }

            $cashFlows['NetCash'] += $cashImpact;
        }

        if (!$filterApplied) {
            $cashFlows['BeginningCash'] = 0;
        }

        $cashFlows['EndingCash'] = $cashFlows['BeginningCash'] + $cashFlows['NetCash'];
        return $cashFlows;
    }


    public function getRecentTransactions()
    {
        $sql = " SELECT t.id, t.reference_no, t.description, t.total_amount, t.transaction_date, t.rule_id, tr.rule_name, u.username
        FROM transactions AS t
        JOIN transaction_rules AS tr ON t.rule_id = tr.id
        JOIN users AS u ON t.created_by = u.id
        ORDER BY t.transaction_date DESC
        LIMIT 5";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
