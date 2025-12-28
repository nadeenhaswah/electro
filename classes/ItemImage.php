<?php
/**
 * Item Image Model
 * Handles product image management
 */

require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/classes/Database.php';

class ItemImage {
    private $db;
    private $table = "item_images";

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Add image to item
     */
    public function addImage($item_id, $image_path, $is_main = 0) {
        // If this is main image, unset other main images
        if ($is_main) {
            $this->unsetMainImages($item_id);
        }

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (item_id, image_path, is_main) VALUES (:item_id, :image_path, :is_main)");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->bindParam(':image_path', $image_path);
                $stmt->bindParam(':is_main', $is_main, PDO::PARAM_INT);
                $result = $stmt->execute();
            } else {
                $stmt = $this->db->prepare("INSERT INTO {$this->table} (item_id, image_path, is_main) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $item_id, $image_path, $is_main);
                $result = $stmt->execute();
                $stmt->close();
            }

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get images for item
     */
    public function getItemImages($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE item_id = :item_id ORDER BY is_main DESC, image_id ASC");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll();
            } else {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE item_id = ? ORDER BY is_main DESC, image_id ASC");
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_all(MYSQLI_ASSOC);
            }
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Get main image for item
     */
    public function getMainImage($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE item_id = :item_id AND is_main = 1 LIMIT 1");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetch();
            } else {
                $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE item_id = ? AND is_main = 1 LIMIT 1");
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $stmt->close();
                return $result->fetch_assoc();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Set main image
     */
    public function setMainImage($image_id, $item_id) {
        // Unset other main images
        $this->unsetMainImages($item_id);

        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET is_main = 1 WHERE image_id = :image_id");
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET is_main = 1 WHERE image_id = ?");
                $stmt->bind_param("i", $image_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Unset all main images for item
     */
    private function unsetMainImages($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET is_main = 0 WHERE item_id = :item_id");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                $stmt = $this->db->prepare("UPDATE {$this->table} SET is_main = 0 WHERE item_id = ?");
                $stmt->bind_param("i", $item_id);
                $stmt->execute();
                $stmt->close();
            }
        } catch (Exception $e) {
            // Ignore errors
        }
    }

    /**
     * Delete image
     */
    public function deleteImage($image_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE image_id = :image_id");
                $stmt->bindParam(':image_id', $image_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE image_id = ?");
                $stmt->bind_param("i", $image_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete all images for item
     */
    public function deleteItemImages($item_id) {
        try {
            if ($this->db instanceof PDO) {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE item_id = :item_id");
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
                return $stmt->execute();
            } else {
                $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE item_id = ?");
                $stmt->bind_param("i", $item_id);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            }
        } catch (Exception $e) {
            return false;
        }
    }
}


