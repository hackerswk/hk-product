<?php
/**
 * Platform product categories class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class PlatformProductCategories
 * Perform CRUD operations for the platform_product_categories table.
 */
class PlatformProductCategories
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * PlatformProductCategories constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product category.
     *
     * @param array $data Category data
     * @return bool True on success, False on failure
     */
    public function createCategory($data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO platform_product_categories
                (category_id, parent_id, name, retail, inquiry, is_sensitive, sensitive_type, created_at, updated_at)
                VALUES
                (:category_id, :parent_id, :name, :retail, :inquiry, :is_sensitive, :sensitive_type, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product category by its ID.
     *
     * @param int $categoryId Category ID
     * @return mixed|null Category data if found, null otherwise
     */
    public function getCategoryById($categoryId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM platform_product_categories WHERE category_id = :category_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Get all product categories.
     *
     * @return array All product categories
     */
    public function getCategories()
    {
        try {
            $sql = "SELECT * FROM platform_product_categories";
            $stmt = $this->conn->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Update an existing product category.
     *
     * @param array $data Updated category data
     * @return bool True on success, False on failure
     */
    public function updateCategory($data)
    {
        try {
            $sql = <<<EOF
                UPDATE platform_product_categories
                SET parent_id = :parent_id, name = :name, retail = :retail, inquiry = :inquiry, is_sensitive = :is_sensitive, sensitive_type = :sensitive_type, updated_at = CURRENT_TIMESTAMP()
                WHERE category_id = :category_id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product category by its ID.
     *
     * @param int $categoryId Category ID
     * @return bool True on success, False on failure
     */
    public function deleteCategory($categoryId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM platform_product_categories WHERE category_id = :category_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
