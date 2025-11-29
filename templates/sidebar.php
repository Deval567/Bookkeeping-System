<!-- Sidebar -->
<aside class="row-span-2 w-64 bg-white border-r border-gray-500 shadow-md flex flex-col z-10">

  <!-- User Info -->
  <div class="px-6 py-3 flex items-center justify-between border-b">
    <div>
      <p class="text-md opacity-80">Logged in as: <span class="font-semibold"><?php echo $_SESSION['username']; ?></span></p>
      <p class="text-md">Role: <span class="font-semibold">
          <?php
          $role = $_SESSION['role'];
          $roleStyles = [
            'Admin' => 'bg-red-100 text-red-700',
            'Staff' => 'bg-blue-100 text-blue-700',
          ];
          $style = $roleStyles[$_SESSION['role']] ?? 'bg-gray-100 text-gray-700';
          ?>
          <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $style ?>">
            <?= $role ?>
          </span>
        </span>
      </p>
    </div>
  </div>

  <!-- Menu -->
  <nav class="flex-1 overflow-y-auto px-4 py-6 text-gray-700">
    <ul class="space-y-4">

      <!-- Dashboard (Both Admin & Staff) -->
      <li>
        <a href="dashboard.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
          <?php echo ($title == 'Dashboard')
            ? 'bg-red-50 text-red-700 font-semibold'
            : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
            </svg>
          </span>
          Dashboard
        </a>
      </li>
      <hr class="border-gray-200 mt-4">

      <!-- Bookkeeping Setup (Admin Only) -->
      <?php if ($role == 'Admin'): ?>
        <li>
          <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
            Bookkeeping Setup
          </div>

          <ul class="ml-8 mt-2 space-y-1 text-sm">
            <li>
              <a href="chartofaccounts.php" class="flex flex items-center gap-3 px-2 py-2 rounded-lg transition-all 
                <?php echo ($title == 'Chart of Accounts')
                  ? 'bg-red-50 text-red-700 font-semibold'
                  : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                  </svg>
                </span>
                Chart of Accounts
              </a>
            </li>

            <li>
              <a href="transactionrules.php" class="flex flex items-center gap-3 px-2 py-2 rounded-lg transition-all 
                <?php echo ($title == 'Transaction Rules')
                  ? 'bg-red-50 text-red-700 font-semibold'
                  : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                  </svg>
                </span>
                Transaction Rules
              </a>
            </li>

            <li>
              <a href="transactionrulelines.php" class="flex flex items-center gap-3 px-2 py-2 rounded-lg transition-all 
                <?php echo ($title == 'Transaction Rule Lines')
                  ? 'bg-red-50 text-red-700 font-semibold'
                  : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                  </svg>
                </span>
                Transaction Rule Lines
              </a>
            </li>
          </ul>
          <hr class="border-gray-200 mt-4">
        </li>
      <?php endif; ?>

      <!-- Transactions (Both Admin & Staff) -->
      <li>
        <a href="transactions.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
          <?php echo ($title == 'Transactions')
            ? 'bg-red-50 text-red-700 font-semibold'
            : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
          <span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>
          </span>
          Transactions
        </a>
      </li>
      <hr class="border-gray-200 mt-4">

      <!-- Records (Both Admin & Staff) -->
      <li>
        <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
          Records
        </div>

        <ul class="ml-8 mt-2 space-y-1 text-sm">
          <li>
            <a href="journalentries.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Journal Entries')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
              </span>
              Journal Entries
            </a>
          </li>

          <li>
            <a href="generalledger.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'General Ledger')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                </svg>
              </span>
              General Ledger
            </a>
          </li>
        </ul>
        <hr class="border-gray-200 mt-4">
      </li>

      <!-- Reports (Both Admin & Staff) -->
      <li>
        <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
          Reports
        </div>

        <ul class="ml-8 mt-2 space-y-1 text-sm">
          <li>
            <a href="trialbalance.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Trial Balance')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                </svg>
              </span>
              Trial Balance
            </a>
          </li>

          <li>
            <a href="balancesheet.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Balance Sheet')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.416 48.416 0 0 0 12 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.988 5.988 0 0 1-2.031.352 5.988 5.988 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L18.75 4.971Zm-16.5.52c.99-.203 1.99-.377 3-.52m0 0 2.62 10.726c.122.499-.106 1.028-.589 1.202a5.989 5.989 0 0 1-2.031.352 5.989 5.989 0 0 1-2.031-.352c-.483-.174-.711-.703-.59-1.202L5.25 4.971Z" />
                </svg>
              </span>
              Balance Sheet
            </a>
          </li>

          <li>
            <a href="incomestatement.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Income Statement')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
              </span>
              Income Statement
            </a>
          </li>

          <li>
            <a href="cashflow.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Cash Flow Statement')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
              <span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
              </span>
              Cash Flow Statement
            </a>
          </li>
        </ul>
        <hr class="border-gray-200 mt-4">
      </li>

      <!-- Users Management (Admin Only) -->
      <?php if ($role == 'Admin'): ?>
        <li>
          <a href="users.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
            <?php echo ($title == 'Users Management')
              ? 'bg-red-50 text-red-700 font-semibold'
              : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
            <span>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
              </svg>
            </span>
            Users Management
          </a>
        </li>
        <hr class="border-gray-200 mt-4">
      <?php endif; ?>

    </ul>
  </nav>

  <!-- Footer -->
  <div class="px-6 py-3 border-t text-xs text-gray-500 text-center mt-auto">
    © 2025 JJ&C
  </div>

</aside>