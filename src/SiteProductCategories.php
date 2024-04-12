<?php
/**
 * Site product categories class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductCategories
 * Perform CRUD operations for the site_product_categories table.
 */
class SiteProductCategories
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductCategories constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product category.
     *
     * @param string $table The table name
     * @param array $data Category data
     * @return bool True on success, False on failure
     */
    public function createCategory($table, $data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO $table
                (category_id, parent_id, site_id, name, created_at, updated_at)
                VALUES
                (:category_id, :parent_id, :site_id, :name, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
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
                SELECT * FROM site_product_categories WHERE category_id = :category_id
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
     * Update an existing product category.
     *
     * @param string $table The table name
     * @param array $data Updated category data
     * @return bool True on success, False on failure
     */
    public function updateCategory($table, $data)
    {
        try {
            $sql = <<<EOF
                UPDATE $table
                SET parent_id = :parent_id, site_id = :site_id, name = :name, updated_at = CURRENT_TIMESTAMP()
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
                DELETE FROM site_product_categories WHERE category_id = :category_id
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
