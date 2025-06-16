<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - UAGC Online Course Registration System</title>
</head>
<body>
  <h1>Login</h1>
  <p>Login Here</p>

  <form action="process_login.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>
    
    <button type="submit">Login</button>
  </form>

<nav>
  <ul>
    <li><a href="home.php">Home</a></li>
      <li><a href="enroll.php">Enroll</a></li>
    <li><a href="registration.php">Register</a></li>
  </ul>
</nav>

  <footer>
    <p>&copy; 2025 UAGC Online Course Registration System. All rights reserved.</p>
  </footer>
</body>
</html>
