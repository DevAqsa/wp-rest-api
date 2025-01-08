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
    resgister_rest_route("students/v1", "student", [
        "method" => "POST",
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

    ]);

});

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