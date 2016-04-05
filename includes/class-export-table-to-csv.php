<?php

class ExportTableToCsv
{

    private $db;
    private $table_name;
    private $separator;


    function __construct($table_n, $sep, $filename)
    {

        global $wpdb;                                               //We gonna work with database aren't we?
        $this->db = $wpdb;                                          //Can't use global on it's own within a class so lets assign it to local object.
        $this->table_name = $table_n;
        $this->separator = $sep;

        $generatedDate = date('d-m-Y His');                         //Date will be part of file name. I dont like to see ...(23).csv downloaded

        $csvFile = $this->generate_csv();                           //Getting the text generated to download
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private", false);                    //Forces the browser to download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $filename . " " . $generatedDate . ".csv\";");
        header("Content-Transfer-Encoding: binary");

        echo $csvFile;                                              //Whatever is echoed here will be in the csv file
        exit;

    }


    function generate_csv()
    {

        $csv_output = '';                                           //Assigning the variable to store all future CSV file's data
        $table = $this->db->prefix . $this->table_name;             //For flexibility of the plugin and convenience, lets get the prefix

        $result = $this->db->get_results("SHOW COLUMNS FROM " . $table . "");   //Displays all COLUMN NAMES under 'Field' column in records returned

        if (count($result) > 0) {

            foreach ($result as $row) {
                $csv_output = $csv_output . $row->Field . $this->separator;
            }
            $csv_output = substr($csv_output, 0, -1);               //Removing the last separator, because thats how CSVs work

        }
        $csv_output .= "\n";

        $values = $this->db->get_results("SELECT * FROM " . $table . "");       //This here

        foreach ($values as $rowr) {
            $fields = array_values((array)$rowr);                  //Getting rid of the keys and using numeric array to get values
            $csv_output .= implode($this->separator, $fields);      //Generating string with field separator
            $csv_output .= "\n";    //Yeah...
        }

        return $csv_output; //Back to constructor

    }


}

