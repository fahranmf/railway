-- Check Transaction 3 Items with Batches
SELECT 
    ti.id,
    ti.transaction_id,
    ti.product_id,
    ti.product_batch_id,
    ti.quantity,
    ti.price,
    pb.batch_number,
    pb.expired_date,
    p.name as product_name
FROM transaction_items ti
LEFT JOIN product_batches pb ON ti.product_batch_id = pb.id
LEFT JOIN products p ON ti.product_id = p.id
WHERE ti.transaction_id = 3;
