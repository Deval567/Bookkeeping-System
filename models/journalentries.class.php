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

        // Step 1: Get transaction IDs for this page
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

        // Step 2: Get all journal entry rows for these transaction IDs
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

        // Step 3: Group by transaction_id
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
}
