# somToday-PHP-API
Easy to use somToday API written in PHP (works like somtoday mobile application)

License: [GPL](http://choosealicense.com/licenses/gpl-2.0/).

# Installation

1.Download and extract into a folder.

2.Create index.php

3.Write into index.php:
```php
include("somAPI.php");
$somtoday=new somtodayapi("username","password","schoolname","brincode");
```
4.You are ready to go!

# Examples

- Get grades
```php
$resp = $somtoday->getGrades();
echo $resp["raw"] //returns raw respond

foreach($resp["json"]["data"] as $key=>$val){ //returns every grade
  echo $val["vak"]." - ".$val["resultaat"]."<br>";
}
```

- Get homework
```php
$resp = $somtoday->getHomework(4); //gets homework for next 4 days
echo $resp["raw"] //returns raw respond

foreach($resp["json"]["data"] as $key=>$val){ //returns every homework
  echo $val["vak"]." - ".$val["huiswerk"]."<br><br>";
}
```
