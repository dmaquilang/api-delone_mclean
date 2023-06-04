<?php

namespace App\Controllers;
use App\Models\Api_response;

use CodeIgniter\RESTful\ResourceController;

class Data extends ResourceController
{
    
    public function __construct()
    {
        $this->api_key = $_SERVER['HTTP_API_KEY'];
    }

    /**
     * Get Datas
     */
    public function index()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }
        $db = \Config\Database::connect();
        $sql = <<<EOT
    SELECT * FROM respondent
    WHERE respondent.is_deleted = 0
EOT;
        $query = $db->query($sql);
        $respondents = $query->getResultArray();

        // var_dump($respondents);
        // die();
        $counter = 0;
        foreach($respondents AS $respondent) {
            $id = $respondent['id'];
            $sql = <<<EOT
SELECT * 
FROM respondent_response
WHERE respondent_response.respondent_id = $id
ORDER BY respondent_response.question_id ASC;
EOT;
            $query = $db->query($sql);
            $responses = $query->getResultArray();
            $respondent['responses'] = [];
            foreach($responses AS $response) {
                $respondent['responses'][] = $response['response'];
            }
            $respondents[$counter]['responses'] = $respondent['responses'];
            $counter += 1;
        }



        $response = $this->respond($respondents);

        return $response;
    }

    /**
     * Get Datas
     */
    public function systemsuccess()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }
        $db = \Config\Database::connect();
        $sql = <<<EOT
    SELECT * FROM respondent
    WHERE respondent.is_deleted = 0
EOT;
        $query = $db->query($sql);
        $respondents = $query->getResultArray();

        // var_dump($respondents);
        // die();
        $counter = 0;
        $data = [];
