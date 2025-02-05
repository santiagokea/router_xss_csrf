<?php
/*
MIT License

Copyright (c) 2021 <info@phprouter.com>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

function get($route, $function_to_call){
  if( $_SERVER['REQUEST_METHOD'] == "GET" ){
    route($route, $function_to_call);
  }  
}
function post($route, $function_to_call){
  if( $_SERVER['REQUEST_METHOD'] == "POST" ){
    route($route, $function_to_call);
  }    
}
function any($route, $function_to_call){
  route($route, $function_to_call);   
}
function route($route, $function_to_call){
  if($route == "/404"){
    http_response_code(404);
    call_user_func_array($function_to_call, []);
    exit();
  }  
  $request_url = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
  $request_url = rtrim($request_url, '/');
  $request_url = strtok($request_url, '?');
  $route_parts = explode("/", $route);
  $request_url_parts = explode("/", $request_url);
  if( count($request_url_parts) == 1){ array_shift($route_parts); }
  if( count($route_parts) != count($request_url_parts) ){ return; }
  if( count($request_url_parts) == 1 && ( $route_parts[0] == $request_url_parts[0]) ){
    call_user_func_array($function_to_call, []);
    exit();
  }
  $parameters = [];
  for( $i = 1; $i < count($route_parts); $i++ ){
    $route_part = $route_parts[$i];
    if( preg_match("/^[:]/", $route_part) ){
      $route_part = ltrim($route_part, ':');
      array_push($parameters, $request_url_parts[$i]);
    }
    else if( $route_parts[$i] != $request_url_parts[$i] ){
      return;
    } 
  } // end for
  call_user_func_array($function_to_call, $parameters);
  exit();
}
function out($text){echo htmlspecialchars($text);}
function set_csrf(){
  if(session_status() == 1){ session_start(); }
  $csrf_token = bin2hex(random_bytes(25));
  $_SESSION['csrf'] = $csrf_token;
  echo '<input type="hidden" name="csrf" value="'.$csrf_token.'">';
}
function is_csrf_valid(){
  if(session_status() == 1){ session_start(); }
  if( ! isset($_SESSION['csrf']) || ! isset($_POST['csrf'])){ return false; }
  if( $_SESSION['csrf'] != $_POST['csrf']){ return false; }
  return true;
}
