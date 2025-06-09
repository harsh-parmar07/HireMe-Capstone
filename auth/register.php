<?php include("../includes/header.php"); ?>
<h2>Register</h2>
<form method="post">
  Name: <input name="name"><br>
  Email: <input name="email"><br>
  Password: <input type="password" name="password"><br>
  Role: 
  <select name="role">
    <option value="client">Client</option>
    <option value="freelancer">Freelancer</option>
  </select><br>
  <input type="submit" value="Register">
</form>
<p>// Logic to save user will go here</p>
<?php include("../includes/footer.php"); ?>