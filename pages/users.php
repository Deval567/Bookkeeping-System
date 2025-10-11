<?php
$title = "Users Management";
include_once "../templates/header.php";
include_once "../templates/sidebar.php";
include_once "../templates/banner.php";
require_once "../configs/dbc.php";
require_once "../models/users.class.php";
$userModel = new Users($conn, "", "", "", "");
$users = $userModel->showAllUsers();
?>
<main class="g-gray-100 p-6">
    <div>
        <h2 class="text-2xl font-semibold"><?php echo $title ?></h2>
        <p class="mt-2 text-gray-600">Manage user accounts here.</p>
    </div>

    <div class="flex justify-end mb-6">
        <button
            command="show-modal"
            commandfor="dialog"
            id="openModalBtn"
            class="flex items-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 rounded-md shadow-sm transform transition duration-200 hover:-translate-y-0.5 hover:scale-105">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
            <span>Add User</span>
        </button>
    </div>

    <!-- Success and Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-center gap-3 rounded-lg bg-green-50 border border-green-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-5 w-5 text-green-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-green-800">
                        <?php echo htmlspecialchars($_SESSION['success_message']); ?>
                    </p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-green-600 hover:text-green-800">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    <?php
        unset($_SESSION['success_message']);
    endif;
    ?>

    <?php if (isset($_SESSION['user_errors'])): ?>
        <div class="fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 max-w-md">
            <div class="flex items-start gap-3 rounded-lg bg-red-50 border border-red-200 px-4 py-3 shadow-lg">
                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-100">
                    <svg class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <?php foreach ($_SESSION['user_errors'] as $error): ?>
                        <p class="text-sm font-medium text-red-800">
                            <?php echo htmlspecialchars($error); ?>
                        </p>
                    <?php endforeach; ?>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-600 hover:text-red-800">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    <?php
        unset($_SESSION['user_errors']);
    endif;
    ?>

    <!-- Users Table -->
    <div class=" rounded-lg shadow">
   <table class="min-w-full bg-white">
        <thead>
            <tr class="bg-red-700 text-white">
                <th class="py-4 px-6 text-left text-sm font-medium">Username</th>
                <th class="py-4 px-6 text-left text-sm font-medium">Password</th>
                <th class="py-4 px-6 text-left text-sm font-medium">Role</th>
                <th class="py-4 px-6 text-center text-sm font-medium">Actions</th>
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200">
            <?php foreach ($users as $user): ?>
                <tr class="hover:bg-gray-100 hover:scale-[1.02] transition-all duration-200 hover:shadow-lg">
                    <td class="py-4 px-6 text-gray-900 font-medium">
                        <?= htmlspecialchars($user['username']); ?>
                    </td>
                    <td class="py-4 px-6 text-gray-600">
                        <?= htmlspecialchars($user['password']); ?>
                    </td>
                    <td class="py-4 px-6">
                        <?php
                        $role = htmlspecialchars($user['role']);
                        $roleStyles = [
                            'Admin' => 'bg-purple-100 text-purple-700',
                            'Staff' => 'bg-blue-100 text-blue-700',
                        ];
                        $style = $roleStyles[$user['role']] ?? 'bg-gray-100 text-gray-700';
                        ?>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium <?= $style ?>">
                            <?= $role ?>
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center justify-center space-x-3">
                            <button command="show-modal" commandfor="edit-dialog" class="relative group p-2 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                    Edit
                                </span>
                            </button>

                            <button command="show-modal" commandfor="delete-dialog" class="relative group p-2 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                </svg>
                                <span class="absolute -top-8 left-1/2 transform -translate-x-1/2 px-2 py-1 text-xs text-white bg-gray-900 rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                    Delete
                                </span>
                            </button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>

<script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
<!--Modals -->
<el-dialog>
    <dialog id="dialog" aria-labelledby="dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="dialog-title" class="text-lg font-semibold text-gray-900 pt-2">Add New User</h3>
                        </div>
                    </div>
                </div>

                <!-- Add User Form -->
                <form action="../controllers/user.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                    <input type="hidden" name="action" value="add_user">
                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" placeholder="Enter username"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" placeholder="Enter password"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Role</label>
                        <select name="role"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            <option value="">Select role</option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Save
                        </button>
                        <button type="button" command="close" commandfor="dialog"
                            class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>
<!-- Edit User Modal -->
<el-dialog>
    <dialog id="edit-dialog" aria-labelledby="edit-dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="edit-dialog-title" class="text-lg font-semibold text-gray-900">Edit User</h3>
                        </div>
                    </div>
                </div>

                <!-- Edit User Form -->
                <form action="../controllers/user.controller.php" method="POST" class="px-6 pb-4 space-y-4">
                    <input type="hidden" name="id" value="<?php echo  htmlspecialchars($user['id']); ?>">
                    <input type="hidden" name="action" value="update_user">

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required placeholder="Enter username" value="<?php echo htmlspecialchars($user['username']); ?>"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">New Password </label>
                        <input type="password" name="password" placeholder="Enter new password"
                            value="<?php echo htmlspecialchars($user['password']); ?>"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 placeholder-gray-400 focus:ring focus:ring-blue-400 focus:outline-none" />
                    </div>

                    <div>
                        <label class="block text-sm text-gray-700 mb-1">Role</label>
                        <select name="role"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-gray-900 focus:ring focus:ring-blue-400 focus:outline-none">
                            <option value="">Select role</option>
                            <option value="Admin">Admin</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                            Update
                        </button>
                        <button type="button" command="close" commandfor="edit-dialog"
                            class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>

<!-- Delete User Modal -->
<el-dialog>
    <dialog id="delete-dialog" aria-labelledby="delete-dialog-title" class="fixed inset-0 size-auto max-h-none max-w-none overflow-y-auto bg-transparent backdrop:bg-transparent">
        <el-dialog-backdrop class="fixed inset-0 bg-gray-900/50 transition-opacity data-closed:opacity-0 data-enter:duration-300 data-enter:ease-out data-leave:duration-200 data-leave:ease-in"></el-dialog-backdrop>

        <div tabindex="0" class="flex min-h-full items-end justify-center p-4 text-center focus:outline-none sm:items-center sm:p-0">
            <el-dialog-panel class="relative w-full max-w-lg transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all">

                <!-- Header -->
                <div class="px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex items-start space-x-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-red-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h3 id="delete-dialog-title" class="text-lg font-semibold text-gray-900">Delete User</h3>
                            <p class="mt-2 text-sm text-gray-600">
                                Are you sure you want to delete <span class="font-semibold"><?php echo htmlspecialchars($user['username']); ?></span>? This action cannot be undone.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Delete Form -->
                <form action="../controllers/user.controller.php" method="POST" class="px-6 pb-4">
                    <input type="hidden" name="action" value="delete_user">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">

                    <!-- Buttons -->
                    <div class="flex flex-col sm:flex-row sm:flex-row-reverse sm:space-x-3 sm:space-x-reverse mt-4">
                        <button type="submit"
                            class="inline-flex justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Delete
                        </button>
                        <button type="button" command="close" commandfor="delete-dialog"
                            class="mt-3 sm:mt-0 inline-flex justify-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-900 hover:bg-gray-200">
                            Cancel
                        </button>
                    </div>
                </form>

            </el-dialog-panel>
        </div>
    </dialog>
</el-dialog>