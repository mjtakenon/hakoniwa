<?php

namespace App\Services\Hakoniwa\Log;

use App\Services\Hakoniwa\Util\Point;

class LogVisibility
{
    // 全
    public const VISIBILITY_GLOBAL = 'global';
    // 自島のみ
    public const VISIBILITY_PUBLIC = 'public';
    // 自分のみ
    public const VISIBILITY_PRIVATE = 'private';
}
