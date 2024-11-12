<?php

require_once './dbconnection.php';
require_once './vendor/autoload.php';
use Cloudinary\Cloudinary;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Configuration\Configuration;

session_start();
$localid = $_SESSION['id'] ?? null;

if (!$localid) {
    die("User not authenticated.");
}

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    Configuration::instance([
        'cloud' => [
            'cloud_name' => 'ddujkyfzj',
            'api_key' => '897774582825532',
            'api_secret' => 'DC5Y_PbDb6Dvv2hAgHvXGB1STnE',
        ],
        'url' => [
            'secure' => true
        ]
    ]);

    $uploadedFile = $_FILES['profile_picture']['tmp_name'];

    try {
        $uploadResult = (new UploadApi())->upload($uploadedFile, [
            'folder' => 'profile_pics',
            'public_id' => 'user_' . uniqid(),
        ]);

        $imageUrl = $uploadResult['secure_url'];

        $conn = $db->connect();
        $stmt = $conn->prepare("UPDATE users SET profile_picture = :imageUrl WHERE id = :localid");
        $stmt->bindParam(":imageUrl", $imageUrl);
        $stmt->bindParam(":localid", $localid);

        if ($stmt->execute()) {
            header('Location: ./profile.php');
            exit();
        } else {
            echo "Failed to update image URL in the database.";
        }

    } catch (Exception $e) {
        echo "Upload failed: " . $e->getMessage();
    }
}
