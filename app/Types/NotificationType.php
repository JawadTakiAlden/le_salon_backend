<?php

namespace App\Types;

class NotificationType
{
    public const NEWORDER = 'add-order';
    public const DELETEORDER = 'delete-order';
    public const TORUNNER = 'move-order-to-runner';
    public const TOCASHER = 'move-order-to-casher';
}
