<?php
$db_hostName = "localhost";
$db_userName = "root";
$db_password = "";
$db_name     = "Domaarow_CBT";

// Connect to MySQL server
$connection = new mysqli($db_hostName, $db_userName, $db_password);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Create database if it doesn't exist
if ($connection->query("CREATE DATABASE IF NOT EXISTS $db_name")) {
     "Database checked/created successfully.<br>";
} else {
    die("Error creating database: " . $connection->error);
}

// Select the database
$connection->select_db($db_name);

// Read and run the SQL file
$db_all_query_setup = file_get_contents('php/admin.sql');
if ($db_all_query_setup === false) {
    die("Error reading SQL file.");
}

if ($connection->multi_query($db_all_query_setup)) {
    do {
        // Store first result set
        if ($result = $connection->store_result()) {
            $result->free();
        }
    } while ($connection->more_results() && $connection->next_result());
     "Database structure imported successfully.";
} else {
     "Error executing SQL: " . $connection->error;
}
// -------------------function begin
    //------ chatgpt select from member where student_id=$student_id if true get profiles details (json) from same database
    // then select  exams(json) from exams compare class with student_id profiles(json) class
    // if true select exams(json) from  submit_papers where student_id=$student_id and access_code=$access_code
    // if it exists echo json status false, message examination completed
    function startExam($conn, $student_id, $access_code) {
        // Step 1: Get student class
        $stmt = $conn->prepare("SELECT class FROM users WHERE student_id = ?");
        $stmt->bind_param("s", $student_id);
        $stmt->execute();
        $stmt->bind_result($class);
        if (!$stmt->fetch()) {
            return ['status' => false, 'message' => 'Invalid student ID'];
        }
        $stmt->close();
    
        // Step 2: Get matching exam
        $stmt = $conn->prepare("SELECT id, title, class, duration, questions FROM exams WHERE access_code = ?");
        $stmt->bind_param("s", $access_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $exam = $result->fetch_assoc();
        $stmt->close();
    
        if (!$exam || $exam['class'] !== $class) {
            return ['status' => false, 'message' => 'No exam found for your class with that access code.'];
        }
    
        // Step 3: Check if already submitted
        $stmt = $conn->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ? AND access_code = ?");
        $stmt->bind_param("ss", $student_id, $access_code);
        $stmt->execute();
        $stmt->bind_result($attempts);
        $stmt->fetch();
        $stmt->close();
    
        $max_attempts = 1;
        if ($attempts >= $max_attempts) {
            return ['status' => false, 'message' => 'You have already completed this exam.'];
        }
    
        return [
            'status' => true,
            'message' => 'Exam ready',
            'exam' => [
                'title' => $exam['title'],
                'duration' => $exam['duration'],
                'questions' => json_decode($exam['questions'], true)
            ]
        ];
    }
    
    // -------------------------submit exams 

    function submitExam($conn, $student_id, $access_code, $answers, $duration) {
        // Count attempts
        $stmt = $conn->prepare("SELECT COUNT(*) FROM submissions WHERE student_id = ? AND access_code = ?");
        $stmt->bind_param("ss", $student_id, $access_code);
        $stmt->execute();
        $stmt->bind_result($attempts);
        $stmt->fetch();
        $stmt->close();
    
        $max_attempts = 1;
        if ($attempts >= $max_attempts) {
            return ['status' => false, 'message' => 'Maximum attempts reached'];
        }
    
        // Save answers
        $attempt_number = $attempts + 1;
        $answers_json = json_encode($answers);
    
        $stmt = $conn->prepare("
            INSERT INTO submissions (student_id, access_code, answers, duration, attempt_number)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("sssii", $student_id, $access_code, $answers_json, $duration, $attempt_number);
    
        if ($stmt->execute()) {
            return ['status' => true, 'message' => 'Submission saved successfully'];
        } else {
            return ['status' => false, 'message' => 'Submission failed: ' . $stmt->error];
        }
    }
    

// -------------admin

function adminLogin($conn, $id, $pass) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE student_id = ? AND role = 'admin'");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$admin) {
        return ['status' => false, 'message' => 'Admin not found'];
    }

    if ($pass !== "admin123") { // Optional: hash/verify real passwords
        return ['status' => false, 'message' => 'Invalid password'];
    }

    session_start();
    $_SESSION['admin'] = $admin['student_id'];
    return ['status' => true, 'message' => 'Login successful'];
}



function total_student($connection) {
    $sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'student'";
    $result = mysqli_query($connection, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    } else {
        return 0;
    }
}

