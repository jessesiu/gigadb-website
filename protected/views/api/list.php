<?php
header("Content-Type: text/xml");
$xml="<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml.="<datasets>";
if($attribute == null)
{
foreach($models as $model){
$xml.="<doi prefix=\"10.5524\">".$model->identifier."</doi>";  
    
}
}else{
    
foreach($models as $model){
$xml.="<doi>";  
$xml.="<identifier>".$model->identifier."</identifier>";
$xml.="<publication_date>".$model->publication_date."</publication_date>";  
$xml.="</doi>";  
    
}   
}
$xml.="</datasets>";

$xml=preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
$output= simplexml_load_string($xml);
echo $output->asXML();