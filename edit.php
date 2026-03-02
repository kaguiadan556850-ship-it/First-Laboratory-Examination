<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$student = null;
$message = "";
$message_type = "";


$id = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id) {
    header("Location: dashboard.php");
    exit();
}


$stmt = $conn->prepare("SELECT * FROM students WHERE id_number = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: dashboard.php");
    exit();
}

$student = $result->fetch_assoc();
$stmt->close();


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    
   
    if (empty($name) || empty($email) || empty($course)) {
        $message = "All fields are required!";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $message_type = "error";
    } else {
        
        $stmt = $conn->prepare("UPDATE students SET name = ?, email = ?, course = ? WHERE id_number = ?");
        $stmt->bind_param("ssss", $name, $email, $course, $id);
        
        if ($stmt->execute()) {
            $message = "Student record updated successfully!";
            $message_type = "success";
            
            
            $student['name'] = $name;
            $student['email'] = $email;
            $student['course'] = $course;
        } else {
            $message = "Error updating student: " . $conn->error;
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Record</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .navbar h1 {
            font-size: 24px;
            margin: 0;
        }
        
        .navbar-actions {
            display: flex;
            gap: 20px;
        }
        
        .back-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            background-color: #f0f0f0;
        }
        
        .logout-btn {
            background: white;
            color: #667eea;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .logout-btn:hover {
            transform: translateY(-2px);
            background-color: #f0f0f0;
        }
        
        .container {
            max-width: 500px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .form-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .form-card h2 {
            margin-bottom: 10px;
            color: #667eea;
            font-size: 24px;
        }
        
        .form-card p {
            color: #999;
            margin-bottom: 25px;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
            animation: slideIn 0.3s ease;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }
        
        .form-group input:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
        }
        
        .btn-submit {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 15px;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 15px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📚 Student Record System</h1>
        <div class="navbar-actions">
            <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="form-card">
            <h2>✏️ Edit Student Record</h2>
            <p>ID: <?php echo htmlspecialchars($student['id_number']); ?></p>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="id_number">ID Number (Read-only):</label>
                    <input type="text" id="id_number" value="<?php echo htmlspecialchars($student['id_number']); ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="name">Student Name:</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="course">Course:</label>
                    <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($student['course']); ?>" required>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="update_student" class="btn-submit">Update Record</button>
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
