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
             <?= $role ?></p>
     </div>
     <span class="text-lg"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-700 ">
         <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
       </svg>
     </span>
   </div>

   <!-- Menu -->
   <nav class="flex-1 overflow-y-auto px-4 py-6 text-gray-700">
     <ul class="space-y-4">


       <!-- Dashboard -->
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

       <!-- Accounting Setup -->
       <li>
         <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
           Accounting Setup
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
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-7">
                 <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
               </svg>
               </span>
               Transaction Rule Lines
             </a>
           </li>
         </ul>

         <hr class="border-gray-200 mt-4">

         <!-- Transactions -->
       <li>
         <a href="transactions.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
    <?php echo ($title == 'Transactions')
      ? 'bg-red-50 text-red-700 font-semibold'
      : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
             <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
           </svg>

           </span>
           Transactions
         </a>
       </li>
       <hr class="border-gray-200 mt-4">

       <!-- Records -->

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
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
               </span>
               General Ledger
             </a>
           </li>

           <li>
             <a href="trialbalance.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Trial Balance')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
               </span>
               Trial Balance
             </a>
           </li>
         </ul>
         <hr class="border-gray-200 mt-4">


         <!-- Reports -->

       <li>
         <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
           Reports
         </div>


         <ul class="ml-8 mt-2 space-y-1 text-sm">
           <li>
             <a href="balancesheet.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Balance Sheet')
                ? 'bg-red-50 text-red-700 font-semibold'
                : 'text-gray-700 hover:bg-red-50 hover:text-red-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
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
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
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
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
               </span>
               Cash Flow Statement
             </a>
           </li>
         </ul>
         <hr class="border-gray-200 mt-4">
         <!-- Users -->
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

     </ul>
   </nav>

   <!-- Footer -->
   <div class="px-6 py-3 border-t text-xs text-gray-500 text-center mt-auto">
     © 2025 JJ&C
   </div>

 </aside>