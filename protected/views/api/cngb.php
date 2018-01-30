<?php
header('Content-Type: text/xml');
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$total_size=count($datasetids);
$xml.="<gigadb_entrys total_dataset_num=\"$total_size\">";
if(!empty($datasetids)){
$dataset_no=1;
$datasetids=array_slice($datasetids,$offset);   
foreach($datasetids as $datasetid)
{
if($dataset_no>$limit)
{
    break;
}        
$xml.='<gigadb_entry>';      
$model=  Dataset::model()->findByPk($datasetid);
$xml.="<dataset doi=\"$model->identifier\">";
//title,description,
$title = htmlspecialchars($model->title, ENT_XML1, 'UTF-8');
$xml.="<title>$title </title>";
$description=  str_replace("<br>","<br />", $model->description);
$description= htmlspecialchars($description, ENT_XML1, 'UTF-8');
$xml.="<description> $description</description>";

$xml.="<publication date=\"$model->publication_date\">";
$xml.="<modification_date>$model->modification_date</modification_date>";
$xml.="</publication>";
$xml.="</dataset>"; 
$xml.='</gigadb_entry>'; 
$dataset_no++;
}    
}  

$xml.="</gigadb_entrys>";
$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();