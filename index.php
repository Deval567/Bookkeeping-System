<?php
$title = "Login to Your Account";
include_once "templates/header.php";
?>
<main>
  <section class="bg-gray-300">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
      <a href="#" class="flex items-center mb-6 text-2xl font-semibold text-gray-900 ">
        <img class="w-24 h-24 mr-2 rounded-full" src="images/logo.jpg" alt="JJ&c logo">
        JJ&C Stainless Steel Fabrication Services
      </a>
      <div class="w-full bg-white rounded-lg shadow  md:mt-0 sm:max-w-md xl:p-0 ">
        <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
          <h1 class="text-xl text-center font-bold leading-tight tracking-tight text-gray-900 md:text-2xl ">
            Sign in to your account
          </h1>
           <!-- Success Message -->
    <?php if (isset($_SESSION['logout_message'])): ?>
        <div class="fixed <?= $successBottom ?> left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['logout_message'] as $message): ?>
                        <p class="text-sm font-medium text-green-800"><?= $message; ?></p>
                    <?php endforeach; ?>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['logout_message']); ?>
    <?php endif; ?>
          <!-- Error Message -->
          <?php if (isset($_SESSION['login_errors'])): ?>
            <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
              <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                  <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="flex-1">
                  <?php foreach ($_SESSION['login_errors'] as $error): ?>
                    <p class="text-sm font-medium text-red-800"><?= htmlspecialchars($error); ?></p>
                  <?php endforeach; ?>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                  <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                  </svg>
                </button>
              </div>
            </div>
            <?php unset($_SESSION['login_errors']); ?>
          <?php endif; ?>
          <form class="space-y-4 md:space-y-6" method="POST" action="controllers/login.controller.php">
            <div>
              <label for="username" class="block mb-2 text-sm font-medium text-gray-900 ">Username</label>
              <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                placeholder="Enter your username" required>
            </div>
            <div>
              <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
              <input type="password" name="password" id="password"
                placeholder="••••••••"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5" required>
            </div>
            <button type="submit"
              class="text-white bg-gradient-to-br from-red-600 to-blue-500 w-full
                       focus:ring-4 focus:outline-none focus:ring-red-300 
                       font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
              Sign In
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>
</body>