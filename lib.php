<?php
// HTMLエスケープ用の関数（安全対策）
function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// スタンプの種類一覧
const STAMPS_AVAILABLE = ['💝', '🙋', '💡', '🍵', '🌏'];
