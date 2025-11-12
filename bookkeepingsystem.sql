-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 12, 2025 at 09:08 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookkeepingsystem`
--
CREATE DATABASE `bookkeepingsystem`;

-- --------------------------------------------------------

--
-- Table structure for table `chart_of_accounts`
--


CREATE TABLE `chart_of_accounts` (
  `id` int(11) NOT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') DEFAULT NULL,
  `cash_flow_category` enum('Operating','Investing','Financing','Not Applicable') DEFAULT 'Not Applicable',
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chart_of_accounts`
--

INSERT INTO `chart_of_accounts` (`id`, `account_name`, `account_type`, `cash_flow_category`, `description`) VALUES
(2, 'Cash in Bank', 'Asset', 'Operating', 'Company’s cash deposits in bank accounts.'),
(3, 'Accounts Receivable', 'Asset', 'Operating', 'Amounts owed by customers.'),
(4, 'Inventory', 'Asset', 'Operating', 'Goods held for sale.'),
(5, 'Prepaid Expenses', 'Asset', 'Not Applicable', 'Payments made in advance for future expenses.'),
(6, 'Petty Cash', 'Asset', 'Not Applicable', 'Small fund for minor daily expenses.'),
(7, 'Supplies', 'Asset', 'Not Applicable', 'Office or operational materials used in business.'),
(8, 'Land', 'Asset', 'Not Applicable', 'Land owned by the business.'),
(9, 'Building', 'Asset', 'Not Applicable', 'Structures owned by the company used in operations.'),
(10, 'Equipment', 'Asset', 'Investing', 'Machinery and tools used for production or operations.'),
(11, 'Furniture and Fixtures', 'Asset', 'Not Applicable', 'Office furniture and permanent installations.'),
(12, 'Vehicles', 'Asset', 'Not Applicable', 'Company-owned transportation assets.'),
(13, 'Accumulated Depreciation', 'Asset', 'Not Applicable', 'Total depreciation of fixed assets (contra-asset).'),
(15, 'Salaries Payable', 'Liability', 'Not Applicable', 'Unpaid employee wages.'),
(16, 'Interest Payable', 'Liability', 'Not Applicable', 'Interest expenses owed but not yet paid.'),
(17, 'Taxes Payable', 'Liability', 'Not Applicable', 'Taxes owed to the government.'),
(18, 'Unearned Revenue', 'Liability', 'Not Applicable', 'Payments received before providing goods or services.'),
(19, 'Notes Payable', 'Liability', 'Not Applicable', 'Written promises to pay specific amounts.'),
(20, 'Mortgage Payable', 'Liability', 'Not Applicable', 'Long-term debt secured by real estate.'),
(21, 'Bonds Payable', 'Liability', 'Not Applicable', 'Long-term debt issued to investors.'),
(22, 'Owner’s Capital', 'Equity', 'Financing', 'Owner’s initial and additional investments.'),
(23, 'Owner’s Drawings', 'Equity', 'Not Applicable', 'Withdrawals made by the owner (contra-equity).'),
(24, 'Retained Earnings', 'Equity', 'Not Applicable', 'Accumulated profits kept in the business.'),
(25, 'Common Stock', 'Equity', 'Not Applicable', 'Capital raised through issuing shares.'),
(26, 'Additional Paid-in Capital', 'Equity', 'Not Applicable', 'Capital received from shareholders in excess of par value.'),
(27, 'Sales Revenue', 'Revenue', 'Operating', 'Income from selling products.'),
(28, 'Service Revenue', 'Revenue', 'Not Applicable', 'Income from providing services.'),
(29, 'Interest Income', 'Revenue', 'Not Applicable', 'Earnings from interest on investments or receivables.'),
(30, 'Rent Income', 'Revenue', 'Not Applicable', 'Earnings from renting out company property.'),
(31, 'Commission Income', 'Revenue', 'Not Applicable', 'Earnings from commissions or fees.'),
(32, 'Cost of Goods Sold', 'Expense', 'Not Applicable', 'Direct cost of producing or purchasing goods sold.'),
(33, 'Salaries Expense', 'Expense', 'Operating', 'Employee compensation costs.'),
(34, 'Rent Expense', 'Expense', 'Operating', 'Cost of renting office or building space.'),
(35, 'Utilities Expense', 'Expense', 'Operating', 'Expenses for electricity, water, and other utilities.'),
(36, 'Supplies Expense', 'Expense', 'Not Applicable', 'Cost of using supplies in operations.'),
(37, 'Depreciation Expense', 'Expense', 'Not Applicable', 'Allocation of cost of fixed assets over time.'),
(38, 'Advertising Expense', 'Expense', 'Not Applicable', 'Cost of promoting products or services.'),
(39, 'Insurance Expense', 'Expense', 'Not Applicable', 'Premiums paid for insurance coverage.'),
(40, 'Repairs and Maintenance Expense', 'Expense', 'Not Applicable', 'Costs of maintaining equipment and facilities.'),
(41, 'Taxes and Licenses', 'Expense', 'Not Applicable', 'Government fees, licenses, and taxes.'),
(42, 'Interest Expense', 'Expense', 'Not Applicable', 'Interest paid on borrowings.'),
(43, 'Miscellaneous Expense', 'Expense', 'Not Applicable', 'Other minor or irregular business expenses.'),
(44, 'Sales Returns and Allowances', 'Revenue', 'Not Applicable', 'Tracks returns of sold goods'),
(45, 'Sales Discounts', 'Revenue', 'Not Applicable', 'Tracks discounts given to customers'),
(46, 'Purchase Returns and Allowances', 'Expense', 'Not Applicable', 'Tracks goods returned to suppliers'),
(47, 'Purchase Discounts', 'Expense', 'Not Applicable', 'Tracks discounts received from suppliers'),
(50, 'Accrued Expenses Payable', 'Liability', 'Not Applicable', 'Expenses incurred but not yet paid'),
(59, 'Owner’s Equity', 'Equity', 'Not Applicable', 'owner adds capital to the business.'),
(62, 'Cash', 'Asset', 'Operating', 'Cash'),
(63, 'Accounts Payable', 'Liability', 'Operating', '');

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `rule_line_id` int(11) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `credit` decimal(15,2) DEFAULT 0.00,
  `description` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_entries`
--

INSERT INTO `journal_entries` (`id`, `transaction_id`, `rule_line_id`, `debit`, `credit`, `description`, `date`) VALUES
(94, 40, 61, 8000.00, 0.00, 'Paid supplier for inventory', '2025-01-30'),
(95, 40, 62, 0.00, 8000.00, 'Paid supplier for inventory', '2025-01-30'),
(96, 38, 57, 3500.00, 0.00, 'Paid employee salaries', '2025-01-25'),
(97, 38, 58, 0.00, 3500.00, 'Paid employee salaries', '2025-01-25'),
(98, 39, 59, 500.00, 0.00, 'Paid utility bills', '2025-01-25'),
(99, 39, 60, 0.00, 500.00, 'Paid utility bills', '2025-01-25'),
(100, 37, 55, 2000.00, 0.00, 'Paid monthly rent', '2025-01-20'),
(101, 37, 56, 0.00, 2000.00, 'Paid monthly rent', '2025-01-20'),
(102, 36, 53, 12000.00, 0.00, 'Collected payment from customer', '2025-01-15'),
(103, 36, 54, 0.00, 12000.00, 'Collected payment from customer', '2025-01-15'),
(104, 34, 49, 12000.00, 0.00, 'Sold goods on credit', '2025-01-10'),
(105, 34, 50, 0.00, 12000.00, 'Sold goods on credit', '2025-01-10'),
(106, 35, 51, 5000.00, 0.00, 'Cost of inventory sold\r\n\r\n', '2025-01-10'),
(107, 35, 52, 0.00, 5000.00, 'Cost of inventory sold\r\n\r\n', '2025-01-10'),
(108, 33, 47, 8000.00, 0.00, 'Purchased inventory from supplier', '2025-01-05'),
(109, 33, 48, 0.00, 8000.00, 'Purchased inventory from supplier', '2025-01-05'),
(110, 32, 45, 0.00, 15000.00, 'Purchased office equipment', '2025-01-02'),
(111, 32, 46, 15000.00, 0.00, 'Purchased office equipment', '2025-01-02'),
(112, 31, 43, 50000.00, 0.00, 'Initial capital investment', '2025-01-01'),
(113, 31, 44, 0.00, 50000.00, 'Initial capital investment', '2025-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `transaction_date` date NOT NULL,
  `total_amount` decimal(15,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `rule_id`, `reference_no`, `description`, `transaction_date`, `total_amount`, `created_by`) VALUES
(31, 43, 'OI-010120', 'Initial capital investment', '2025-01-01', 50000.00, 2),
(32, 46, 'PE-010225', 'Purchased office equipment', '2025-01-02', 15000.00, 2),
(33, 44, 'PI-010525', 'Purchased inventory from supplier', '2025-01-05', 8000.00, 2),
(34, 1, 'SI-011025', 'Sold goods on credit', '2025-01-10', 12000.00, 2),
(35, 47, 'CGS-011025', 'Cost of inventory sold\r\n\r\n', '2025-01-10', 5000.00, 2),
(36, 48, 'RCP-011525', 'Collected payment from customer', '2025-01-15', 12000.00, 2),
(37, 22, 'RE-012025', 'Paid monthly rent', '2025-01-20', 2000.00, 2),
(38, 20, 'SE-012525', 'Paid employee salaries', '2025-01-25', 3500.00, 2),
(39, 24, 'UE-12525', 'Paid utility bills', '2025-01-25', 500.00, 2),
(40, 49, 'PAP-013025', 'Paid supplier for inventory', '2025-01-30', 8000.00, 2);

-- --------------------------------------------------------

--
-- Table structure for table `transaction_rules`
--

CREATE TABLE `transaction_rules` (
  `id` int(11) NOT NULL,
  `rule_name` varchar(100) NOT NULL,
  `category` enum('Invoice','Payment','Purchase','General') NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_rules`
