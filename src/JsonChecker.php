<?php

namespace JsonFileChecker;

class JsonChecker
{
    /*
     * Script to compare language json files to ensure a complete key base.
     */

    private string $helpText = "Usage: \n" .
    "call the script with 2 param:\n" .
    "  -d <directory> - path to the directory containing the language files\n" .
    "  -b <base language> - only the 2 char abbreviation, like en or de\n";


    public function run()
    {
        #check params
        $help = getopt("h");
        $options = getopt("d:b:");
        if (sizeof($options) != 2 || !empty($help)) {
            echo  $this->helpText;
            exit();
        }

        #get files of dir
        $files = array_filter(scandir($options["d"]), function ($file) use ($options) {
            $parts = explode(".", $file);
            return ("json" === array_pop($parts) && $parts[0] !== $options['b']);
        });

        #prepare files for comparison
        $preparedFiles = array_map(function ($file) use ($options) {
            return $this->prepareFile($options["d"] . $file);
        }, $files);

        #get base file
        $baseFile = $this->prepareFile($options["d"] . $options['b'] . ".json");
        $baseFileName = $baseFile["fileName"];

        #compare all files with base file
        $error = false;
        foreach ($preparedFiles as $file) {
            $key = $file['fileName'];
            $compare = $file['content'];


            if ($compare === $baseFile["content"]) {
                continue;
            } else {
                [$diffToBase, $diffFromBase] = $this->compareFiles($baseFile["content"], $compare);
                if (empty($diffFromBase) && empty($diffToBase)) {
                    continue;
                } else {
                    $returnString =  "=================================\n==========Files differ!==========\n=================================\n\n";
                    $returnString .= empty($diffToBase) ? "" : "Missing in $key: \n\n" . json_encode($diffToBase)  . "\n\n";
                    $returnString .= empty($diffFromBase) ? "" : "Missing in $baseFileName: \n\n" . json_encode($diffFromBase)  . "\n\n";
                    echo $returnString;
                    $error = true;
                }
            }
        }
        if ($error) {
            exit(1);
        }
    }

    /**
     *  Reduces a multi level array file to a single level array
     *  ["a" => ["b" => "c"]]  -> ["a.b" => "c"]
     * @param array $array
     * @param string $prefix
     * @return array|mixed
     */
    private function flatten(array $array, string $prefix = '')
    {
        $result = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result = $result + $this->flatten($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }
        return $result;
    }

    /**
     *  Prepare file for comparison, file to array to flattened array
     * @param string $file
     * @return array
     */
    private function prepareFile(string $file): array
    {
        $parsedFile = json_decode(file_get_contents($file), true);
        $flattenedFile = array_keys($this->flatten($parsedFile));
        return ["fileName" => $file, "content" => $flattenedFile];
    }

    /**
     * Compare 2 files (now arrays) in both directions
     * @param $base
     * @param $compare
     * @return array[]
     */
    private function compareFiles($base, $compare): array
    {
        $diffToBase = [];
        foreach ($base as &$key) {
            if (!in_array($key, $compare)) {
                $diffToBase[] = $key;
            }
        }

        $diffFromBase = [];
        foreach ($compare as &$key) {
            if (!in_array($key, $base)) {
                $diffFromBase[] = $key;
            }
        }

        return [$diffToBase, $diffFromBase];
    }
}