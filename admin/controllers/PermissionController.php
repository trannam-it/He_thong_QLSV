<?php
require_once __DIR__ . '/../models/Permission.php';

class PermissionController
{
    private $permission;

    public function __construct($conn)
    {
        $this->permission = new Permission($conn);
    }

    public function index()
    {
        $permissions = $this->permission->all();
        include __DIR__ . '/../views/permissions/index.php';
    }
}
