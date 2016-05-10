<?php

namespace Oc\Libse\Cache;

class StatusCache
{
    const Active = 1;
    const TempUnavailable = 2;
    const Archived = 3;
    const ToBeApproved = 4;
    const NotYetPubliced = 5;
    const Blocked = 6;
    const BlockedHidden = 7;
}