--

INSERT INTO `transaction_rules` (`id`, `rule_name`, `category`, `description`) VALUES
(1, 'Sales Invoice', 'Invoice', 'Record sale made on credit to a client'),
(2, 'Cash Sale', 'Invoice', 'Record immediate cash sale to a client'),
(3, 'Bank Sale', 'Invoice', 'Record immediate bank sale to a client'),
(4, 'Service Revenue (Cash)', 'Invoice', 'Cash received from a client for services'),
(5, 'Service Revenue (Bank)', 'Invoice', 'Bank received from a client for services'),
(9, 'Payment to Supplier (Cash)', 'Payment', 'Paying supplier in cash'),
(10, 'Payment to Supplier (Bank)', 'Payment', 'Paying supplier via bank'),
(11, 'Asset Acquisition (Cash)', 'Purchase', 'Buying equipment with cash'),
(12, 'Asset Acquisition (Bank)', 'Purchase', 'Buying equipment via bank'),
(13, 'Depreciation', 'General', 'Record asset depreciation'),
(14, 'Loan Received (Cash)', 'General', 'Cash received from loan'),
(15, 'Loan Received (Bank)', 'General', 'Bank loan received'),
(16, 'Loan Payment (Cash)', 'Payment', 'Paying loan in cash'),
(17, 'Loan Payment (Bank)', 'Payment', 'Paying loan via bank'),
(18, 'Owner Capital', 'General', 'Owner injects cash into business'),
(19, 'Owner Draw', 'General', 'Owner withdraws cash'),
(20, 'Salaries Expense (Cash)', 'General', 'Paying employee salaries in cash'),
(21, 'Salaries Expense (Bank)', 'General', 'Paying employee salaries via bank'),
(22, 'Rent Expense (Cash)', 'General', 'Paying rent in cash'),
(23, 'Rent Expense (Bank)', 'General', 'Paying rent via bank'),
(24, 'Utilities Expense (Cash)', 'General', 'Paying utilities in cash'),
(25, 'Utilities Expense (Bank)', 'General', 'Paying utilities via bank'),
(26, 'Insurance Expense (Cash)', 'General', 'Paying insurance in cash'),
(27, 'Insurance Expense (Bank)', 'General', 'Paying insurance via bank'),
(28, 'Interest Expense (Cash)', 'General', 'Paying interest in cash'),
(29, 'Interest Expense (Bank)', 'General', 'Paying interest via bank'),
(30, 'Taxes and Licenses (Cash)', 'General', 'Paying taxes in cash'),
(31, 'Taxes and Licenses (Bank)', 'General', 'Paying taxes via bank'),
(32, 'Accrued Expenses', 'General', 'Record expenses incurred but not yet paid'),
(33, 'Unearned Revenue', 'General', 'Cash received from a client before providing goods/services'),
(34, 'Adjusting Journal Entry', 'General', 'Manual adjustment entries'),
(42, 'Cash Purchase of Supplies', 'Purchase', 'Used when the business purchases office supplies with cash.'),
(43, 'Owner Investment', 'General', 'Owner adds capital to the business.'),
(44, 'Purchase Inventory', 'Purchase', ''),
(46, 'Purchase Equipment', 'Purchase', 'Buying an equipment using cash'),
(47, 'Cost of Goods Sold', 'General', 'Cost of inventory sold'),
(48, 'Received Customer\'s Payment', 'Invoice', 'Collected payment from customer'),
(49, 'Paid Accounts Payable', 'Payment', 'Paid supplier for inventory');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_rule_lines`
--

CREATE TABLE `transaction_rule_lines` (
  `id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `entry_type` enum('debit','credit') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_rule_lines`
--

INSERT INTO `transaction_rule_lines` (`id`, `rule_id`, `account_id`, `entry_type`) VALUES
(43, 43, 62, 'debit'),
(44, 43, 22, 'credit'),
(45, 46, 62, 'credit'),
(46, 46, 10, 'debit'),
(47, 44, 4, 'debit'),
(48, 44, 63, 'credit'),
(49, 1, 3, 'debit'),
(50, 1, 27, 'credit'),
(51, 47, 32, 'debit'),
(52, 47, 4, 'credit'),
(53, 48, 62, 'debit'),
(54, 48, 3, 'credit'),
(55, 22, 34, 'debit'),
(56, 22, 62, 'credit'),
(57, 20, 33, 'debit'),
(58, 20, 62, 'credit'),
(59, 24, 35, 'debit'),
(60, 24, 62, 'credit'),
(61, 49, 63, 'debit'),
(62, 49, 62, 'credit');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(25) NOT NULL,
  `role` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(2, 'Joshua', 'joshua', 'Admin'),
(14, 'Testacc', 'Test123', 'Staff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_entries_ibfk_1` (`transaction_id`),
  ADD KEY `fk_journal_rule_line` (`rule_line_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transactions_created_by` (`created_by`),
  ADD KEY `transaction_rules_1` (`rule_id`);

--
-- Indexes for table `transaction_rules`
--
ALTER TABLE `transaction_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_rule_lines`
--
ALTER TABLE `transaction_rule_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rule` (`rule_id`),
  ADD KEY `fk_account` (`account_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chart_of_accounts`
--
ALTER TABLE `chart_of_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `transaction_rules`
--
ALTER TABLE `transaction_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `transaction_rule_lines`
--
ALTER TABLE `transaction_rule_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD CONSTRAINT `fk_journal_rule_line` FOREIGN KEY (`rule_line_id`) REFERENCES `transaction_rule_lines` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_journal_transaction` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rule_line_id` FOREIGN KEY (`rule_line_id`) REFERENCES `transaction_rule_lines` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `journal_entries_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `fk_transactions_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_transactions_rule` FOREIGN KEY (`rule_id`) REFERENCES `transaction_rules` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transactions_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `transaction_rules_1` FOREIGN KEY (`rule_id`) REFERENCES `transaction_rules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaction_rule_lines`
--
ALTER TABLE `transaction_rule_lines`
  ADD CONSTRAINT `fk_rule_lines_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rule_lines_rule` FOREIGN KEY (`rule_id`) REFERENCES `transaction_rules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
