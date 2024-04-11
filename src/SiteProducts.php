<?php
/**
 * Site products class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProducts
 * Perform CRUD operations for the site_products table.
 */
class SiteProducts
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProducts constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product.
     *
     * @param array $data Product data
     * @return bool True on success, False on failure
     */
    public function createProduct($data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO site_products
                (product_id, site_id, platform_category_id, name, description, type, price, member_price,
                supply_status, inventory, scheduled_release_time, scheduled_offshelf_time, auto_offshelf_soldout,
                only_member, status, created_at, updated_at)
                VALUES
                (:product_id, :site_id, :platform_category_id, :name, :description, :type, :price, :member_price,
                :supply_status, :inventory, :scheduled_release_time, :scheduled_offshelf_time, :auto_offshelf_soldout,
                :only_member, :status, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product by its ID and status.
     *
     * @param int $productId Product ID
     * @param int $status Product status (default = 1)
     * @return mixed|null Product data if found, null otherwise
     */
    public function getProductById($productId, $status = 1)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM site_products WHERE product_id = :product_id AND status = :status
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Update an existing product.
     *
     * @param array $data Updated product data
     * @return bool True on success, False on failure
     */
    public function updateProduct($data)
    {
        try {
            $sql = <<<EOF
                UPDATE site_products
                SET site_id = :site_id, platform_category_id = :platform_category_id, name = :name,
                description = :description, type = :type, price = :price, member_price = :member_price,
                supply_status = :supply_status, inventory = :inventory, scheduled_release_time = :scheduled_release_time,
                scheduled_offshelf_time = :scheduled_offshelf_time, auto_offshelf_soldout = :auto_offshelf_soldout,
                only_member = :only_member, status = :status, updated_at = CURRENT_TIMESTAMP()
                WHERE product_id = :product_id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product by its ID.
     *
     * @param int $productId Product ID
     * @return bool True on success, False on failure
     */
    public function deleteProduct($productId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM site_products WHERE product_id = :product_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
