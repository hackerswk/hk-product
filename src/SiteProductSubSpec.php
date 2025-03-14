<?php
/**
 * Site product sub spec class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\HkProduct;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class SiteProductSubSpec
 * Perform CRUD operations for the site_product_sub_spec table.
 */
class SiteProductSubSpec
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * SiteProductSubSpec constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Create a new product sub specification.
     *
     * @param string $table The name of the SQL table
     * @param array $data Product sub specification data
     * @return bool True on success, False on failure
     */
    public function createProductSubSpec($table, $data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO $table
                (main_spec_id, name, price, member_price, supply_status, inventory, created_at, updated_at, created_by)
                VALUES
                (:main_spec_id, :name, :price, :member_price, :supply_status, :inventory, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), :created_by)
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve a product sub specification by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $subSpecId Sub specification ID
     * @return mixed|null Sub specification data if found, null otherwise
     */
    public function getProductSubSpecById($table, $subSpecId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM $table WHERE sub_spec_id = :sub_spec_id AND deleted_at IS NULL
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sub_spec_id', $subSpecId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Retrieve product sub specifications by main specification ID.
     *
     * @param string $table The name of the SQL table
     * @param int $mainSpecId Main specification ID
     * @return array Product sub specifications
     */
    public function getProductSubSpecsByMainSpec($table, $mainSpecId)
    {
        try {
            $sql = <<<EOF
            SELECT * FROM $table WHERE main_spec_id = :main_spec_id AND deleted_at IS NULL
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':main_spec_id', $mainSpecId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Update an existing product sub specification.
     *
     * @param string $table The name of the SQL table
     * @param array $data Updated product sub specification data
     * @return bool True on success, False on failure
     */
    public function updateProductSubSpec($table, $data)
    {
        try {
            $sql = <<<EOF
                UPDATE $table
                SET main_spec_id = :main_spec_id, name = :name, price = :price, member_price = :member_price,
                supply_status = :supply_status, inventory = :inventory, updated_at = CURRENT_TIMESTAMP(), updated_by = :updated_by
                WHERE sub_spec_id = :sub_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update an existing product sub specification inventory.
     *
     * @param string $table The name of the SQL table
     * @param int $subSpecId Sub specification ID
     * @param int $inventory New inventory data
     * @param int $updator Someone who updates the inventory of a product sub specification. Default = 0 (system)
     * @return bool True on success, False on failure
     */
    public function updateProductSubSpecInventory(string $table, int $subSpecId, int $inventory, int $updator = 0): bool
    {
        try {
            $sql = <<<SQL
                UPDATE $table SET inventory = :inventory, updated_by = :updated_by
                WHERE sub_spec_id = :sub_spec_id
SQL;

            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':sub_spec_id' => $subSpecId,
                ':inventory' => $inventory,
                ':updated_by' => $updator,
            ]);
        } catch (PDOException $e) {
            echo "Update SubSpec Inventroy Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Delete a product sub specification by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $subSpecId Sub specification ID
     * @return bool True on success, False on failure
     */
    public function deleteProductSubSpec($table, $subSpecId, $updated_by)
    {
        try {
            $sql = <<<EOF
                UPDATE $table SET deleted_at = NOW(), updated_by = :updated_by WHERE sub_spec_id = :sub_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sub_spec_id', $subSpecId, PDO::PARAM_INT);
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
