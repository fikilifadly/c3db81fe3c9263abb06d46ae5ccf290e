<?php include_once '/var/www/html/layouts/header.php'; ?>
<h1>Login</h1>
<?= __DIR__ ?>
<form class="flex flex-col gap-3" action="/user/loginhandler" method="post">
    <div class="
    ">
        <label for="">login</label>
        <input type="text" placeholder="email" name="email" id="email" class="p-2 border border-gray-300">
    </div>
    <div class="
    ">
        <label for="">password</label>
        <input type="password" placeholder="password" name="password" id="password" class="p-2 border border-gray-300">
    </div>
    <button>Submit</button>
</form>
<?php var_dump($users) ?>
<?php include_once '/var/www/html/layouts/footer.php'; ?>