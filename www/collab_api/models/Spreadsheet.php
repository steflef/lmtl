<?php
class spreadsheet {
    private $token;
    private $spreadsheet;
    private $worksheet;
    private $spreadsheetid;
    private $worksheetid;

    public function __construct() {
    }

    public function authenticate($username, $password) {
        $url = "https://www.google.com/accounts/ClientLogin";
        $fields = array(
            "accountType" => "HOSTED_OR_GOOGLE",
            "Email" => $username,
            "Passwd" => $password,
            "service" => "wise",
            "source" => "source"
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($status == 200) {
            if(stripos($response, "auth=") !== false) {
                preg_match("/auth=([a-z0-9_\-]+)/i", $response, $matches);
                $this->token = $matches[1];
            }
        }
    }

    public function getToken(){
        return $this->token;
    }

    public function setSpreadsheet($title) {
        $this->spreadsheet = $title;
    }

    public function setSpreadsheetId($id) {
        $this->spreadsheetid = $id;
    }
    public function getSpreadsheetId() {
        return $this->spreadsheetid;
    }

    public function setWorksheetId($id) {
        $this->worksheetid = $id;
    }
    public function getWorksheetId() {
        return $this->worksheetid;
    }
    public function setWorksheet($title) {
        $this->worksheet = $title;
    }

    public function listSheets()
    {
        if (empty($this->token)) {
            return false;
        }
        $url = 'https://spreadsheets.google.com/feeds/spreadsheets/private/full/';
        $url = 'https://spreadsheets.google.com/feeds/worksheets/0ApilBOlHO2BDdHZvdlNlaWdFNi15d2J0dHpUZ2lJV2c/private/full';
        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($curl, CURLOPT_POST, true);
        //curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if($status == 200) {
            $sheetsIDs = array();
            $xml = simplexml_load_string($response);

            $sheets = array();
            if($xml->entry) {
                $sheetsCount = sizeof($xml->entry);
                for($c = 0; $c < $sheetsCount; ++$c){
                    $singleSheet = array(
                        'entry' => $xml->entry[$c],
                        'id' => (string) $xml->entry[$c]->id,
                        'title' => (string) $xml->entry[$c]->title,
                        'link' => (string) $xml->entry[$c]->link[1]['href'],
                        'updated' => (string) $xml->entry[$c]->updated,
                        'author' => (string) $xml->entry[$c]->author->email
                    );

                    $sheets[] = $singleSheet;
                }
            }

            return array(
                'status' => $status,
                'response' => $sheets
            );
        }

        return array(
            'status' => $status,
            'response' => ''
        );
    }

    public function addSheet($title,$rows=1,$cols=2)
    {
        if (!empty($this->token)) {
            //$url = 'https://spreadsheets.google.com/feeds/worksheets/key/private/full';
            //$url = 'https://spreadsheets.google.com/feeds/default/private/full';
            $url = 'https://spreadsheets.google.com/feeds/worksheets/'.$this->getSpreadsheetId().'/private/full';

            //$url = 'https://spreadsheets.google.com/feeds/upload/worksheets/key/private/full';
            //$url = 'https://docs.google.com/feeds/upload/create-session/default/private/full';

            $headers = array(
                "Content-Type: application/atom+xml",
                "Authorization: GoogleLogin auth=" . $this->token,
                "GData-Version: 3.0"
            );

            $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">
                         <title>' . $title . '</title>
                         <content type="text">Test Expenses</content>
                         <gs:rowCount>' . $rows . '</gs:rowCount>
                         <gs:colCount>' . $cols . '</gs:colCount>
                       </entry>';
            $fields =  preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$fields);
            $headers[] = "Content-Length: ". strlen($fields);

/*            echo '<pre><code>';
            print_r(htmlentities($fields));
            echo '</code></pre>';*/

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            $response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $xml = simplexml_load_string($response);

            return array(
                'status' => $status,
                'response' => $xml
            );
        }
    }

    public function add($data) {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }
            //$url = $this->getPostUrl();
            //$url = 'https://spreadsheets.google.com/feeds/list/key/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/private/full';
            $url = 'https://spreadsheets.google.com/feeds/list/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/1/private/full';
            //$url = 'https://spreadsheets.google.com/feeds/spreadsheets/tesWsiZPGjeWd2r-4BPSoig/private/full';
            //if(!empty($url)) {
                $headers = array(
                    "Content-Type: application/atom+xml",
                    "Authorization: GoogleLogin auth=" . $this->token,
                    "GData-Version: 3.0"
                );

                $columnIDs = $this->getColumnIDs();
        echo '$columnIDs: ';
        echo '<pre>';
        print_r( $columnIDs );
        echo '</pre>';
                if($columnIDs) {
                    $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
                    foreach($data as $key => $value) {
                        $key = $this->formatColumnID($key);
                        if(in_array($key, $columnIDs))
                            $fields .= "<gsx:$key><![CDATA[$value]]></gsx:$key>";
                    }
                    $fields .= '</entry>';

                    //echo $fields .'<br>';
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
                    $response = curl_exec($curl);
                    $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                    curl_close($curl);

                    return array(
                        'status' => $status,
                        'response' => $response
                    );
/*                    if ($status == 201){
                        return true;
                    } else {
                        return false;
                    }*/
                }
            //}
        //}
    }


    /**
     * @param $data
     * @return array|bool
     */
    public function addRow($data) {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }

        $url = 'https://spreadsheets.google.com/feeds/list/'. $this->getSpreadsheetId() . '/'.$this->getWorksheetId().'/private/full';

        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );

        $columnIDs = $this->getColumnIDs();
