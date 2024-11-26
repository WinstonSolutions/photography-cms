<?php
require_once __DIR__ . '/Database.php';

class Image {
    private $db;
    private $config;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->config = require __DIR__ . '/../config/config.php';
    }
    
    // 处理图片上传
    public function upload($file, $album_id, $user_id) {
        // 检查文件类型
        $file_type = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($file_type, $this->config['allowed_image_types'])) {
            throw new Exception('Unsupported file type');
        }
        
        // 检查文件大小
        if ($file['size'] > $this->config['max_file_size']) {
            throw new Exception('File too large');
        }
        
        // 生成唯一文件名
        $filename = uniqid() . '.' . $file_type;
        $upload_path = $this->config['upload_path'] . $filename;
        
        // 移动上传的文件
        if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
            throw new Exception('File upload failed');
        }
        
        // 返回上传的路径
        return $upload_path; // 返回原图路径
    }

    public function getAllImages() {
        $query = "SELECT file_path, thumbnail_path, album_id FROM images"; // 获取所有图片，包括缩略图路径和相册ID
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Save both original and thumbnail paths to the database.
     *
     * @param string $file_path The path of the original image.
     * @param string $thumbnail_path The path of the thumbnail image.
     * @param int $album_id The ID of the album.
     * @param int $user_id The ID of the user.
     * @param string $filename The filename of the image.
     * @return bool True on success, false on failure.
     */
    public function saveImagePaths($file_path, $thumbnail_path, $album_id, $user_id, $filename) {
        // 只存储相对路径
        $relative_file_path = 'uploads/' . basename($file_path); // 只存储文件名
        $relative_thumbnail_path = 'uploads/' . basename($thumbnail_path); // 只存储缩略图文件名

        // Prepare the SQL statement
        $stmt = $this->db->prepare("INSERT INTO images (file_path, thumbnail_path, album_id, user_id, filename, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bindParam(1, $relative_file_path);
        $stmt->bindParam(2, $relative_thumbnail_path);
        $stmt->bindParam(3, $album_id);
        $stmt->bindParam(4, $user_id);
        $stmt->bindParam(5, $filename); // 绑定文件名参数

        // Execute the statement and return the result
        return $stmt->execute();
    }
} 