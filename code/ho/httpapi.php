<?php
    function curl($url, $postData=array(),$headres = array()){
     	$ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url);
//        curl_setopt($ch, CURLOPT_URL,  "http://127.0.0.1:5984/pos/_design/staff/_list/getuser/staff_code"); 
    //    curl_setopt($ch, CURLOPT_URL,  "http://127.0.0.1:5984/pos/_bulk_docs"); 

        curl_setopt($ch, CURLOPT_HEADER, FALSE); 
        if(count($postData)>0){
            curl_setopt($ch, CURLOPT_POST, TRUE); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        }        
        curl_setopt($ch, CURLOPT_NOBODY, FALSE); // remove body 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        if(array_key_exists('is_content_type_allowed',$headres) && $headres['is_content_type_allowed']){
            if(array_key_exists('contentType', $headres)){
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type:".$headres['contentType']));
            }
        }

        $result = curl_exec($ch); 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
        curl_close($ch);
        return $result;
    }
/*
    $post['docs'][0] = array("class"=>"10");
    $post['docs'][1] = array("class"=>"11");
    $post['docs'][2] = array("class"=>"12");
    $post['docs'][3] = array("class"=>"13");
    $post['docs'][4] = array("class"=>"14");
    $post['docs'][5] = array("class"=>"15");
    $post['docs'][6] = array("class"=>"16");
    $post['docs'][7] = array("class"=>"17");
    $post['docs'][8] = array("class"=>"18");

    print_r(curl(($post)));

    /*

"getuser": "function(head, req) { var output = JSON.stringify(head) + '\\n\\n\\n' + JSON.stringify(req)+'\\n\\n\\n'; for (var i in req.form){ output += '\\n\\t\\t' + i + '=>' + req.form[i];}  while(row = getRow()) { output+=row.key+'=>'+  row.value+'\\n\\n\\n';} return output;}"
    */
    