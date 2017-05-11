# SSH2_PHP_CRUD

Simple class to work with CRUD operations in SSH2


#Usage
```php
require_once 'SFTPConnection.php';
try {
  	$sftp = new SFTPConnection("your_host","username", "password");	   		
  	$file_path = 'your_path/file_name.txt'; // txt, csv... 
	 	$file_content = "File content";  		
  	$sftp->create_file($file_path,$file_content);   		   		   
    
}catch (Exception $e) {
    echo $e->getMessage() . "\n";
}
```
	
  
  
