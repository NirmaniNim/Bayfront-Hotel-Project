<?php 
session_start();



class RoomController {


    public function index() {
        date_default_timezone_set("Asia/Colombo");
        $current_date = date('Y-m-d');
        
        if(!isset($_SESSION['user_id'])) {
            view::load('dashboard/dashboard');    
        }
        else {
            $db = new RoomDetails();
            $typename = $db->getRoomTypes();
            $data['typename'] = $typename;
            
            
            view::load('dashboard/room/index', $data);

        }
           
    }


    public function view() {
        if(!isset($_SESSION['user_id'])) {
            view::load('dashboard/dashboard');    
        }
        else {
                $data = array();
                $db = new RoomDetails;
                if(isset($_POST['search'])) {
                    $search = $_POST['search'];
                                      
                        $data['rooms'] = $db->getSearchRoomAll($search);
                        view::load('dashboard/room/show', $data);
                }
                else {
                    $data['rooms'] = $db->getAllRoomAll();
                    view::load('dashboard/room/show', $data);
                }
        }
    }

    public function details($room_number) {
        if(!isset($_SESSION['user_id'])) {
            view::load('dashboard/dashboard');    
        }
        else {
            // Get Given Room number details
            $db = new RoomDetails;
            $room = $db->getRoom($room_number);
            
            // Get Given Room number Room type
            $room_type_id = $room['type_id']; 


            // Add discount
            date_default_timezone_set("Asia/Colombo");
            $current_date = date("Y-m-d"); 

            $discount = $db->getRoomDiscount($room_type_id);

            $start_date = $discount['start_date'];
            $end_date = $discount['end_date'];
            
            if($start_date < $current_date && $end_date > $current_date) {
                $discount_rate = $discount['discount_rate'];
               
            }
            else {
                $discount_rate = 0;
                
            }


           
            $room_type = $db->getRoomType($room_type_id);
            
            $room_id = $room['room_id'];
            $reservations = $db->getReservations($room_id);

            $data['discount'] = array("discount"=>$discount_rate);
            $data['room'] = $room;
            $data['room_type'] = $room_type;
            $data['reservations'] = $reservations;

            view::load("dashboard/room/view", $data);
         
            
        }
    }

    public function check() {

        if(isset($_POST['submit'])) {

            $type_name = $_POST['type_name']; 
           
            $check_in_date = $_POST['check_in_date'];
            $check_out_date = $_POST['check_out_date'];

            
            $db = new RoomDetails();
            $type_id = $db->getTypeID($type_name);
            $room_type_id = $type_id['room_type_id'];
            
            $rooms = $db->getRoomAllID($room_type_id);
            
            $update = $db->getRoomsUpdate();
            
            if($update == 1) {
                foreach($rooms as $room) {
                    
                    $result = $db->roomAvalability($room['room_id'],$check_in_date,$check_out_date);
                    
                    if($result == 1) {
                        
                        $result = $db->roomTodayBookedUpdate($room['room_id']);
                        

                    }
    
                }
            }
            

            $rooms = $db->getAvailableRooms($room_type_id, $check_in_date, $check_out_date);
            
            $typename = $db->getRoomTypes();
            $data['typename'] = $typename;

            if(empty($rooms)) {
                $data['details'] = array('type_name' =>$type_name, 'check_in_date '=>$check_in_date, 'check_out_date'=>$check_out_date );
                view::load("dashboard/room/index", ["errors"=>"Data Update Unsuccessfully", 'details'=>$data['details'], 'typename'=>$data['typename']]);
            }
            else {
                
                $data['rooms'] = $rooms;
                view::load("dashboard/room/result", $data);
                // echo "2";
            }
            

            

        }

    }

    

    
}

