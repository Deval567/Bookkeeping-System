<!-- Header with Sign Out Button and Modal -->
<header class="flex items-center justify-between px-6 py-3 z-10">
  <div class="flex items-center space-x-3">
    <img src="../images/logo.jpg" alt="Company Logo" class="h-10 w-10 rounded-full">
    <span class="text-xl font-semibold">JJ&C Stainless Steel Fabrication Services</span>
  </div>
  
  <!-- Sign Out Button -->
  <button command="show-modal" commandfor="signout-dialog" class="flex items-center gap-2 px-2 py-1 text-red-600 hover:text-red-700 transition-colors font-medium ml-auto">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
    </svg>
    Sign Out
  </button>

  <!-- Sign Out Modal -->
  <el-dialog>
    <dialog id="signout-dialog" aria-labelledby="signout-dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent z-50">
      <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>
      
      <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
        <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">
          <!-- Header -->
          <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex items-start space-x-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
              </div>
              <div class="text-left">
                <h3 id="signout-dialog-title" class="text-lg font-semibold text-gray-900">Sign Out</h3>
                <p class="mt-2 text-sm text-gray-600">
                  Are you sure you want to sign out? You will need to log in again to access your account.
                </p>
              </div>
            </div>
          </div>
          
          <!-- Sign Out Form -->
          <form action="../controllers/logout.controller.php" method="POST" class="px-6 pb-4">
            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
              <button type="submit" class="inline-flex justify-center items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Sign Out
              </button>
              <button type="button" command="close" commandfor="signout-dialog" class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200 transition-colors">
                Cancel
              </button>
            </div>
          </form>
        </el-dialog-panel>
      </div>
    </dialog>
  </el-dialog>
</header>