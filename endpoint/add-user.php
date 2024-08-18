<?php 
include ('../conn/conn.php');
session_start();

if (isset($_POST['name'], $_POST['contact'], $_POST['email'], $_POST['generated_code'])) {
    $name = $_POST['name'];
    $contactNumber = $_POST['contact'];
    $email = $_POST['email'];
    $generatedCode = $_POST['generated_code'];
    
    try {
        $stmt = $conn->prepare("SELECT `name` FROM `clients` WHERE `name` = :name ");
        $stmt->execute(['name' => $name]);

        $nameExist = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($nameExist)) {
            $conn->beginTransaction();

            $insertStmt = $conn->prepare("INSERT INTO `clients` (`name`, `contact`, `email`, `generated_code`) VALUES (:name, :contact, :email, :generated_code)");
            $insertStmt->bindParam('name', $name, PDO::PARAM_STR);
            $insertStmt->bindParam('contact', $contactNumber, PDO::PARAM_STR);
            $insertStmt->bindParam('email', $email, PDO::PARAM_STR);
            $insertStmt->bindParam('generated_code', $generatedCode, PDO::PARAM_STR);

            $insertStmt->execute();

            $conn->commit();

            echo "
            <script>
                alert('Registered Successfully!');
                window.location.href = 'http://localhost/sogodmarket/';
            </script>
            ";
        } else {
            echo "
            <script>
                alert('Account Already Exists!');
                window.location.href = 'http://localhost/sogodmarket/';
            </script>
            ";
        }

    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>
