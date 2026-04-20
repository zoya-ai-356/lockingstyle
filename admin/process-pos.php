<?php
require_once '../common/config.php';
require_once '../common/functions.php';
checkAdminAuth();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_json'])) {
    $cart_data = json_decode($_POST['cart_json'], true);
    $final_amount = (float)$_POST['final_amount'];
    $payment_mode = sanitize($_POST['payment_mode']);
    $customer_phone = sanitize($_POST['customer_phone']);
    $order_no = 'POS-' . strtoupper(uniqid());
    $admin_user = $_SESSION['admin_name'];

    if (empty($cart_data)) {
        $_SESSION['error'] = "ERROR: EMPTY_NODE_TRANSACTION";
        redirect('pos.php');
    }

    // 1. Transaction Start
    $conn->begin_transaction();

    try {
        // 2. Insert Order
        $customer_note = "POS Transaction | Cashier: $admin_user | Cust: $customer_phone";
        $sql_order = "INSERT INTO orders (user_id, order_number, total_amount, payment_method, order_status, is_confirmed, customer_note) 
                      VALUES (0, '$order_no', '$final_amount', '$payment_mode', 'delivered', 1, '$customer_note')";
        $conn->query($sql_order);
        $order_id = $conn->insert_id;

        // 3. Process Items & Stock reversal
        foreach ($cart_data as $item) {
            $p_id = (int)$item['id'];
            $p_qty = (int)$item['qty'];
            $p_price = (float)$item['price'];
            $p_name = sanitize($item['name']);

            $conn->query("INSERT INTO order_items (order_id, product_id, product_name, quantity, price) 
                          VALUES ('$order_id', '$p_id', '$p_name', '$p_qty', '$p_price')");

            // Decrement Stock in Main Branch (ID 1)
            $conn->query("UPDATE inventory SET stock_qty = stock_qty - $p_qty WHERE product_id = '$p_id' AND branch_id = 1");
        }

        $conn->commit();
        logAudit('POS_SALE', "Order #$order_no generated for $final_amount via $payment_mode");
        $_SESSION['success'] = "Transaction Successful: #$order_no";
        redirect("pos-receipt.php?id=$order_id");

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = "CRITICAL_TRANSACTION_FAILURE: " . $e->getMessage();
        redirect('pos.php');
    }
}
?>