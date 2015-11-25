<?php
/*
 * put.php
 * by @reptar_xl
 * simple multiple file uploader over http
 *
 * command line usage
 *   to upload, for example, /etc/passwd and /etc/resolv.conf to a put.php running on
 *   localhost, you would do this:
 *
 *   $ curl -F 'up[]=@/etc/passwd' -F 'up[]=@/etc/resolv.conf' http://localhost/put.php
 *
 *   and if all is good, the output should be something like:
 *     [OK] 0: passwd                 ------>               passwd 1
 *     [OK] 1: resolv.conf            ------>            resolv.conf
 *
 */



/* don't put this in your public html root unless you're running it
   on localhost or something. otherwise, anyone can just upload and
   run any sort of php, etc. this uploader doesn't reject php files */
define("UPLOAD_DIR", '/www/put/uploads/');

if (! isset($_FILES['up'])){
    die("No files specified\n");
}

/* sanitizing */
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

for ($i=0; $i<count($up['name']); $i++){ // for each file being uploaded

    $name = mr_sparkle($up['name'][$i]);
    $temp = $up['tmp_name'][$i];

    /*  if we have more than one file with a name, we have to
        rename it before we save it! this loops a maximum of 
        999 times   */
    if (file_exists($name)){
        $o = 1;
        while (file_exists($name ." ". $o)){
            $o++;
            $o < 999?: die("Too many files with that name");
        }
        $name = $name ." ". $o;
    }

    move_uploaded_file($temp, $name) or die("Could not move upload to upload directory\n");
    @chmod($name, 00600) or die ("Could not chmod uploaded file\n");


    /* [OK] 1: file                 ------>             file */
    printf("[%s] %d: %-20s   ------>   %20s\n",
        (file_exists($name)? 'OK' : 'ERROR'), $i, $up['name'][$i],
        preg_replace('/\/.*\//', '', $name) );

    }


__halt_compiler ();
