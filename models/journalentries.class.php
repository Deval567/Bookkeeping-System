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

    public function __construct($conn, $transaction_id, $account_id, $entry_type, $amount,$description)
    {
        $this->conn = $conn;
        $this->transaction_id = $transaction_id;
        $this->account_id = $account_id;
        $this->entry_type = $entry_type;
        $this->amount = $amount;
        $this->description = $description;
    }
    public function createJournalEntry($transaction_id, $account_id, $entry_type, $amount,$description)
    {
        $debit = $entry_type === 'debit' ? $amount : 0;
        $credit = $entry_type === 'credit' ? $amount : 0;

        $sql = "INSERT INTO journal_entries (transaction_id, account_id, debit, credit,description)
            VALUES (?, ?, ?, ?,?)";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "iidds", $transaction_id, $account_id, $debit, $credit,$description);
        return mysqli_stmt_execute($stmt);
    }
    public function deleteJournalEntriesByTransactionId($transaction_id)
    {
        $sql = "DELETE FROM journal_entries WHERE transaction_id = ?";
        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $transaction_id);
        return mysqli_stmt_execute($stmt);
    }
    public function updateJournalEntry($id, $account_id, $entry_type, $amount,$description)
    {
        $debit = $entry_type === 'debit' ? $amount : 0;
        $credit = $entry_type === 'credit' ? $amount : 0;

        $sql = "UPDATE journal_entries 
            SET debit = ?, credit = ?, description = ?
            WHERE transaction_id = ? AND account_id = ?";

        $stmt = mysqli_prepare($this->conn, $sql);
        mysqli_stmt_bind_param($stmt, "ddsii", $debit, $credit, $description, $id, $account_id);
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $result;
    }
}
