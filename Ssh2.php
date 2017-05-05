<?php

public class SFTPConnection
{
    private $connection;
    private $sftp;

    public function __construct($_host,$_user,$_password, $_port=22){
        $this->connection = @ssh2_connect($_host, $_port);
        if (! $this->connection)
            throw new Exception("Could not connect to $_host on port $_port.");
        $this->login($_user,$_password);
    }

    public function login($_username, $_password){
        if (! @ssh2_auth_password($this->connection, $_username, $_password))
            throw new Exception("Could not authenticate with username $_username " .
                "and password $_password.");

        $this->sftp = @ssh2_sftp($this->connection);
        if (! $this->sftp)
            throw new Exception("Could not initialize SFTP subsystem.");
    }

    public function create_file($_file_path, $_file_content){
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://".intval($sftp)."$_file_path", 'w');

        if (!$stream)
            throw new Exception("Could not open file: $_remote_file");

        if (@fwrite($stream, $_file_content) === false)
            throw new Exception("Could not write data in $_file_path");

        @fclose($stream);
    }

    public function upload_file($_local_file, $_remote_file){
        $sftp = $this->sftp;
        $stream = @fopen("ssh2.sftp://".intval($sftp)."$_remote_file", 'w');

        if (! $stream)
            throw new Exception("Could not open file: $_remote_file");

        $data_to_send = @file_get_contents($_local_file);
        if ($data_to_send === false)
            throw new Exception("Could not open local file: $_local_file.");

        if (@fwrite($stream, $data_to_send) === false)
            throw new Exception("Could not send data from file: $_local_file.");

        @fclose($stream);
    }

    public function scan_file_system($_remote_file){

        $sftp = $this->sftp;
        $dir = "ssh2.sftp://".intval($sftp)."$_remote_file";

        $tempArray = array();
        $handle = opendir($dir);

        while (false !== ($file = readdir($handle))){
            if (substr($file, 0, 1) != "."){
                if(substr($file, -4, 1) != '.'){
                    $tempArray[$file] = $this->scan_file_system("$_remote_file/$file");
                }else{
                    $tempArray[]=$file;
                }
            }
        }
        closedir($handle);
        return $tempArray;
    }

    public function read_file($_remote_file){
        $sftp = $this->sftp;
        $files = scandir('ssh2.sftp://' .intval($sftp). $_remote_file);
        if (!empty($files)) {
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    if(substr($file, -4, 1) != '.'){
                        $this->read_file("$_remote_file/$file");
                    }else{
                        $stream = @fopen("ssh2.sftp://".intval($sftp)."$_remote_file/$file", 'r');
                        while ($buf = fgets($stream) ){
                            $data .= $buf;
                            echo $buf."<br/>";
                        }
                        @fclose($stream);
                    }

                }
            }
        }
    }


}