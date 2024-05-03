<?php
/**
 * Site product videos class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductVideos
 * Perform CRUD operations for the site_product_videos table.
 */
class SiteProductVideos
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductVideos constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product video.
     *
     * @param string $table The name of the SQL table
     * @param array $data Product video data
     * @return bool True on success, False on failure
     */
    public function createProductVideo($table, $data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO $table
                (product_id, video_url, created_at, updated_at)
                VALUES
                (:product_id, :video_url, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product video by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $videoId Video ID
     * @return mixed|null Video data if found, null otherwise
     */
    public function getProductVideoById($table, $videoId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE id = :video_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieve product videos by product ID.
     *
     * @param string $table The name of the SQL table
     * @param int $productId Product ID
     * @return array Product videos
     */
    public function getProductVideos($table, $productId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE product_id = :product_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Update an existing product video.
     *
     * @param string $table The name of the SQL table
     * @param array $data Updated product video data
     * @return bool True on success, False on failure
     */
    public function updateProductVideo($table, $data)
    {
        try {
            $sql = <<<EOF
                UPDATE $table
                SET product_id = :product_id, video_url = :video_url, updated_at = CURRENT_TIMESTAMP()
                WHERE id = :id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product video by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $videoId Video ID
     * @return bool True on success, False on failure
     */
    public function deleteProductVideo($table, $videoId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM $table WHERE id = :video_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':video_id', $videoId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Set the database for the connection.
     *
     * @param string $dbName Database name
     * @return void
     */
    public function setDatabase($dbName)
    {
        try {
            $this->conn->exec("USE $dbName");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

}
