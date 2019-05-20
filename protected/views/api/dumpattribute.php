<?php
header("Content-Type: text/xml");
$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
$xml .= "<attributes>";
foreach ($model as $attribute) {
    $xml .= "<attribute>";
    $attribute_id = $attribute->id;
    $attribute_name = $attribute->attribute_name;
    $attribute_definition = $attribute->definition;
    $model = $attribute->model;
    $comment_name = $attribute->structured_comment_name;
    $value_syntax = $attribute->value_syntax;
    $units = $attribute->allowed_units;
    $occurance = $attribute->occurance;
    $ontology_link = $attribute->ontology_link;
    $note = $attribute->note;
    if (isset($attribute_name) && $attribute_name != "") {

        $xml .= "<id>$attribute_id</id>";
        $xml .= "<name>$attribute_name</name>";
        if (isset($attribute_defination))
            $xml .= "<definition>$attribute_defination</definition>";
        if (isset($model))
            $xml .= "<model>$model</model>";
        if (isset($comment_name))
            $xml .= "<structured_comment_name>$comment_name</structured_comment_name>";
        if (isset($value_syntax))
            $value_syntax = htmlspecialchars($value_syntax, ENT_XML1, 'UTF-8');
        $xml .= "<value_syntax>$value_syntax</value_syntax>";
        if (isset($units))
            $xml .= "<allowed_units>$units</allowed_units>";
        if (isset($occurance))
            $xml .= "<occurance>$occurance</occurance>";
        if (isset($ontology_link))
            $xml .= "<ontology_link>$ontology_link</ontology_link>";
        if (isset($note))
            $xml .= "<note>$note</note>";
    }
    $xml .= "</attribute>";
}
$xml .= "</attributes>";
$output = simplexml_load_string($xml);
echo $output->asXML();