/*        print_r($columnIDs);
        exit;*/

        if($columnIDs) {
            $rows = array();
            foreach ($data as $row) {

                //$fields = '<atom:entry xmlns:atom="http://www.w3.org/2005/Atom">';
                $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended">';
                foreach($row as $key => $value) {
/*                    echo $key . "<br>";
                    echo $value . "<br>";
                    echo '--------<br>';*/
/*                    $key = $this->formatColumnID($key);
                    if(in_array($key, $columnIDs))*/
                    //$key = $this->formatColumnID($key);
                    if(in_array($this->formatColumnID($key), $columnIDs))
                        //$fields .= '<gsx:'.$key.' xmlns:gsx="http://schemas.google.com/spreadsheets/2006/extended"><![CDATA['.$value.']]></gsx:'.$key.'>';
                     $fields .= '<gsx:'.$key.'><![CDATA['.$value.']]></gsx:'.$key.'>';
                }
                //$fields .= '</atom:entry>';
                $fields .= '</entry>';
                $rows[] = $fields;
            }

            $curl_arr = array();
            $master = curl_multi_init();
            $rowsCount = count($data);

            for($i = 0; $i < $rowsCount; $i++)
            {
                $curl_arr[$i] = curl_init($url);
                curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_arr[$i], CURLOPT_URL, $url);
                curl_setopt($curl_arr[$i], CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($curl_arr[$i], CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl_arr[$i], CURLOPT_POST, true);
                curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $rows[$i]);
                curl_multi_add_handle($master, $curl_arr[$i]);
                echo $rows[$i];
                echo '<br> --------------- <br>';
            }

            do {
                curl_multi_exec($master,$running);
            } while($running > 0);

            $results = array();
            $status = array();
            for($i = 0; $i < $rowsCount; $i++)
            {
                $result = curl_multi_getcontent ( $curl_arr[$i] );
                //$status[] = curl_multi_info_read( $curl_arr[$i] );
                $results[] = $i . ' '. $result;
            }
        } else {
            $results = array('No Columns');
            $status = array('400');
        }

        print_r($results);

        return array(
            //'status' => $status,
            'response' => $results
        );
    }


    /**
     * @param $data
     * @return array|bool
     */
    public function addCellRows($data, $bid = '') {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }

        $url = 'https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'.$this->getWorksheetId().'/private/full/batch';

        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0",
            "If-Match: *"
        );

        $rowId = 1;

        $feed = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:batch="http://schemas.google.com/gdata/batch" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
        $feed .= '<id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full</id>';
        foreach ($data as $row) {

            $colId = 1;
            foreach($row as $col=>$val) {

                $feed .= '<entry>
                        <batch:id>BID_'.$bid.'_R'.$rowId.'C'.$colId.'</batch:id>
                        <batch:operation type="update"/>
                        <id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R'.$rowId.'C'.$colId.'</id>
                        <link rel="edit" type="application/atom+xml"
                          href="https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R'.$rowId.'C'.$colId.'/version"/>
                        <gs:cell row="'.$rowId.'" col="'.$colId.'" inputValue="'.$val.'"/>
                      </entry>';
                $colId++;
            }
            $rowId ++;
        }
        $feed .= '</feed>';
        //echo preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$feed) .'<br>';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$feed) );
        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array(
            'status' => $status,
            'response' => $response
        );
    }


    /**
     * Mode BATCH COL Mets 1 colonne a la fois
     * @param $data
     * @return array|bool
     */
    public function addBatchCellCols($data) {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }

        $url = 'https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'.$this->getWorksheetId().'/private/full/batch';

        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0",
            "If-Match: *"
        );

        $rowId = 1;
        $colId = 1;

        $fields = array();

        foreach ($data as $col) {
            $feed = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:batch="http://schemas.google.com/gdata/batch" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
            $feed .= '<id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full</id>';

            foreach($col as $val) {

                $feed .= '<entry>
                        <batch:id>B_R'.$rowId.'C'.$colId.'</batch:id>
                        <batch:operation type="update"/>
                        <id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R'.$rowId.'C'.$colId.'</id>
                        <link rel="edit" type="application/atom+xml"
                          href="https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R'.$rowId.'C'.$colId.'/version"/>
                        <gs:cell row="'.$rowId.'" col="'.$colId.'" inputValue="'.$val.'"/>
                      </entry>';
                $rowId++;
            }

            $feed .= '</feed>';
            $fields[] = preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$feed);
            $colId ++;
        }

        $curl_arr = array();
        $master = curl_multi_init();

        for($i = 0; $i < count($data); $i++)
        {
            $curl_arr[$i] = curl_init($url);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl_arr[$i], CURLOPT_URL, $url);
            curl_setopt($curl_arr[$i], CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl_arr[$i], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl_arr[$i], CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl_arr[$i], CURLOPT_POST, true);
            curl_setopt($curl_arr[$i], CURLOPT_POSTFIELDS, $fields[$i]);
            curl_multi_add_handle($master, $curl_arr[$i]);
        }

        do {
            curl_multi_exec($master,$running);
        } while($running > 0);

        $results = array();
        $status = array();
        for($i = 0; $i < count($data); $i++)
        {
            $result = curl_multi_getcontent ( $curl_arr[$i] );
            //$status[] = curl_multi_info_read( $curl_arr[$i] );
            $results[] = $i . ' '. $result;
        }

        print_r($results);

        return array(
            //'status' => $status,
            'response' => $results
        );

    }

    /**
     * Build Columns with the Google API in Batch Cell mode
     * @param $data
     * @return array|bool
     */
    public function addColumn($data) {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }

        $url = 'https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'.$this->getWorksheetId().'/private/full/batch';

        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0",
            "If-Match: *"
        );

        $feed = '<feed xmlns="http://www.w3.org/2005/Atom" xmlns:batch="http://schemas.google.com/gdata/batch" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
        $feed .= '<id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full</id>';
        $count = 1;
        foreach($data as $col) {
            $feed .= '<entry>
                            <batch:id>A'.$count.'</batch:id>
                            <batch:operation type="update"/>
                            <id>https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R1C'.$count.'</id>
                            <link rel="edit" type="application/atom+xml"
                              href="https://spreadsheets.google.com/feeds/cells/'. $this->getSpreadsheetId() . '/'. $this->getWorksheetId().'/private/full/R1C'.$count.'/version"/>
                            <gs:cell row="1" col="'.$count.'" inputValue="'.$col.'"/>
                          </entry>';
            $count++;
        }

        $feed .= '</feed>';

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~','$1',$feed) );
        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array(
            'status' => $status,
            'response' => $response
        );
    }

    private function getColumnIDs() {
        $url = "https://spreadsheets.google.com/feeds/cells/" . $this->spreadsheetid . "/" . $this->worksheetid . "/private/full?max-row=1";
        //$url = "https://spreadsheets.google.com/feeds/cells/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/1/private/full?max-row=1";
        $headers = array(
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

/*        echo '<pre>';
        print_r( $response);
        echo '</pre>';*/

        if($status == 200) {
            $columnIDs = array();
            $xml = simplexml_load_string($response);
            if($xml->entry) {
                $columnSize = sizeof($xml->entry);
                for($c = 0; $c < $columnSize; ++$c)
                    $columnIDs[] = $this->formatColumnID($xml->entry[$c]->content);
            }
            return $columnIDs;
        }

        return "";
    }

    public function updateTitle($newTitle) {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }
        //$url = $this->getPostUrl();
        //$url = 'https://spreadsheets.google.com/feeds/list/key/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/private/full';
        $url = 'https://spreadsheets.google.com/feeds/worksheets/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/private/full';

        //if(!empty($url)) {
        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );

        //$columnIDs = $this->getColumnIDs();
        //echo '$columnIDs: ';
        //echo '<pre>';
       // print_r( $columnIDs );
       // echo '</pre>';
        //if($columnIDs) {
        //$fields = '<entry>';
            $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
        //$fields .= '<id>https://spreadsheets.google.com/feeds/list/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/0/private/full</id>';
        //$fields .= '<category scheme="http://schemas.google.com/spreadsheets/2006" term="http://schemas.google.com/spreadsheets/2006#worksheet"/>';

            //$fields .= '<title type="text">'.$newTitle.'</title>';
            //$fields .= '<gs:worksheet name="Voter registration"/>';
            //$fields .= '<gs:rowCount>45</gs:rowCount><gs:colCount>15</gs:colCount>';
            $fields .= '<gs:cell row="2" col="4" inputValue="=FLOOR(R[0]C[-1]/(R[0]C[-2]*60),.0001)"
    numericValue="0.0033">0.0033</gs:cell>';
            $fields .= '</entry>';



            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
            $response = curl_exec($curl);
            $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            return array(
                'status' => $status,
                'response' => $response
            );
    }

    public function updateCell() {
        if(empty($this->token)) {
            echo 'Empty Token';
            return false;
        }
echo 'ok';
      // $url = 'https://spreadsheets.google.com/feeds/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/tables';
        $url = 'https://spreadsheets.google.com/feeds/cells/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/od6/private/full';

        //if(!empty($url)) {
        $headers = array(
            "Content-Type: application/atom+xml",
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );
        $concat = htmlentities( '=ImportData(CONCATENATE("http://maps.google.com/maps/geo?output=csv&q=";A2;",";B2))');
        $fields = '<entry xmlns="http://www.w3.org/2005/Atom" xmlns:gs="http://schemas.google.com/spreadsheets/2006">';
        $fields .= '<id>https://spreadsheets.google.com/feeds/cells/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/1/private/full/R2C4</id>';
        $fields .= '<link rel="edit" type="application/atom+xml" href="https://spreadsheets.google.com/feeds/cells/0ApilBOlHO2BDdGVzV3NpWlBHamVXZDJyLTRCUFNvaWc/1/private/full/R2C4"/>';
        $fields .= '<gs:cell row="2" col="11" inputValue="'.$concat.'"/>';
        //$fields .= '<title>CQ ATLAS -- test</title>';
       // $fields .= '<gs:data insertionMode="overwrite" numRows="5" startRow="5"><gs:column index="2" name="Cheers"/></gs:data>';
       // $fields .= '<gs:data insertionMode="overwrite" startRow="1"><gs:cell row="2" col="4" inputValue="test"/></gs:data>';
       // $fields .= '<gs:header row="1"/>';
       // $fields .= '<gs:worksheet name="Sheet 1"/>';

        $fields .= '</entry>';



        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return array(
            'status' => $status,
            'response' => $response
        );
    }

    private function getPostUrl() {
        $url = "https://spreadsheets.google.com/feeds/spreadsheets/private/full?title=" . urlencode($this->spreadsheet);
        $headers = array(
            "Authorization: GoogleLogin auth=" . $this->token,
            "GData-Version: 3.0"
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if($status == 200) {
            $spreadsheetXml = simplexml_load_string($response);
            if($spreadsheetXml->entry) {
                $this->spreadsheetid = basename(trim($spreadsheetXml->entry[0]->id));
                $url = "https://spreadsheets.google.com/feeds/worksheets/" . $this->spreadsheetid . "/private/full";
                if(!empty($this->worksheet))
                    $url .= "?title=" . $this->worksheet;

                curl_setopt($curl, CURLOPT_URL, $url);
                $response = curl_exec($curl);
                $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if($status == 200) {
                    $worksheetXml = simplexml_load_string($response);
                    if($worksheetXml->entry)
                        $this->worksheetid = basename(trim($worksheetXml->entry[0]->id));
                }
            }
        }
        curl_close($curl);
        if(!empty($this->spreadsheetid) && !empty($this->worksheetid))
            return "https://spreadsheets.google.com/feeds/list/" . $this->spreadsheetid . "/" . $this->worksheetid . "/private/full";

        return "";
    }

    private function formatColumnID($val) {
        return preg_replace("/[^a-zA-Z0-9.-]/", "", strtolower($val));
    }

    public function SaveNewFile($inputFile, $service, $client) {
        try {
            //$mimeType = 'text/plain';
            $mimeType = 'text/csv';
            $file = new Google_DriveFile();
            $file->setTitle('Mon Titre ...');
            $file->setDescription('Ma Description ...');
            $file->setMimeType($mimeType);
            // Set the parent folder.
/*            if ($inputFile->parentId != null) {
                $parentsCollectionData = new DriveFileParentsCollection();
                $parentsCollectionData->setId($inputFile->parentId);
                $file->setParentsCollection(array($parentsCollectionData));
            }*/
           // Google_FilesServiceResource
            $insertService = new Google_FilesServiceResource($client);
            $createdFile = $insertService->insert($file, array(
                //'data' => '',
                'mimeType' => $mimeType,
            ));
            return $createdFile;
        } catch (apiServiceException $e) {
            /*
             * Log error and re-throw
             */
            error_log('Error saving new file to Drive: ' . $e->getMessage(), 0);
            throw $e;
        }
    }
}