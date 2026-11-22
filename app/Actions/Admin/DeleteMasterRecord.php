<?php

namespace App\Actions\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use RuntimeException;

class DeleteMasterRecord
{
    /**
     * Delete a master record, converting FK violations into a friendly message.
     */
    public function handle(Model $record): void
    {
        try {
            $record->delete();
        } catch (QueryException $e) {
            if ((string) $e->getCode() === '23000') {
                throw new RuntimeException('Data tidak dapat dihapus karena masih dipakai oleh data lain.', 0, $e);
            }

            throw $e;
        }
    }
}
