<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - UAGC Online Course Registration System</title>
</head>
<body>
  <h1>Registration</h1>
  <p>Register and check your registration</p>

  <nav>
    <ul>
      <li><a href="login.php">Login</a></li>
      <li><a href="home.php">Home</a></li>
    </ul>
  </nav>

  <form action="process_registration.php" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>
    
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required><br>
    
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" required><br>
    
    <label for="phone">Phone:</label>
    <input type="text" id="phone" name="phone" required><br>
    
    
    <button type="submit">Register</button>
  </form>

  <footer>
    <p>&copy; 2025 UAGC Online Course Registration System. All rights reserved.</p>
  </footer>
</body>
</html>
