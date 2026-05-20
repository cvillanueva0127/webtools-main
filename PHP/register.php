<?php
session_start();
include 'connect.php';
include 'mailer.php';

// ===========================
// SIGN IN
// ===========================
if (isset($_POST['signIn'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT Id, password, is_verified FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($id, $hashedPassword, $isVerified);
    $stmt->fetch();
    $stmt->close();

    if (!$id || !password_verify($password, $hashedPassword)) {
        header("Location: ../HTML/login.html?error=" . urlencode("Invalid email or password."));
        exit();
    }

    if (!$isVerified) {
        header("Location: ../HTML/login.html?error=" . urlencode("Please verify your email before logging in."));
        exit();
    }

    // Login success — set session and redirect
    $_SESSION['user_id'] = $id;
    $_SESSION['email']   = $email;
    header("Location: homepage.php");
    exit();
}

// ===========================
// SIGN UP
// ===========================
if (isset($_POST['signUp'])) {
    $firstName = trim($_POST['firstName']);
    $lastName  = trim($_POST['lastName']);
    $email     = trim($_POST['email']);
    $gender    = $_POST['gender'];
    $password  = $_POST['password'];
    $confirm   = $_POST['confirmPassword'];

    if ($password !== $confirm) {
        header("Location: ../HTML/login.html?panel=register&error=" . urlencode("Passwords do not match."));
        exit();
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT Id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        header("Location: ../HTML/login.html?panel=register&error=" . urlencode("Email already registered."));
        exit();
    }
    $check->close();

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $token = bin2hex(random_bytes(32));

    // Insert user
    $stmt = $conn->prepare(
        "INSERT INTO users (firstName, lastName, email, password, gender, is_verified, verification_token)
         VALUES (?, ?, ?, ?, ?, 0, ?)"
    );
    $stmt->bind_param("ssssss", $firstName, $lastName, $email, $hashedPassword, $gender, $token);

    if ($stmt->execute()) {
        sendVerificationEmail($email, $firstName, $token);
        header("Location: ../HTML/login.html?success=" . urlencode("Registration successful! Check your email to verify your account."));
    } else {
        header("Location: ../HTML/login.html?panel=register&error=" . urlencode("Registration failed. Please try again."));
    }
    $stmt->close();
    exit();
}