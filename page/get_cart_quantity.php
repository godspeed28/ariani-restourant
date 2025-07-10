<?php
session_start();

$total_items = 0;

if (isset($_SESSION['keranjang']) && is_array($_SESSION['keranjang'])) {
    foreach ($_SESSION['keranjang'] as $item) {
        if (is_array($item)) {
            $total_items += $item['kuantitas'];
        }
    }
}

echo $total_items;
?>
