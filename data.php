<?php
set_time_limit(300);
$servername = "localhost";
$username = "ttt_stripe";
$password = "b828712582d";
$dbname = "ttt_stripe";
$conn = mysql_connect($servername, $username, $password);
mysql_select_db($dbname) or die(mysql_error());
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

ini_set('display_errors', 'On');
error_reporting(E_ALL);
require './stripe/lib/Stripe.php';
Stripe::setApiKey("sk_live_YypZ0rO8jEwtY13xhk88iz4Q");
$start = microtime(true);


//Customers
$starting_after = 0;
$has_more = true;
$n = -1;
while($has_more == true){
    $n = $n + 1;
    if($starting_after > 0){
        ${'customer_data'.$n} = Stripe_Customer::all(array('count' => '100', 'offset' => $starting_after));
    }else{
      ${'customer_data'.$n} = Stripe_Customer::all(array('count' => '100'));
    }
    $has_more = ${'customer_data'.$n}['has_more'];
    $starting_after = $starting_after + 100;
}
  
for ($i=0; $i<=$n; $i++) {
    $customer_data = ${'customer_data'.$i};
    // store the plan ID as the array key and the plan name as the value
    foreach($customer_data['data'] as $customer) {
        // store the plan ID as the array key and the plan name as the value
        $customerid = $customer['id'];

        if(isset($customer['email'])){
            $email = $customer['email'];
        }
        
        $sql = "SELECT COUNT(*) FROM customers WHERE ID='$customerid'";
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        $count = $row['COUNT(*)'];
        if($count<1){
            $sql = "INSERT INTO customers (ID, email) VALUES ('$customerid', '$email')";
            mysql_query($sql) or die(mysql_error());
        }
        
        
        if(isset($customer['subscriptions']['data'][0])){
            $plan1 = $customer['subscriptions']['data'][0]['plan']['name'];
            $amountplan1 = $customer['subscriptions']['data'][0]['plan']['amount'];
            $statusplan1 = $customer['subscriptions']['data'][0]['status'];
            $sql = "UPDATE customers SET plan1 = '$plan1', amount1 = '$amountplan1', statusplan1 = '$statusplan1' WHERE ID='$customerid'";
            mysql_query($sql) or die(mysql_error());
        }
        
        if(isset($customer['subscriptions']['data'][1])){
            $plan2 = $customer['subscriptions']['data'][1]['plan']['name'];
            $amountplan2 = $customer['subscriptions']['data'][1]['plan']['amount'];
            $statusplan2 = $customer['subscriptions']['data'][1]['status'];
            $sql = "UPDATE customers SET plan2 = '$plan2', amount2 = '$amountplan2', statusplan2 = '$statusplan2' WHERE ID='$customerid'";
            mysql_query($sql) or die(mysql_error());
        }
        
    }   
}
sleep(2);
//Invoices
$starting_after = 0;
$has_more = true;
$n = -1;
while($has_more == true){
    $n = $n + 1;
    if($starting_after > 0){
        ${'invoice_data'.$n} = Stripe_Invoice::all(array('count' => '100', 'offset' => $starting_after));
    }else{
      ${'invoice_data'.$n} = Stripe_Invoice::all(array('count' => '100'));
    }
    $has_more = ${'invoice_data'.$n}['has_more'];
    $starting_after = $starting_after + 100;
}
  
for ($i=0; $i<=$n; $i++) {
    $invoice_data = ${'invoice_data'.$i};
    // store the plan ID as the array key and the plan name as the value
    foreach($invoice_data['data'] as $invoice) {
        // store the plan ID as the array key and the plan name as the value
        $invoiceid = $invoice['id'];
        $customerid = $invoice['customer'];
        
        $sql = "SELECT COUNT(*) FROM invoices WHERE ID='$invoiceid'";
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        $count = $row['COUNT(*)'];
        if($count<1){
            $sql = "INSERT INTO invoices (ID, customer) VALUES ('$invoiceid', '$customerid')";
            mysql_query($sql) or die(mysql_error());
        }
        
        
        $status = $invoice['paid'];
        $total = $invoice['total'];
        $periodstart = $invoice['date'];
        $periodend = $invoice['date'] + 2592000;
        if(isset($invoice['charge'])){
            $charge = $invoice['charge'];
        }
        $sql = "UPDATE invoices SET paid = '$status', total = '$total', periodstart = '$periodstart', periodend = '$periodend', charge = '$charge' WHERE ID='$invoiceid'";
        mysql_query($sql) or die(mysql_error());
        
    }   
}

sleep(2);
//Charges
$starting_after = 0;
$has_more = true;
$n = -1;
while($has_more == true){
    $n = $n + 1;
    if($starting_after > 0){
        ${'charge_data'.$n} = Stripe_Charge::all(array('count' => '100', 'offset' => $starting_after));
    }else{
      ${'charge_data'.$n} = Stripe_Charge::all(array('count' => '100'));
    }
    $has_more = ${'charge_data'.$n}['has_more'];
    $starting_after = $starting_after + 100;
}
  
for ($i=0; $i<=$n; $i++) {
    $charge_data = ${'charge_data'.$i};
    // store the plan ID as the array key and the plan name as the value
    foreach($charge_data['data'] as $charge) {
        // store the plan ID as the array key and the plan name as the value
        $chargeid = $charge['id'];
        $created = $charge['created'];
        
        
        $sql = "SELECT COUNT(*) FROM charges WHERE ID='$chargeid'";
        $result = mysql_query($sql);
        $row = mysql_fetch_assoc($result);
        $count = $row['COUNT(*)'];
        if($count<1){
            $sql = "INSERT INTO charges (ID, created) VALUES ('$chargeid', '$created')";
            mysql_query($sql) or die(mysql_error());
        }
        
        
        $paid = $charge['paid'];
        $refunded = $charge['refunded'];
        $amount = $charge['amount'];
        $refundamount = $charge['amount_refunded'];
        $remainder = $amount - $refundamount;
        if(isset($charge['description']) && strpos($charge['description'],'Store') !== false){
            $description = mysql_real_escape_string($charge['description']);
            $wooidguess = filter_var($description, FILTER_SANITIZE_NUMBER_INT);
            $wooidguess = str_replace('-', '', $wooidguess);
            $wooidguess = mysql_real_escape_string($wooidguess);
        }else if(isset($charge['description'])){
            $description = mysql_real_escape_string($charge['description']);
            $wooidguess = '';
        }else{
            $description = '';
            $wooidguess = '';
        }
        
        if(isset($charge['customer'])){
        $customerid = $charge['customer'];
        }
        if(isset($charge['invoice'])){
        $invoiceid = $charge['invoice'];
        }
        
        if(isset($charge['card']['name'])){
            $name = mysql_real_escape_string($charge['card']['name']);
        }
        
        
        $sql = "UPDATE charges SET paid = '$paid', refunded = '$refunded', amount = '$amount', refundamount = '$refundamount', remainder = '$remainder', customer = '$customerid', invoice = '$invoiceid', description = '$description', name = '$name', wooorderidguess = '$wooidguess' WHERE ID='$chargeid'";
        mysql_query($sql) or die(mysql_error());
        
    }   
}

$time_elapsed_us = microtime(true) - $start;
echo $time_elapsed_us;
$message = "TTT data script took $time_elapsed_us seconds to complete";
mail('kassel01@gmail.com','TTT Reporting Data Update',$message);


?>