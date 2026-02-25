<?php

if (! function_exists('tenant')) {
    function tenant(): \App\Data\TenantContext
    {
        return app(\App\Data\TenantContext::class);
    }
}
