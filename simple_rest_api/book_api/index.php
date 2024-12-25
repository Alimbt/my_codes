<?php
// اتصال به دیتابیس
$servername = "localhost";
$username = "root"; // یا نام کاربری شما
$password = ""; // یا پسورد شما
$dbname = "book_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// بررسی اتصال
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// دریافت روش HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? $_GET['id'] : null; // دریافت id از URL اگر موجود باشد

switch ($method) {
    case 'GET':
        if ($id) {
            // دریافت یک کتاب خاص
            $sql = "SELECT * FROM books WHERE id = $id";
            $result = $conn->query($sql);
            $book = $result->fetch_assoc();
            echo json_encode($book);
        } else {
            // دریافت همه کتاب‌ها
            $sql = "SELECT * FROM books";
            $result = $conn->query($sql);
            $books = [];
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            echo json_encode($books);
        }
        break;
    
    case 'POST':
        // اضافه کردن یک کتاب جدید
        $title = $_POST['title'];
        $author = $_POST['author'];
        $published_date = $_POST['published_date'];
        
        $sql = "INSERT INTO books (title, author, published_date) VALUES ('$title', '$author', '$published_date')";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['message' => 'New book added successfully']);
        } else {
            echo json_encode(['error' => 'Error: ' . $conn->error]);
        }
        break;
    
    case 'PUT':
        // به‌روزرسانی کتاب
        parse_str(file_get_contents("php://input"), $put_vars); // دریافت داده‌های PUT
        $title = $put_vars['title'];
        $author = $put_vars['author'];
        $published_date = $put_vars['published_date'];
        
        $sql = "UPDATE books SET title='$title', author='$author', published_date='$published_date' WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['message' => 'Book updated successfully']);
        } else {
            echo json_encode(['error' => 'Error: ' . $conn->error]);
        }
        break;
    
    case 'DELETE':
        // حذف یک کتاب
        $sql = "DELETE FROM books WHERE id=$id";
        if ($conn->query($sql) === TRUE) {
            echo json_encode(['message' => 'Book deleted successfully']);
        } else {
            echo json_encode(['error' => 'Error: ' . $conn->error]);
        }
        break;
    
    default:
        echo json_encode(['error' => 'Method not allowed']);
        break;
}

// بستن اتصال
$conn->close();
?>