function select_student($connection,$limiter) {
    $sql = "SELECT id, user AS student_id, class, profile 
            FROM users 
            WHERE role = 'student' 
            ORDER BY id DESC 
            LIMIT $limiter";
    
    $result = mysqli_query($connection, $sql);
    $output = '';
    $sn = 1;

    if ($result && mysqli_num_rows($result) > 0) {
        while ($student = mysqli_fetch_assoc($result)) {
            $id = $student['id'];
            $name = 'N/A';

            // Decode JSON profile
            if (!empty($student['profile'])) {
                $profile = json_decode($student['profile'], true);
                if (json_last_error() === JSON_ERROR_NONE && isset($profile['name'])) {
                    $name = htmlspecialchars($profile['name']);
                }
            }

            $student_id = htmlspecialchars($student['student_id']);
            $class = htmlspecialchars($student['class']);

            $output .= "
                <tr>
                    <td>{$sn}</td>
                    <td>{$name}</td>
                    <td>{$student_id}</td>
                    <td>{$class}</td>
                    <td class='d-flex gap-2'>
                        <a href='admin-view-student-profile?u={$id}' class='btn btn-primary'>View Profiles</a>
                    </td>
                </tr>
            ";
            $sn++;
        }
    } else {
        $output = '<tr><td colspan="5">No students found.</td></tr>';
    }

    return $output;
}


function search_student($connection, $search, $limit = 10) {
    $search = mysqli_real_escape_string($connection, $search);
    $limit = (int)$limit;

    $sql = "SELECT id, user AS student_id, class, profile 
            FROM users 
            WHERE role = 'student' 
              AND (
                  user LIKE '%$search%' OR 
                  class LIKE '%$search%' OR 
                  JSON_EXTRACT(profile, '$.name') LIKE '%$search%'
              )
            ORDER BY id DESC
            LIMIT $limit";

    $result = mysqli_query($connection, $sql);
    $output = '';
    $sn = 1;

    if ($result && mysqli_num_rows($result) > 0) {
        while ($student = mysqli_fetch_assoc($result)) {
            $id = $student['id'];
            $name = 'N/A';

            if (!empty($student['profile'])) {
                $profile = json_decode($student['profile'], true);
                if (json_last_error() === JSON_ERROR_NONE && isset($profile['name'])) {
                    $name = htmlspecialchars($profile['name']);
                }
            }

            $student_id = htmlspecialchars($student['student_id']);
            $class = htmlspecialchars($student['class']);

            $output .= "
                <tr>
                    <td>{$sn}</td>
                    <td>{$name}</td>
                    <td>{$student_id}</td>
                    <td>{$class}</td>
                    <td class='d-flex gap-2'>
                        <a href='admin-view-student-profile?u={$id}' class='btn btn-primary'>View Profile</a>
                    </td>
                </tr>
            ";
            $sn++;
        }
    } else {
        $output = '<tr><td colspan="5">No matching students found.</td></tr>';
    }

    echo $output;
}

function view_profiles($connection, $id) {
    $id = intval($id); // Always sanitize input

    $stmt = $connection->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $profile = json_decode($user['profile'], true);

        $name    = $profile['name']    ?? 'Unknown';
        $dob     = $profile['dob']     ?? 'N/A';
        $gender  = $profile['gender']  ?? 'N/A';
        $phone   = $profile['phone']   ?? 'N/A';
        $phone_2   = $profile['phone-2']   ?? 'N/A';
        $address = $profile['address'] ?? 'N/A';
        $img = $profile['img'] ?? 'images/profile.png';

        $email   = htmlspecialchars($user['user']);
        $class   = htmlspecialchars($user['class']);
        $role    = htmlspecialchars($user['role']);

        return <<<HTML
        <div class="mx-auto" style="max-width: 500px;">
          <div class="text-center p-4">
            <img src="$img" alt="Profile Photo" class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
            <h4 class="card-title mb-0">$name</h4>
            <small class="text-muted d-block mb-3">Role: $role</small>
            <hr>
            <div class="text-start">
              <p><strong>Email:</strong> $email</p>
              <p><strong>Phone:</strong> $phone</p>
              <p><strong>Phone-2:</strong> $phone_2</p>
              <p><strong>Gender:</strong> $gender</p>
              <p><strong>Date of Birth:</strong> $dob</p>
              <p><strong>Class:</strong> $class</p>
              <p><strong>Address:</strong> $address</p>
            </div>
            <!--<div class="text-end">
               <a href="edit-profile.php?id=$id" class="btn btn-outline-primary mt-3">Edit Profile</a>
            </div>-->
          </div>
        </div>
HTML;
    } else {
        return "<div class='m-2 bg-warning text-center'>User not found.</div>";
    }
}
