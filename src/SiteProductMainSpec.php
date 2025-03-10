<?php
/**
 * Site product main spec class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductMainSpec
 * Perform CRUD operations for the site_product_main_spec table.
 */
class SiteProductMainSpec
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductMainSpec constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product main specification.
     *
     * @param string $table The name of the SQL table
     * @param array $data Product main specification data
     * @return int|bool The last insert ID on success, False on failure
     */
    public function createProductMainSpec($table, $data)
    {
        try {
            $sql = <<<EOF
            INSERT INTO $table
            (product_id, name, img_url, price, member_price, supply_status, inventory, created_at, updated_at, created_by)
            VALUES
            (:product_id, :name, :img_url, :price, :member_price, :supply_status, :inventory, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), :created_by)
EOF;

            $stmt = $this->conn->prepare($sql);
            if ($stmt->execute($data)) {
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
     * Retrieve a product main specification by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $mainSpecId Main specification ID
     * @return mixed|null Main specification data if found, null otherwise
     */
    public function getProductMainSpecById($table, $mainSpecId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE main_spec_id = :main_spec_id AND deleted_at IS NULL
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':main_spec_id', $mainSpecId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieve product main specifications by product ID.
     *
     * @param string $table The name of the SQL table
     * @param int $productId Product ID
     * @return array Product main specifications
     */
    public function getProductMainSpecs($table, $productId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE product_id = :product_id AND deleted_at IS NULL
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
     * Update an existing product main specification.
     *
     * @param string $table The name of the SQL table
     * @param array $data Updated product main specification data
     * @return bool True on success, False on failure
     */
    public function updateProductMainSpec($table, $data)
    {
        try {
            $sql = <<<EOF
                UPDATE $table
                SET product_id = :product_id, name = :name, img_url = :img_url, price = :price, member_price = :member_price,
                supply_status = :supply_status, inventory = :inventory, updated_at = CURRENT_TIMESTAMP(), updated_by = :updated_by
                WHERE main_spec_id = :main_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update an existing product main specification inventory.
     *
     * @param string $table The name of the SQL table
     * @param int $mainSpecId Main specification ID
     * @param int $inventory New inventory data
     * @param int $updator Someone who updates the inventory of a product main specification. Default = 0 (system)
     * @return bool True on success, False on failure
     */
    public function updateProductMainSpecInventory(string $table, int $mainSpecId, int $inventory, int $updator): bool
    {
        try {
            $sql = <<<SQL
                UPDATE $table SET inventory = :inventory, update_by = :update_by
                WHERE main_spec_id = :main_spec_id
SQL;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':main_spec_id' => $mainSpecId,
                ':inventory' => $inventory,
                ':update_by' => $updator,
            ]);
        } catch (PDOException $e) {
            echo "Update MainSpec Inventroy Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product main specification by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $mainSpecId Main specification ID
     * @return bool True on success, False on failure
     */
    public function deleteProductMainSpec($table, $mainSpecId, $updated_by)
    {
        try {
            $sql = <<<EOF
                UPDATE $table SET deleted_at = NOW(), updated_by = :updated_by WHERE main_spec_id = :main_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':main_spec_id', $mainSpecId, PDO::PARAM_INT);
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

}
