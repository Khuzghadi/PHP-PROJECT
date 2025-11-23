<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up - Inventory System</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(45deg, #cdd89a, #9fc8bc);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .signup-box {
      background:#a7c4b1;
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 14px 200px rgba(0,0,0,0.5);
      width: 350px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 15px;
    }

    label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
    }

    input, select {
      width: 100%;
      padding: 8px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 4px;
    }

    input.error, select.error {
      border-color: red;
    }

    .error-message {
      color: red;
      font-size: 13px;
      margin-top: 4px;
      display: none;
    }

    .error-message.active {
      display: block;
    }

    button {
      width: 100%;
      padding: 10px;
      background: #28a745;
      color: #fff;
      border: none;
      border-radius: 4px;
      margin-top: 10px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background: #218838;
    }

    .link {
      text-align: center;
      margin-top: 15px;
    }

    .link a {
      color: #007bff;
      text-decoration: none;
    }

    .link a:hover {
      text-decoration: underline;
    }
	.gender-options {
  display: flex;
  gap: 20px;
  margin-top: 5px;
}
.gender-options label {
  font-weight: normal;
}

  </style>
</head>
<body>

  <div class="signup-box">
    <h2>Sign Up</h2>
    <form id="signupForm" novalidate>
      <div class="form-group">
        <label for="name">Name</label>
        <input type="text" id="name">
        <div class="error-message" id="nameError">Please enter your name</div>
      </div>
      <div class="form-group">
        <label for="number">ITS Number</label>
        <input type="text" id="number">
        <div class="error-message" id="numberError">Please enter your number</div>
      </div>
			  <div class="form-group">
		  <label>Gender</label>
		  <div class="gender-options">
			<label><input type="radio" name="gender" value="Male"> Male</label>
			<label><input type="radio" name="gender" value="Female"> Female</label>
		  </div>
		  <div class="error-message" id="genderError">Please select a gender</div>
		</div>

      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email">
        <div class="error-message" id="emailError">Enter a valid email (must include @ and .)</div>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone">
        <div class="error-message" id="phoneError">Phone number must be 10 digits</div>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password">
        <div class="error-message" id="passwordError">Please enter a password</div>
      </div>
      <div class="form-group">
        <label for="confirmPassword">Confirm Password</label>
        <input type="password" id="confirmPassword">
        <div class="error-message" id="confirmPasswordError">Passwords do not match</div>
      </div>
      <button type="submit">Register</button>
    </form>
    <div class="link">
      <p>Already have an account? <a href="User-login.php">Login</a></p>
    </div>
  </div>

  <script>
    document.getElementById('signupForm').addEventListener('submit', function (e) {
  e.preventDefault();

  let isValid = true;

  const fields = ['name', 'number', 'email', 'phone', 'password', 'confirmPassword'];

  // Clear previous errors
  fields.forEach(id => {
    const input = document.getElementById(id);
    const error = document.getElementById(id + 'Error');
    input.classList.remove('error');
    error.classList.remove('active');
  });

  // Field elements
  const name = document.getElementById('name');
  const number = document.getElementById('number');
  const email = document.getElementById('email');
  const phone = document.getElementById('phone');
  const password = document.getElementById('password');
  const confirmPassword = document.getElementById('confirmPassword');

  // Name validation (only characters)
  if (name.value.trim() === '' || !/^[A-Za-z\s]+$/.test(name.value.trim())) {
    name.classList.add('error');
    nameError.textContent = "Name must contain only letters";
    nameError.classList.add('active');
    isValid = false;
  }

  // ITS Number required
  if (number.value.trim() === '') {
    number.classList.add('error');
    numberError.classList.add('active');
    isValid = false;
  }

  // Gender validation (radio button check)
  const genderRadios = document.querySelectorAll('input[name="gender"]');
  let genderSelected = false;
  genderRadios.forEach(radio => {
    if (radio.checked) genderSelected = true;
  });
  if (!genderSelected) {
    genderError.classList.add('active');
    isValid = false;
  }

  // Email validation
  const emailValue = email.value.trim();
  if (
    emailValue === '' ||
    !emailValue.includes('@') ||
    !emailValue.includes('.') ||
    emailValue !== emailValue.toLowerCase()
  ) {
    email.classList.add('error');
    emailError.classList.add('active');
    isValid = false;
  }

  // Phone number validation (10 digits only)
  const phoneDigits = phone.value.trim();
  if (!/^\d{10}$/.test(phoneDigits)) {
    phone.classList.add('error');
    phoneError.classList.add('active');
    isValid = false;
  }

  // Password required
  if (password.value.trim() === '') {
    password.classList.add('error');
    passwordError.classList.add('active');
    isValid = false;
  }

  // Confirm password match
  if (confirmPassword.value.trim() === '' || password.value !== confirmPassword.value) {
    confirmPassword.classList.add('error');
    confirmPasswordError.classList.add('active');
    isValid = false;
  }

  // If valid, submit
  if (isValid) {
    alert('Signup successful!');
    window.location.href = 'login.html';
  }
});
  </script>

</body>
</html>
