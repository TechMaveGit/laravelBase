<?php

use Illuminate\Support\Facades\Crypt;

function encryptUrlId($id) {
    $encrypted = Crypt::encryptString($id);
    return str_replace(['/', '+', '='], ['-', '_', ''], base64_encode($encrypted));
}

function decryptUrlId($encryptedId) {
    $decoded = base64_decode(str_replace(['-', '_'], ['/', '+'], $encryptedId));
    return Crypt::decryptString($decoded);
}
