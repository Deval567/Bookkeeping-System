<?php

$title = "Login to Your Account";
require_once "validations/login.validation.php";
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
          <?php
          if (isset($_SESSION['login_errors']) && !empty($_SESSION['login_errors'])): ?>
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
              <ul class="mt-2 list-disc list-inside">
                <?php foreach ($_SESSION['login_errors'] as $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php
            unset($_SESSION['login_errors']); 
            session_destroy();
          endif;
          ?>
          <form class="space-y-4 md:space-y-6" method="POST" action="logics/login.process.php">
            <div>
              <label for="username" class="block mb-2 text-sm font-medium text-gray-900 ">Username</label>
              <input type="text" name="username" id="username" class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5"
                placeholder="Enter your username">
            </div>
            <div>
              <label for="password" class="block mb-2 text-sm font-medium text-gray-900 ">Password</label>
              <input type="password" name="password" id="password"
                placeholder="••••••••"
                class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5">
            </div>
            <input type="submit"
              class="text-white bg-gradient-to-br from-red-600 to-blue-500 w-full
                       focus:ring-4 focus:outline-none focus:ring-red-300 
                       font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2"
              value="Sign In">
            </input>
          </form>
        </div>
      </div>
    </div>
  </section>
</main>
</body>
<?php include_once "templates/closing.php"; ?>