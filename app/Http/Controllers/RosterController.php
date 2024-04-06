<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Symfony\Component\Yaml\Yaml;
use Illuminate\Support\Facades\Storage;
use \DOMDocument;
use \DOMXPath;
// use Symfony\Component\Panther\PantherTestCase;
// use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\Activity;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use \Exception;

class RosterController extends Controller
{
    private $dom;
    private $xpath;
    private $monolog;
    private $roster;

    public function __construct()
    {
        
        $this->monolog = new Logger('name');
        $this->monolog->pushHandler(new StreamHandler(storage_path('logs/monolog.log'), Logger::DEBUG));
    }

    public function truncateDatabase()
    {
        DB::statement('PRAGMA foreign_keys=OFF;');

        $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");

        foreach ($tables as $table) {
            if ($table->name !== 'sqlite_sequence') {
                DB::table($table->name)->truncate();
            }
        }

        DB::statement('PRAGMA foreign_keys=ON;');

        return response()->json(['status' => false,'message' => 'Database truncated successfully']);
    }

    public function getSBYNextWeek(Request $request)
    {
        try {

            $date = $request->input('date');
            if (isset($date) && isset($date)) {
                $fromDate = date('d', strtotime($date));
                $toDate = date('d', strtotime($date . ' + 7 days'));
            } else {
                throw new Exception("From and To date missing", 1);

            }

            $activities = Activity::where('activity_date_withoutday','>=', $fromDate)
                                    ->where('activity_date_withoutday','<=', $toDate)
                                    ->where('activity', 'SBY')
                                    ->get();
            $response = [];
            
            foreach ($activities as $activity) {
                $response[] = $activity->getShowableAttributes();
            }

            return response()->json(['status' => true,'data' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()], 400);
        }
    }

    public function getFligtsNextWeek(Request $request)
    {
        try {

            $date = $request->input('date');
            if (isset($date) && isset($date)) {
                $fromDate = date('d', strtotime($date));
                $toDate = date('d', strtotime($date . ' + 7 days'));
            } else {
                throw new Exception("From and To date missing", 1);

            }

            $activities = Activity::where('activity_date_withoutday','>=', $fromDate)
                                    ->where('activity_date_withoutday','<=', $toDate)
                                    ->get();
            $response = [];
            
            foreach ($activities as $activity) {
                $response[] = $activity->getShowableAttributes();
            }

            return response()->json(['status' => true,'data' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false,'message' => $e->getMessage()], 400);
        }
    }

