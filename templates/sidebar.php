 <!-- Sidebar -->
 <aside class="row-span-2 w-64 bg-white border-r border-gray-200 shadow-md flex flex-col">

   <!-- User Info -->
   <div class="px-6 py-3 flex items-center justify-between border-b">
     <div>
       <p class="text-md opacity-80">Logged in as: <span class="font-semibold"><?php echo $_SESSION['username']; ?></span></p>
       <p class="text-md">Role: <span class="font-semibold"><?php echo $_SESSION['role']; ?></p>
     </div>
     <span class="text-lg"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
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
      ? 'bg-purple-50 text-purple-700 font-semibold'
      : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
           <span>
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
               <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
             </svg>
           </span>
           Dashboard
         </a>
       </li>

       <hr class="border-gray-200">

       <!-- Transactions -->
       <li>
         <div class="px-3 py-2 font-semibold text-gray-800 flex items-center gap-3 rounded-lg cursor-pointer transition-all">
           Transactions
         </div>
         <ul class="ml-8 mt-2 space-y-1 text-sm">
           <li>
             <a href="general.transaction.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'General Transactions')
                ? 'bg-purple-50 text-purple-700 font-semibold'
                : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                 </svg>
               </span>
               General
             </a>
           </li>
           <li>
             <a href="#" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Invoices')
                ? 'bg-purple-50 text-purple-700 font-semibold'
                : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="m9 14.25 6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185ZM9.75 9h.008v.008H9.75V9Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm4.125 4.5h.008v.008h-.008V13.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                 </svg>
               </span>
               Invoices
             </a>
           </li>
           <li>
             <a href="#" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Purchases')
                ? 'bg-purple-50 text-purple-700 font-semibold'
                : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                 </svg>
               </span>
               Purchases
             </a>
           </li>
           <li>
             <a href="#" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
              <?php echo ($title == 'Payments')
                ? 'bg-purple-50 text-purple-700 font-semibold'
                : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
               <span>
                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                   <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                 </svg>
               </span>
               Payments
             </a>
           </li>
         </ul>
       </li>

       <!-- Reports -->


       <!-- Users -->
       <li>
         <a href="users.php" class="flex flex items-center gap-3 px-3 py-2 rounded-lg transition-all 
    <?php echo ($title == 'Users')
      ? 'bg-purple-50 text-purple-700 font-semibold'
      : 'text-gray-700 hover:bg-purple-50 hover:text-purple-700 font-medium'; ?>">
           <span>
             <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
               <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
             </svg>
           </span>
           Users
         </a>
       </li>

     </ul>
   </nav>

   <!-- Footer -->
   <div class="px-6 py-3 border-t text-xs text-gray-500 text-center mt-auto">
     © 2025 JJ&C
   </div>

 </aside>