<?php

require_once 'init.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    [$email, $password] = required_keys(['email', 'password']);

    $user = $db->query("SELECT * FROM teachers WHERE email = '$email' AND password = '$password'");
    if (!$user || $user->num_rows == 0) {
        die_json(['success' => false, 'error' => 'Invalid email or password']);
    }

    $user = $user->fetch_assoc();
    $_SESSION['user_id'] = $user['id'];
    unset($user['id']);
    $_SESSION = array_merge($_SESSION, $user);
    $name = $user['name'];
    die_json(['success' => true, 'message' => "Login successful. Hi $name!"]);
}

if (isset($_SESSION['user_id'])) {
    header('Location: /');
    die();
}

require_once 'header.php';
?>

<style>
    body {
        height: 100vh;
        width: 100vw;
        background-image: url("/assets/homepage-background.jpg");
        background-size: 90% 90%;
        background-position: bottom right;
        background-repeat: no-repeat;
    }
</style>

<div class="flex-center flex-column pb-5" style="width: 40%; height: 100vh;">
    <form id="login-form" onsubmit="return submitLoginForm()">
        <div class="vstack flex-grow-0 align-self-auto gap-3 align-items-center p-5" style="width: 20rem;">
            <div class="vstack gap-3 align-items-center w-100">
                <img src="/assets/logo.png" alt="Logo" width="65" height="65">
                <h2 class="h2">Sign in</h2>
            </div>

            <div class="form-group w-100">
                <label for="email">Email</label>
                <input type="text" class="form-control w-100" id="email" name="email" required>
            </div>

            <div class="form-group w-100">
                <label for="password">Password</label>
                <input type="password" class="form-control w-100" id="password" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-3">Submit</button>
        </div>
    </form>
</div>

<script>
    function submitLoginForm() {
        const values = $('#login-form').serialize();
        $.post('/login.php', values, function(response) {
            if (response.success) {
                showToast(response.message, {
                    type: 'success'
                });
                setTimeout(() => {
                    window.location.href = '/dashboard.php'
                }, 1000);
            }
        });
        return false;
    }
</script>

<?php
require_once 'footer.php';
