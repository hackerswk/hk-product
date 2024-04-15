<?php
/**
 * Site product images class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductImages
 * Perform CRUD operations for the site_product_images table.
 */
class SiteProductImages
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductImages constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product image.
     *
     * @param string $table The name of the SQL table
     * @param array $data Product image data
     * @return bool True on success, False on failure
     */
    public function createProductImage($table, $data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO $table
                (product_id, img_url, cover_pic, created_at, updated_at)
                VALUES
                (:product_id, :img_url, :cover_pic, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product image by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $imageId Image ID
     * @return mixed|null Image data if found, null otherwise
     */
    public function getProductImageById($table, $imageId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE id = :image_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Update an existing product image.
     *
     * @param string $table The name of the SQL table
     * @param array $data Updated product image data
     * @return bool True on success, False on failure
     */
    public function updateProductImage($table, $data)
    {
        try {
            $sql = <<<EOF
                UPDATE $table
                SET product_id = :product_id, img_url = :img_url, cover_pic = :cover_pic, updated_at = CURRENT_TIMESTAMP()
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
     * Delete a product image by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $imageId Image ID
     * @return bool True on success, False on failure
     */
    public function deleteProductImage($table, $imageId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM $table WHERE id = :image_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':image_id', $imageId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
