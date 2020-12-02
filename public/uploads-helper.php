<?php

if ( ! empty($_FILES['receipt']['name'] ) ) {

    $errors = array();
    $file_name = $_FILES['receipt']['name'];
    $file_tmp = $_FILES['receipt']['tmp_name'];
    $file_type = $_FILES['receipt']['type'];

    $file_data = explode( '.', $file_name );
    $file_ext = end( $file_data );
    $file_ext = strtolower( $file_ext );

    $extensions = array("pdf", "jpg", "jpeg", "png");

    if ( ! empty( $file_ext ) ) {
        if (in_array($file_ext, $extensions) === false) {
            $errors[] = "extension not allowed, please choose a pdf or docx file.";
        }
    }

    $receipt_dir = 'wp-content/uploads/receipt-submission/';
    $base_dir = ! empty( $_POST[ 'base_dir' ] ) ? $_POST[ 'base_dir' ] : '';

    if ( ! is_dir( $base_dir . $receipt_dir ) ) {
        mkdir( $base_dir . $receipt_dir, 0755, true);
    }

    if( empty( $errors ) == true ) {

        move_uploaded_file( $file_tmp, $base_dir . $receipt_dir . $file_name );
        echo json_encode(
            array(
                'result'    => 'success',
                'path'    => $base_dir . $receipt_dir . $file_name,
            )
        );
    } else {
        echo json_encode(
            array(
                'result'    => 'failure',
                'errors'    =>  $errors
            )
        );
    }
    die();
}