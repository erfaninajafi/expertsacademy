<?php
declare(strict_types=1);
require_once __DIR__.'/include_site/studio/bootstrap.php';
use ExpertsAcademy\Studio\{Connection,DesignTokens,Http,Problem,PublicLead,StudioAssets};
Http::headers();
try{
    $method=$_SERVER['REQUEST_METHOD']??'GET';$token=PublicLead::token();
    if($method==='GET'&&isset($_GET['token'])){Http::json(['csrf'=>$token]);exit;}
    if($method==='POST'){
        $json=str_starts_with(strtolower($_SERVER['CONTENT_TYPE']??''),'application/json');$body=$json?Http::body():(object)$_POST;
        $result=PublicLead::receive(Connection::open(),$body,$_SERVER['HTTP_X_PUBLIC_CSRF']??($body->csrf??null));
        if($json){Http::json(['ok'=>true]+$result,201);exit;}
        $message='Thank you. Your request has been received.';
    }elseif($method!=='GET')throw new Problem(405,'Use GET or POST.');
    $e=fn($v)=>htmlspecialchars((string)$v,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Contact / Experts Academy</title><link rel="stylesheet" href="'.$e(StudioAssets::url('asset_site/css/page-studio.css')).'"></head><body style="margin:0"><main class="ea-studio" data-design-version="2" style="'.$e(DesignTokens::css(DesignTokens::defaults())).'"><section class="ds-component"><header class="ds-intro"><p class="ds-eyebrow">EXPERTS ACADEMY</p><h1>Start a conversation.</h1></header>';
    if(isset($message))echo '<p role="status">'.$e($message).'</p>';
    else echo '<form class="ds-lead-form" style="max-width:600px" method="post"><input type="hidden" name="csrf" value="'.$e($token).'"><input type="hidden" name="intent" value="contact"><label for="lead-name">Your name</label><input id="lead-name" name="name" autocomplete="name" maxlength="120" required><label for="lead-email">Email address</label><input id="lead-email" name="email" type="email" autocomplete="email" maxlength="200" required><label for="lead-message">Your message</label><textarea id="lead-message" name="message" maxlength="3000" required></textarea><label class="ds-consent"><input name="consent" type="checkbox" value="yes" required><span>I agree that the academy may store these details and contact me about this request.</span></label><button class="ds-button">Send your request</button></form>';
    echo '</section></main></body></html>';
}catch(Throwable $error){Http::error($error);}
