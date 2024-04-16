<?php

include_once 'includes/header.php';

if(isset($_POST['useredit-submit'])){
	$feedbackMessage = $user->editUserInfo($_POST['uname'], $_POST['umail'], $_POST['oldpass'], $_POST['newpass'], $_POST['confirmpass']);
	
	foreach($feedbackMessage as $item){
		echo $item;
	}
}
?>


<div class="container">
<h1>Edit User</h1>
    <form method="post">
		<label for="uname">Username</label><br>
        <input type="text" name="uname" id="uname"><br>
		<label for="umail">Email</label><br>
        <input type="text" name="umail" id="umail"><br>
		<label for="oldpass">old password:</label><br>
        <input type="password" name="oldpass" id="oldpass"><br>
		<label for="newpass">new password:</label><br>
        <input type="password" name="newpass" id="newpass">
		<br><label for="confirmpass">password confirm:</label><br>
        <input type="password" name="confirmpass" id="confirmpass"><br>
        <input type="submit" name="useredit-submit" value="Register">
    </form>
</div>	
<?php 
include_once 'includes/footer.php';
?>