<?php
$title = "Login to Your Account";
include_once "templates/header.php";
?>

<style>
  @keyframes float {

    0%,
    100% {
      transform: translateY(0px);
    }

    50% {
      transform: translateY(-20px);
    }
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(-30px);
    }

    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  @keyframes pulse {

    0%,
    100% {
      opacity: 1;
    }

    50% {
      opacity: 0.5;
    }
  }

  .animate-float {
    animation: float 6s ease-in-out infinite;
    will-change: transform;
  }

  .animate-fadeIn {
    opacity: 0;
    will-change: opacity, transform;
    animation: fadeIn 0.8s ease-out forwards;
  }

  .animate-slideIn {
    opacity: 0;
    transform: translateX(-30px);
    will-change: transform, opacity;
    animation: slideIn 0.6s ease-out forwards;
  }

  .animate-pulse-custom {
    animation: pulse 2s ease-in-out infinite;
  }

  .glass-effect {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
  }

  .gradient-bg {
    background: linear-gradient(135deg, #fef2f2 0%, #dbeafe 50%, #fae8ff 100%);
  }
</style>

<main class="min-h-screen gradient-bg relative overflow-hidden">
  <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="absolute top-20 -left-20 w-96 h-96 bg-red-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float"></div>
    <div class="absolute top-40 -right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 2s;"></div>
    <div class="absolute -bottom-20 left-1/2 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-float" style="animation-delay: 4s;"></div>
  </div>

  <section class="relative z-10 flex flex-col items-center justify-center px-6 py-8 mx-auto min-h-screen">
    <a href="#" class="flex items-center mb-8 animate-fadeIn" style="animation-delay: 0s;">
      <img class="w-24 h-24 mr-3 rounded-full shadow-lg" src="images/logo.jpg" alt="JJ&C logo">
      <h1 class="text-2xl font-semibold text-gray-900">JJ&C Stainless Steel Fabrication Services</h1>
    </a>

    <div class="w-full max-w-md animate-fadeIn" style="animation-delay: 0.2s;">
      <div class="glass-effect rounded-2xl p-8 space-y-6">
        <div class="text-center">
          <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back</h2>
          <p class="text-gray-600">Sign in to access your account</p>
        </div>

        <?php
        // Determine success message position
        $successBottom = isset($_SESSION['logout_message']) ? 'bottom-20' : 'bottom-4';
        ?>

        <?php if (!empty($_SESSION['logout_message'])): ?>
          <div class="fixed <?= $successBottom ?> left-1/2 transform -translate-x-1/2 z-50 w-11/12 sm:max-w-xl md:max-w-2xl">
            <div class="flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 shadow-lg">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex-1">
                <?php
                $messages = (array) $_SESSION['logout_message'];
                $first = reset($messages);
                echo '<p class="text-sm font-medium text-green-800">' . htmlspecialchars($first) . '</p>';
                ?>
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
        <?php if (!empty($_SESSION['login_errors'])): ?>
          <div class="fixed <?= $successBottom ?> left-1/2 transform -translate-x-1/2 z-50 w-11/12 sm:max-w-xl md:max-w-2xl">
            <div class="flex items-center gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
              <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="flex-1">
                <?php
                $messages = (array) $_SESSION['login_errors'];
                $first = reset($messages);
                echo '<p class="text-sm font-medium text-red-800">' . htmlspecialchars($first) . '</p>';
                ?>
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



        <form class="space-y-5" method="POST" action="controllers/login.controller.php">
          <div class="animate-slideIn" style="animation-delay: 0.3s;">
            <label for="username" class="block mb-2 text-sm font-semibold text-gray-700">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                </svg>
                Username
              </span>
            </label>
            <input type="text" name="username" id="username"
              class="bg-white border-2 border-gray-200 text-gray-900 rounded-xl focus:ring-0  block w-full p-3.5 transition duration-200"
              placeholder="Enter your username" required>
          </div>

          <div class="animate-slideIn" style="animation-delay: 0.45s;">
            <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">
              <span class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                </svg>
                Password
              </span>
            </label>
            <input type="password" name="password" id="password"
              placeholder="••••••••"
              class="bg-white border-2 border-gray-200 text-gray-900 rounded-xl focus:ring-0  block w-full p-3.5 transition duration-200" required>
          </div>

          <button type="submit"
            class="w-full mt-6 text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-bold rounded-xl text-base px-6 py-4 text-center transform transition duration-200 hover:scale-105 shadow-lg hover:shadow-2xl animate-fadeIn"
            style="animation-delay: 0.6s;">
            <span class="flex items-center justify-center gap-2">
              Sign In
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
              </svg>
            </span>
          </button>
        </form>

        <div class="pt-4 text-center text-sm text-gray-600 border-t border-gray-200">
          <p>© 2025 JJ&C Stainless Steel. All rights reserved.</p>
        </div>
      </div>
    </div>
  </section>
</main>
</body>