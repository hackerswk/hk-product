<?php
/**
 * Site product category class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductCategory
 * Perform CRUD operations for the site_product_category table.
 */
class SiteProductCategory
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductCategory constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Assign a product to a category.
     *
     * @param array $data Product category data
     * @return bool True on success, False on failure
     */
    public function assignProductToCategory($data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO site_product_category (product_id, category_id)
                VALUES (:product_id, :category_id)
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve categories assigned to a product.
     *
     * @param int $productId Product ID
     * @return array Categories assigned to the product
     */
    public function getCategoriesByProductId($productId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM site_product_category WHERE product_id = :product_id
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
     * Remove a product from a category.
     *
     * @param int $productId Product ID
     * @param int $categoryId Category ID
     * @return bool True on success, False on failure
     */
    public function removeProductFromCategory($productId, $categoryId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM site_product_category
                WHERE product_id = :product_id AND category_id = :category_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
