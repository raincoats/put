<?php
/*
 * real simple uploader
 * 24 nov 2015 at 7:34 pm
 */

define("UPLOAD_DIR", '/www/put/uploads/');


if (! isset($_FILES['up'])){
    die('{}');
}

function mr_sparkle($in)
{
    if (preg_match('/(r57\.php|c99\.php)/', $in)){
        echo 'Are you joking? Fuck off'; die();
    }
    $in = preg_replace('/( %2F | %2E | \.\. | \/ )/xi', '-', $in);         //dir traversal
    $in = preg_replace('/[^a-zA-Z0-9\.,?%@\(\)_:+=\{\}&]/ms', '-', $in);
    $in = preg_replace('/%[0-9A-F][0-9A-F]/i', '', $in);
    $in = preg_replace('/[\s-]+/', '-', $in);                              //squash dash, dots, whitespace
    $in = preg_replace('/\.+/', '.', $in);
    $in = preg_replace('/(.{0,250}).*/', "$1", $in);                       //max 250 chars
    $in = preg_replace('/( ^[^a-zA-Z0-9] | [^a-zA-Z0-9]$ )/x', '', $in);   //stricter on beginning and end

    if (strlen($in) < 1) {  // effectively empty
            $in = 'untitled';
    }
    return (UPLOAD_DIR.$in);
}

$up = $_FILES['up'];

for ($i=0; $i<count($up['name']); $i++){

    $name = mr_sparkle($up['name'][$i]);
    $temp = $up['tmp_name'][$i];

    if (file_exists($name)){
        $o = 1;
        while (file_exists($name ." ". $o)){
            $o++;
            $o < 999?: die("Too many files with that name");
        }
        $name = $name ." ". $o;
    }

    move_uploaded_file($temp, $name);
    @chmod($name, 00600);



    printf("[%s] %d: %-20s   ------>   %20s\n",
        (file_exists($name)? 'OK' : 'ERROR'), $i, $up['name'][$i],
        preg_replace('/\/.*\//', '', $name) );

    }


__halt_compiler ();
