<?php 

    class View{
        public static function load($view_name, $view_data=[]){
            $file = VIEWS.$view_name.'.php';
            // echo $file;
            // print_r($view_data);
            
            if (file_exists($file)) {
                extract($view_data);
                ob_start();
                require($file);
                ob_end_flush();
            }else{
                echo "This view : " .$view_name . "does not exist";  
            }
        }
    }

?>