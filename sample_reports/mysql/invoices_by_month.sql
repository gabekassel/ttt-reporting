-- Invoices By Month/Coach
-- This report lets you view all orders within a specified time period.
-- You can click on a customer name to drill down into all orders for that customer.
-- VARIABLE: { 
--      name: "range", 
--      display: "Range:",
--      type: "daterange", 
--      default: { start: "yesterday", end: "yesterday" }
-- }
--ROLLUP: {
--  columns: {
--    "Customer ID": "Total",
--    "Invoice Amount": "{{sum}}"
--  }
--}

SELECT
    customers.email as `Email`,
    invoices.ID as `Invoice ID`,
    IFNULL(invoices.customer,NULL) as `Customer ID`,
    IFNULL(FORMAT((invoices.total/100),2),0) as `Invoice Amount`,
    IFNULL(FROM_UNIXTIME(invoices.periodstart),0) as `Period Start`,
    IFNULL(FROM_UNIXTIME(invoices.periodend),0) as `Period End`,
    COALESCE(NULLIF(invoices.paid,''), 'NOT PAID') as `Paid Status`,
    IFNULL(customers.plan1,NULL) as `Plan 1`,
    IFNULL(customers.plan2,NULL) as `Plan 2`
    
FROM
    invoices
LEFT JOIN
    customers
ON
    invoices.customer=customers.ID
WHERE
    periodstart >= UNIX_TIMESTAMP("{{ range.start }}")
AND
    periodend < (UNIX_TIMESTAMP("{{ range.end }}") + 1296000)




