-- All Orders
-- This report lets you view all orders within a specified time period.
-- You can click on a customer name to drill down into all orders for that customer.
-- VARIABLE: { 
--      name: "range", 
--      display: "Report Range",
--      type: "daterange", 
--      default: { start: "this month", end: "now" }
-- }
-- VARIABLE: { 
--      name: "plan", 
--      display: "Plan Name Contains",
--      type: "string", 
--      default: { name: "max" }
-- }

SELECT
    ID as `Customer ID`,
    email as 'Email',
    plan1 as `Plan 1`,
    amount1 as 'Plan 1 Amount',
    statusplan1 as 'Status1',
    plan2 as 'Plan2',
    amount2 as 'Plan 2 Amount',
    statusplan2 as 'Status2'
FROM
    customers
WHERE
    created_at BETWEEN "{{ range.start }}" AND "{{ range.end }}"
AND
    'plan1' LIKE "{{ plan.name }}"
OR
    'plan2' LIKE "{{ plan.name }}"
