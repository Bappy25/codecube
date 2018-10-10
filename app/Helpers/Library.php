<?php

namespace App\Library;

	class Library extends Database{


      // Function for setting session data */
    public static function setSubmittedData(){
      $_SESSION['data'] = $_POST;
    }

      // Function for getting data
    public static function getSubmittedData($allData){  
        if(isset($_SESSION['data'])){
          $allData['class'] = "form-line focused";
          foreach($_SESSION['data'] as $key => $value){
            if(array_key_exists($key, $allData)){
              $allData[$key] = $value;
            }
          }
          unset($_SESSION['data']);
        }
        return $allData;
    }

      // Function for checking resultselt
    public static function showResult($result, $page, $alertMessage = "Empty Result: Nothing found in the database!", $pagination=''){  
        if($result){
          foreach($result as $key => $value){
        if(!empty($result[$key]['update_date'])){
            $result[$key]['update_date'] = date("F d (l), Y", strtotime($value['update_date']));
          }
          else{
            $result[$key]['update_date'] = "<span class='label bg-orange'>Not updated yet!</span>";
          }
          if(!empty($result[$key]['image'])){
            $result[$key]['image'] = Image::checkImage('all_images/', $result[$key]['image']);
          }
      }
            require_once($page);  
        }
        else{
          require_once('views/home/alert.php');
        }
    }

      // Function for creating current page title
    public static function generateTitle($string=''){
      $splitString = explode('_', $string);
      $title = '';
      foreach($splitString as $split){
        $title = $title." ".ucwords($split);
      }
      return $title.' ||';
    }

    	// Function for getting current page URL
    public static function getURL($exclude=''){
    		// $parts = explode('/', $_SERVER['REQUEST_URI']);
			 // $url = $parts[count($parts) - 1];
  		if (strpos($_SERVER['REQUEST_URI'], $exclude) !== false) {
  			$url = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], $exclude));
  		}
  		else{
  			$url = $_SERVER['REQUEST_URI'];
  		}
  		return $url;
    }

      // Function for creating SEO friendly url
    public static function sweetURL($string, $separator = '-'){
      
      /*  $string = str_replace("-", "_", strtolower($_SERVER['REQUEST_URI']));
        $splitString = explode('/', $string);
        $_GET['controller'] = $splitString['2'];
        $_GET['action'] = $splitString['3'];*/
      $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
      $special_cases = array( '&' => 'and', "'" => '');
      $string = mb_strtolower( trim( $string ), 'UTF-8' );
      $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
      $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
      $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
      $string = preg_replace("/[$separator]+/u", "$separator", $string);
      return $string;
    } 

      // Fucntion for setting page name
    public static function setPageName(){
      $parts = explode('/', $_SERVER['REQUEST_URI']);
      $getName = $parts[count($parts) - 1]; 
      $currect_name = str_replace(".php", "", $getName);
      $name = ucFirst($currect_name);
      return $name;
    }

      // Function for cutting a string
    public static function substrwords($text, $maxchar, $end='...') {
        if (strlen($text) > $maxchar || $text == '') {
            $words = preg_split('/\s/', $text);      
            $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } 
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } 
        else {
            $output = $text;
        }
        return $output;
    }

      //  Function for generating HTML colors
    public static function generateColor(){
      return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    } 
  
      // Function for generating year array
    public static function generateYearArray($previousYears = 1){
      $dates = array();
            for ($year=date("Y")-$previousYears; $year<=date("Y"); $year++) {
                $yearOfDates = array();
                for ($month=1; $month<=12; $month++) {
                    $begin = strtotime($year."-".$month."-01 12:00:00AM");
                    $end = strtotime($year."-".($month+1)."-01 12:00:00AM")-1;
                    $key = date("F, Y", $begin); // January, 2017
                    $yearOfDates[$key] = array(
                        'start' => $begin,
                        'end' => $end
                    );
                }
                if ($year==date("Y")) $dates['previousYear'] = $yearOfDates;
                else $dates['currentYear'] = $yearOfDates; //assume 2018
            }
        return $dates;
    }

      // Function for handling error
    public static function handle_error(){
        trigger_error("Cannot divide by zero", E_USER_ERROR);
        require_once('views/auth/view_error_message.php');
    }

            // Function for declaring pagination
    public static function paginate_declare($perpage=1) {
      if(isset($_GET["page"])){
          $page = intval($_GET["page"]);
      }
      else {
        $page = 1;
      }
      $calc = $perpage * $page;
      $start = $calc - $perpage;
      return array("perpage"=>$perpage, "page"=>$page, "start"=>$start);
    }    

      // Funcntion for pagination
    public static function paginate($total=1, $perpage=1, $page=1) {
      $url = Self::getURL('&page');
      $totalPages = ceil($total / $perpage);
      $navigation = array('nav'=>'', 'skip_previous'=>'', 'previous'=>'', 'link'=>'', 'next'=>'', 'skip_next'=>'','endnav'=>'');
      $navigation['nav'] = "<nav><ul class='pagination'>";
      if($page > 1 ){
        $navigation['skip_previous'] = "<li>
                  <a href='$url&page=1' class='waves-effect'>
                      |<
                  </a>
              </li>";
      }
    
      if($page <=1 ){
        $navigation['previous'] = " <li class='disabled'>
                      <a href='javascript:void(0);'>
                          Previous
                      </a>
                  </li>";
      }
      else{
      $j = $page - 1;
        $navigation['previous'] = " <li>
                  <a href='$url&page=$j' class='waves-effect'>
                      Previous
                  </a>
              </li>";
      }
    
      if ($totalPages >=1 && $page <= $totalPages){
        $range = 2;
        $limit = $range+1;
        $navigation['link'] = "";
        if ($page > $limit){ 
          $navigation['link'] = "<li><a href='$url&page=1' class='waves-effect'>1</a></li>
          <li class='disabled'><a href = 'javascript:void(0);'>...</a></li>";
        }
            for($i= ($page-$range); $i <(($page + $range)  + 1); $i++){
          if (($i > 0) && ($i <= $totalPages)){
            if($i<>$page){
              $navigation['link'] .= "<li> <a href='$url&page=$i' class='waves-effect'>$i</a></li>";
            }
            else{
              $navigation['link'] .= "<li class='active'><a href='javascript:void(0);'>$i</a></li>";
            }       
          }
            }
            if ($page <= $totalPages - $limit){ 
        $navigation['link'] .= "<li class='disabled'><a href = 'javascript:void(0);'>...</a></li>
            <li> <a href='$url&page=" .$totalPages." ' class='waves-effect'>$totalPages</a></li>"; 
        }
      } 
      if($page == $totalPages ){
        $navigation['next'] = "<li class='disabled'>
           <a href='javascript:void(0);'>
                      Next
                  </a>
               </li>";
      }
      else{
        $j = $page + 1;
        $navigation['next'] = "<li>
           <a href='$url&page=$j' class='waves-effect'>
                      Next
                  </a>
               </li>";
      }
      if($page < $totalPages ){ 
        $navigation['skip_next'] = "<li>
                <a href='$url&page=" .$totalPages." ' class='waves-effect'>
                    >|
                </a>
            </li>"; 
      }
      $navigation['endnav'] = "</ul></nav>";
      return $pagination = $navigation['nav'].$navigation['skip_previous'].$navigation['previous'].$navigation['link'].$navigation['next'].$navigation['skip_next'].$navigation['endnav'];
    }


   
}

?>