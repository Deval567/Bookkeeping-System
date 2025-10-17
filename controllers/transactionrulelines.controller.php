<?php

$rule_id=$_POST['rule_id'];
$accounts=$_POST['account_id'];
$entries=$_POST['entry_type'];
require_once '../models/transactionrulelines.class.php';
require_once '../validations/transactionrulelines.validation.php';

$validation = new transactionRuleLinesValidation();
$errors = $validation->validate($rule_id, $accounts, $entries);
if (!empty($errors)) {
    session_start();
    $_SESSION['ruleline_errors'] = $errors;
    header("Location: ../pages/transactionrulelines.php?action=add");
    exit();
}
