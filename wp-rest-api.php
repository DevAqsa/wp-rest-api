<?php
/*
 * Plugin Name: WP REST API
 * Description: This is custom plugin for REST APIS related to student table
 * Version:1.0
 * Author: Aqsa Mumtaz
 *
 */

if(!defined("WPINC")){
    exit;
}


//Activation Hook

register_activation_hook(__FILE__, "wpcp_create_student_table" );

function wpcp_create_student_table(){
    global $wpdb;

    $tablePrefix = $wpdb->prefix; //wp_
    $tableName = $tablePrefix . "students";


    $sqlQuery = "
CREATE TABLE `".$tableName."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(80) NOT NULL,
  `phone_no` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
";


    include_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sqlQuery);
}

//Deactivation Hook

register_deactivation_hook(__FILE__, "wpcp_drop_students_table" );

function wpcp_drop_students_table(){
    global $wpdb;
    $tableName = $wpdb->prefix . "students";

    $wpdb->query("DROP TABLE IF EXISTS {$tableName}");
}

//APIS
add_action("rest_api_init", function(){

    //get all students
    register_rest_route("students/v1", "student" , [
        "methods" => "GET",
        "callback" => "wp_handle_student_list",

    ]);

    //Create student API - POST(name, email, phone_no)
    register_rest_route("students/v1", "student", [
        "methods" => "POST",
        "callback" => "wp_handle_create_student",
        "args" => [
            "name" => [
                "required" => true,
                "type" => "string",
            ],
            "email" => [
                "required" => true,
                "type" => "string",
            ],
            "phone_no" => [
                "required" => false,
                "type" => "string",
            ],
        ]    

    ]);

    //update student API - PUT (name, email, phone_no) -> id - url
    register_rest_route("students/v1", "student/(?P<id>\d+)", [
        "methods" => "PUT",
        "callback" => "wp_handle_update_student",
        "args" => [
            "name",
            "email",
            "phone_no",
        ]
    ]);
      
    //Delete student API - DELETE-> ID-> url -> D
    register_rest_route("students/v1", "student/(?P<id>\d+)", [
        "methods" => "DELETE",
        "callback" => "wp_handle_student_delete",

    ]);


});

//list data
function wp_handle_student_list(){
    global $wpdb;

    $tableName = $wpdb->prefix . "students";
    $students = $wpdb->get_results("SELECT * FROM {$tableName}", ARRAY_A);

    return rest_ensure_response([
        "status" => true,
        "message" => "List Students API",
        "data" => $students
    ]);
};

//create data
function wp_handle_create_student($request){

    global $wpdb;
    $tableName = $wpdb->prefix . "students";
   

    $name = $request->get_param("name");
    $email = $request->get_param("email");
    $phone_no = $request->get_param("phone_no");

    $wpdb->insert($tableName, [
        "name" => $name,
        "email" => $email,
        "phone_no" => $phone_no
    ]);

    return rest_ensure_response([
        "status" => true,
        "message" => "Student created successfully",

    ]);

}

//update student 
function wp_handle_update_student($request){
   
    global $wpdb;
    $tableName = $wpdb->prefix . "students";

    $student_id = $request->get_param("id");

    if(!empty($student_id)){

        $studentData = $wpdb->get_row("SELECT * FROM {$tableName} WHERE id = {$student_id}", ARRAY_A);
        if(!empty($studentData)){
            //student exists
            $wpdb->update($tableName, [
                "name" => $request->get_param("name") ?? $studentData['name'],
                "email" => $request->get_param("email") ?? $studentData['email'],
                "phone_no" => $request->get_param("phone_no") ?? $studentData['phone_no'],
            
            ],
                ["id" => $student_id],
            );

            return rest_ensure_response([
                "status" => true,
                "message" => "Student updated successfully",
            ]);

        }else {
            return rest_ensure_response([
                "status" => false,
                "message" => "Failde to get student data",
        
            ]);
        }


    }else {
           return rest_ensure_response([
            "status" => false,
            "message" => "Student ID is required",
          ]);
        }
     

};

//Delete student
function wp_handle_student_delete($request){

    global $wpdb;
    $tableName = $wpdb->prefix . "students";

    $student_id = $request->get_param("id");

    $studentData = $wpdb->get_row("SELECT * FROM {$tableName} WHERE id = {$student_id}", ARRAY_A);

    if(!empty($studentData)){
        $wpdb->delete($tableName, ["id" => $student_id]);
        return rest_ensure_response([
            "status" => true,
            "message" => "Student deleted successfully",
        ]);
    }else{
        return rest_ensure_response([
            "status" => false,
            "message" => "Student not found",
        ]);
    }

};

