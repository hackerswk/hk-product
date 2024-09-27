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
            only_member, status, created_at, updated_at, created_by)
            VALUES
            (:site_id, :platform_category_id, :name, :description, :type, :price, :member_price,
            :supply_status, :inventory, :release_at, :offshelf_at, :scheduled_release_time, :scheduled_offshelf_time, :auto_offshelf_soldout,
            :only_member, :status, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), :created_by)
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
     * Retrieves products from the specified table with pagination, filtering by name, status, category_id, and inventory status.
     *
     * Dynamically generates the product main spec and category tables based on the suffix of the given $table.
     *
     * @param string $table The name of the database table to query (must follow format: site_products_<letter>).
     * @param int $site_id The ID of the site to filter products by.
     * @param int $page The page number for pagination.
     * @param int $pageSize The number of records per page.
     * @param string|null $name The optional name filter for products (fuzzy search).
     * @param int|null $status The status filter for products (default is 1).
     * @param int|null $category_id The category filter (optional).
     * @param string $inventory_status The inventory status filter (default is 'normal'). Options: 'normal', 'partial', 'none'.
     *
     * @return array An array of products matching the criteria.
     */
    public function getProducts($table, $site_id, $page, $pageSize, $name = null, $status = 1, $category_id = null, $inventory_status = 'normal')
    {
        try {
            // Calculate OFFSET value
            $offset = ($page - 1) * $pageSize;

            // Extract the suffix from the $table (last character after "_")
            $suffix = substr($table, strrpos($table, '_') + 1);

            // Construct the dynamic table names for product main spec and category
            $productMainSpecTable = "site_product_main_spec_" . $suffix;
            $productCategoryTable = "site_product_category_" . $suffix;

            // Start building SQL query
            $sql = "SELECT * FROM $table WHERE site_id = :site_id AND deleted_at IS NULL";

            // Add conditions for name and status if provided
            if ($name !== null) {
                $sql .= " AND name LIKE :name";
            }
            if ($status !== null) {
                $sql .= " AND status = :status";
            }

            // Add condition for category if provided
            if ($category_id !== null) {
                // Filter by product_id in the subquery for category filtering
                $sql .= " AND product_id IN (SELECT product_id FROM $productCategoryTable WHERE category_id = :category_id)";
            }

            // Add condition for inventory status
            switch ($inventory_status) {
                case 'partial':
                    // Products with some specifications that have zero stock
                    $sql .= " AND EXISTS (
                    SELECT 1 FROM $productMainSpecTable
                    WHERE $productMainSpecTable.product_id = $table.product_id
                    AND $productMainSpecTable.inventory = 0
                ) AND $table.inventory > 0"; // At least one product specification is out of stock
                    break;
                case 'none':
                    // Products with zero or negative inventory
                    $sql .= " AND $table.inventory <= 0";
                    break;
                case 'normal':
                default:
                    // Products with positive inventory
                    $sql .= " AND $table.inventory > 0";
                    break;
            }

            // Add ORDER BY and pagination
            $sql .= " ORDER BY product_id DESC LIMIT :pageSize OFFSET :offset";

            // Prepare SQL statement
            $stmt = $this->conn->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            if ($name !== null) {
                $likeName = "%$name%";
                $stmt->bindParam(':name', $likeName, PDO::PARAM_STR);
            }
            if ($status !== null) {
                $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            }
            if ($category_id !== null) {
                $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            }
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
        only_member = :only_member, status = :status, updated_at = CURRENT_TIMESTAMP(), updated_by = :updated_by
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
    public function deleteProduct($table, $productId, $updated_by)
    {
        try {
            $sql = <<<EOF
            UPDATE $table SET deleted_at = NOW(), updated_by = :updated_by WHERE product_id = :product_id
EOF;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':updated_by', $updated_by, PDO::PARAM_INT);
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
