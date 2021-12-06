<?php

// 暗号化パスワード発行テスト
$original_str = 'password';
echo password_hash($original_str, PASSWORD_DEFAULT);

?>