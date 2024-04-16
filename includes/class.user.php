<?php
class User {
  public $username;
  public $email;
  private $password;
  public $role;
  private $status;
  public $pdo;
  private $errorMessages = [];
  private $errorState = 0;


  function __construct($pdo) {
	$this->role = 4;
	$this->username = "RandomGuest123";
	$this->pdo = $pdo;
  }
  
  private function cleanInput($data){
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
  
  public function checkUserRegisterInput($uname, $umail, $upass, $upassrepeat){
	  $this->errorState = 0;
	  //START Kolla om användarens inmatade username eller email finns i databasen
	  $stmt_checkUsername = $this->pdo->prepare('SELECT * FROM table_users WHERE u_name = :uname OR u_email = :email');
	  $stmt_checkUsername->bindParam(":uname", $uname, PDO::PARAM_STR);
	  $stmt_checkUsername->bindParam(":email", $umail, PDO::PARAM_STR);
	  $stmt_checkUsername->execute();
	  
	  //Kolla om queryn returnerar något resultat
	  if($stmt_checkUsername->rowCount() > 0){
		  array_push($this->errorMessages,"Username or email is already taken! ");
		  $this->errorState = 1;
	  }
	  //SLUT Kolla om användarens inmatade username eller email finns i databasen
	  
	  //START Kolla om användarens inmatade lösenord stämmer överens ochj är tillräckligt långt
	  if($upass !== $upassrepeat){
		  array_push($this->errorMessages,"Passwords do not match! ");
		  $this->errorState = 1;
	  }
	  
	  else{
		  if(strlen($upass) < 8){
			array_push($this->errorMessages,"Password is too short! ");
			$this->errorState = 1;
		  }
	  }
	  //SLUT Kolla om användarens inmatade lösenord stämmer överens
	  
	  //START Kolla om användarens inmatade email är en "riktig" adress
	  if (!filter_var($umail, FILTER_VALIDATE_EMAIL)) {
			array_push($this->errorMessages,"Email not in correct format! ");
			$this->errorState = 1;
		}
	  
	 if($this->errorState === 1){ 
	 return $this->errorMessages;
	 }
	 else {
		 return 1;
	 }
  }
  
  public function register($uname, $umail, $upass){
	  $hashedPassword = password_hash($upass, PASSWORD_DEFAULT);
	  $uname = $this->cleanInput($uname);
	  
	  $stmt_registerUser = $this->pdo->prepare('INSERT INTO table_users (u_name, u_password, u_email, u_role_fk, u_status) VALUES (:name, :pw, :email, 1, 1)');
	  $stmt_registerUser->bindParam(":name", $uname, PDO::PARAM_STR);
	  $stmt_registerUser->bindParam(":pw", $hashedPassword, PDO::PARAM_STR);
	  $stmt_registerUser->bindParam(":email", $umail, PDO::PARAM_STR);
	  
	  if($stmt_registerUser->execute()){
		  header("Location: index.php?newuser=1");
	  }
	  else{
		  array_push($this->errorMessages, "Your info was input correctly,but something went wrong when saving to database, please be in touch with support!");
	  }
	  
  }
  
 public function login($unamemail, $upass){
	$this->errorState = 0;
	$stmt_checkUsername = $this->pdo->prepare('SELECT * FROM table_users WHERE u_name = :uname OR u_email = :email');
	$stmt_checkUsername->bindParam(":uname", $unamemail, PDO::PARAM_STR);
	$stmt_checkUsername->bindParam(":email", $unamemail, PDO::PARAM_STR);
	$stmt_checkUsername->execute();
	//Check if statement returns a result
	if($stmt_checkUsername->rowCount() === 0){
		  array_push($this->errorMessages,"Username or email does not exist! ");
		  $this->errorState = 1;
	}
	//Save user data to an array
	$userData = $stmt_checkUsername->fetch();
	
	if(password_verify($upass, $userData['u_password'])){
		$_SESSION['user_id'] = $userData['u_id'];
		$_SESSION['user_name'] = $userData['u_name'];
		$_SESSION['user_role'] = $userData['u_role_fk'];
		header("Location: home.php");
	}
	else{
		array_push($this->errorMessages,"Password is incorrect! ");
		return $this->errorMessages;
	}
	
	
 }
	public function checkLoginStatus(){
		if(isset($_SESSION['user_id'])){
			return true;
		}
		else{
			header("Location: index.php");
		}
	}
	
	public function checkUserRole($val){
		
		$stmt_checkUserRoleLevel = $this->pdo->prepare('SELECT * FROM table_roles WHERE r_id = :rid
		');
		$stmt_checkUserRoleLevel->bindParam(":rid", $_SESSION['user_role'], PDO::PARAM_INT);
		$stmt_checkUserRoleLevel->execute();
		$result = $stmt_checkUserRoleLevel->fetch();
		
		if($result['r_level'] >= $val){
			return true;
		}
		
		else{
			return false;
			}
	}
	
	public function logout(){
		session_unset();
		session_destroy();
		header("Location: index.php");
		
	}


	protected function editUserInfo($userID, $username, $email, $password_old, $password_new, $password_confirm){

	

	}




}