    public function roasterFilter(Request $request)
    {
        try {
            $fromDate = $request->input('fromDate');
            $toDate = $request->input('toDate');

            if (isset($fromDate) && isset($toDate)) {
                $activities = Activity::where('activity_date_withoutday','>=', $fromDate)
                                    ->where('activity_date_withoutday','<=', $toDate)
                                    ->get();
            } else {
                throw new Exception("From and To date missing", 1);
            }
            
            $response = [];
            
            foreach ($activities as $activity) {
                $response[] = $activity->getShowableAttributes();
            }

            return response()->json(['status' => true, 'data' => $response], 200);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getFligts(Request $request)
    {
        $airportCode = $request->input('airportCode');
        $activities = Activity::where('activity_from_airport_code', $airportCode)->get();
        $response = [];
        
        foreach ($activities as $activity) {
            $response[] = $activity->getShowableAttributes();
        }

        return response()->json(['status' => true,'data' => $response], 200);
    }

    public function getRosters(Request $request)
    {
        try{
            $activities = Activity::all();
            $response = [];
            foreach ($activities as $activity) {
                $response[] = $activity->getShowableAttributes();
            }
            // var_dump($response);die;

            return response()->json(['status' => true, 'data' => $response], 200);
        } catch (QueryException $e){
            $this->monolog->error( $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $this->monolog->info('file upload started');

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $this->monolog->info('file upload completed');

                $filePath = $file->store('uploads');

                if (Storage::exists($filePath)) {
                    foreach (config('config.xpath') as $key => $value) {
                        $this->roster[$key] = $this->parseFile($filePath, $value);
                    }
                }

                $this->formatData();


                return response()->json(['status' => true,'message' => 'File uploaded successfully']);
            } else {
                $this->monolog->info('file not found');
                return response()->json(['status' => false, 'message' => 'No file uploaded'], 400);
            }
        } catch (Exception $e) {
            $this->monolog->error( $e->getMessage());
            return response()->json(['status' => false, 'message' => $e->getMessage()]);

        } 
    }

    private function formatData()
    {
        foreach ($this->roster as $key => $value) {
            $function = 'format_'.$key;
            $this->$function($value);
        }
    }

    function removeNonBreakingSpace(&$item, $key) {
        $unicodeCharacter = json_decode('"\u00A0"');
        $nonBreakingSpace = $unicodeCharacter;
        
        $item = str_replace($nonBreakingSpace, ' ', $item);
    }

    private function format_activityGrid($value)
    {
        $parcedDatas = [];
        foreach ($value as $key => $datas) {
            if ($key == 0) {
                continue;
            }

            foreach ($datas as $index => $val) {
                if (trim($value[0][$index]) !== '' && $value[0][$index] !== ' ') {
                    $parcedData[$value[0][$index]] = $val;
                }
            }

            if (isset($parcedData['Date']) && $parcedData['Date'] != '') {
                $date = $parcedData['Date'];
            }
            $parcedDateData[$date][] = $parcedData;
            
        }
        

        foreach ($parcedDateData as $key => $value) {
            $pattern = '/^[A-Z]{2}\d+$/';

            // Check if the activity matches the pattern
            $Activity = array_filter(array_column($value, 'Activity'));
            foreach ($Activity as $Activity) {
                if (!preg_match($pattern, $Activity) && !in_array($Activity, ['OFF', 'SBY', 'CAR'])) {
                    throw new Exception("$Activity. Activity does not follow the rule", 1);
                }
            }

            $Date = array_filter(array_column($value, 'Date'));
            // var_dump( $Date[0]);die;
            $data[] = [
                'activity_date' => $Date[0],
                'activity_date_withoutday' => (int) explode(" ", $Date[0])[1],
                'activity_rev' => json_encode( array_filter(array_column($value, 'Rev'))),
                'activity_dc' => json_encode( array_filter(array_column($value, 'DC'))),
                'activity_check_in_l' => json_encode( array_filter(array_column($value, 'C/I(L)'))),
                'activity_check_in_z' => json_encode( array_filter(array_column($value, 'C/I(Z)'))),
                'activity_check_out_l' => json_encode( array_filter(array_column($value, 'C/O(L)'))),
                'activity_check_out_z' => json_encode( array_filter(array_column($value, 'C/0(Z)'))),
                'activity' => json_encode( $Activity),
                'activity_remark' => json_encode( array_filter(array_column($value, 'Remark'))),
                'activity_from' => json_encode( array_filter(array_column($value, 'From'))),
                'activity_from_airport_code' => array_filter(array_column($value, 'From'))[0],
                'activity_from_std_l' => json_encode( array_filter(array_column($value, 'STD(L)'))),
                'activity_from_std_z' => json_encode( array_filter(array_column($value, 'STD(Z)'))),
                'activity_to' => json_encode( array_filter(array_column($value, 'To'))),
                'activity_to_std_l' => json_encode( array_filter(array_column($value, 'STA(L)'))),
                'activity_to_std_z' => json_encode( array_filter(array_column($value, 'STA(Z)'))),
                'activity_hotel' => json_encode( array_filter(array_column($value, 'AC/Hotel'))),
                'activity_blh' => json_encode( array_filter(array_column($value, 'BLH'))),
                'activity_flight_time' => json_encode( array_filter(array_column($value, 'Flight Time'))),
                'activity_night_time' => json_encode( array_filter(array_column($value, 'Night Time'))),
                'activity_dur' => json_encode( array_filter(array_column($value, 'Dur'))),
                'activity_ext' => json_encode( array_filter(array_column($value, 'Ext'))),
                'activity_pax_booked' => json_encode( array_filter(array_column($value, 'Pax booked'))),
                'activity_acreg' => json_encode( array_filter(array_column($value, 'ACReg'))),
                'activity_crew_meal' => json_encode( array_filter(array_column($value, 'CrewMeal'))),
                'activity_resources' => json_encode( array_filter(array_column($value, 'Resources'))),
                'activity_cc' => json_encode( array_filter(array_column($value, 'CC'))),
                'activity_name' => json_encode( array_filter(array_column($value, 'Name'))),
                'activity_pos' => json_encode( array_filter(array_column($value, 'Pos.'))),
                'activity_work_phone' => json_encode( array_filter(array_column($value, 'Work Phone'))),
                'activity_hd_crew' => json_encode( array_filter(array_column($value, 'DH Crew'))),
                'activity_hd_name' => json_encode( array_filter(array_column($value, 'DH Name'))),
                'activity_hd_seat' => json_encode( array_filter(array_column($value, 'DH Seat'))),
                'activity_remarks' => json_encode( array_filter(array_column($value, 'Remarks'))),
                'activity_fdp_time' => json_encode( array_filter(array_column($value, 'Fdp Time'))),
                'activity_max_fdp' => json_encode( array_filter(array_column($value, 'Max Fdp'))),
                'activity_rest_compl' => json_encode( array_filter(array_column($value, 'Rest Compl.'))),
            ];
        }

        try {
            $result =  Activity::insert($data);
        } catch (QueryException $e) {
            die($e->getMessage());
            $this->monolog->error(sprintf('Invalid xpath %s', $e->getMessage()));
            return response()->json(['message' => $e->getMessage()]);

        }   
    }

    public function parseFile($filePath, $className)
    {
        $fileContent = Storage::get($filePath);
        $Crawler = new Crawler($fileContent);
        $tableData = []; // Initialize $tableData array outside the each function

        $tables = $Crawler->filter('table');

        $tables->each(function (Crawler $table, $index) use (&$className, &$tableData) {
            if (strpos($table->attr('id'), $className) !== false) {
                $rows = $table->filter('tr');
                $rowData = []; // Initialize $rowData array for each table
                $rows->each(function (Crawler $row, $index) use (&$rowData) {
                    $cells = $row->filter('td');
                    $cellData = []; // Initialize $cellData array for each row

                    $cells->each(function (Crawler $cell) use (&$cellData) {
                        $cellData[] = str_replace(" ", "", $cell->text());
                    });

                    $rowData[] = $cellData;
                });
                $tableData = $rowData; // Add $rowData to $tableData array
            }
        });

        return $tableData;
    }

    private function loadXml($htmlpath)
    {
        if (Storage::exists($filePath)) {
            // convert to xml
            $fileContent = Storage::get($filePath);
            @$this->dom = new DOMDocument();
            $this->dom->loadHTML($fileContent);
            $xml = $this->dom->saveXML();
            $this->dom->loadXML($xml);
            $this->xpath = new DOMXPath($this->dom);
        }
    }

    private function readXpath($key)
    {
        $value = config('config.xpath');
        if (isset($value[$key]) === true) {
            return $value[$key];
        }
        
        $this->monolog->error(sprintf('Invalid xpath %s', $key));
        throw new Exception(sprintf('Invalid xpath %s', $key), 501);
    }
}
