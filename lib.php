<?php

/**
 * HTMLエスケープという安全対策を行う関数を取り入れてみました
 */
function h($s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/**
 * スタンプの種類
 */
const STAMPS_AVAILABLE = ['💝', '🙋', '💡', '🍵', '🌏'];
