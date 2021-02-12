<?php 

    session_start();
    require_once 'Libs/payment/vendor/autoload.php';

    class PaymentController{

        public function payonline() {
            if(!isset($_SESSION['user_id'])) {
                $dashboard = new DashboardController();
                $dashboard->index();
            }
            else {
            //    echo "success";
                \Stripe\Stripe::setApiKey('sk_test_51HyZrDHjjnJcmL75t5HSvCcjQyGdy3759Vn6zJNWasJ5EmUJjdUIps5rcys9U7VjqS51v02i90qRqSiKd66qFEOt00YdP2Gu4R');

                // Sanitize POST Array
                $POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);
            
                $first_name = $POST['first_name'];
                $email = $POST['email'];
                $room_name = $POST['room_name'];
                $room_view = $POST['room_view'];
                $room_price = $POST['room_price'];
                $payment_way = $POST['payment_way'];
                $customer_id = $POST['customer_id'];
                $reservation_id = $POST['reservation_id'];
                $token = $POST['stripeToken'];

                // echo $cardnumber;
                // echo $payment_way;
                // die();
                // echo $token;

                if($payment_way == "ONLINEHALF") {
                    $amount = $room_price*(0.5);
                }
                else {
                    $amount = $room_price;
                }
                // echo $amount;
                // echo "<br>";
                //convert to cent dollar
                //stripe only access integer only
                $amount = $amount*1000;
                // real amount should divide from 1000
                // echo $room_price;
                // echo "<br>";
                // echo $amount;
                // die();

                // echo $amount;
                // die();

                // Create Customer In Stripe
                $customer = \Stripe\Customer::create(array(
                    "email" => $email,
                    "source" => $token
                ));

                // Charge Customer
                $charge = \Stripe\Charge::create(array(
                    "amount" => $amount,
                    "currency" => "USD",
                    "description" => $room_name."Room Reservation",
                    "customer" => $customer->id
                ));

                // // Customer Data
                // $customerData = [
                //     'id'=> $charge->customer,
                //     'first_name'=> $first_name,
                //     'email'=> $email
                // ];

                // Instantiate Customer
                // $customer = new Customer();
                // $customerPay = new Payment();


                // // Add Customer to DB
                // $customerPay->addCustomer($customerData);


                // Transaction Data
                $transactionData = [
                    'reservation_id' => $reservation_id,
                    'stripe_id'=> $charge->id,
                    'customerstripe_id'=> $charge->customer,
                    'customer_id'=> $customer_id,
                    'roomdesc'=> $charge->description,
                    'amount'=> $charge->amount,
                    'currency'=> $charge->currency,
                    'status'=> $charge->status
                ];

                // Instantiate Transaction
                $transaction = new Payment();

                // Add Transaction to DB
                $result = $transaction->addTransaction($transactionData);
                if($result == 1) {
                    // Redirect to homepage
                    // $dashboard = new DashboardController();
                    // $dashboard->index();
                    view::load('dashboard/reservation/paymentThanks');
                }
                
            }
        }
    }