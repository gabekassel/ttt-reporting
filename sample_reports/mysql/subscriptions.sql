-- Active/Trial/Unpaid Subscriptions
-- This report lets you view all orders within a specified time period.
-- You can click on a customer name to drill down into all orders for that customer.
--VARIABLE: { 
--  name: "status", 
--  display: "Status", 
--  type: "select",
--  multiple: true,
--  options: ["active","trialing","unpaid"] 
--}
--VARIABLE: { 
--  name: "plan", 
--  display: "Plan", 
--  type: "select",
--  options: ["Kyle Mentorship 150","Kyle Coaching 275"] 
--}
--ROLLUP: {
--  columns: {
--    "Plan 1": "Total",
--    "Plan 1 Amount": "{{sum}}"
--  }
--}

(SELECT
    ID as `Customer ID`,
    email as `Email`,
    IFNULL(plan1,NULL) as `Plan 1`,
    IFNULL(FORMAT((amount1/100),2),0) as `Plan 1 Amount`,
    IFNULL(statusplan1,0) as `Status1`,
    IFNULL(plan2,NULL) as `Plan2`,
    IFNULL(FORMAT((amount2/100),2),0) as `Plan 2 Amount`,
    IFNULL(statusplan2,0) as `Status2`
FROM
    customers
WHERE
    statusplan1 = "{{ status.0 }}"
OR
    statusplan2 = "{{ status.0 }}"
OR
    statusplan1 = "{{ status.1 }}"
OR
    statusplan2 = "{{ status.1 }}"
OR
    statusplan1 = "{{ status.2 }}"
OR
    statusplan2 = "{{ status.2 }}")
UNION
(SELECT
    ID as `Customer ID`,
    email as `Email`,
    IFNULL(plan1,NULL) as `Plan 1`,
    IFNULL(FORMAT((amount1/100),2),0) as `Plan 1 Amount`,
    IFNULL(statusplan1,0) as `Status1`,
    IFNULL(plan2,NULL) as `Plan2`,
    IFNULL(FORMAT((amount2/100),2),0) as `Plan 2 Amount`,
    IFNULL(statusplan2,0) as `Status2`
FROM
    customers
WHERE
    plan1  = "{{ plan.0 }}"
OR
    plan2 = "{{ plan.1 }}")




