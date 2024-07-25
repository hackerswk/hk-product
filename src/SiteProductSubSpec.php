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
                (main_spec_id, name, price, member_price, supply_status, inventory, created_at, updated_at)
                VALUES
                (:main_spec_id, :name, :price, :member_price, :supply_status, :inventory, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP())
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
                SELECT * FROM $table WHERE sub_spec_id = :sub_spec_id
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
            SELECT * FROM $table WHERE main_spec_id = :main_spec_id
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
                supply_status = :supply_status, inventory = :inventory, updated_at = CURRENT_TIMESTAMP()
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
     * Delete a product sub specification by its ID.
     *
     * @param string $table The name of the SQL table
     * @param int $subSpecId Sub specification ID
     * @return bool True on success, False on failure
     */
    public function deleteProductSubSpec($table, $subSpecId)
    {
        try {
            $sql = <<<EOF
                UPDATE $table SET deleted_at = :now WHERE sub_spec_id = :sub_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':now', $now, PDO::PARAM_STR);
            $stmt->bindParam(':sub_spec_id', $subSpecId, PDO::PARAM_INT);
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
