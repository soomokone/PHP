<?php

require_once "./PHPExcel_1.8.0/Classes/PHPExcel.php"; // PHPExcel.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.
$objPHPExcel = new PHPExcel();
require_once "./PHPExcel_1.8.0/Classes/PHPExcel/IOFactory.php"; // IOFactory.php을 불러와야 하며, 경로는 사용자의 설정에 맞게 수정해야 한다.
$filename = './pos_data.xlsx'; // 읽어들일 엑셀 파일의 경로와 파일명을 지정한다.
try {  // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.    
    $objReader = PHPExcel_IOFactory::createReaderForFile($filename);    // 읽기전용으로 설정    
    $objReader->setReadDataOnly(true);    // 엑셀파일을 읽는다    
    $objExcel = $objReader->load($filename);    // 첫번째 시트를 선택    
    $objExcel->setActiveSheetIndex(0);    
    $objWorksheet = $objExcel->getActiveSheet();    
    $rowIterator = $objWorksheet->getRowIterator();    
    foreach ($rowIterator as $row) { // 모든 행에 대해서               
        $cellIterator = $row->getCellIterator();               
        $cellIterator->setIterateOnlyExistingCells(false);     
    }    
    $maxRow = $objWorksheet->getHighestRow();    
    for ($i = 0 ; $i <= $maxRow ; $i++) {               
        $name = $objWorksheet->getCell('A'.$i)->getValue(); // A열               
        $addr1 = $objWorksheet->getCell('B'.$i)->getValue(); // B열               
        $addr2 = $objWorksheet->getCell('C'.$i)->getValue(); // C열                                
    }
}  
catch (exception $e) {    
    echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';
}

$con = mysqli_connect('localhost', 'root', '1234', 'mydb');

exclude(login.php);

$MeterByDegree = Array('lat'=> 111000, 'lon' => 88800 );

function getMeter( $gpsNow, $gpsOther, $show=0 ) {
global $MeterByDegree;

$latDist = abs($gpsNow[1] - $gpsOther[1]); // 위도
$lonDist = abs($gpsNow[2] - $gpsOther[2]); // 경도
echo '위도 = ' . number_format($latDist, 10) . "<br>\n";
echo '경도 = ' . number_format($lonDist, 10) . "<br>\n";
$latMeter = $latDist * $MeterByDegree['lat']; // 위도미터
$lonMeter = $lonDist * $MeterByDegree['lon']; // 경도미터
echo '위도미터 = ' . number_format($latMeter, 10) . "<br>\n";
echo '경도미터 = ' . number_format($lonMeter, 10) . "<br>\n";
// 루트 ( 제곱 + 제곱 )
$DistMeter = sqrt( $latMeter*$latMeter +  $lonMeter*$lonMeter );

if($show=='show') showMeter( $gpsNow, $gpsOther, $DistMeter );
return $DistMeter; 
}

function showMeter( $gpsNow, $gpsOther, $DistMeter ) {
$DistKm = $DistMeter / 1000;
$DistKmStr = number_format( $DistKm );
$DistMeterStr = number_format( $DistMeter );

$str  = "\n";
$str .= $gpsNow[0] . '와 ' . $gpsOther[0] . " 의 거리 = ";
$str .= $DistMeter . ' m (<B>' . $DistKm . "</B> km) 입니다.<br>\n";
$str .= $DistMeterStr . ' m (<B>' . $DistKmStr . "</B> km) 입니다.<br>\n";
$str .= $gpsNow[0] . ' => (' . $gpsNow[3] . ")<br>\n";
$str .= $gpsOther[0] . ' => (' . $gpsOther[3] . ")<br>\n";
if( $DistMeter < 60000 )
$str .= "<font color=red> 60km 이내의 거리에 있습니다.</font><br>\n";
$str .= '<hr>';
echo $str;
}

$user_id = $send;
if (!$con)  
{  
    echo "MySQL 접속 에러 : ";
    echo mysqli_connect_error();
    exit();  
}  

mysqli_set_charset($con,"utf8"); 


$result1 = mysqli_query($con,"SELECT lon, lat FROM user WHERE user_id = 5");
$response1 = array();//배열 선언

if($result1){
    $row1 = mysqli_fetch_array($result1);
    array_push($response1,array("lon"=>$row1[0], "lat"=>$row1[1]));
                   }

$result = mysqli_query($con, "SELECT * FROM order1");
$response = array();//배열 선언


if($result){
    while($row = mysqli_fetch_array($result)){
 array_push($reponse,array("order_id1"=>$row[0], "lon_1"=>$row[1], "lat_1"=>$row[2]));

        $dist = get_distance($row1[1], $row1[0], $row[2],$row[1]);
        
        if ($dist<10){
        array_push($reresponse,array("order_id1"=>$row[0], "lon_1"=>$row[1], "lat_1"=>$row[2]));
                   }
        
                   
    
}



//response라는 변수명으로 JSON 타입으로 $response 내용을 출력
header('Content-Type: application/json; charset=utf8');
$json = json_encode(array("reresponse"=>$reresponse), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE);
echo $json;

}  
else{  
    echo "SQL문 처리중 에러 발생 : "; 
    echo mysqli_error($con);
} 


 
mysqli_close($con);  
   
?>