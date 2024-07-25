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
     * @param string $table The name of the SQL table
     * @param array $data Product data
     * @return int|bool The last inserted ID on success, False on failure
     */
    public function createProduct($table, $data)
    {
        try {
            $sql = <<<EOF
            INSERT INTO $table
            (site_id, platform_category_id, name, description, type, price, member_price,
            supply_status, inventory, release_at, offshelf_at, scheduled_release_time, scheduled_offshelf_time, auto_offshelf_soldout,
            only_member, status, created_at, updated_at)
            VALUES
            (:site_id, :platform_category_id, :name, :description, :type, :price, :member_price,
            :supply_status, :inventory, :release_at, :offshelf_at, :scheduled_release_time, :scheduled_offshelf_time, :auto_offshelf_soldout,
            :only_member, :status, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
EOF;

            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute($data);
            if ($result) {
                return $this->conn->lastInsertId();
            } else {
                return false;
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product by its ID and status.
     *
     * @param string $table The name of the SQL table
     * @param int $productId Product ID
     * @param int $status Product status (default = 1)
     * @return mixed|null Product data if found, null otherwise
     */
    public function getProductById($table, $productId, $status = 1)
    {
        try {
            $sql = <<<EOF
            SELECT * FROM $table WHERE product_id = :product_id AND status = :status AND deleted_at IS NULL
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
     * Get products with pagination for a specific site.
     *
     * @param string $table The name of the SQL table
     * @param int $site_id The ID of the site
     * @param int $page The page number
     * @param int $pageSize The number of products per page
     * @return array Products for the specified site and page
     */
    public function getProducts($table, $site_id, $page, $pageSize)
    {
        try {
            // Calculate OFFSET value
            $offset = ($page - 1) * $pageSize;

            // Build SQL query
            $sql = "SELECT * FROM $table WHERE site_id = :site_id AND deleted_at IS NULL LIMIT :pageSize OFFSET :offset";

            // Prepare SQL statement
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            $stmt->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            // Execute query
            $stmt->execute();

            // Return results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Update an existing product.
     *
     * @param string $table The name of the SQL table
     * @param array $data Updated product data
     * @return bool True on success, False on failure
     */
    public function updateProduct($table, $data)
    {
        try {
            $sql = <<<EOF
        UPDATE $table
        SET site_id = :site_id, platform_category_id = :platform_category_id, name = :name,
        description = :description, type = :type, price = :price, member_price = :member_price,
        supply_status = :supply_status, inventory = :inventory, release_at = :release_at,
        offshelf_at = :offshelf_at, scheduled_release_time = :scheduled_release_time,
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
     * @param string $table The name of the SQL table
     * @param int $productId Product ID
     * @return bool True on success, False on failure
     */
    public function deleteProduct($table, $productId)
    {
        try {
            $sql = <<<EOF
            UPDATE $table SET deleted_at = NOW() WHERE product_id = :product_id
EOF;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
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

    /**
     * Get products that are on sell (status != 0) for a specific site with pagination.
     *
     * @param string $table The name of the SQL table
     * @param int $site_id The ID of the site
     * @param int $page The page number
     * @param int $pageSize The number of products per page
     * @return array Products on sell for the specified site and page
     */
    public function getOnSellProducts($table, $site_id, $page, $pageSize)
    {
        try {
            // Calculate OFFSET value
            $offset = ($page - 1) * $pageSize;

            // Build SQL query
            $sql = "SELECT * FROM $table WHERE site_id = :site_id AND status != 0 AND deleted_at IS NULL LIMIT :pageSize OFFSET :offset";

            // Prepare SQL statement
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            $stmt->bindParam(':pageSize', $pageSize, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

            // Execute query
            $stmt->execute();

            // Return results
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Returns the product code.
     *
     * @param int $productId The product ID.
     * @return string The product code.
     */
    public function getProductCoding($productId, $suffix)
    {
        $prefix = strtoupper(str_replace('_', '', $suffix));
        return $prefix . str_pad($productId, 11, '0', STR_PAD_LEFT);
    }

}
