<?php
namespace Main\Tools;



class XmlParse
{
    public function readENV($filePath)
    {
        $xml = simplexml_load_file($filePath);
        $arraySite = (array)$xml->config->siteConfig;
        $arrayDb = (array)$xml->config->dbConfig;
        $arraySwift = (array)$xml->config->swiftMailerConfig;
        $arrTMP = array_merge($arraySite, $arraySwift);
        return array_merge($arrTMP, $arrayDb);
    }
}