<?php

namespace App\Queries\Organization;

use App\Models\OrganizationInvite;
use Illuminate\Pagination\CursorPaginator;

class ListOrganizationInvitationsQueryHandler
{
    public function handle(ListOrganizationInvitationsQuery $query): CursorPaginator
    {
        $builder = OrganizationInvite::query()
            ->where('organization_id', $query->organizationId)
            ->when(filled($query->search), fn ($builder) => $builder->where('email', 'LIKE', '%'.$query->search.'%'))
            ->when($query->status === 'pending', function ($builder): void {
                $builder->whereNull('accepted_at')
                    ->where(function ($nestedBuilder): void {
                        $nestedBuilder->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            })
            ->when($query->status === 'accepted', fn ($builder) => $builder->whereNotNull('accepted_at'))
            ->when($query->status === 'expired', fn ($builder) => $builder->whereNull('accepted_at')->whereNotNull('expires_at')->where('expires_at', '<=', now()))
            ->latest('id');

        return $builder->cursorPaginate($query->perPage);
    }
}
