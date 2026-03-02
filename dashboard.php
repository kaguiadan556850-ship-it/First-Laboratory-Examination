<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$message = "";
$message_type = "";


$students = [];
$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
if ($result) {
    $students = $result->fetch_all(MYSQLI_ASSOC);
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $id_number = trim($_POST['id_number']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    
   
    if (empty($id_number) || empty($name) || empty($email) || empty($course)) {
        $message = "All fields are required!";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format!";
        $message_type = "error";
    } else {
      
        $check = $conn->prepare("SELECT id_number FROM students WHERE id_number = ?");
        $check->bind_param("s", $id_number);
        $check->execute();
        $check->store_result();
        
        if ($check->num_rows > 0) {
            $message = "ID number already exists!";
            $message_type = "error";
        } else {
        
            $stmt = $conn->prepare("INSERT INTO students (id_number, name, email, course) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $id_number, $name, $email, $course);
            
            if ($stmt->execute()) {
                $message = "Student record added successfully!";
                $message_type = "success";
                
               
                $result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
                if ($result) {
                    $students = $result->fetch_all(MYSQLI_ASSOC);
                }
            } else {
                $message = "Error adding student: " . $conn->error;
                $message_type = "error";
            }
            $stmt->close();
        }
        $check->close();
    }
}


if (isset($_GET['delete'])) {
    $id_number = $_GET['delete'];
    
    $stmt = $conn->prepare("DELETE FROM students WHERE id_number = ?");
    $stmt->bind_param("s", $id_number);
    
    if ($stmt->execute()) {
        $message = "Student record deleted successfully!";
        $message_type = "success";
        
        
        $result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
        if ($result) {
            $students = $result->fetch_all(MYSQLI_ASSOC);
        }
    } else {
        $message = "Error deleting student: " . $conn->error;
        $message_type = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Record Dashboard</title>
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
        
        .navbar-info {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        
        .user-info {
            font-size: 14px;
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
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
        
        .content {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 900px) {
            .content {
                grid-template-columns: 1fr;
            }
        }
        
        .form-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .form-card h2 {
            margin-bottom: 20px;
            color: #667eea;
            font-size: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #555;
            font-size: 14px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-submit {
            width: 100%;
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
        
        .students-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }
        
        .students-card h2 {
            margin-bottom: 20px;
            color: #667eea;
            font-size: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        table th {
            background-color: #667eea;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 600;
        }
        
        table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        
        table tbody tr:hover {
            background-color: #f9f9f9;
        }
        
        .action-btns {
            display: flex;
            gap: 8px;
        }
        
        .btn-edit, .btn-delete {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-edit {
            background-color: #28a745;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        
        .empty-message {
            text-align: center;
            padding: 40px;
            color: #999;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>📚 Student Record System</h1>
        <div class="navbar-info">
            <div class="user-info">
                Welcome, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
            </div>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="content">
            <!-- Add Student Form -->
            <div class="form-card">
                <h2>➕ Add New Student</h2>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="id_number">ID Number:</label>
                        <input type="text" id="id_number" name="id_number" placeholder="e.g., STU001" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="name">Student Name:</label>
                        <input type="text" id="name" name="name" placeholder="Full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="student@example.com" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="course">Course:</label>
                        <input type="text" id="course" name="course" placeholder="e.g., BS Computer Science" required>
                    </div>
                    
                    <button type="submit" name="add_student" class="btn-submit">Add Student</button>
                </form>
            </div>
            
            <!-- Students List -->
            <div class="students-card">
                <h2>📋 Student Records</h2>
                <?php if (empty($students)): ?>
                    <div class="empty-message">
                        No student records yet. Add a new student to get started!
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['id_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="edit.php?id=<?php echo urlencode($student['id_number']); ?>" class="btn-edit">Edit</a>
                                            <a href="?delete=<?php echo urlencode($student['id_number']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
