<?php

namespace App\Entity\Log;

class LogConst
{
    public const COLOR_PRIMARY = 'color: #00d1b2;';
    public const COLOR_LINK = 'color: #485fc7;';
    public const COLOR_SUCCESS = 'color: #3e8ed0;';
    public const COLOR_INFO = 'color: #48c78e;';
    public const COLOR_WARNING = 'color: #ffe08a;';
    public const COLOR_DANGER = 'color: #f14668;';
    public const BOLD = 'font-weight: bold;';

    // 全
    public const VISIBILITY_GLOBAL = 'global';
    // 自島のみ
    public const VISIBILITY_PUBLIC = 'public';
    // 自分のみ
    public const VISIBILITY_PRIVATE = 'private';
}
