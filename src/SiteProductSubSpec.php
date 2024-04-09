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
     * @param array $data Product sub specification data
     * @return bool True on success, False on failure
     */
    public function createProductSubSpec($data)
    {
        try {
            $sql = <<<EOF
                INSERT INTO site_product_sub_spec
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
     * @param int $subSpecId Sub specification ID
     * @return mixed|null Sub specification data if found, null otherwise
     */
    public function getProductSubSpecById($subSpecId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM site_product_sub_spec WHERE sub_spec_id = :sub_spec_id
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
     * Update an existing product sub specification.
     *
     * @param array $data Updated product sub specification data
     * @return bool True on success, False on failure
     */
    public function updateProductSubSpec($data)
    {
        try {
            $sql = <<<EOF
                UPDATE site_product_sub_spec
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
     * @param int $subSpecId Sub specification ID
     * @return bool True on success, False on failure
     */
    public function deleteProductSubSpec($subSpecId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM site_product_sub_spec WHERE sub_spec_id = :sub_spec_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':sub_spec_id', $subSpecId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
