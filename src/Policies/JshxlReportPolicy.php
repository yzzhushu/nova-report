<?php

namespace Jshxl\Report\Policies;

class JshxlReportPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function view(): bool
    {
        return true;
    }

    public function create(): bool
    {
        return true;
    }

    public function update(): bool
    {
        return true;
    }

    public function replicate(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }
}