// 			{
// 				"group": "Dataset 1",
// 				"q1": 1
// 			}

        $finalResponse = new \stdClass();
        for($i = 1; $i<=24; $i++) {
            $finalResponse->{"q".$i} = [];
        }

        foreach($respondents AS $respondent) {
            $id = $respondent['id'];
            $sql = <<<EOT
SELECT * 
FROM respondent_response
LEFT JOIN respondent ON respondent.id = respondent_response.respondent_id
WHERE respondent_response.respondent_id = $id
AND respondent.is_deleted = 0
ORDER BY respondent_response.question_id ASC;
EOT;
            $query = $db->query($sql);
            $responses = $query->getResultArray();
            $respondent['responses'] = [];

            foreach($responses AS $response) {
                $respondent_data = new \stdClass();
                $respondent_data->group = "Dataset " . strval($response['question_id']);
                $respondent_data->{"q". strval($response['question_id'])} = $response['response'];
                array_push($finalResponse->{"q".$response['question_id']}, $respondent_data);
            }
        }



        $response = $this->respond($finalResponse);

        return $response;
    }

    /**
     * Get Demographics
     */
    public function demographics()
    {
        if ($this->api_key != API_KEY) {
            return $this->failUnauthorized('API key unauthorized');
        }

        $db = \Config\Database::connect();
        $sql = <<<EOT
    SELECT * FROM respondent
    WHERE respondent.is_deleted = 0
EOT;
        $query = $db->query($sql);
        $respondents = $query->getResultArray();

        $counter = 0;
        foreach($respondents AS $respondent) {
            $id = $respondent['id'];
            $sql = <<<EOT
SELECT * 
FROM respondent_response
WHERE respondent_response.respondent_id = $id
ORDER BY respondent_response.question_id ASC;
EOT;
            $query = $db->query($sql);
            $responses = $query->getResultArray();
            $respondent['responses'] = [];
            foreach($responses AS $response) {
                $respondent['responses'][] = $response['response'];
            }
            $respondents[$counter]['responses'] = $respondent['responses'];
            $counter += 1;
        }

        $students = 0;
        $faculty = 0;
        $first_year = 0;
        $second_year = 0;
        $third_year = 0;
        $fourth_year = 0;
        $SOM = 0;
        $CSS = 0;
        $COS = 0;
        $CCAD = 0;
        $male = 0;
        $female = 0;
        $less_than_five = 0;
        $five_to_ten = 0;
        $eleven_to_twenty = 0;
        $twentyone_to_thirty = 0;
        $thirtyone_to_sixty = 0;
        $sixtyone_and_over = 0;
        $total_respondents = 0;
        $age = new \stdClass();

        foreach($respondents AS $respondent) {
            $total_respondents += 1;
            if($respondent['role'] == "faculty/admin") {
                $faculty += 1;
            } elseif($respondent['role'] == "student") {
                $students += 1;
            }

            if($respondent['year_college'] == "1") {
                $first_year += 1;
            } elseif($respondent['year_college'] == "2") {
                $second_year += 1;
            } elseif($respondent['year_college'] == "3") {
                $third_year += 1;
            } elseif($respondent['year_college'] == "4") {
                $fourth_year += 1;
            }

            if($respondent['year_college'] == "SOM") {
                $SOM += 1;
            } elseif($respondent['year_college'] == "CSS") {
                $CSS += 1;
            } elseif($respondent['year_college'] == "COS") {
                $COS += 1;
            } elseif($respondent['year_college'] == "CCAD") {
                $CCAD += 1;
            }

            if($respondent['sex'] == "male") {
                $male += 1;
            } elseif($respondent['sex'] == "female") {
                $female += 1;
            }

            if($respondent['minutes_spent'] == "less than 5") {
                $less_than_five += 1;
            } elseif($respondent['minutes_spent'] == "5-10") {
                $five_to_ten += 1;
            } elseif($respondent['minutes_spent'] == "11-20") {
                $eleven_to_twenty += 1;
            } elseif($respondent['minutes_spent'] == "21-30") {
                $twentyone_to_thirty += 1;
            } elseif($respondent['minutes_spent'] == "31-60") {
                $thirtyone_to_sixty += 1;
            } elseif($respondent['minutes_spent'] == "61 and over") {
                $sixtyone_and_over += 1;
            }

            if($respondent['age']) {
                if(property_exists($age, $respondent['age'])) {
                    $age->{$respondent['age']} += 1;
                    // var_dump($age, $respondent['age'], "bing");
                } else {
                    $age->{$respondent['age']} = 1;
                    // var_dump($age, $respondent['age'], "bong");
                }
            }
        }

        $counts = new \stdClass();
        $counts->students = $students;
        $counts->faculty = $faculty;
        $counts->first_year = $first_year;
        $counts->second_year = $second_year;
        $counts->third_year = $third_year;
        $counts->fourth_year = $fourth_year;
        $counts->SOM = $SOM;
        $counts->CSS = $CSS;
        $counts->COS = $COS;
        $counts->CCAD = $CCAD;
        $counts->male = $male;
        $counts->female = $female;
        $counts->less_than_five = $less_than_five;
        $counts->five_to_ten = $five_to_ten;
        $counts->eleven_to_twenty = $eleven_to_twenty;
        $counts->twentyone_to_thirty = $twentyone_to_thirty;
        $counts->thirtyone_to_sixty = $thirtyone_to_sixty;
        $counts->sixtyone_and_over = $sixtyone_and_over;
        $counts->total_respondents = $total_respondents;

        $RIS = [
                ["Role in SAIS", "Student", "Faculty"],
                ["Role", $students, $faculty]
              ];
        $RISpie = [
                ["Role in SAIS", "Role"],
                ["Student", $students],
                ["Faculty", $faculty],
              ];
        // $optPie = new \stdClass();
        // $optPie->chart = new \stdClass();
        // $optPie->chart->title = "Role in SAIS";
        // $optPie->axes = new \stdClass();
        // $optPie->axes->y = new \stdClass();
        // $optPie->axes->y->{'0'} = new \stdClass();
        // $optPie->axes->y->{'0'}->side = 'top';
        // $optPie->axes->y->{'0'}->label = 'top';
        $SYL = [
            ["Student Year Level", "1st Year","2nd Year","3rd Year","4th Year"],
            ["Year Level", $first_year, $second_year, $third_year, $fourth_year]
        ];

        $SYLpie = [
            ["Student Year Level", "Number"],
            ["1st Yr", $first_year],
            ["2nd Yr", $second_year],
            ["3rd Yr", $third_year],
            ["4th Yr", $fourth_year]
        ];

        $FAC = [
            ["Faculty/Admin College", "SOM","CSS","COS","CCAD",],
            ["COLLEGE", $SOM, $CSS, $COS, $CCAD]
        ];

        $FACpie = [
            ["Faculty/Admin College", "Number"],
            ["SOM", $SOM],
            ["CSS", $CSS],
            ["COS", $COS],
            ["CCAD", $CCAD]
        ];

        $SEX = [
            ["Sex", "Male", "Female"],
            ["Sex", $male, $female]
        ];

        $SEXpie = [
            ["Sex", "Number"],
            ["Male", $male],
            ["Female", $female]
        ];

        $AGE = [
            ["AGE"],
            ["AGE"]
        ];

        $AGEpie = [
            ["Age", "Number"]
        ];

        foreach($age AS $key => $value) {
            array_push($AGE[0], $key);
            array_push($AGE[1], $value);
            array_push($AGEpie, [$key, $value]);
        }

        $MAS = [
            ["Monthly Average Spent on SAIS",
                  "Less than 5","5 to 10","11 to 20",
                  "21 to 30","31 to 60","61 and over"],
            ["AVERAGE SPENT", $less_than_five, $five_to_ten, $eleven_to_twenty, $twentyone_to_thirty, $thirtyone_to_sixty, $sixtyone_and_over]
        ];

        $MASpie = [
            ["Monthly Average Spent on SAIS","Number"],
            ["Less then 5", $less_than_five],
            ["5 to 10", $five_to_ten],
            ["11 to 20", $eleven_to_twenty],
            ["21 to 30", $twentyone_to_thirty],
            ["31 to 60", $thirtyone_to_sixty],
            ["61 and over", $sixtyone_and_over]
        ];

        $counts->RIS = $RIS;
        $counts->RISpie = $RISpie;
        $counts->SYL = $SYL;
        $counts->SYLpie = $SYLpie;
        $counts->FAC = $FAC;
        $counts->FACpie = $FACpie;
        $counts->SEX = $SEX;
        $counts->SEXpie = $SEXpie;
        $counts->AGE = $AGE;
        $counts->AGEpie = $AGEpie;
        $counts->MAS = $MAS;
        $counts->MASpie = $MASpie;

        $response = $this->respond($counts);
        return $response;
    }
}
